<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/LeaveApproval.php';
require_once 'src/models/Leavetype.php';
require_once 'src/models/User.php';
require_once 'src/models/Notification.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LeaveController
{
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();

            $user_id = $_SESSION['user_id'];
            $leave_type_id = $_POST['leave_type_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $remarks = $_POST['remarks'];
            $message = $_SESSION['user_khmer_name'] . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $activity =  "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // Handle file upload for attachment
            $attachment_name = $this->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Handle file upload for signature
            $signature_name = $this->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signature_name === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Fetch leave type details including duration from database
            $leaveTypeModel = new Leavetype();
            $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
            if (!$leaveType) {
                $_SESSION['error'] = [
                    'title' => "Leave Type Error",
                    'message' => "Invalid leave type selected."
                ];
                header("Location: /elms/dashboard");
                exit();
            }

            $leave_type_duration = $leaveType['duration'];

            // Calculate duration in business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $this->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare duration_days with leave_type_duration
            if ($duration_days > $leave_type_duration) {
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
                ];
                header("Location: /elms/dashboard");
                exit();
            }

            // Fetch the user's office details
            $userDoffice = $userModel->getdOffice();
            if (!is_array($userDoffice) || !isset($userDoffice['doffice_id'])) {
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find office details. Please contact support."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            $managerEmail = $userDoffice['demail'];
            $managerNumber = $userDoffice['dnumber'];
            $senderProfileImageUrl = 'public/img/icons/brands/logo2.png'; // Adjust the path as needed

            // Create leave request
            $leaveRequestModel = new LeaveRequest();
            $leaveRequestId = $leaveRequestModel->create($user_id, $leave_type_id, $leaveType['name'], $start_date, $end_date, $remarks, $duration_days, $attachment_name, $signature_name);

            
            // Send email notification 
            if (!$this->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }
            // Create notification for the user
            $notificationModel = new Notification();
            $notificationModel->createNotification($userDoffice['doffice_id'], $user_id, $leaveRequestId, $message);
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ " . $userDoffice['dkhmer_name']
            ];
            header("Location: /elms/leave-requests");
            exit();
        } else {
            require 'src/views/leave/apply.php';
        }
    }

    // private function sendTelegramNotification($userChatId, $message)
    // {
    //     $botToken = "7138737839:AAG6VnvPWQJLBHAGt6_N4S1U59ZROruHseo"; // Replace with your bot token
    //     $chatId = "$userChatId"; // Replace with the actual chat ID of the manager

    //     // Ensure chatId is a numeric value, not a URL or string
    //     if (!is_numeric($chatId)) {
    //         $_SESSION['error'] = [
    //             'title' => "Telegram Error",
    //             'message' => "Invalid chat ID. Please check the chat ID and try again."
    //         ];
    //         header("Location: /elms/apply-leave");
    //         exit();
    //     }

    //     $url = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($message);

    //     // Send the request to the Telegram API
    //     $ch = curl_init();
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_URL, $url);

    //     // Disable SSL verification for testing
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    //     $result = curl_exec($ch);

    //     if ($result === false) {
    //         // Capture and log the cURL error
    //         $error = curl_error($ch);
    //         error_log("cURL error: $error");
    //         $_SESSION['error'] = [
    //             'title' => "Telegram Error",
    //             'message' => "Notification could not be sent via Telegram. cURL error: $error"
    //         ];
    //         curl_close($ch);
    //         header("Location: /elms/apply-leave");
    //         exit();
    //     }

    //     curl_close($ch);

    //     // Check the Telegram API response
    //     $response = json_decode($result, true);
    //     if ($response['ok'] !== true) {
    //         // Capture and log the Telegram API error
    //         $errorMessage = $response['description'] ?? 'Unknown error';
    //         error_log("Telegram API error: $errorMessage");
    //         $_SESSION['error'] = [
    //             'title' => "Telegram Error",
    //             'message' => "Notification could not be sent via Telegram. API error: $errorMessage"
    //         ];
    //         header("Location: /elms/apply-leave");
    //         exit();
    //     }

    //     // Log success message for debugging
    //     error_log("Telegram message sent successfully.");
    // }

    private function sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password   = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format dates
            $start_date_formatted = (new DateTime($start_date))->format('j F, Y');
            $end_date_formatted = (new DateTime($end_date))->format('j F, Y');

            //Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');
            $mail->addAddress($managerEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
            <html>
            <head>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <style>
                    .profile-img {
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #e2e2e2;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .header {
                        background-color: #007bff;
                        color: white;
                        padding: 10px;
                        border-radius: 10px 10px 0 0;
                    }
                    .icon {
                        vertical-align: middle;
                        margin-right: 10px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #f9f9f9;
                    }
                    .btn {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 10px;
                        color: white;
                        background-color: #007bff;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        padding: 10px;
                        text-align: center;
                        background-color: #f1f1f1;
                        border-radius: 0 0 10px 10px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h4><img src='http://localhost/elms/public/img/icons/brands/logo2.png' class='icon' alt='Leave Request' /> Leave Request Notification</h4>
                    </div>
                    <div class='content'>
                        <p>$message</p>
                        <p><strong>រយៈពេល :</strong> $duration_days ថ្ងៃ</p>
                        <p><strong>ប្រភេទច្បាប់ :</strong> $leaveType</p>
                        <p><strong>ចាប់ពីថ្ងៃ :</strong> $start_date_formatted</p>
                        <p><strong>ដល់ថ្ងៃ​ :</strong> $end_date_formatted</p>
                        <p><strong>មូលហេតុ :</strong> $remarks</p>
                        <a href='http://localhost/elms/view-leave-detail?leave_id={$leaveRequestId}' class='btn'>ចុចទីនេះ</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Leave Management System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $managerEmail");
                return true;
            } else {
                error_log("Email failed to send to $managerEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function sendEmailNotificationToHOffice($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth   = true;
            $mail->Username   = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password   = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format dates
            $start_date_formatted = (new DateTime($start_date))->format('j F, Y');
            $end_date_formatted = (new DateTime($end_date))->format('j F, Y');
            $updated_at_formatted = (new DateTime($updatedAt))->format('j F, Y H:i:s');

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');
            $mail->addAddress($managerEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
            <html>
            <head>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <style>
                    .profile-img {
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #e2e2e2;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .header {
                        background-color: #007bff;
                        color: white;
                        padding: 10px;
                        border-radius: 10px 10px 0 0;
                    }
                    .icon {
                        vertical-align: middle;
                        margin-right: 10px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #f9f9f9;
                    }
                    .btn {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 10px;
                        color: white;
                        background-color: #007bff;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        padding: 10px;
                        text-align: center;
                        background-color: #f1f1f1;
                        border-radius: 0 0 10px 10px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h4>
                            <img src='http://localhost/elms/public/img/icons/brands/logo2.png' class='icon' alt='Leave Request' /> 
                            Leave Request Notification
                        </h4>
                    </div>
                    <div class='content'>
                        <p>$username</p>
                        <p><strong>រយៈពេល :</strong> $duration_days ថ្ងៃ</p>
                        <p><strong>ប្រភេទច្បាប់ :</strong> $leaveType</p>
                        <p><strong>ចាប់ពីថ្ងៃ :</strong> $start_date_formatted</p>
                        <p><strong>ដល់ថ្ងៃ​ :</strong> $end_date_formatted</p>
                        <p><strong>មូលហេតុ :</strong> $uremarks</p>
                        <hr>
                        <p>$message</p>"
                . (!empty($remarks) ? "<p><strong>មតិយោបល់ :</strong> $remarks</p>" : "") . "
                        <p><strong>បានអនុម័តនៅថ្ងៃ:</strong> $updated_at_formatted</p>
                        <a href='http://localhost/elms/view-leave-detail?leave_id={$leaveRequestId}' class='btn'>ចុចទីនេះ</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Leave Management System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $managerEmail");
                return true;
            } else {
                error_log("Email failed to send to $managerEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
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

    private function calculateBusinessDays(DateTime $start_date, DateTime $end_date)
    {
        $business_days = 0;
        $current_date = clone $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = $current_date->format('N');
            if ($day_of_week < 6) { // Monday to Friday are business days
                $business_days++;
            }
            $current_date->modify('+1 day');
        }

        return $business_days;
    }

    public function viewRequests()
    {
        $leaveRequestModel = new LeaveRequest();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/requests.php';
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new LeaveRequest();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/requests.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new LeaveRequest();
            $leave_id = (int)$_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id);

            if ($request) {
                require 'src/views/leave/viewleave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Retrieve POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'];
            $uremarks = $_POST['uremarks'];
            $uname = $_POST['uname'];
            $leaveType = $_POST['leaveType'];
            $user_id = $_POST['user_id']; // ID of the user who applied for leave
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $duration_days = $_POST['duration'];
            $approver_id = $_SESSION['user_id'];
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // Handle file upload for manager's signature
            $signaturePath = $this->handleFileUpload($_FILES['manager_signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signaturePath === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Create approval record
            $leaveApproval = new LeaveApproval();
            $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks, $signaturePath);

            // Fetch office details
            $userModel = new User();
            $userHoffice = $userModel->gethOffice();
            if (!is_array($userHoffice) || !isset($userHoffice['hoffice_id'])) {
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find office details. Please contact support."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            $managerEmail = $userHoffice['hemail'];

            // Send email notification
            if (!$this->sendEmailNotificationToHOffice($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Create notification
            $notificationModel = new Notification();
            $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);
            // Create Activity
            $userModel = new User();
            $userId = $_SESSION['user_id'];
            $activity = "បាន " . $status . "ច្បាប់ឈប់សម្រាក " . $uname;
            $userModel->logUserActivity($userId, $activity, $_SERVER['REMOTE_ADDR']);

            $_SESSION['success'] = [
                'title' => "សំណើច្បាប់",
                'message' => "សំណើច្បាប់ត្រូវបាន " . $status
            ];
            header('location: /elms/pending');
            exit();
        } else {
            $leaveApprovalModel = new LeaveApproval();
            $requests = $leaveApprovalModel->getPendingRequestsForApprover($_SESSION['user_id']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/approvals.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new LeaveApproval();
        $requests = $leaveRequestModel->getdhapproved($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/approved.php';
    }

    public function viewCalendar()
    {
        $leaveRequestModel = new LeaveRequest();
        $leaves = $leaveRequestModel->getAllLeaves();

        require 'src/views/leave/calendar.php';
    }

    public function delete($id)
    {
        $deleteLeaveRequest = new LeaveRequest();
        if ($deleteLeaveRequest->deleteLeaveRequest($id)) {
            $_SESSION['success'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "លុបសំណើច្បាប់បានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "មិនអាចលុបសំណើច្បាប់នេះបានទេ។"
            ];
        }
        header("Location: /elms/leave-requests");
        exit();
    }

    public function cancel($id, $status)
    {
        $deleteLeaveRequest = new LeaveRequest();
        if ($deleteLeaveRequest->cancelLeaveRequest($id, $status)) {
            $_SESSION['success'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "លុបសំណើច្បាប់បានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបសំណើច្បាប់",
                'message' => "មិនអាចលុបសំណើច្បាប់នេះបានទេ។"
            ];
        }
        header("Location: /elms/leave-requests");
        exit();
    }
}
