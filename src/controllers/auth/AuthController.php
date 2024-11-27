<?php
require_once 'src/models/User.php';

class AuthController
{
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
                                $_SESSION['2fa_attempts'] = $user2FA['is_2fa_enabled'];
                                $_SESSION['temp_secret'] = $user2FA['secret_code'];
                                $_SESSION['user_id'] = $user['id'];
                                $_SESSION['idCard'] = $user['idCard'];
                                $_SESSION['temp_token'] = $token;
                                $_SESSION['temp_user_data'] = $user;
                                $_SESSION['user_khmer_name'] = $user['lastNameKh'] . ' ' . $user['firstNameKh'];
                                $_SESSION['dob'] = $user['dateOfBirth'];
                                $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'];
                                $_SESSION['role'] = $user['roleLeave'] ?? 'NULL';
                                $_SESSION['token'] = $token;
                                $_SESSION['departmentId'] = $user['departmentId'];
                                $_SESSION['email'] = $user['email'];
                                $_SESSION['officeId'] = $user['officeId'];
                                $_SESSION['roleId'] = $user['roleId'];

                                $_SESSION['BotUsername'] = "myelmsbot";
                                $_SESSION['LateInBot'] = "lateinoutbot";

                                // Fetch additional details
                                $position = $userModel->getRoleApi($user['roleId'], $token);
                                $_SESSION['position'] = $position['data']['roleNameKh'];
                                $_SESSION['position_color'] = $position['data']['color'];
                                $department = $userModel->getDepartmentApi($user['departmentId'], $token);
                                $_SESSION['departmentName'] = $department['data']['departmentNameKh'] ?? 'null';
                                $office = $userModel->getOfficeApi($user['officeId'], $token);
                                $_SESSION['officeName'] = $office['data']['officeNameKh'] ?? 'null';

                                header('Location: /elms/v2faCode');
                                exit;
                            }

                            // Store user data if 2FA is not enabled
                            $_SESSION['user_id'] = $user['id'];
                            $_SESSION['idCard'] = $user['idCard'];
                            $_SESSION['email'] = $user['email'];
                            $_SESSION['user_khmer_name'] = $user['lastNameKh'] . ' ' . $user['firstNameKh'];
                            $_SESSION['dob'] = $user['dateOfBirth'];
                            $_SESSION['user_eng_name'] = $user['engName'];
                            $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'];
                            $_SESSION['role'] = $user['roleLeave'] ?? 'NULL';
                            $_SESSION['officeId'] = $user['officeId'];
                            $_SESSION['departmentId'] = $user['departmentId'];
                            $_SESSION['isAdmin'] = $user['isAdmin'];
                            $_SESSION['token'] = $token;

                            // telegram information 
                            $_SESSION['BotUsername'] = "myelmsbot";
                            $_SESSION['LateInBot'] = "lateinoutbot";

                            // Fetch additional details
                            $position = $userModel->getRoleApi($user['roleId'], $token);
                            $_SESSION['position'] = $position['data']['roleNameKh'];
                            $_SESSION['position_color'] = $position['data']['color'];
                            $department = $userModel->getDepartmentApi($user['departmentId'], $token);
                            $_SESSION['departmentName'] = $department['data']['departmentNameKh'] ?? 'null';
                            $office = $userModel->getOfficeApi($user['officeId'], $token);
                            $_SESSION['officeName'] = $office['data']['officeNameKh'] ?? 'null';

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
                        'title' => "Authentication Error",
                        'message' => "Invalid email or password"
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
}
