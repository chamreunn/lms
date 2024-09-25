<?php
require_once 'src/models/admin/AdminModel.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

                // Validate required fields
                $requiredFields = ['leave_type_id', 'start_date', 'end_date'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Missing required fields. Please fill out all fields.");
                    }
                }

                // Handle file upload for attachment
                $attachment_name = $leaveRequestModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
                if ($attachment_name === false) {
                    throw new Exception("Unable to upload attachment. Please try again.");
                }

                // Handle file upload for signature
                $signature_name = $leaveRequestModel->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/signatures/');
                if ($signature_name === false) {
                    throw new Exception("Unable to upload signature. Please try again.");
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

                $managerEmail = $userDoffice['emails'];
                if (is_array($managerEmail)) {
                    $managerEmail = implode(',', $managerEmail);
                }

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
                    attachment: $attachment_name,
                    signature: $signature_name
                );

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
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
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
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
            $checkIn = $_POST['checkIn'] ?? null;
            $lateIn = $_POST['lateIn'] ?? null;

            $action = "បាន" . $status . "ការចូលយឺត";
            $comment = $_POST['comment'] ?? ''; // Ensure comment is set (avoid null)
            $title = "សំណើចូលយឺត";

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Ensure all required fields are provided
                if (empty($lateId) || empty($status) || empty($uId) || empty($checkIn) || empty($lateIn)) {
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

                    // If the status is not "Rejected", update the API with late-in information
                    if ($status !== 'Rejected') {
                        $updateToApi = $userModel->updateLateInToApi($uId, $checkIn, $lateIn, $_SESSION['token']);

                        // If API update fails, log or handle the error
                        if (!$updateToApi) {
                            throw new Exception("Failed to update late-in information to the API.");
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

        require 'src/views/admin/AllLeave.php';
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

    public function getPendingLate()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLatein();
        $getAlls = $adminModel->getAll();
        $getLateInCount = $adminModel->getLateinCount();
        $getLateOutCount = $adminModel->getLateoutCount();
        $getLeaveEarlyCount = $adminModel->getLeaveearlyCount();
        $getAllLate = $adminModel->getAllLate();
        $getApproved = $adminModel->getApprovedLateCount();

        require 'src/views/admin/lateinpending.php';
    }

    public function getPendingLateOut()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLateOut();
        $getAlls = $adminModel->getAll();
        $getLateInCount = $adminModel->getLateinCount();
        $getLateOutCount = $adminModel->getLateoutCount();
        $getLeaveEarlyCount = $adminModel->getLeaveearlyCount();
        $getAllLate = $adminModel->getAllLate();
        $getApproved = $adminModel->getApprovedLateCount();

        require 'src/views/admin/lateinpending.php';
    }

    public function getPendingLeaveEarly()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLeaveEarly();
        $getAlls = $adminModel->getAll();
        $getLateInCount = $adminModel->getLateinCount();
        $getLateOutCount = $adminModel->getLateoutCount();
        $getLeaveEarlyCount = $adminModel->getLeaveearlyCount();
        $getAllLate = $adminModel->getAllLate();
        $getApproved = $adminModel->getApprovedLateCount();

        require 'src/views/admin/lateinpending.php';
    }

    public function getAllLate()
    {
        $adminModel = new AdminModel();
        $getAll = $adminModel->getAllLeaveEarly();
        $getAlls = $adminModel->getAll();
        $getLateInCount = $adminModel->getLateinCount();
        $getLateOutCount = $adminModel->getLateoutCount();
        $getLeaveEarlyCount = $adminModel->getLeaveearlyCount();
        $getAllLate = $adminModel->getAllLate();
        $getApproved = $adminModel->getApprovedLateCount();

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
}
