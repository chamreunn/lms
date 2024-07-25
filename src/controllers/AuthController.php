<?php
require_once 'src/models/User.php';

class AuthController
{
    public function login()
    {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = htmlspecialchars($_POST['password']);

            if ($email && $password) {
                $userModel = new User();
                $user = $userModel->findByEmail($email);

                if (!$user) {
                    $_SESSION['error'] = [
                        'title' => "អាសយដ្ឋានអ៊ីមែល",
                        'message' => "អាសយដ្ឋានអ៊ីមែលមិនត្រឹមត្រូវ"
                    ];
                } elseif (!password_verify($password, $user['password_hash'])) {
                    $_SESSION['error'] = [
                        'title' => "ពាក្យសម្ងាត់",
                        'message' => "ពាក្យសម្ងាត់មិនត្រឹមត្រូវ"
                    ];
                } elseif ($user['status'] === 'Inactive') {
                    // Pass user data to block page
                    $_SESSION['blocked_user'] = true;
                    $_SESSION['user_khmer_name'] = $user['khmer_name']; // Example: Pass user name for display
                    $_SESSION['user_profile'] = $user['profile_picture'];
                    require 'src/views/errors/block_page.php';
                    exit;
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_khmer_name'] = $user['khmer_name'];
                    $_SESSION['user_profile'] = $user['profile_picture'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['officeId'] = $user['office_id'];
                    // Fetch position_name and store it in session
                    $position = $userModel->getUserByPosition($user['id']);
                    $_SESSION['position'] = $position['position_name'];

                    $userModel->logLoginTrace($user['id'], $_SERVER['REMOTE_ADDR']);

                    header('Location: dashboard');
                    exit;
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "ការវាយបញ្ចូលមិនត្រឹមត្រូវ",
                    'message' => "សូមវាយបញ្ចូលអាសយដ្ឋានអ៊ីមែល និងពាក្យសម្ងាត់"
                ];
            }
        }

        require 'src/views/auth/login.php';
    }
}
