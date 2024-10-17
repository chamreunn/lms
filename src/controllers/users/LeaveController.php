<?php
require_once 'src/models/users/LeaveModel.php';
require_once 'src/models/telegram/TelegramModel.php';
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

                $leaveRemarks = "ច្បាប់";
                $status = "On Leave";
                $mission = "Mission";

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
                    throw new Exception("ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត");
                }

                // Fetch the user's office details via API
                $Depoffice = $userModel->getEmailLeaderDOApi($user_id, $_SESSION['token']);
                if (!$Depoffice || $Depoffice['http_code'] !== 200 || empty($Depoffice['ids'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = !empty($Depoffice['ids']) ? $Depoffice['ids'][0] : null;
                $managerEmail = !empty($Depoffice['emails']) ? $Depoffice['emails'][0] : null;
                $managerName = !empty($Depoffice['lastNameKh']) && !empty($Depoffice['firstNameKh'])
                    ? $Depoffice['lastNameKh'][0] . ' ' . $Depoffice['firstNameKh'][0]
                    : null;

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave or mission today using the leave_requests table
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                $link = "https://leave.iauoffsa.us/elms/pending";

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
                    $attachment_name
                );

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
                }

                if ($isManagerOnLeave || $isManagerOnMission) {
                    // Submit approval to backup manager if primary is on leave or mission
                    $leaveApproval = new LeaveModel();
                    $leaveApproval->submitApproval($leaveRequestId, $managerId, $isManagerOnLeave ? $status : $mission, $isManagerOnLeave ? $leaveRemarks : $mission);

                    // Fetch another available manager if the current manager is on leave
                    $backupManager = $userModel->getEmailLeaderHOApi($user_id, $_SESSION['token']);
                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                    }

                    // Update to backup manager's details
                    $managerId = !empty($backupManager['ids']) ? $backupManager['ids'][0] : null;
                    $managerEmail = $backupManager['emails'][0];
                    $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                    $link = "https://leave.iauoffsa.us/elms/headofficepending";

                    // Send Telegram notification for backup manager
                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                } else {

                    // Send Telegram notification for primary manager
                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                }

                // Send email notification
                if (!$this->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification([$managerId], $user_id, $leaveRequestId, $message);

                // Log user activity
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $this->pdo->commit();

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
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
        // Fetch holidays from the database
        $holidayModel = new CalendarModel();
        $holidays = $holidayModel->getHoliday(); // Assume this returns an array of holiday dates

        // Convert holidays to DateTime objects for comparison
        $holidayDates = array_map(function ($holiday) {
            return new DateTime($holiday['holiday_date']);
        }, $holidays);

        // Proceed to calculate the number of business days between the start and end date
        $business_days = 0;
        $current_date = clone $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = $current_date->format('N');

            // Check if the current date is a weekday and not a holiday
            if ($day_of_week < 6 && !in_array($current_date, $holidayDates)) {
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

    public function displayAttendances()
    {
        // Get the current page and limit from the request, default to 1 and 10 respectively
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;

        // Fetch all user attendance data from the model
        $userModel = new User();
        $userAttendances = $userModel->getUserAttendanceByIdApi($_SESSION['user_id'], $_SESSION['token']);

        // Check if the API response is valid
        if (isset($userAttendances['data'])) {
            // Get total records for pagination
            $totalRecords = count($userAttendances['data']); // Total records from API response

            // Calculate total pages
            $totalPages = ceil($totalRecords / $limit);

            // Calculate the offset for the current page
            $offset = ($page - 1) * $limit;

            // Slice the data for the current page
            $pagedData = array_slice($userAttendances['data'], $offset, $limit);

            // Assign paged data back to the variable
            $userAttendances['data'] = $pagedData;
        } else {
            $userAttendances['data'] = []; // Fallback if no data is present
        }

        // Pass the data and pagination info to the view
        require 'src/views/attendence/myAttendance.php';
    }

    public function filterAttendence()
    {
        // Retrieve the start and end dates from the GET request
        $startDate = $_GET['fromDate'] ?? null;
        $endDate = $_GET['toDate'] ?? null;

        // If dates are provided, pass them to the model to filter attendance data
        $userModel = new User();
        $userAttendances = $userModel->getUserFilterAttendanceByIdApi($_SESSION['user_id'], $_SESSION['token'], $startDate, $endDate);

        // Load the view with the filtered attendance data
        require 'src/views/attendence/myAttendance.php';
    }

}
