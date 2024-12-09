<?php
require_once 'src/models/User.php';

class AuthController
{

    private $authModel;

    public function __construct()
    {
        $this->authModel = new User();
    }

    public function login()
    {
        // Check if the user is already logged in
        if (isset($_SESSION['user_id'])) {
            header('Location: dashboard'); // Redirect to the dashboard or another appropriate page
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars($_POST['password']);

            if ($email && $password) {
                $userModel = new User();
                $authResult = $userModel->authenticateUser($email, $password);

                // Check if $authResult is not false or null before accessing its elements
                if ($authResult && isset($authResult['http_code']) && $authResult['http_code'] === 200) {
                    $user = $authResult['data']['user']; // Access the actual user data
                    $token = $authResult['data']['token'];

                    // Check if user data is valid
                    if (isset($user['id'])) {
                        // Check if user is blocked
                        if ($user['active'] === '0') {
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['blocked_user'] = true;
                            $_SESSION['user_khmer_name'] = $user['lastNameKh'] . ' ' . $user['firstNameKh'];
                            $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'];

                            header('Location: /elms/block_page');
                            exit();
                        } else {
                            // Check if 2FA is enabled
                            $user2FA = $userModel->getUser2FA($user['id']);

                            if ($user2FA && isset($user2FA['is_2fa_enabled']) && $user2FA['is_2fa_enabled'] == '1') {
                                $_SESSION['2fa_attempts'] = $user2FA['is_2fa_enabled'] ?? 'N/A';
                                $_SESSION['temp_secret'] = $user2FA['secret_code'] ?? 'N/A';
                                $_SESSION['user_id'] = $user['id'] ?? 'N/A';
                                $_SESSION['idCard'] = $user['idCard'] ?? 'N/A';
                                $_SESSION['contact'] = $user['phoneNumber'] ?? 'N/A';
                                $_SESSION['gender'] = $user['gender'] ?? 'N/A';
                                $_SESSION['nationality'] = $user['nationality'] ?? 'N/A';
                                $_SESSION['identifyCard'] = $user['identifyCard'] ?? 'N/A';
                                $_SESSION['exprireDateIdenCard'] = $user['exprireDateIdenCard'] ?? 'N/A';
                                $_SESSION['passport'] = $user['passport'] ?? 'N/A';
                                $_SESSION['exprirePassport'] = $user['exprirePassport'] ?? 'N/A';
                                $_SESSION['pobAddress'] = $user['pobAddress'] ?? 'N/A';
                                $_SESSION['currentAddress'] = $user['currentAddress'] ?? 'N/A';
                                $_SESSION['status'] = $user['status'] ?? 'N/A';

                                $_SESSION['temp_token'] = $token ?? 'N/A';
                                $_SESSION['temp_user_data'] = $user ?? 'N/A';
                                $_SESSION['user_khmer_name'] = ($user['lastNameKh'] ?? 'N/A') . ' ' . ($user['firstNameKh'] ?? 'N/A');
                                $_SESSION['dob'] = $user['dateOfBirth'] ?? 'N/A';
                                $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default.jpg');
                                $_SESSION['role'] = $user['roleLeave'] ?? 'NULL';
                                $_SESSION['token'] = $token ?? 'N/A';
                                $_SESSION['departmentId'] = $user['departmentId'] ?? 'N/A';
                                $_SESSION['email'] = $user['email'] ?? 'N/A';
                                $_SESSION['officeId'] = $user['officeId'] ?? 'N/A';
                                $_SESSION['roleId'] = $user['roleId'] ?? 'N/A';

                                $_SESSION['BotUsername'] = "myelmsbot";
                                $_SESSION['LateInBot'] = "lateinoutbot";

                                // Fetch additional details
                                $position = $userModel->getRoleApi($user['roleId'] ?? '0', $token);
                                $_SESSION['position'] = $position['data']['roleNameKh'] ?? 'N/A';
                                $_SESSION['position_color'] = $position['data']['color'] ?? 'N/A';

                                $department = $userModel->getDepartmentApi($user['departmentId'] ?? '0', $token);
                                $_SESSION['departmentName'] = $department['data']['departmentNameKh'] ?? 'N/A';

                                $office = $userModel->getOfficeApi($user['officeId'] ?? '0', $token);
                                $_SESSION['officeName'] = $office['data']['officeNameKh'] ?? 'N/A';

                                header('Location: /elms/v2faCode');
                                exit;
                            }

                            // Store user data if 2FA is not enabled
                            $_SESSION['user_id'] = $user['id'] ?? 'N/A';
                            $_SESSION['idCard'] = $user['idCard'] ?? 'N/A';
                            $_SESSION['contact'] = $user['phoneNumber'] ?? 'N/A';
                            $_SESSION['gender'] = $user['gender'] ?? 'N/A';
                            $_SESSION['nationality'] = $user['nationality'] ?? 'N/A';
                            $_SESSION['identifyCard'] = $user['identifyCard'] ?? 'N/A';
                            $_SESSION['exprireDateIdenCard'] = $user['exprireDateIdenCard'] ?? 'N/A';
                            $_SESSION['passport'] = $user['passport'] ?? 'N/A';
                            $_SESSION['exprirePassport'] = $user['exprirePassport'] ?? 'N/A';
                            $_SESSION['email'] = $user['email'] ?? 'N/A';
                            $_SESSION['user_khmer_name'] = ($user['lastNameKh'] ?? 'N/A') . ' ' . ($user['firstNameKh'] ?? 'N/A');
                            $_SESSION['dob'] = $user['dateOfBirth'] ?? 'N/A';
                            $_SESSION['user_eng_name'] = $user['engName'] ?? 'N/A';
                            $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default.jpg');
                            $_SESSION['pobAddress'] = $user['pobAddress'] ?? 'N/A';
                            $_SESSION['currentAddress'] = $user['currentAddress'] ?? 'N/A';
                            $_SESSION['status'] = $user['status'] ?? 'N/A';

                            $_SESSION['role'] = $user['roleLeave'] ?? 'NULL';
                            $_SESSION['officeId'] = $user['officeId'] ?? 'N/A';
                            $_SESSION['departmentId'] = $user['departmentId'] ?? 'N/A';
                            $_SESSION['isAdmin'] = $user['isAdmin'] ?? false;  // Assuming `isAdmin` should be a boolean
                            $_SESSION['token'] = $token ?? 'N/A';

                            // Telegram information 
                            $_SESSION['BotUsername'] = "myelmsbot";
                            $_SESSION['LateInBot'] = "lateinoutbot";

                            // Fetch additional details
                            $position = $userModel->getRoleApi($user['roleId'] ?? '0', $token);
                            $_SESSION['position'] = $position['data']['roleNameKh'] ?? 'N/A';
                            $_SESSION['position_color'] = $position['data']['color'] ?? 'N/A';

                            $department = $userModel->getDepartmentApi($user['departmentId'] ?? '0', $token);
                            $_SESSION['departmentName'] = $department['data']['departmentNameKh'] ?? 'N/A';

                            $office = $userModel->getOfficeApi($user['officeId'] ?? '0', $token);
                            $_SESSION['officeName'] = $office['data']['officeNameKh'] ?? 'N/A';

                            // Log the login trace
                            $userModel->logLoginTrace($user['id'], $_SERVER['REMOTE_ADDR']);
                            header('Location: /elms/dashboard');
                            exit;
                        }
                    } else {
                        $_SESSION['error'] = [
                            'title' => "Authentication Error",
                            'message' => "User data is missing or invalid"
                        ];
                    }
                } else {
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "អាសយដ្ឋានអ៊ីមែល ឬពាក្យសម្ងាត់មិនត្រឹមត្រូវ។"
                    ];
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "អ៊ីម៉ែល និងពាក្យសម្ងាត់",
                    'message' => "សូមបញ្ចូលអាសយដ្ឋានអ៊ីម៉ែល និងពាក្យសម្ងាត់។"
                ];
            }
        }

        require 'src/views/auth/login.php';
    }

    public function logout($token)
    {
        $response = $this->authModel->logoutFromApi($token);

        if ($response['success']) {
            session_start();
            session_unset();
            session_destroy();

            header("Location: /elms/login");
            exit();
        } else {
            error_log("Logout Error: " . $response['message']);
            echo "<p>Error: Unable to log out. Please try again later.</p>";
        }
    }
}
