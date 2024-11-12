<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

require_once 'src/models/LeaveRequest.php';
require_once 'src/models/departments-h/HeadDepartmentModel.php';
require_once 'src/models/Leavetype.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HeadDepartmentController
{

    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            require 'src/views/leave/department-h/myLeave.php';
            return;
        }

        // Instantiate models
        $userModel = new User();
        $HeadDepartmentModel = new HeadDepartmentModel();
        $leaveTypeModel = new Leavetype();
        $notificationModel = new Notification();

        // Fetch session details
        $user_id = $_SESSION['user_id'] ?? null;
        $user_email = $_SESSION['email'] ?? null;
        $position = $_SESSION['position'] ?? null;
        $office = $_SESSION['officeName'] ?? null;
        $department = $_SESSION['departmentName'] ?? null;
        $token = $_SESSION['token'] ?? null;
        $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;
        $transfer = $_POST['transferId'];

        // Check if session information is complete
        if (!$user_id || !$position || !$department || !$token || !$user_khmer_name) {
            $_SESSION['error'] = [
                'title' => "Session Error",
                'message' => "Session information is missing. Please log in again."
            ];
            header("Location: /elms/login");
            exit();
        }

        // Fetch input data
        $leave_type_id = $_POST['leave_type_id'] ?? null;
        $start_date = $_POST['start_date'] ?? null;
        $end_date = $_POST['end_date'] ?? null;
        $remarks = $_POST['remarks'] ?? null;
        $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
        $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

        // Validate dates
        if (new DateTime($end_date) < new DateTime($start_date)) {
            $_SESSION['error'] = [
                'title' => "កំហុសកាលបរិច្ឆេទ",
                'message' => "ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត"
            ];
            header("Location: /elms/my-leaves");
            exit();
        }

        // Handle file upload for attachment
        $attachment_name = $HeadDepartmentModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
        if ($attachment_name === false) {
            $_SESSION['error'] = [
                'title' => "ឯកសារភ្ជាប់",
                'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
            ];
            header("Location: /elms/hdepartmentLeave");
            exit();
        }

        // Fetch leave type details
        $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
        if (!$leaveType) {
            $_SESSION['error'] = [
                'title' => "Leave Type Error",
                'message' => "Invalid leave type selected."
            ];
            header("Location: /elms/hdepartmentLeave");
            exit();
        }

        $leave_type_duration = $leaveType['duration'];

        // Calculate business days between start_date and end_date
        $datetime_start = new DateTime($start_date);
        $datetime_end = new DateTime($end_date);
        $duration_days = $HeadDepartmentModel->calculateBusinessDays($datetime_start, $datetime_end);

        // Compare duration_days with leave_type_duration
        if ($duration_days > $leave_type_duration) {
            $_SESSION['error'] = [
                'title' => "រយៈពេល",
                'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
            ];
            header("Location: /elms/hdepartmentLeave");
            exit();
        }

        // Fetch office details based on department
        $userDoffice = null;
        if (in_array($department, ["នាយកដ្ឋានកិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
            $userDoffice = $userModel->getEmailLeaderDHU1Api($user_id, $token);
            $link = "https://leave.iauoffsa.us/elms/dunit1pending";
        } else {
            $userDoffice = $userModel->getEmailLeaderDHU2Api($user_id, $token);
            $link = "https://leave.iauoffsa.us/elms/dunit2pending";
        }

        if (!$userDoffice || empty($userDoffice['ids']) || empty($userDoffice['emails'])) {
            $_SESSION['error'] = [
                'title' => "Office Error",
                'message' => "Unable to find Department details or no emails found. Please contact support."
            ];
            header("Location: /elms/hdepartmentLeave");
            exit();
        }

        $managerId = $userDoffice['ids'][0] ?? null;
        $managerEmail = $userDoffice['emails'][0] ?? null;
        $managerName = ($userDoffice['lastNameKh'][0] ?? '') . ' ' . ($userDoffice['firstNameKh'][0] ?? '');

        if (!$managerId || !$managerEmail) {
            throw new Exception("No valid manager details found.");
        }

        // Check if the manager is on leave or mission today
        $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
        $isManagerOnMission = $userModel->isManagerOnMission($managerId);

        // Create leave request
        $leaveRequestId = $HeadDepartmentModel->create(
            $user_id,
            $user_email,
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
            $transfer
        );

        if (!$leaveRequestId) {
            $_SESSION['error'] = [
                'title' => "Leave Request Error",
                'message' => "Failed to create leave request. Please try again."
            ];
            header("Location: /elms/hdepartmentLeave");
            exit();
        }

        // Handle manager approval and backup if necessary
        if ($isManagerOnLeave || $isManagerOnMission) {
            $status = $isManagerOnLeave ? "On Leave" : "Mission";
            $remarksText = $isManagerOnLeave ? "ច្បាប់" : "បេសកកម្ម";
            $HeadDepartmentModel->updateApproval($leaveRequestId, $managerId, $status, $remarksText);

            // Fetch backup manager if primary is unavailable
            $backupManager = $userModel->getEmailLeaderHUApi($user_id, $token);
            if (!$backupManager || empty($backupManager['emails'])) {
                throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
            }

            $managerId = $backupManager['ids'][0] ?? null;
            $managerEmail = $backupManager['emails'][0] ?? null;
            $managerName = ($backupManager['lastNameKh'][0] ?? '') . ' ' . ($backupManager['firstNameKh'][0] ?? '');
            $link = "https://leave.iauoffsa.us/elms/hunitpending";
        }

        // Send notifications (Telegram and Email)
        $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
        if (!$this->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
            $_SESSION['error'] = [
                'title' => "Email Error",
                'message' => "Notification email could not be sent. Please try again."
            ];
            header("Location: /elms/apply-leave");
            exit();
        }

        // Log user activity and create notification
        $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);
        $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

        // Success message
        $_SESSION['success'] = [
            'title' => "ជោគជ័យ",
            'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
        ];
        header("Location: /elms/hdepartmentLeave");
        exit();
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

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new HeadDepartmentModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/departments-h/myLeave.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new HeadDepartmentModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        $userModel = new User();
        $depdepart = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);
        require 'src/views/leave/departments-h/myLeave.php';
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
            try {
                $userModel = new User();
                $HeadDepartmentModel = new HeadDepartmentModel();
                $notificationModel = new Notification();

                // Retrieve POST data
                $request_id = $_POST['request_id'] ?? null;
                $status = $_POST['status'] ?? null;
                $remarks = $_POST['remarks'] ?? '';
                $uremarks = $_POST['uremarks'] ?? '';
                $uname = $_POST['uname'] ?? '';
                $uEmail = $_POST['uemail'] ?? '';
                $leaveType = $_POST['leaveType'] ?? '';
                $user_id = $_POST['user_id'] ?? null;
                $start_date = $_POST['start_date'] ?? null;
                $end_date = $_POST['end_date'] ?? null;
                $duration_days = $_POST['duration'] ?? null;
                $approver_id = $_SESSION['user_id'] ?? null;
                $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
                $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $leave = 1 ?? null;

                // Fetch department details
                $leaveRemarks = "ច្បាប់";
                $action = "On Leave";
                $onMission = "On Mission";
                $departmentName = $_SESSION['departmentName'] ?? null;

                if ($departmentName) {
                    // Determine the appropriate API based on department name
                    if (in_array($departmentName, ["នាយកដ្ឋានកិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
                        $userDoffice = $userModel->getEmailLeaderDHU1Api($_SESSION['user_id'], $_SESSION['token']);
                        $link = "https://leave.iauoffsa.us/elms/dunit1pending";
                    } else {
                        $userDoffice = $userModel->getEmailLeaderDHU2Api($_SESSION['user_id'], $_SESSION['token']);
                        $link = "https://leave.iauoffsa.us/elms/dunit2pending";
                    }

                    if (empty($userDoffice) || empty($userDoffice['ids'])) {
                        throw new Exception("Unable to find Department details or no emails found.");
                    }

                    // Use the first available manager's ID and email
                    $managerId = $userDoffice['ids'][0] ?? null;
                    $managerEmail = $userDoffice['emails'][0] ?? null;
                    $managerName = isset($userDoffice['lastNameKh'][0]) && isset($userDoffice['firstNameKh'][0])
                        ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                        : null;

                    if (!$managerId || !$managerEmail) {
                        throw new Exception("No valid manager details found.");
                    }

                    // Check if the manager is on leave today using the leave_requests table
                    $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                    $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                    // If manager is on leave or mission, assign a backup manager
                    if ($isManagerOnLeave || $isManagerOnMission) {

                        $updatedAt = $HeadDepartmentModel->updatePendingApproval($request_id, $managerId, $isManagerOnLeave ? $action : $onMission, $isManagerOnLeave ? $leaveRemarks : $onMission);
                        // Update approval with backup manager if the current manager is on leave
                        $backupManager = $userModel->getEmailLeaderHUApi($user_id, $_SESSION['token']);
                        if (!$backupManager || empty($backupManager['emails'])) {
                            throw new Exception("Both primary and backup managers are unavailable.");
                        }

                        // Update to backup manager's details
                        $managerEmail = $backupManager['ids'][0];
                        $managerEmail = $backupManager['emails'][0];
                        $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                        $link = "https://leave.iauoffsa.us/elms/hunitpending";
                    }
                } else {
                    throw new Exception("Department name not found in session. Please log in again.");
                }

                // Fetch user's role for leave processing
                $roleLeave = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

                if (empty($roleLeave['data']['roleLeave']) && $duration_days <= 3) {
                    // Direct approval for Users with leave duration <= 3 days
                    $updateToApi = $HeadDepartmentModel->updateToApi($user_id, $start_date, $end_date, $leave, $_SESSION['token']);

                    if (!$updateToApi) {
                        throw new Exception("Failed to update leave to API. " . ($updateToApi['error'] ?? 'Unknown error'));
                    }

                    $updatedAt = $HeadDepartmentModel->submitApproval($request_id, $approver_id, $status, $remarks);

                    // Send email notification to Department Unit
                    if (!$HeadDepartmentModel->sendEmailNotification($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                        throw new Exception("Notification email could not be sent.");
                    }

                    // Retrieve user's email from the user model
                    $userDetails = $userModel->getUserByIdApi($user_id, $_SESSION['token']);
                    $uEmail = $userDetails['data']['email'] ?? null;

                    if (!$uEmail) {
                        throw new Exception("Failed to retrieve user email.");
                    }

                    // Send email back to the user confirming approval/rejection
                    if (!$HeadDepartmentModel->sendEmailBacktoUser($uEmail, $message, $status, $start_date, $end_date, $remarks, $leaveType)) {
                        throw new Exception("Failed to send email back to user.");
                    }

                    // Send notifications Telegram
                    $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                    // Create notification for user and approver
                    $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                    // Log user activity
                    $userModel->logUserActivity($approver_id, "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname, $_SERVER['REMOTE_ADDR']);

                    $_SESSION['success'] = [
                        'title' => "សំណើច្បាប់",
                        'message' => "សំណើច្បាប់ត្រូវបានអនុម័តដោយជោគជ័យ។"
                    ];
                    header('location: /elms/headdepartmentpending');
                    exit();
                } else {
                    // Handle escalation for non-User roles or leave duration > 3 days
                    $updatedAt = $HeadDepartmentModel->submitApproval($request_id, $approver_id, $status, $remarks);

                    // Send email notification to Department Unit
                    if (!$HeadDepartmentModel->sendEmailNotification($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                        throw new Exception("Notification email could not be sent.");
                    }

                    // Send email back to the user confirming approval/rejection
                    if (!$HeadDepartmentModel->sendEmailBacktoUser($uEmail, $message, $status, $start_date, $end_date, $remarks, $leaveType)) {
                        throw new Exception("Failed to send email back to user.");
                    }

                    // Send notifications Telegram
                    $userModel->sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link);
                    $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                    // Create notification for user and approver
                    $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                    // Log user activity
                    $userModel->logUserActivity($approver_id, "បាន " . $status . "ច្បាប់ឈប់សម្រាក " . $uname, $_SERVER['REMOTE_ADDR']);

                    $_SESSION['success'] = [
                        'title' => "បរាជ័យ",
                        'message' => "សំណើច្បាប់ត្រូវបានបញ្ជូនទៅអ្នកដឹកនាំសម្រាប់បន្តដំណើរការ។" . $managerName
                    ];
                    header('location: /elms/headdepartmentpending');
                    exit();
                }
            } catch (Exception $e) {
                // Log the error and set an error message
                error_log("Error in approving leave: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header('location: /elms/headdepartmentpending');
                exit();
            }
        } else {
            // Handle non-POST requests
            try {
                $leaveRequestModel = new HeadDepartmentModel();
                $requests = $leaveRequestModel->getAllLeaveRequests();

                // Initialize the UserModel
                $userModel = new User();

                // Get approver based on role and department
                $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

                // Initialize the HoldModel to retrieve any holds for the current user
                $holdsModel = new HoldModel();
                $hold = $holdsModel->getHoldByuserId($_SESSION['user_id']);

                $leavetypeModel = new Leavetype();
                $leavetypes = $leavetypeModel->getAllLeavetypes();

                require 'src/views/leave/departments-h/pending.php';
            } catch (Exception $e) {
                error_log("Error in loading leave requests: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => "Failed to load leave requests."
                ];
                header('location: /elms/headdepartmentpending');
                exit();
            }
        }
    }

    public function approved()
    {
        $leaveRequestModel = new HeadDepartmentModel();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/departments-h/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new HeadDepartmentModel();
        $requests = $leaveRequestModel->gethrejected($_SESSION['user_id']);

        require 'src/views/leave/departments-h/rejected.php';
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
        header("Location: /elms/hdepartmentLeave");
        exit();
    }

    public function pendingCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
    WHERE dhead_department IN (?, ?)
    AND head_department = ?
    AND position IN (?, ?, ?, ?)
    AND department = ?
    AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Pending', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
    }

    public function rejectedCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
         WHERE dhead_department IN (?, ?)
         AND head_department = ?
         AND position IN (?, ?, ?, ?)
         AND department = ?
         AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Rejected', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
    }

    public function approvedCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
    WHERE dhead_department IN (?, ?)
    AND head_department = ?
    AND position IN (?, ?, ?, ?)
    AND department = ?
    AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Approved', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
    }

    public function viewLeaveDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new HeadDepartmentModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/departments-h/viewLeaveDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function action()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $holdId = $_POST['holdId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepOfficeModel();
                $leaveApproval->updateHoldApproval($userId, $holdId, $approverId, $action, $comment);

                if ($leaveApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/headdepartmentpending");
                    exit();
                }
                // Commit transaction after successful approval update
                $this->pdo->commit();

            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/headdepartmentpending");
                exit();
            }
        }
    }
}
