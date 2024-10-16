<?php
require_once 'src/models/SettingModel.php';
require_once 'src/models/User.php';

class SettingController
{
    public function index()
    {
        $settingModel = new User();
        $myaccounts = $settingModel->getAllUserApi($_SESSION['user_id']);

        require 'src/views/settings/myaccount.php';
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

    public function updateProfilePicture()
    {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $userModel = new User();
            $userId = $_SESSION['user_id'];

            // Handle file upload
            $file = $_FILES['profile_picture'];
            $fileName = basename($file['name']);
            $uploadDir = 'public/uploads/profiles/';
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Update profile picture path in database
                $userModel->updateUserProfileApi($userId, $uploadFile, $_SESSION['token']);
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កែប្រែរូបភាពបានជោគជ័យ។"
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "មិនអាចបញ្ចូលរូបភាពបានទេ"
                ];
            }
        } else {
            $_SESSION['error'] = [
                'title' => "បរាជ័យ",
                'message' => "មិនអាចបញ្ចូលរូបភាពបានទេ"
            ];
        }
        sleep(1);
        header('Location: /elms/edit_user_detail?user_id=' . $userId);
        exit();
    }
}
