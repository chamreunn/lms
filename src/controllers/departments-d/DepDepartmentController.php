<?php
require_once 'src/models/departments-d/DepDepartmentModel.php';
require_once 'src/models/Leavetype.php';

class DepDepartmentController
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
            try {
                $userModel = new User();
                $leaveTypeModel = new Leavetype();
                $model = new DepDepartmentModel();

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
                $mission = "Mission";
                $missionRemarks = "បេសកកម្ម";

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
                $attachment_name = $model->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
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
                $duration_days = $model->calculateBusinessDays($datetime_start, $datetime_end);

                // Compare duration_days with leave_type_duration
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("The selected leave type allows for a maximum of " . $leave_type_duration . " days. Please check your selection.");
                }

                // Fetch the user's office details
                $userDoffice = $userModel->getEmailLeaderHDApi($user_id, $_SESSION['token']);
                if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                    error_log("API Response: " . print_r($userDoffice, true));
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = !empty($userDoffice['ids']) ? $userDoffice['ids'][0] : null;
                $managerEmail = !empty($userDoffice['emails']) ? $userDoffice['emails'][0] : null;
                $managerName = !empty($userDoffice['lastNameKh']) && !empty($userDoffice['firstNameKh'])
                    ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                    : null;
                $link = "https://leave.iauoffsa.us/elms/headdepartmentpending";

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave today using the leave_requests table
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                // Convert array to comma-separated string if necessary
                if (is_array($managerEmail)) {
                    $managerEmail = implode(',', $managerEmail);
                }

                // Start a database transaction
                $this->pdo->beginTransaction();

                // Create a new leave request
                $leaveRequestId = $model->create(
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
                    // Submit approval
                    $updatedAt = $model->updateApproval($leaveRequestId, $managerId, $isManagerOnLeave ? $status : $mission, $isManagerOnLeave ? $leaveRemarks : $missionRemarks);

                    // Fetch another available manager if the current manager is on leave
                    $backupManager = null;
                    if (in_array($department, ["នាយកដ្ឋានកិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
                        // Fetch the user's Unit details
                        $backupManager = $userModel->getEmailLeaderDHU1Api($user_id, $_SESSION['token']);
                        // Update to backup manager's details
                        $managerId = !empty($backupManager['ids']) ? $backupManager['ids'][0] : null;
                        $managerEmail = $backupManager['emails'][0];
                        $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                        $link = "https://leave.iauoffsa.us/elms/dunit1pending";
                    } else {
                        $backupManager = $userModel->getEmailLeaderDHU2Api($user_id, $_SESSION['token']);
                        // Update to backup manager's details
                        $managerId = !empty($backupManager['ids']) ? $backupManager['ids'][0] : null;
                        $managerEmail = $backupManager['emails'][0];
                        $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                        $link = "https://leave.iauoffsa.us/elms/dunit2pending";
                    }

                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                    }

                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                    // Send email notification to the manager
                    if (!$model->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                        $_SESSION['error'] = [
                            'title' => "Email Error",
                            'message' => "Notification email could not be sent. Please try again."
                        ];
                        header("Location: /elms/apply-leave");
                        exit();
                    }
                } else {
                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                    // Send email notification to the manager
                    if (!$model->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                        $_SESSION['error'] = [
                            'title' => "Email Error",
                            'message' => "Notification email could not be sent. Please try again."
                        ];
                        header("Location: /elms/apply-leave");
                        exit();
                    }
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

                // Log user activity
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit the transaction
                $this->pdo->commit();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header("Location: /elms/ddepartmentLeave");
                exit();
            } catch (Exception $e) {
                // Rollback the transaction if an error occurs
                if ($this->pdo->inTransaction()) {
                    $this->pdo->rollBack();
                }

                // Log the error and set an error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/ddepartmentLeave");
                exit();
            }
        } else {
            // Redirect for non-POST requests
            header("Location: /elms/ddepartmentLeave");
            exit();
        }
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepDepartmentModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        require 'src/views/leave/departments-d/myLeave.php';
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new DepDepartmentModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/departments-d/myLeave.php';
    }

    public function pending()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validate required POST fields
            $requiredFields = ['request_id', 'status', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];
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

            // Initialize variables
            $link = "";  // Ensure $link is defined early to avoid "undefined variable" warning

            // Assign POST data to variables
            $Model = new DepDepartmentModel();
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'];
            $uremarks = $_POST['uremarks'];
            $uname = $_POST['uname'];
            $uEmail = $_POST['uemail'];
            $leaveType = $_POST['leaveType'];
            $user_id = $_POST['user_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $duration_days = $_POST['duration'];
            $approver_id = $_SESSION['user_id'];
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            $leaveRemarks = "ច្បាប់";
            $onLeave = "On Leave";
            $onMission = "On Mission";  // New variable for mission check  
            $department = $_SESSION['departmentName'];

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                // Create approval record
                $leaveApproval = new DepDepartmentModel();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHDApi($_SESSION['user_id'], $_SESSION['token']);

                if (!is_array($userHoffice) || !isset($userHoffice['ids'])) {
                    throw new Exception("Unable to find Department details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = $userHoffice['ids'][0] ?? null;
                $managerEmail = $userHoffice['emails'][0] ?? null;
                $managerName = isset($userHoffice['lastNameKh'][0]) && isset($userHoffice['firstNameKh'][0])
                    ? $userHoffice['lastNameKh'][0] . ' ' . $userHoffice['firstNameKh'][0]
                    : null;

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave or mission today
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                // If manager is on leave or mission, assign a backup manager
                if ($isManagerOnLeave || $isManagerOnMission) {
                    $updatedAt = $leaveApproval->updatePendingApproval($request_id, $managerId, $isManagerOnLeave ? $onLeave : $onMission, $isManagerOnLeave ? $leaveRemarks : $onMission);

                    // Fetch backup manager
                    $backupManager = null;
                    if (in_array($department, ["នាយកដ្ឋានកិច្ចការទូទៅ", "នាយកដ្ឋានសវនកម្មទី២"])) {
                        $backupManager = $userModel->getEmailLeaderDHU1Api($user_id, $_SESSION['token']);

                        // Update to backup manager's details
                        $managerEmail = $backupManager['emails'][0];
                        $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                        $link = "https://leave.iauoffsa.us/elms/dunit1pending";
                    } else {
                        $backupManager = $userModel->getEmailLeaderDHU2Api($user_id, $_SESSION['token']);

                        // Update to backup manager's details
                        $managerEmail = $backupManager['emails'][0];
                        $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                        $link = "https://leave.iauoffsa.us/elms/dunit2pending";
                    }

                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                    }
                }

                // Ensure that $link has a valid URL in all cases
                if (empty($link)) {
                    $link = "https://leave.iauoffsa.us/elms/depdepartmentpending";
                }

                // Send notifications via Telegram and email
                $userModel->sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link);
                $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                if (!$Model->sendEmailNotification($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                if (!$Model->sendEmailBackToUser($uEmail, $_SESSION['user_khmer_name'], $request_id, $status, $updatedAt, $remarks)) {
                    throw new Exception("Notification email to user could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log user activity
                $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $this->pdo->commit();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header('location: /elms/depdepartmentpending');
                exit();
            } catch (Exception $e) {
                // Rollback transaction
                $this->pdo->rollBack();

                // Log and display error
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/depdepartmentpending");
                exit();
            }
        } else {
            // Handle GET request
            $leaveRequestModel = new DepDepartmentModel();
            $requests = $leaveRequestModel->getAllLeaveRequests();

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

            // Initialize the HoldModel to retrieve any holds for the current user
            $holdsModel = new HoldModel();
            $hold = $holdsModel->getHoldByuserId($_SESSION['user_id']);

            // Initialize the TransferoutModel to retrieve transfer-out details for the current user
            $transferoutModel = new TransferoutModel();
            $transferouts = $transferoutModel->getTransferoutByUserId($_SESSION['user_id']);

            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/departments-d/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepDepartmentModel();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/departments-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepDepartmentModel();
        $requests = $leaveRequestModel->gethrejected($_SESSION['user_id']);

        require 'src/views/leave/departments-d/rejected.php';
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
        header("Location: /elms/ddepartmentLeave");
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
        header("Location: /elms/dashboard");
        exit();
    }

    public function viewLeaveDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new DepDepartmentModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/departments-d/viewLeaveDetail.php';
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
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepDepartmentModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $leaveApproval->updateHoldApproval($userId, $holdId, $action, $comment);
                // Recursive manager delegation
                $leaveApproval->delegateManager($leaveApproval, $userModel, $managers, $holdId, $userId);

                if ($leaveApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/depdepartmentpending");
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
                header("Location: /elms/depdepartmentpending");
                exit();
            }
        }
    }

    public function actiontransferout()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $transferoutId = $_POST['transferoutId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $transferoutapproval = new DepDepartmentModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $transferoutapproval->updateTransferoutApproval($userId, $transferoutId, $action, $comment);
                // Recursive manager delegation
                $transferoutapproval->delegateManagerTransferout($transferoutapproval, $userModel, $managers, $transferoutId, $userId);

                if ($transferoutapproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/depdepartmentpending");
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
                header("Location: /elms/depdepartmentpending");
                exit();
            }
        }
    }

    public function actionResign()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $resignId = $_POST['resignId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepOfficeModel();
                $leaveApproval->updateResignApproval($userId, $resignId, $approverId, $action, $comment);

                if ($leaveApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតលាឈប់",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតលាឈប់រួចរាល់។"
                    ];
                    header("Location: /elms/depdepartmentpending");
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
                header("Location: /elms/depdepartmentpending");
                exit();
            }
        }
    }
}
