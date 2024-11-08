<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

require_once 'src/models/LeaveRequest.php';
require_once 'src/models/HeadDepartLeave.php';
require_once 'src/models/Leavetype.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HeadDepartController
{
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $userModel = new User();

            // Fetch session details
            $user_id = $_SESSION['user_id'] ?? null;
            $position = $_SESSION['position'] ?? null;
            $office = $_SESSION['officeName'] ?? null;
            $department = $_SESSION['departmentName'] ?? null;
            $token = $_SESSION['token'] ?? null;
            $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;

            if (!$user_id || !$position || !$department || !$token || !$user_khmer_name) {
                $_SESSION['error'] = [
                    'title' => "Session Error",
                    'message' => "Session information is missing. Please log in again."
                ];
                header("Location: /elms/login");
                exit();
            }

            $leave_type_id = $_POST['leave_type_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $remarks = $_POST['remarks'] ?? null;
            $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

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

            // Fetch leave type details including duration from the database
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

            // Fetch office details based on department
            $userDoffice = null;
            if (in_array($department, ["កិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
                // Fetch the user's Unit details
                $userDoffice = $userModel->getEmailLeaderDHU1Api($user_id, $token);
            } else {
                $userDoffice = $userModel->getEmailLeaderDHU2Api($user_id, $token);
            }

            if (empty($userDoffice) || empty($userDoffice['ids']) || empty($userDoffice['emails'])) {
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find Department details or no emails found. Please contact support."
                ];
                header('location: /elms/headofficepending');
                exit();
            }

            $managerEmail = is_array($userDoffice['emails']) ? implode(',', $userDoffice['emails']) : $userDoffice['emails'];

            // Create leave request
            $leaveRequestModel = new HeadDepartLeave();
            $leaveRequestId = $leaveRequestModel->create(
                $user_id,
                $leave_type_id,
                $position,
                $office,
                $department,
                $leaveType['name'],
                $start_date,
                $end_date,
                $remarks,
                $duration_days,
                $attachment_name,
                $signature_name
            );

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/hod-apply-leave");
                exit();
            }

            // Log user activity
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            // Send email notification 
            if (!$this->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/hod-apply-leave");
                exit();
            }

            // Create notification for the user
            $notificationModel = new Notification();
            $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
            ];
            header("Location: /elms/leave-requests");
            exit();
        } else {
            require 'src/views/leave/apply.php';
        }
    }

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
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

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

    private function sendEmailNotificationToDUnit($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)
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
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

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
        $leaveRequestModel = new HeadDepartLeave();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);

        require 'src/views/leave/requests.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new LeaveRequest();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);

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
            $request_id = $_POST['request_id'] ?? null;
            $status = $_POST['status'] ?? null;
            $remarks = $_POST['remarks'] ?? '';
            $uremarks = $_POST['uremarks'] ?? '';
            $uname = $_POST['uname'] ?? '';
            $leaveType = $_POST['leaveType'] ?? '';
            $user_id = $_POST['user_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $duration_days = $_POST['duration'] ?? null;
            $approver_id = $_SESSION['user_id'] ?? null;
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $leave = 1 ?? null;

            // Handle file upload for manager's signature
            $signaturePath = $this->handleFileUpload($_FILES['manager_signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signaturePath === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header('location: /elms/headdepartmentapproved');
                exit();
            }

            // Fetch office details
            $userModel = new User();
            $departmentName = $_SESSION['departmentName'] ?? null;

            if ($departmentName) {
                if (in_array($departmentName, ["កិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
                    // Fetch the user's Unit details
                    $userDoffice = $userModel->getEmailLeaderDHU1Api($_SESSION['user_id'], $_SESSION['token']);
                } else {
                    $userDoffice = $userModel->getEmailLeaderDHU2Api($_SESSION['user_id'], $_SESSION['token']);
                }

                if (empty($userDoffice) || empty($userDoffice['ids'])) {
                    $_SESSION['error'] = [
                        'title' => "Office Error",
                        'message' => "Unable to find Department details or no emails found. Please contact support."
                    ];
                    header('location: /elms/headofficepending');
                    exit();
                }

                $managerEmail = is_array($userDoffice['emails']) ? implode(',', $userDoffice['emails']) : $userDoffice['emails'];
            } else {
                // Handle the case where departmentName is not set
                $_SESSION['error'] = [
                    'title' => "Session Error",
                    'message' => "Department name not found in session. Please log in again."
                ];
                header("Location: /elms/login");
                exit();
            }

            $roleLeaves = new User();
            $roleLeave = $roleLeaves->getUserByIdApi($user_id, $_SESSION['token']);

            $leaveApproval = new HeadDepartLeave();

            // Check if approval should be processed or escalated
            if ($roleLeave['data']['roleLeave'] === 'User' && $duration_days <= 3) {
                // Direct approval for Users with leave duration <= 3 days
                $updateToApi = $leaveApproval->updateToApi($user_id, $start_date, $end_date, $leave, $_SESSION['token']);

                if ($updateToApi) {
                    // If the API call was successful
                    $_SESSION['success'] = [
                        'title' => "API Update",
                        'message' => "Leave status has been successfully updated in the API."
                    ];
                } else {
                    // If the API call failed
                    $_SESSION['error'] = [
                        'title' => "API Error",
                        'message' => "Failed to update leave to API. " . $updateToApi['error']
                    ];
                    header('location: /elms/headdepartpending');
                    exit();
                }

                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks, $signaturePath);

                // Handle escalation for non-User roles or leave duration > 3 days
                if (!$this->sendEmailNotificationToDUnit($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    $_SESSION['error'] = [
                        'title' => "Email Error",
                        'message' => "Notification email could not be sent. Please try again."
                    ];
                    header('location: /elms/headdepartpending');
                    exit();
                }
                // Create notification
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log user activity
                $userModel->logUserActivity($approver_id, "បាន " . $status . "ច្បាប់ឈប់សម្រាក " . $uname, $_SERVER['REMOTE_ADDR']);

                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "សំណើច្បាប់ត្រូវបានអនុម័តដោយជោគជ័យ។"
                ];
                header('location: /elms/headdepartpending');
                exit();
            } else {
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks, $signaturePath);

                // Handle escalation for non-User roles or leave duration > 3 days
                if (!$this->sendEmailNotificationToDUnit($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    $_SESSION['error'] = [
                        'title' => "Email Error",
                        'message' => "Notification email could not be sent. Please try again."
                    ];
                    header('location: /elms/headdepartpending');
                    exit();
                }
                // Create notification
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log user activity
                $userModel->logUserActivity($approver_id, "បាន " . $status . "ច្បាប់ឈប់សម្រាក " . $uname, $_SERVER['REMOTE_ADDR']);

                $_SESSION['success'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សំណើច្បាប់ត្រូវបានបញ្ជូនទៅអ្នកដឹកនាំសម្រាប់បន្តដំណើរការ។" . $managerEmail
                ];
                header('location: /elms/headdepartpending');
                exit();
            }
        } else {
            $leaveRequestModel = new HeadDepartLeave();
            $requests = $leaveRequestModel->getAllLeaveRequests();
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/departments-h/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new HeadDepartLeave();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/headdepartmentapproved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new HeadDepartLeave();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/headdepartmentapproved.php';
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
        header("Location: /elms/dashboard");
        exit();
    }

    public function cancel($id, $status)
    {
        $deleteLeaveRequest = new LeaveRequest();
        if ($deleteLeaveRequest->cancelLeaveRequest($id, $status)) {
            $_SESSION['success'] = [
                'title' => "បោះបង់ច្បាប់ឈប់សម្រាក",
                'message' => "បានបោះបង់។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "បោះបង់ច្បាប់ឈប់សម្រាក",
                'message' => "មិនអាចបោះបង់បានទេ។"
            ];
        }
        header("Location: /elms/dashboard");
        exit();
    }
    
}
