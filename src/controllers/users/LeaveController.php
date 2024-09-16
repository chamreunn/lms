<?php
require_once 'src/models/users/LeaveModel.php';
require_once 'src/models/LeaveApproval.php';
require_once 'src/models/Leavetype.php';
require_once 'src/models/User.php';
require_once 'src/models/Notification.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LeaveController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function myLeaves()
    {
        $leaveRequestModel = new LeaveModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/users/myLeave.php';
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            try {
                // Start a database transaction
                $this->pdo->beginTransaction();

                $userModel = new User();

                $user_id = $_SESSION['user_id'];
                $user_email = $_SESSION['email'];
                $position = $_SESSION['position'];
                $office = $_SESSION['officeName'];
                $department = $_SESSION['departmentName'];

                $leave_type_id = $_POST['leave_type_id'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $remarks = $_POST['remarks'];
                $message = $_SESSION['user_khmer_name'] . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

                // Validate that the end date is not smaller than the start date
                if (new DateTime($end_date) < new DateTime($start_date)) {
                    $_SESSION['error'] = [
                        'title' => "កំហុសកាលបរិច្ឆេទ",
                        'message' => "ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត"
                    ];
                    header("Location: /elms/my-leaves");
                    exit();
                }

                // Handle file upload for attachment
                $attachment_name = $this->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
                if ($attachment_name === false) {
                    $_SESSION['error'] = [
                        'title' => "ឯកសារភ្ជាប់",
                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                    ];
                    header("Location: /elms/my-leaves");
                    exit();
                }

                // Fetch leave type details including duration from the database
                $leaveTypeModel = new Leavetype();
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }

                $leave_type_duration = $leaveType['duration'];

                // Calculate duration in business days between start_date and end_date
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $this->calculateBusinessDays($datetime_start, $datetime_end);

                // Compare duration_days with leave_type_duration
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("The selected leave type allows only " . $leave_type_duration . " days. Please check your selection.");
                }

                // Fetch the user's office details via API
                $userDoffice = $userModel->getEmailLeaderDOApi($user_id, $_SESSION['token']);
                if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                $managerEmail = $userDoffice['emails'];

                // Convert array to comma-separated string if necessary
                if (is_array($managerEmail)) {
                    $managerEmail = implode(',', $managerEmail);
                }

                // Create leave request
                $leaveRequestModel = new LeaveModel();
                $leaveRequestId = $leaveRequestModel->create(
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
                );

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
                }

                // Send email notification
                if (!$this->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

                // Log user activity
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $this->pdo->commit();

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
                ];
                header("Location: /elms/my-leaves");
                exit();
            } catch (Exception $e) {
                // Rollback the transaction if something fails
                $this->pdo->rollBack();

                // Log the error
                error_log($e->getMessage());

                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/my-leaves");
                exit();
            }

        } else {
            header("Location: /elms/my-leaves");
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
                        <a href='https://leave.iauoffsa.us/elms/view-leave-detail?leave_id={$leaveRequestId}' class='btn'>ចុចទីនេះ</a>
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

    private function sendEmailBackToUser($uEmail, $adminApproved, $leaveRequestId, $status, $updatedAt, $remarks)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com';
            $mail->Password = 'kyph nvwd ncpa gyzi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format date
            $updated_at_formatted = (new DateTime($updatedAt))->format('j F, Y H:i:s');

            // Recipients
            $mail->setFrom('no-reply@example.com', 'ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់');
            $mail->addAddress($uEmail);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = "ការស្នើសុំច្បាប់ត្រូវបាន $status";

            // Updated body with "khmer MEF1" font
            $body = "
        <html>
        <head>
            <style>
                @font-face {
                    font-family: 'khmer MEF1';
                    src: url('../../public/dist/fonts/Khmer-MEF1.ttf') format('truetype');
                }
                body {
                    font-family: 'khmer MEF1', Arial, sans-serif;
                    background-color: #f7f7f7;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 40px auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-size: 24px;
                    font-weight: bold;
                }
                .content {
                    padding: 20px;
                    font-size: 16px;
                    color: #333333;
                }
                .content p {
                    margin: 0 0 15px;
                }
                .status-badge {
                    display: inline-block;
                    background-color: " . ($status === 'Approved' ? '#28a745' : '#dc3545') . ";
                    color: white;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .footer {
                    background-color: #f1f1f1;
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                    color: #666666;
                    border-top: 1px solid #e2e2e2;
                }
            </style>
        </head>
        <body>
            <div class='container-fluid'>
                <p><strong>Status:</strong> $status</p>
                <p><strong>Approved by:</strong> $adminApproved</p>
                <p><strong>Date:</strong> $updated_at_formatted</p>"
                . (!empty($remarks) ? "<p><strong>Remarks:</strong> $remarks</p>" : "") . "
            </div>
            <div class='footer'>
                &copy; " . date("Y") . " ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់។ រក្សាសិទ្ធិគ្រប់យ៉ាង។
            </div>
        </body>
        </html>
        ";

            $mail->Body = $body;

            // Send email
            if ($mail->send()) {
                error_log("Email sent successfully to $uEmail");
                return true;
            } else {
                error_log("Email failed to send to $uEmail: " . $mail->ErrorInfo);
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

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new LeaveModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/users/myLeave.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new LeaveRequest();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/users/viewLeave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function pending()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate required POST fields
            $requiredFields = [
                'request_id',
                'status',
                'remarks',
                'uremarks',
                'uname',
                'uemail',
                'leaveType',
                'user_id',
                'start_date',
                'end_date',
                'duration'
            ];

            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = [
                        'title' => "Invalid Input",
                        'message' => "Missing required fields. Please try again."
                    ];
                    header("Location: /elms/apply-leave");
                    exit();
                }
            }

            // Initialize variables from POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'];
            $uremarks = $_POST['uremarks'];
            $uname = $_POST['uname'];
            $uEmail = $_POST['uemail'];
            $leaveType = $_POST['leaveType'];
            $user_id = $_POST['user_id']; // ID of the user who applied for leave
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $duration_days = $_POST['duration'];
            $approver_id = $_SESSION['user_id']; // Approver's ID (logged-in user)
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepOfficeModel();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details via API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHOApi($_SESSION['user_id'], $_SESSION['token']);

                // Validate office details response
                if (!$userHoffice || $userHoffice['http_code'] !== 200 || empty($userHoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Convert emails array to string if necessary
                $managerEmail = is_array($userHoffice['emails']) ? implode(',', $userHoffice['emails']) : $userHoffice['emails'];

                // Send email notification to HO office
                if (
                    !$leaveApproval->sendEmailNotificationToHOffice(
                        $managerEmail,
                        $message,
                        $request_id,
                        $start_date,
                        $end_date,
                        $duration_days,
                        $leaveType,
                        $remarks,
                        $uremarks,
                        $username,
                        $updatedAt
                    )
                ) {
                    throw new Exception("Notification email could not be sent to the office. Please try again.");
                }

                // Send confirmation email to the user
                if (
                    !$leaveApproval->sendEmailBackToUser(
                        $uEmail,
                        $_SESSION['user_khmer_name'],
                        $request_id,
                        $status,
                        $updatedAt,
                        $remarks
                    )
                ) {
                    throw new Exception("Notification email to the user could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log the approver's activity
                $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit the transaction
                $this->pdo->commit();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
                ];
                header('Location: /elms/pending');
                exit();

            } catch (Exception $e) {
                // Rollback transaction in case of any error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        } else {
            // Handle GET request to view pending leave requests
            $leaveApprovalModel = new DepOfficeModel();
            $requests = $leaveApprovalModel->getAllLeaveRequests();

            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            // Load the approval view
            require 'src/views/leave/offices-d/approvals.php';
        }
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
        header("Location: /elms/my-leaves");
        exit();
    }

    public function uploadAttachment()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $leave_id = $_POST['leave_id'];
            $attachment = $_FILES['attachment'];

            // Define allowed extensions and max file size (in bytes)
            $allowed_extensions = ['pdf', 'doc', 'docx', 'jpg', 'png'];
            $max_size = 2 * 1024 * 1024; // 2 MB
            $upload_path = 'public/uploads/leave_attachments/';

            // Handle file upload
            $uploaded_file_name = $this->handleFileUpload($attachment, $allowed_extensions, $max_size, $upload_path);

            if ($uploaded_file_name) {
                // Update the leave request with the attachment URL
                $leaveRequestModel = new LeaveRequest();
                $attachmentUrl = $upload_path . $uploaded_file_name;
                $updateSuccess = $leaveRequestModel->updateAttachment($leave_id, $attachmentUrl);

                if ($updateSuccess) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "Update Attachment",
                        'message' => "Successfully Updated the Attachment."
                    ];
                    header('Location: /elms/view-leave-detail?leave_id=' . $leave_id);
                    exit();
                } else {
                    // Handle update failure
                    $_SESSION['error'] = [
                        'title' => "Database Error",
                        'message' => "Failed to update the leave request with the attachment."
                    ];
                    header('Location: /elms/view-leave-detail?leave_id=' . $leave_id);
                    exit();
                }
            } else {
                // Redirect or handle file upload errors
                header('Location: /elms/view-leave-detail?leave_id=' . $leave_id);
                exit();
            }
        }
    }
}
