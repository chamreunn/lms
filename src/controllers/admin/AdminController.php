<?php
require_once 'vendor/autoload.php';
require_once 'src/models/admin/AdminModel.php';
require_once 'src/models/telegram/TelegramModel.php';
require_once 'src/models/qrcode/QrModel.php';
// Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Logo\Logo;

class AdminController
{

    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();
            $leaveRequestModel = new AdminModel();
            $leaveTypeModel = new Leavetype();
            $notificationModel = new Notification();

            try {
                // Start transaction
                $leaveRequestModel->beginTransaction();

                // Retrieve session data
                $user_id = $_SESSION['user_id'];
                $user_email = $_SESSION['email'];
                $position = $_SESSION['position'];
                $office = $_SESSION['officeName'];
                $department = $_SESSION['departmentName'];

                // Retrieve POST data
                $leave_type_id = $_POST['leave_type_id'];
                $start_date = $_POST['start_date'];
                $end_date = $_POST['end_date'];
                $remarks = $_POST['remarks'];
                $message = $_SESSION['user_khmer_name'] . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

                $leaveRemarks = "ច្បាប់";
                $status = "On Leave";

                // Validate required fields
                $requiredFields = ['leave_type_id', 'start_date', 'end_date'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Missing required fields. Please fill out all fields.");
                    }
                }

                if (new DateTime($end_date) < new DateTime($start_date)) {
                    $_SESSION['error'] = [
                        'title' => "កំហុសកាលបរិច្ឆេទ",
                        'message' => "ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត"
                    ];
                    header("Location: /elms/my-leaves");
                    exit();
                }

                // Handle file upload for attachment
                $attachment_name = $leaveRequestModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
                if ($attachment_name === false) {
                    throw new Exception("Unable to upload attachment. Please try again.");
                }


