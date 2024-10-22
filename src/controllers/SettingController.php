<?php
require_once 'src/models/SettingModel.php';
require_once 'src/models/User.php';
require_once 'vendor/autoload.php';

class SettingController
{
    public function index()
    {
        $settingModel = new User();
        $myaccounts = $settingModel->getAllUserApi($_SESSION['user_id']);

        require 'src/views/settings/myaccount.php';
    }

    public function create2fa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sanitize and retrieve the 2FA code and secret from the POST data
            $code = htmlspecialchars(trim($_POST['2fa_code']), ENT_QUOTES, 'UTF-8');
            $secret = htmlspecialchars(trim($_POST['secretCode']), ENT_QUOTES, 'UTF-8');
            $userId = $_SESSION['user_id'];

            // Verify that the required data is present
            if (empty($code) || empty($secret)) {
                $_SESSION['error'] = [
                    'title' => 'Token Error',
                    'message' => 'Both 2FA code and secret code are required.'
                ];
                header('location: /elms/setting_security?user_id=' . $userId);
                exit();
            }

            // Create an instance of the Google Authenticator
            $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

            // Validate the provided 2FA code against the stored secret
            if ($g->checkCode($secret, $code)) {
                // Load the model
                $authModel = new SettingModel();

                // Check if a 2FA record already exists for the user
                $existing2fa = $authModel->get2faByUserId($userId);

                if ($existing2fa) {
                    // Update existing 2FA record
                    $isUpdated = $authModel->update2fa($userId, 1);
                    if ($isUpdated) {
                        // Set the session authenticator to true and show success message
                        $_SESSION['authenticator'] = true;
                        $_SESSION['success'] = [
                            'title' => '2FA Enabled',
                            'message' => 'Two-Factor Authentication has been successfully updated.'
                        ];
                    } else {
                        $_SESSION['error'] = [
                            'title' => 'Database Error',
                            'message' => 'Failed to update 2FA.'
                        ];
                    }
                } else {
                    // Create a new 2FA record
                    $isCreated = $authModel->create2fa($userId, $secret);
                    if ($isCreated) {
                        // Set the session authenticator to true and show success message
                        $_SESSION['authenticator'] = true;
                        $_SESSION['success'] = [
                            'title' => '2FA Enabled',
                            'message' => 'Two-Factor Authentication has been successfully enabled.'
                        ];
                    } else {
                        $_SESSION['error'] = [
                            'title' => 'Database Error',
                            'message' => 'Failed to enable 2FA.'
                        ];
                    }
                }

                // Redirect after successfully enabling 2FA and setting the session
                header('location: /elms/setting_security?user_id=' . $userId);
                exit();
            } else {
                // Code is invalid
                $_SESSION['error'] = [
                    'title' => 'Token Error',
                    'message' => 'Invalid 2FA code provided.'
                ];
                header('location: /elms/setting_security?user_id=' . $userId);
                exit();
            }
        }
    }

    public function disable2fa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $settingModel = new SettingModel();
            $disabled = $settingModel->delete2fa($_SESSION['user_id']);

            if ($disabled) {
                // Unset the 2FA session authenticator flag
                unset($_SESSION['authenticator']);

                // Set a success message
                $_SESSION['success'] = [
                    'title' => '2FA Disabled',
                    'message' => 'Two-Factor Authentication has been successfully disabled.'
                ];

                // Redirect to settings page after successful disable
                header('Location: /elms/setting_security?user_id=' . $_SESSION['user_id']);
                exit;
            } else {
                // Set an error message if unable to disable
                $_SESSION['error'] = [
                    'title' => '2FA Disabled',
                    'message' => 'Failed to disable Two-Factor Authentication.'
                ];

                // Redirect back to settings page
                header('Location: /elms/setting_security?user_id=' . $_SESSION['user_id']);
                exit;
            }
        }
    }

    public function verifyAuth2Fa()
    {
        // Load the Google Authenticator
        $g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Sanitize and combine the user input into a single code
            $code = implode('', [
                htmlspecialchars(trim($_POST['digit1']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim($_POST['digit2']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim($_POST['digit3']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim($_POST['digit4']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim($_POST['digit5']), ENT_QUOTES, 'UTF-8'),
                htmlspecialchars(trim($_POST['digit6']), ENT_QUOTES, 'UTF-8')
            ]);

            // Ensure user_id is in session
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = [
                    'title' => 'Session Error',
                    'message' => 'Your session has expired. Please log in again.'
                ];
                header('Location: /elms/login');
                exit();
            }

            // Fetch the user ID from the session
            $userId = $_SESSION['user_id'];

            // Load the 2FA model or database handler to fetch the secret
            $authModel = new User();
            $user2FA = $authModel->getUser2FA($userId);

            // If no 2FA record found or if it's not an array or empty secret_code, redirect to login
            if (!is_array($user2FA) || empty($user2FA['secret_code'])) {
                $_SESSION['error'] = [
                    'title' => '2FA Error',
                    'message' => 'No 2FA settings found for this user.'
                ];
                header('Location: /elms/login');
                exit();
            }

            $secret = $user2FA['secret_code'];

            // Check the provided code against the stored secret
            if ($g->checkCode($secret, $code)) {
                // 2FA successful: Move session data to permanent session
                $_SESSION['user_id'] = $userId; // Keep the user ID in the session

                // Clean up temporary session variables if any
                unset($_SESSION['temp_user_data']);
                unset($_SESSION['temp_token']);

                // Ensure token is available in session
                if (!isset($_SESSION['token'])) {
                    $_SESSION['error'] = [
                        'title' => 'Session Error',
                        'message' => 'Session token is missing. Please log in again.'
                    ];
                    header('Location: /elms/login');
                    exit();
                }

                // Fetch additional user details like position, department, etc.
                $position = $authModel->getRoleApi($_SESSION['roleId'], $_SESSION['token']);
                $_SESSION['position'] = $position['data']['roleNameKh'] ?? 'N/A';
                $_SESSION['position_color'] = $position['data']['color'] ?? '#000000';

                // Ensure department ID is set in session
                if (isset($_SESSION['departmentId'])) {
                    $department = $authModel->getDepartmentApi($_SESSION['departmentId'], $_SESSION['token']);
                    $_SESSION['departmentName'] = $department['data']['departmentNameKh'] ?? 'null';
                }

                // Ensure office ID is set in session
                if (isset($_SESSION['officeId'])) {
                    $office = $authModel->getOfficeApi($_SESSION['officeId'], $_SESSION['token']);
                    $_SESSION['officeName'] = $office['data']['officeNameKh'] ?? 'null';
                }

                // Log the 2FA login trace (IP logging)
                $authModel->logLoginTrace($userId, $_SERVER['REMOTE_ADDR']);

                $_SESSION['authenticator'] = true;

                // Success message
                $_SESSION['success'] = [
                    'title' => "2FA Success",
                    'message' => "You have successfully authenticated."
                ];

                // Redirect to the dashboard or homepage
                header('Location: /elms/dashboard');
                exit();
            } else {
                // Invalid 2FA code, return error
                $_SESSION['error'] = [
                    'title' => 'Invalid Code',
                    'message' => 'The code you entered is incorrect. Please try again.'
                ];
                header('Location: /elms/v2faCode'); // Redirect back to the 2FA page
                exit();
            }
        } else {
            // If accessed via GET request, redirect to login
            header('Location: /elms/login');
            exit();
        }
    }

    public function updateEmail()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if token is set
            if (!isset($_SESSION['token'])) {
                $_SESSION['error'] = [
                    'title' => 'Token Error',
                    'message' => 'Session token is missing.'
                ];
                header('Location: /elms/setting_security?user_id=' . urlencode($_POST['user_id']));
                exit();
            }

            $userId = $_POST['user_id'];
            $newEmail = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

            if ($newEmail) {
                // Instantiate the User model
                $userModel = new User();

                // Attempt to update the user's email via the API
                $updateResult = $userModel->updateUserEmailApi($userId, $newEmail, $_SESSION['token']);

                if ($updateResult['success']) {
                    $_SESSION['success'] = [
                        'title' => 'Success',
                        'message' => 'Email has been updated successfully.'
                    ];
                } else {
                    $_SESSION['error'] = [
                        'title' => 'Update Failed',
                        'message' => 'Failed to update email. ' . $updateResult['error']
                    ];
                }
            } else {
                $_SESSION['error'] = [
                    'title' => 'Invalid Email',
                    'message' => 'The email address provided is invalid.'
                ];
            }

            // Redirect back to the settings page regardless of success or failure
            header('Location: /elms/setting_security?user_id=' . urlencode($userId));
            exit();
        }
    }

    public function updatePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if token is set
            if (!isset($_SESSION['token'])) {
                $_SESSION['error'] = [
                    'title' => 'Token Error',
                    'message' => 'Session token is missing.'
                ];
                header('Location: /elms/change_password'); // Adjust the redirect URL as needed
                exit();
            }

            $userId = $_POST['user_id']; // Assuming the user ID is stored in the session
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];

            if (!empty($password) && !empty($confirmPassword)) {
                if ($password === $confirmPassword) {

                    // Instantiate the User model
                    $userModel = new User();

                    // Attempt to update the user's password via the API
                    $updateResult = $userModel->updateUserPasswordApi($userId, $password, $_SESSION['token']);

                    if ($updateResult['success']) {
                        $_SESSION['success'] = [
                            'title' => 'Success',
                            'message' => 'Password has been updated successfully.'
                        ];
                    } else {
                        $_SESSION['error'] = [
                            'title' => 'Update Failed',
                            'message' => 'Failed to update password. ' . $updateResult['error']
                        ];
                    }
                } else {
                    $_SESSION['error'] = [
                        'title' => 'Password Mismatch',
                        'message' => 'The passwords do not match.'
                    ];
                }
            } else {
                $_SESSION['error'] = [
                    'title' => 'Empty Fields',
                    'message' => 'Please fill in both password fields.'
                ];
            }

            // Redirect back to the change password page
            header('Location: /elms/setting_security?user_id=' . urlencode($userId)); // Adjust the redirect URL as needed
            exit();
        }
    }

    public function activity()
    {
        $settingModel = new SettingModel();
        $activities = $settingModel->getUserActivity($_SESSION['user_id']);

        // Format timestamps using the timeAgo function
        foreach ($activities as &$activity) {
            $activity['created_at'] = $settingModel->timeAgo($activity['created_at']);
        }

        require 'src/views/settings/activity.php';
    }

    // In your controller or wherever the file upload happens
    public function updateProfilePicture()
    {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $userModel = new User();
            $userId = $_SESSION['user_id'];

            // Handle file upload
            $file = $_FILES['profile_picture'];
            $fileTmpPath = $file['tmp_name'];
            $fileName = basename($file['name']);

            // Define the target directory to store the uploaded file
            $uploadDir = 'public/uploads/profiles/'; // Adjust the path as per your setup
            $uploadFilePath = $uploadDir . $fileName;

            // Move uploaded file to the desired directory
            if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
                // Send the file itself to the API, not just the path
                $apiResponse = $userModel->updateUserProfileApi($userId, $uploadFilePath, $_SESSION['token']);

                if ($apiResponse['success']) {
                    $_SESSION['success'] = [
                        'title' => "Success",
                        'message' => "Profile picture updated successfully."
                    ];
                } else {
                    error_log("API response: " . print_r($apiResponse, true));
                    $_SESSION['error'] = [
                        'title' => "Failed",
                        'message' => "Failed to update profile picture in the API: " . $apiResponse['response']['message'] ?? $apiResponse['error']
                    ];
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "Failed",
                    'message' => "Failed to move the uploaded file to the server."
                ];
            }
        } else {
            $_SESSION['error'] = [
                'title' => "Failed",
                'message' => "No file was uploaded or there was an error uploading the file."
            ];
        }

        header('Location: /elms/edit_user_detail?user_id=' . $_SESSION['user_id']);
        exit();
    }
}
