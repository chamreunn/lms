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

                if (!$authResult || $authResult['http_code'] !== 200) {
                    $_SESSION['error'] = [
                        'title' => "Authentication Error",
                        'message' => "Invalid email or password"
                    ];
                } else {
                    $user = $authResult['user'];
                    $token = $authResult['token'];

                    if ($user['active'] === '0') {
                        $_SESSION['blocked_user'] = true;
                        $_SESSION['user_khmer_name'] = $user['khmer_name'];
                        $_SESSION['user_profile'] = $user['profile_picture'];
                        require 'src/views/errors/block_page.php';
                        exit;
                    } else {
                        // Store user data and token in session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['user_khmer_name'] = $user['lastNameKh'] . ' ' . $user['firstNameKh'];
                        $_SESSION['user_eng_name'] = $user['engName'];
                        $_SESSION['user_profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'];
                        // user role ftor redirec to dashboard 
                        $_SESSION['role'] = $user['roleLeave'];
                        $_SESSION['officeId'] = $user['officeId'];
                        $_SESSION['departmentId'] = $user['departmentId'];
                        // $_SESSION['positionId'] = $user['positionId'];
                        $_SESSION['token'] = $token; // Store the token

                        // Fetch position_name and store it in session
                        $position = $userModel->getRoleApi($user['roleId'], $token);
                        $_SESSION['position'] = $position['data']['roleNameKh'];

                        // get department api 
                        $department = $userModel->getDepartmentApi($user['departmentId'], $token);
                        $_SESSION['departmentName'] = $department['data']['departmentNameKh'];
                        // get office api 
                        $department = $userModel->getOfficeApi($user['officeId'], $token);
                        $_SESSION['officeName'] = $department['data']['officeNameKh'];

                        // Log the login trace
                        $userModel->logLoginTrace($user['id'], $_SERVER['REMOTE_ADDR']);

                        header('Location: /elms/dashboard');
                        exit;
                    }
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

    public function forgotPassword()
    {
        require 'src/views/auth/forgot-password.php';
    }
}