                // Fetch leave type details including duration from database
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }

                $leave_type_duration = $leaveType['duration'];

                // Calculate duration in business days between start_date and end_date
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $leaveRequestModel->calculateBusinessDays($datetime_start, $datetime_end);

                // Compare duration_days with leave_type_duration
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("Leave type duration exceeded. Please check the leave type selected.");
                }

                // Fetch the user's office details
                $userDoffice = $userModel->getEmailLeaderHOApi($user_id, $_SESSION['token']);
                if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = !empty($userDoffice['ids']) ? $userDoffice['ids'][0] : null;
                $managerEmail = !empty($userDoffice['emails']) ? $userDoffice['emails'][0] : null;
                $managerName = !empty($userDoffice['lastNameKh']) && !empty($userDoffice['firstNameKh'])
                    ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                    : null;

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave today using the leave_requests table
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);

                // Create leave request
                $leaveRequestId = $leaveRequestModel->create(
                    user_id: $user_id,
                    user_email: $user_email,
                    leave_type_id: $leave_type_id,
                    position: $position,
                    office: $office,
                    department: $department,
                    leave_type_name: $leaveType['name'],
                    start_date: $start_date,
                    end_date: $end_date,
                    remarks: $remarks,
                    duration_days: $duration_days,
                    attachment: $attachment_name
                );

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
                }

                if ($isManagerOnLeave) {
                    // Submit approval
                    $updatedAt = $leaveRequestModel->updateApproval($leaveRequestId, $managerId, $status, $leaveRemarks);

                    // Fetch another available manager if the current manager is on leave
                    $backupManager = $userModel->getEmailLeaderDDApi($user_id, $_SESSION['token']);
                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                    }

                    // Update to backup manager's details
                    $managerEmail = $backupManager['emails'][0];
                    $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                }

                // Send email notification
                if (!$leaveRequestModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

                // Log user activity
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $leaveRequestModel->commitTransaction();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header("Location: /elms/adminLeave");
                exit();

            } catch (Exception $e) {
                // Rollback transaction on error
                $leaveRequestModel->rollBackTransaction();

                // Set error message and redirect
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => "An error occurred: " . $e->getMessage()
                ];
                header("Location: /elms/adminLeave");
                exit();
            }
        } else {
            header("Location: /elms/adminLeave");
            exit();
        }
    }

    public function ActionLate()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize variables
            $approver_id = $_SESSION['user_id'] ?? null; // Ensure session variable is set
            $uEmail = $_POST['uEmail'] ?? null;
            $lateId = $_POST['lateId'] ?? null;
            $status = $_POST['status'] ?? null;
            $uId = $_POST['uId'] ?? null;
            $comment = $_POST['comment'] ?? ''; // Comment field
            $reason = $_POST['reasons'] ?? null;
            $latenessType = $_POST['latenessType'] ?? null; // Lateness type: late_in, late_out, leave_early

            // Initialize variables for lateness time fields
            $checkIn = $_POST['date'] ?? null; // When the user checks in
            $checkOut = $_POST['date'] ?? null; // When the user checks out
            $lateIn = $_POST['lateIn'] ?? null; // Time late for check-in
            $lateOut = $_POST['lateOut'] ?? null; // Time late for check-out
            $leaveEarly = $_POST['leaveEarly'] ?? null; // Time left early

            // Title and action for the approval email/message
            $action = "បាន" . $status . "ការចូលយឺត";
            $title = "សំណើចូលយឺត";  // You can customize based on lateness type if necessary

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Ensure all required fields are provided
                if (empty($lateId) || empty($status) || empty($uId) || empty($latenessType)) {
                    throw new Exception("Missing required fields for processing.");
                }

                // Update the request in the approval model
                $approveModel = new AdminModel();
                $approvals = $approveModel->updateRequest($approver_id, $status, $lateId, $comment);

                // Log user activity
                $userModel = new User();
                $createActivityResult = $userModel->logUserActivity($approver_id, $action);

                if ($approvals) {
                    // Commit the transaction
                    $this->pdo->commit();

                    // If the status is not "Rejected", update the API with late-in/out/leave-early information
                    if ($status !== 'Rejected') {
                        if ($latenessType === 'latein' && $lateIn !== null) {
                            // Update API with late-in data
                            $updateToApi = $userModel->updateLateInToApi($uId, $checkIn, $lateIn, $_SESSION['token']);
                        } elseif ($latenessType === 'lateout' && $lateOut !== null) {
                            // Update API with late-out data
                            $updateToApi = $userModel->updateLateOutToApi($uId, $checkOut, $lateOut, $_SESSION['token']);
                        } elseif ($latenessType === 'leaveearly' && $leaveEarly !== null) {
                            // Update API with leave-early data
                            $updateToApi = $userModel->updateLeaveEarlyToApi($uId, $checkOut, $leaveEarly, $_SESSION['token']);
                        }

                        // If API update fails, log or handle the error
                        if (!$updateToApi) {
                            throw new Exception("Failed to update lateness information to the API.");
                        }

                        // Fetch Telegram ID to send notification
                        $telegramUser = $userModel->getTelegramIdByUserId($uId);

                        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
                            // Log the telegram_id for debugging
                            error_log("Found telegram_id: " . $telegramUser['telegram_id']);

                            // Prepare the Telegram message based on lateness type
                            $latenessDetail = '';
                            $dateTimeDetail = '';

                            if ($latenessType === 'latein') {
                                $latenessDetail = "បានចូលយឺត: {$lateIn} នាទី";
                                $dateTimeDetail = "📅 *កាលបរិច្ឆេទចូល:* `{$checkIn}`"; // Only show check-in for late-in
                            } elseif ($latenessType === 'lateout') {
                                $latenessDetail = "បានចេញយឺត: {$lateOut} នាទី";
                                $dateTimeDetail = "📅 *កាលបរិច្ឆេទចេញ:* `{$checkOut}`"; // Only show check-out for late-out
                            } elseif ($latenessType === 'leaveearly') {
                                $latenessDetail = "បានចាកចេញមុន: {$leaveEarly} នាទី";
                                $dateTimeDetail = "📅 *កាលបរិច្ឆេទចេញ:* `{$checkOut}`"; // Only show check-out for leave-early
                            }

                            // Prepare the status (approved/rejected)
                            $statusMessage = ($status === 'Approved') ? "ត្រូវបានអនុម័ត" : "ត្រូវបានបដិសេធ"; // Status message in Khmer

                            // Notifications content for Telegram
                            $notifications = [
                                "🔔 *ការស្នើសុំចូលយឺត* {$statusMessage}",  // Include whether the request was approved or rejected
                                "---------------------------------------------",
                                "👤 *អ្នកអនុម័ត:* `{$_SESSION['user_khmer_name']}`",
                                $dateTimeDetail, // Display the correct date-time field based on lateness type
                                "💬 *មូលហេតុ:* `{$reason}`",
                                "⏰ *ស្ថានភាព:* `{$latenessDetail}`", // Lateness details
                            ];

                            // Joining notifications into a single message with new lines
                            $telegramMessage = implode("\n", $notifications);

                            // Send the Telegram notification with the "View the Request" button
                            $telegramModel = new TelegramModel($this->pdo);
                            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage);

                            // Check if the notification was successfully sent
                            if ($success) {
                                error_log("Telegram notification sent successfully to user with telegram_id: " . $telegramUser['telegram_id']);
                            } else {
                                error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramUser['telegram_id']);
                                $_SESSION['error'] = [
                                    'title' => "Telegram Notification Error",
                                    'message' => "Could not send Telegram notification. Please check your settings or contact support."
                                ];
                            }
                        } else {
                            // Log the failure to find a valid telegram_id
                            error_log("No valid telegram_id found for userId: " . $uId);
                            $_SESSION['error'] = [
                                'title' => "Telegram",
                                'message' => "អ្នកប្រើប្រាស់នេះមិនទាន់ភ្ជាប់ទៅកាន់ Telegram ទេ។​ សូមភ្ជាប់ដើម្បីទទួលបានការជូនដំណឹងភ្លាមៗ ។"
                            ];
                        }

                        // Send email notification back to the user about the decision
                        $sendEmailBack = $approveModel->sendEmailBackToUser($uEmail, $approvals, $_SESSION['user_khmer_name'], $status, $comment, $title);
                        if (!$sendEmailBack) {
                            throw new Exception("Failed to send the email update to the user.");
                        }
                    }

                    // Set success message in session
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "សំណើចូលយឺតត្រូវបានអនុម័តដោយជោគជ័យ។"
                    ];
                } else {
                    // Rollback on failure and throw an exception
                    $this->pdo->rollBack();
                    throw new Exception("Failed to update status or log activity.");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                // Set error message in session
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការអនុម័តសំណើ: " . $e->getMessage()
                ];
            }

            // Redirect to pending admin page
            header("Location: /elms/adminpending");
            exit();
        }
    }

    public function ActionLateOut()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize variables
            $approver_id = $_SESSION['user_id'] ?? null; // Ensure session variable is set
            $uEmail = $_POST['uEmail'] ?? null;

            $lateId = $_POST['lateId'] ?? null;
            $status = $_POST['status'] ?? null;

            $uId = $_POST['uId'] ?? null;
            $checkOut = $_POST['checkOut'] ?? null;
            $lateOut = $_POST['lateOut'] ?? null;

            $action = "បាន" . $status . "ការចេញយឺត";
            $comment = $_POST['comment'] ?? ''; // Ensure comment is set (avoid null)
            $title = "សំណើចេញយឺត";

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Ensure all required fields are provided
                if (empty($lateId) || empty($status) || empty($uId) || empty($checkOut) || empty($lateOut)) {
                    throw new Exception("Missing required fields for processing.");
                }

                // Update the request in the approval model
                $approveModel = new AdminModel();
                $approvals = $approveModel->updateRequest($approver_id, $status, $lateId, $comment);

                // Log user activity
                $userModel = new User();
                $createActivityResult = $userModel->logUserActivity($approver_id, $action);

                // Check if both operations were successful
                if ($approvals) {
                    // Commit the transaction
                    $this->pdo->commit();

                    // If the status is not "Rejected", update the API with late-out information
                    if ($status !== 'Rejected') {
                        $updateToApi = $userModel->updateLateOutToApi($uId, $checkOut, $lateOut, $_SESSION['token']);

                        // If API update fails, handle it (optional: log or display an error)
                        if (!$updateToApi['success']) {
                            throw new Exception("Failed to update late-out information to the API. Response: " . $updateToApi['response']);
                        }

                        $sendEmailBack = $approveModel->sendEmailBackToUser($uEmail, $approvals, $_SESSION['user_khmer_name'], $status, $comment, $title);
                        // If send update fails, log or handle the error
                        if (!$sendEmailBack) {
                            throw new Exception("Failed to update late-in information to the API.");
                        }
                    }

                    // Set success message in session
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "សំណើចេញយឺតត្រូវបានអនុម័តដោយជោគជ័យ។"
                    ];
                } else {
                    // If any operation fails, roll back and throw an exception
                    $this->pdo->rollBack();
                    throw new Exception("Failed to update status or log activity.");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $this->pdo->rollBack();
                // Set error message in session
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការអនុម័តសំណើ: " . $e->getMessage()
                ];
            }

            // Redirect to pending admin page
            header("Location: /elms/adminpending?action=lateout");
            exit();
        }
    }

    public function ActionLeaveEarly()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Initialize variables
            $approver_id = $_SESSION['user_id'] ?? null; // Ensure session variable is set
            $uEmail = $_POST['uEmail'] ?? null;

            $lateId = $_POST['lateId'] ?? null;
            $status = $_POST['status'] ?? null;

            $uId = $_POST['uId'] ?? null;
            $checkOut = $_POST['checkOut'] ?? null;
            $exit = $_POST['exit'] ?? null;

            $action = "បាន" . $status . "ការចេញយឺត";
            $comment = $_POST['comment'] ?? ''; // Ensure comment is set (avoid null)
            $title = "សំណើចេញមុន";

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Ensure all required fields are provided
                if (empty($lateId) || empty($status) || empty($uId) || empty($checkOut) || empty($exit)) {
                    throw new Exception("Missing required fields for processing.");
                }

                // Update the request in the approval model
                $approveModel = new AdminModel();
                $approvals = $approveModel->updateRequest($approver_id, $status, $lateId, $comment);

                // Log user activity
                $userModel = new User();
                $createActivityResult = $userModel->logUserActivity($approver_id, $action);

                // Check if both operations were successful
                if ($approvals) {
                    // Commit the transaction
                    $this->pdo->commit();

                    // If the status is not "Rejected", update the API with late-out information
                    if ($status !== 'Rejected') {
                        $updateToApi = $userModel->updateLeaveEarlyToApi($uId, $checkOut, $exit, $_SESSION['token']);

                        // If API update fails, handle it (optional: log or display an error)
                        if (!$updateToApi['success']) {
                            throw new Exception("Failed to update late-out information to the API. Response: " . $updateToApi['response']);
                        }

                        $sendEmailBack = $approveModel->sendEmailBackToUser($uEmail, $approvals, $_SESSION['user_khmer_name'], $status, $comment, $title);
                        // If send update fails, log or handle the error
                        if (!$sendEmailBack) {
                            throw new Exception("Failed to update late-in information to the API.");
                        }
                    }

                    // Set success message in session
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "សំណើចេញយឺតត្រូវបានអនុម័តដោយជោគជ័យ។"
                    ];
                } else {
                    // If any operation fails, roll back and throw an exception
                    $this->pdo->rollBack();
                    throw new Exception("Failed to update status or log activity.");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $this->pdo->rollBack();
                // Set error message in session
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការអនុម័តសំណើ: " . $e->getMessage()
                ];
            }

            // Redirect to pending admin page
            header("Location: /elms/adminpending?action=leaveearly");
            exit();
        }
    }

    public function viewLateDetail()
    {
        if (isset($_GET['id'])) {
            $adminModel = new AdminModel();
            $id = (int) $_GET['id'];
            $detail = $adminModel->getLateById($id);

            if ($detail) {
                require 'src/views/admin/LateDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/adminpending');
        exit();
    }

    public function viewAllLeave()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLeaveEarly();
        $getAlls = $adminModel->getAll();
        $getLateInCount = $adminModel->getLateinCount();
        $getLateOutCount = $adminModel->getLateoutCount();
        $getLeaveEarlyCount = $adminModel->getLeaveearlyCount();
        $getAllLate = $adminModel->getAllLate();
        $getApproved = $adminModel->getApprovedLateCount();
        $getAllLeave = $adminModel->getAllLeave();
        $getLeaveCount = $adminModel->countApprovedLeavesToday();

        require 'src/views/admin/AllLeave.php';
        return;
    }

    public function AllLeaves()
    {
        $adminModel = new AdminModel();
        $getAllLeaves = $adminModel->getAllLeaves();

        require 'src/views/admin/AllLeaves.php';
        return;
    }

    public function viewLateDetailLateOut()
    {
        if (isset($_GET['id'])) {
            $adminModel = new AdminModel();
            $id = (int) $_GET['id'];
            $detail = $adminModel->getLateById($id);

            if ($detail) {
                require 'src/views/admin/LateDetailLateOut.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/adminpending');
        exit();
    }

    public function viewLateDetailLeaveEarly()
    {
        if (isset($_GET['id'])) {
            $adminModel = new AdminModel();
            $id = (int) $_GET['id'];
            $detail = $adminModel->getLateById($id);

            if ($detail) {
                require 'src/views/admin/LateDetailLeaveEarly.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/adminpending');
        exit();
    }

    public function viewLateDetailAllLate()
    {
        if (isset($_GET['id'])) {
            $adminModel = new AdminModel();
            $id = (int) $_GET['id'];
            $detail = $adminModel->getAllLateById($id);

            if ($detail) {
                require 'src/views/admin/LateAllDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/adminpending');
        exit();
    }

    public function getAllMissionTodays()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch missions with pagination
        $getAllMissions = $adminModel->getAllMissionTodays($offset, $recordsPerPage);

        // Fetch total records for pagination calculation
        $totalRecords = $adminModel->getTotalMissionCount();
        $getAllMissionCount = $adminModel->getMissionsTodayCount();

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Pass data to the view
        require 'src/views/admin/allMissionToday.php';
    }

    public function getAllMissions()
    {
        $adminModel = new AdminModel();
        $userModel = new User();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch missions with pagination
        $getAllMissions = $adminModel->getAllMissions($offset, $recordsPerPage);

        // Fetch total records for pagination calculation
        $totalRecords = $adminModel->getTotalMissionCount();
        $getAllMissionCount = $adminModel->getMissionsTodayCount();

        $getAllUser = $userModel->getAllUserApi($_SESSION['token']);

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Pass data to the view
        require 'src/views/admin/allMissions.php';
    }

    public function getAllLeaves()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch missions with pagination
        $getAllLeaves = $adminModel->getLeaves($offset, $recordsPerPage);

        // Fetch total records for pagination calculation
        $totalRecords = $adminModel->getTotalLeaveCount();
        $getLeaveTodayCount = $adminModel->getLeaveTodayCount();

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Pass data to the view
        require 'src/views/admin/leaves/allLeaves.php';
    }

    public function getLeaveFilter()
    {
        // Collect filters from POST request
        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $adminModel = new AdminModel();

        // Get the current page from GET request and set records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set number of records per page

        // Calculate the offset for pagination
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Add limit and offset to filters
        $filters['limit'] = $recordsPerPage;
        $filters['offset'] = $offset;

        // Fetch leave requests based on the filters and pagination
        $getAllLeaves = $adminModel->getLeaveFilters($filters);

        // Get the total number of leave records for pagination
        $totalRecords = $adminModel->getTotalLeaveCount();
        $getLeaveTodayCount = $adminModel->getLeaveTodayCount();

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Include the view to display the leaves
        require 'src/views/admin/leaves/allLeaves.php';
    }

    public function getAllLeaveToday()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch missions with pagination
        $getAllLeaveToday = $adminModel->getLeaveToday($offset, $recordsPerPage);

        // Fetch total records for pagination calculation
        $totalRecords = $adminModel->getTotalLeaveCount();
        $getLeaveTodayCount = $adminModel->getLeaveTodayCount();

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        // Pass data to the view
        require 'src/views/admin/leaves/allLeaveToday.php';
    }

    public function getPendingLate()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllPendingLate();
        $getLeaveCount = $adminModel->countApprovedLeavesToday();
        // get leaves approved 
        $getPendingCount = $adminModel->getLateCountByStatus('Pending');
        $getApprovedCount = $adminModel->getLateCountByStatus('Approved');
        $getRejectedCount = $adminModel->getLateCountByStatus('Rejected');
        $getLeavesApproved = $adminModel->getApprovedLeaveCount();

        $gettodaylatecount = $adminModel->getTodayLateCount('Approved');

        require 'src/views/admin/lateinpending.php';
    }

    public function getApprovedLate()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 10; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch approved late records with pagination
        $getApprovedAll = $adminModel->getAllApprovedLate('Approved', $offset, $recordsPerPage);

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalApprovedLate('Approved'); // Get total count of approved records
        $gettodaylatecount = $adminModel->getTodayLateCount('Approved');

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        $getPendingCount = $adminModel->getLateCountByStatus('Pending');
        $getApprovedCount = $adminModel->getLateCountByStatus('Approved');
        $getRejectedCount = $adminModel->getLateCountByStatus('Rejected');

        // Pass the necessary data to the view
        require 'src/views/admin/adminApproved.php';
    }

    public function getRejectedLate()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch approved late records with pagination
        $getRejectedAll = $adminModel->getAllApprovedLate('Rejected', $offset, $recordsPerPage);

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalApprovedLate('Rejected'); // Get total count of approved records

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        $getPendingCount = $adminModel->getLateCountByStatus('Pending');
        $getApprovedCount = $adminModel->getLateCountByStatus('Approved');
        $getRejectedCount = $adminModel->getLateCountByStatus('Rejected');

        $gettodaylatecount = $adminModel->getTodayLateCount('Approved');

        // Pass the necessary data to the view
        require 'src/views/admin/adminRejected.php';
    }

    public function getTodayLate()
    {
        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 10; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Fetch approved late records with pagination
        $gettodaylates = $adminModel->getAllTodayLate('Approved', $offset, $recordsPerPage);
        $gettodaylatecount = $adminModel->getTodayLateCount('Approved');

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalTodayLate('Approved'); // Get total count of approved records

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        $getPendingCount = $adminModel->getLateCountByStatus('Pending');
        $getApprovedCount = $adminModel->getLateCountByStatus('Approved');
        $getRejectedCount = $adminModel->getLateCountByStatus('Rejected');

        // Pass the necessary data to the view
        require 'src/views/admin/adminTodayLate.php';
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

    public function viewLeavesDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new AdminModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/admin/leaves/viewLeave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
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

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new AdminModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/admin/myLeave.php';
    }

    public function getApprovedLateFilter()
    {
        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'type' => $_POST['type'] ?? null,
        ];

        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Add limit and offset to filters
        $filters['limit'] = $recordsPerPage;
        $filters['offset'] = $offset;

        // Fetch approved late records with pagination and filters
        $getApprovedAll = $adminModel->getApprovedLateByFilter($filters);

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalApprovedLate('Approved'); // Get total count of approved records

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        $getPendingCount = $adminModel->getLateCountByStatus('Pending');
        $getApprovedCount = $adminModel->getLateCountByStatus('Approved');
        $getRejectedCount = $adminModel->getLateCountByStatus('Rejected');

        $getLeavesApproved = $adminModel->getApprovedLeaveCount();
        // get lates in count 
        $getLatesInCount = $adminModel->getLatesInCount();
        // get lates out count 
        $getLatesOutCount = $adminModel->getLatesOutCount();
        // get leaves early count 
        $getLeavesEarlyCount = $adminModel->getLeavesEarlyCount();
        // get missions 
        $getMissions = $adminModel->getMissions();

        require 'src/views/admin/adminApproved.php';
    }

    public function getMissionFilter()
    {
        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
        ];

        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Add limit and offset to filters
        $filters['limit'] = $recordsPerPage;
        $filters['offset'] = $offset;

        // Fetch approved late records with pagination and filters
        $getAllMissions = $adminModel->getMissionTodayFilter($filters);
        $getAllMissionCount = $adminModel->getMissionsTodayCount();

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalMission(); // Get total count of approved records

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        require 'src/views/admin/allMissionToday.php';
    }

    public function getMissionTodayFilter()
    {
        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
        ];

        $adminModel = new AdminModel();

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Add limit and offset to filters
        $filters['limit'] = $recordsPerPage;
        $filters['offset'] = $offset;

        // Fetch approved late records with pagination and filters
        $getAllMissions = $adminModel->getMissionFilter($filters);
        $getAllMissionCount = $adminModel->getMissionsTodayCount();

        // Fetch total approved records for pagination calculation
        $totalRecords = $adminModel->getTotalMission(); // Get total count of approved records

        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);

        require 'src/views/admin/allMissions.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new AdminModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        require 'src/views/leave/admin/myLeave.php';
    }

    public function security()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            // Get user_id from the query string and sanitize it
            $user_id = isset($_GET['user_id']) ? (int) $_GET['user_id'] : null;

            if ($user_id) {
                try {
                    $userController = new AdminModel();
                    $telegramModel = new TelegramModel($this->pdo);

                    // Fetch user details
                    $userDetails = $userController->getUserById($user_id);
                    if (!$userDetails) {
                        throw new Exception("User not found.");
                    }

                    // Fetch leave requests and overtime information
                    $requests = $userController->getUserLeaveRequests($user_id);
                    $getlatein = $userController->getOvertimeIn($user_id);
                    $getleavecounts = $userController->countUserApprovedLeaveRequests($user_id);
                    $getovertimeincount = $userController->getOvertimeInCount($user_id);

                    // Fetch Telegram ID
                    $getTelegramId = $telegramModel->getTelegramUserData($user_id);

                    // Render the view
                    require 'src/views/settings/security.php';
                } catch (Exception $e) {
                    // Handle any errors that occur, such as missing user data or database issues
                    echo "Error: " . $e->getMessage();
                }
            } else {
                // Handle the case where user_id is not provided or invalid
                echo "User ID not provided or invalid.";
            }
        } else {
            // Handle invalid request methods (if this endpoint is for GET only)
            echo "Invalid request method.";
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
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com';
            $mail->Password = 'kyph nvwd ncpa gyzi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

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

    public function delete($id)
    {
        $deleteLeaveRequest = new AdminModel();
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
        header("Location: /elms/adminLeave");
        exit();
    }

    public function displayAllAttendances()
    {
        $userModel = new User();
        $adminModel = new AdminModel();
        $userAttendances = $userModel->getAllUserAttendance($_SESSION['token']);

        // get leaves approved 
        $getLeavesApproved = $adminModel->getApprovedLeaveCount();
        // get lates in count 
        $getLatesInCount = $adminModel->getLatesInCount();
        // get lates out count 
        $getLatesOutCount = $adminModel->getLatesOutCount();
        // get lates out count 
        $getLeavesEarlyCount = $adminModel->getLeavesEarlyCount();
        // get lates out count 
        $getMissions = $adminModel->getMissions();

        require 'src/views/attendence/adminAttendance.php';
    }

    public function indexQR()
    {
        // Retrieve the QR code data from the database
        $qrModel = new QrModel();
        $qrCodeData = $qrModel->getQRCodeByName($_SESSION['user_id']);

        if ($qrCodeData) {
            // Pass the QR code's base64 data to the view
            $qrCodeBase64 = $qrCodeData['image'];
            $qrCodeBase64s = "data:image/png;base64," . $qrCodeBase64;
            $url = $qrCodeData['url'];
            $name = $qrCodeData['name'];
            $qrCodeFound = true; // Flag to indicate QR code was found
            $ids = $qrCodeData['id'];
        } else {
            $qrCodeBase64 = null;
            $qrCodeFound = false; // Flag to indicate QR code was not found
        }

        // Include the view to display the QR code
        require 'src/views/QRCode/qrcode.php';
    }

    public function generate()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $url = $_POST['url'];
            $userId = $_POST['userId'];
            $name = $_POST['name'];
            $size = (int) $_POST['size'];
            $latitude = $_POST['latitude'];  // Get the latitude from the form
            $longitude = $_POST['longitude']; // Get the longitude from the form
            $logoPath = null;

            // Capture device details
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';

            // If the app is behind a proxy or load balancer, we should check the HTTP_X_FORWARDED_FOR header
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                // Sometimes multiple IPs are passed, so we take the first one
                $ipAddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

            // Capture the device ID from the form (sent from the frontend)
            $deviceId = $_POST['device_id'] ?? 'Unknown';

            // Check if a logo is uploaded
            if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
                $fileType = mime_content_type($_FILES['logo']['tmp_name']);
                $allowedTypes = ['image/png', 'image/jpeg', 'image/jpg'];

                if (in_array($fileType, $allowedTypes)) {
                    if (!is_dir('public/uploads/qrcodes')) {
                        mkdir('public/uploads/qrcodes', 0777, true);
                    }
                    $logoPath = 'public/uploads/qrcodes/' . basename($_FILES['logo']['name']);
                    move_uploaded_file($_FILES['logo']['tmp_name'], $logoPath);

                    // Resize the logo
                    $logoPath = $this->resizeLogo($logoPath, $size);
                } else {
                    $_SESSION['error'] = [
                        'title' => "Invalid File Type",
                        'message' => "Only PNG and JPEG files are allowed for logos."
                    ];
                    header("Location: /elms/qrcode");
                    exit();
                }
            } else {
                $logoPath = 'public/img/icons/brands/logo2.png';
                // Resize the logo
                $logoPath = $this->resizeLogo($logoPath, $size);
            }

            // Generate QR Code with specified size and resized logo
            $qrCodeImage = $this->generateQRCodeWithLogo($url, $size, $logoPath);

            // Convert the QR code image to a base64 string for database insertion
            $qrCodeBase64 = base64_encode($qrCodeImage);

            // Save QR code data and location to the database, along with the device ID
            try {
                $adminModel = new QrModel();
                $generated = $adminModel->createQR($url, $userId, $name, $qrCodeBase64, $latitude, $longitude, $ipAddress, $userAgent, $deviceId);

                if ($generated) {
                    $_SESSION['success'] = [
                        'title' => "Generate QR Code",
                        'message' => "QR code generated and saved successfully."
                    ];

                    // Pass the base64 string to the view
                    $_SESSION['qrCodeBase64'] = $qrCodeBase64;

                    header("Location: /elms/qrcode");
                    exit();
                }
            } catch (PDOException $e) {
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => "Could not generate QR code. " . $e->getMessage()
                ];
                header("Location: /elms/qrcode");
                exit();
            }
        }
    }

    private function resizeLogo($logoPath, $qrCodeSize)
    {
        // Load the logo image
        list($logoWidth, $logoHeight, $imageType) = getimagesize($logoPath);

        // Set the desired width for the logo (adjust based on QR code size)
        $logoMaxWidth = $qrCodeSize * 0.3; // Resize logo to 30% of QR code size
        $logoMaxHeight = $qrCodeSize * 0.3; // Resize logo to 30% of QR code size

        // Calculate the new dimensions while maintaining the aspect ratio
        $ratio = min($logoMaxWidth / $logoWidth, $logoMaxHeight / $logoHeight);
        $newWidth = floor($logoWidth * $ratio);
        $newHeight = floor($logoHeight * $ratio);

        // Create a new image resource for the resized logo with alpha support
        $resizedLogo = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve alpha channel for transparency
        imagesavealpha($resizedLogo, true);
        $transparentColor = imagecolorallocatealpha($resizedLogo, 0, 0, 0, 127);
        imagefill($resizedLogo, 0, 0, $transparentColor);

        // Create an image from the original logo based on its type
        switch ($imageType) {
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($logoPath);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($logoPath);
                break;
            default:
                // Invalid file type
                return $logoPath;
        }

        // Resize the logo and copy it into the resized image
        imagecopyresampled($resizedLogo, $source, 0, 0, 0, 0, $newWidth, $newHeight, $logoWidth, $logoHeight);

        // Save the resized logo
        $resizedLogoPath = 'public/uploads/qrcodes/resized_' . basename($logoPath);
        imagepng($resizedLogo, $resizedLogoPath); // Save as PNG to preserve transparency

        // Clean up
        imagedestroy($source);
        imagedestroy($resizedLogo);

        return $resizedLogoPath;
    }

    public function generateQRCodeWithLogo($text, $size, $logoPath)
    {
        // Use the enum case ErrorCorrectionLevel::High directly
        $qrCode = new QrCode(
            data: $text,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High, // Use the enum case directly
            size: $size,
            margin: 10,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Initialize a writer for the PNG format
        $writer = new PngWriter();

        // Correct instantiation for Logo
        $logo = null;
        if ($logoPath && file_exists($logoPath)) {
            $logo = new Logo($logoPath); // Instantiate directly, no create method
            $logo->getPunchoutBackground();
        }

        // Generate the QR code with the optional logo
        $result = $writer->write($qrCode, $logo);

        // Return the QR code as a binary PNG string
        return $result->getString();
    }

    public function deleteQR()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];

            // Validate that the ID is provided
            if (!empty($id)) {
                // Call model to delete holiday
                $qrcode = new QrModel();
                $success = $qrcode->deleteQRCode($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/qrcode");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/qrcode");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/qrcode");
                exit();
            }
        }
    }
}
