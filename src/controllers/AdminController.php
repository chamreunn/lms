<?php
require_once 'src/models/AdminModel.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminController
{
    public function getPendingLate()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLatein();

        require 'src/views/admin/lateinpending.php';
    }

    public function viewDetail()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            // Get user_id from the query string
            $user_id = $_GET['user_id'] ?? null;

            if ($user_id) {
                $userController = new AdminModel();
                $userDetails = $userController->getUserById($user_id);
                $requests = $userController->getUserLeaveRequests($user_id);
                $getlatein = $userController->getOvertimeIn($user_id);
                $getleavecounts = $userController->countUserApprovedLeaveRequests($user_id);
                $getovertimeincount = $userController->getOvertimeInCount($user_id);

                require 'src/views/admin/user_detail.php';
            } else {
                // Handle the case where user_id is not provided
                echo "User ID not provided.";
            }
        }
    }

    public function editUserDetail()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            // Get user_id from the query string
            $user_id = $_GET['user_id'] ?? null;

            if ($user_id) {
                $userController = new AdminModel();
                $userDetails = $userController->getUserById($user_id);
                $requests = $userController->getUserLeaveRequests($user_id);
                $getlatein = $userController->getOvertimeIn($user_id);
                $getleavecounts = $userController->countUserApprovedLeaveRequests($user_id);
                $getovertimeincount = $userController->getOvertimeInCount($user_id);

                require 'src/views/admin/edit_user_detail.php';
            } else {
                // Handle the case where user_id is not provided
                echo "User ID not provided.";
            }
        }
    }

    public function security()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            // Get user_id from the query string
            $user_id = $_GET['user_id'] ?? null;

            if ($user_id) {
                $userController = new AdminModel();
                $userDetails = $userController->getUserById($user_id);
                $requests = $userController->getUserLeaveRequests($user_id);
                $getlatein = $userController->getOvertimeIn($user_id);
                $getleavecounts = $userController->countUserApprovedLeaveRequests($user_id);
                $getovertimeincount = $userController->getOvertimeInCount($user_id);

                require 'src/views/settings/security.php';
            } else {
                // Handle the case where user_id is not provided
                echo "User ID not provided.";
            }
        }
    }

    public function approved()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $approver_id = $_SESSION['user_id'];
            $action = $_POST['action'];
            $request_id = $_POST['id'];
            $user_email = $_POST['user_email'];
            $comment = isset($_POST['comment']) && !empty(trim($_POST['comment'])) ? trim($_POST['comment']) : null;

            // Handle file upload if a file is provided
            if (isset($_FILES['signature']) && $_FILES['signature']['error'] == UPLOAD_ERR_OK) {
                $signature = $this->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/admin_signatures/');
                if ($signature === false) {
                    $_SESSION['error'] = [
                        'title' => "ហត្ថលេខា",
                        'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                    ];
                    header("Location: /elms/adminpending");
                    exit();
                }
            } else {
                $signature = null; // No file was uploaded, set $signature to null
            }

            // Send email notification 
            if (!$this->sendEmailNotification($user_email, $comment, $action)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/adminpending");
                exit();
            }

            $approveModel = new AdminModel();
            $approvals = $approveModel->updateRequest($approver_id, $action, $request_id, $comment, $signature);

            if ($approvals) {
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "អ្នកបាន $action ដោយជោគជ័យ។"
                ];
                header("Location: /elms/adminpending");
                exit();
            } else {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/adminpending");
                exit();
            }
        }

        require 'src/views/admin/lateinpending.php';
    }

    private function handleFileUpload($file, $allowed_extensions, $max_size, $upload_path)
    {
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_error = $file['error'];
        $file_size = $file['size'];

        if ($file_error === UPLOAD_ERR_NO_FILE) {
            // No file was uploaded
            return null;
        }

        if ($file_error !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "An error occurred during the file upload."
            ];
            return false;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Invalid attachment file type."
            ];
            return false;
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Attachment file size exceeds the limit."
            ];
            return false;
        }

        $unique_file_name = uniqid('', true) . '.' . $file_ext;
        $destination = $upload_path . $unique_file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            return $unique_file_name;
        } else {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Failed to move the uploaded file."
            ];
            return false;
        }
    }

    private function sendEmailNotification($user_email, $comment, $action)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pothhchamreun@gmail.com';
            $mail->Password   = 'kyph nvwd ncpa gyzi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');
            $mail->addAddress($user_email);

            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'ការជូនដំណឹងស្តីពីការចូលធ្វើការដែលបានអនុម័ត/បដិសេធ';

            $statusMessage = $action == 'Approved' ? 'បានអនុម័ត' : 'បានបដិសេធ';

            $body = "
        <div style='text-align: center; padding: 50px; font-family: Khmer mef2;'>
            <h1 style='color: " . ($action == 'Approved' ? '#28a745' : '#dc3545') . "; font-size: 36px;'>$statusMessage</h1>
            <p style='font-size: 18px;'>$comment</p>
            <p style='font-size: 14px; color: #777;'>ក្រុមការងារ</p>
        </div>";

            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $user_email");
                return true;
            } else {
                error_log("Email failed to send to $user_email: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
