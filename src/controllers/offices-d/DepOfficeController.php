<?php
require_once 'src/models/Notification.php';
require_once 'src/models/offices-d/DepOfficeModel.php';
require_once 'src/models/hold/HoldModel.php';

class DepOfficeController
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
                // Initialize necessary models
                $userModel = new User();
                $leaveRequestModel = new DepOfficeModel();

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

                // Validate required fields
                $requiredFields = ['leave_type_id', 'start_date', 'end_date'];
                foreach ($requiredFields as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Missing required fields. Please fill out all fields.");
                    }
                }

                // Ensure end_date is not earlier than start_date
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
                    throw new Exception("មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត");
                }

                // Fetch leave type details from the database
                $leaveTypeModel = new Leavetype();
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }

                $leave_type_duration = $leaveType['duration'];

                // Calculate duration in business days
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $leaveRequestModel->calculateBusinessDays($datetime_start, $datetime_end);

                // Check if leave request duration exceeds the leave type's allowed duration
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("ប្រភេទច្បាប់នេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យប្រភេទច្បាប់ដែលបានជ្រើសរើសម្តងទៀត");
                }

                // Fetch the user's office details via API
                $userDoffice = $userModel->getEmailLeaderHOApi($user_id, $_SESSION['token']);
                if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Get manager's details
                $managerId = !empty($userDoffice['ids']) ? $userDoffice['ids'][0] : null;
                $managerEmail = !empty($userDoffice['emails']) ? $userDoffice['emails'][0] : null;
                $managerName = !empty($userDoffice['lastNameKh']) && !empty($userDoffice['firstNameKh'])
                    ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                    : null;

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave today
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                $link = "https://leave.iauoffsa.us/elms/headofficepending";

                // Create leave request in the database
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

                // Handle case where manager is on leave, and backup manager is needed
                if ($isManagerOnLeave || $isManagerOnMission) {
                    // Submit approval to backup manager if primary is on leave or mission
                    $leaveApproval = new DepOfficeModel();
                    $updatedAt = $leaveApproval->updateApproval($leaveRequestId, $managerId, $isManagerOnLeave ? $status : $mission, $isManagerOnLeave ? $leaveRemarks : $mission);

                    // Fetch another available manager if the current manager is on leave
                    $backupManager = $userModel->getEmailLeaderDDApi($user_id, $_SESSION['token']);
                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                    }

                    // Update to backup manager's details
                    $managerId = !empty($backupManager['ids']) ? $backupManager['ids'][0] : null;
                    $managerEmail = $backupManager['emails'][0];
                    $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                    $link = "https://leave.iauoffsa.us/elms/depdepartmentpending";

                    // Send Telegram notification for backup manager
                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                } else {
                    // Send Telegram notification for primary manager
                    $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                }

                // Send email notification to manager
                if (!$leaveRequestModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Create user activity log and notifications
                $notificationModel = new Notification();
                $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header("Location: /elms/dofficeLeave");
                exit();
            } catch (Exception $e) {
                // Handle exceptions and set error message
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/dofficeLeave");
                exit();
            }
        } else {
            // If request method is not POST, redirect to the dashboard
            header("Location: /elms/dofficeLeave");
            exit();
        }
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/myLeave.php';
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new DepOfficeModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/offices-d/myLeave.php';
    }

    public function pending()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate required POST fields
            $requiredFields = [
                'request_id',
                'status',
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
                    header("Location: /elms/pending");
                    exit();
                }
            }

            // Initialize variables from POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'] ?? ''; // Optional, use default if not provided
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
            $action = "On Leave";
            $mission = "Mission";

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new DepOfficeModel();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details via API
                $userModel = new User();
                $userDoffice = $userModel->getEmailLeaderHOApi($approver_id, $_SESSION['token']);

                if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                    throw new Exception("Unable to retrieve office details. Please contact support.");
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
                $link = "https://leave.iauoffsa.us/elms/headofficepending";

                if ($isManagerOnLeave || $isManagerOnMission) {
                    // Update approval with backup manager if the current manager is unavailable
                    $updatedAt = $leaveApproval->updatePendingApproval(
                        $request_id,
                        $managerId,
                        $isManagerOnLeave ? $action : $mission,
                        $isManagerOnLeave ? $leaveRemarks : $mission
                    );

                    // Retrieve backup manager details
                    $backupManager = $userModel->getEmailLeaderDDApi($user_id, $_SESSION['token']);
                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both primary and backup managers are unavailable.");
                    }

                    // Update to backup manager's details
                    $managerId = $backupManager['ids'][0];
                    $managerEmail = $backupManager['emails'][0];
                    $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                    $link = "https://leave.iauoffsa.us/elms/depdepartmentpending";
                }

                // Send notifications
                $userModel->sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link);
                $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

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
                    throw new Exception("Failed to send notification email to the office.");
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
                    throw new Exception("Failed to send notification email to the user.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log the approver's activity
                $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $this->pdo->commit();

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header('Location: /elms/pending');
                exit();
            } catch (Exception $e) {
                // Rollback transaction in case of error
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "បញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/pending");
                exit();
            }
        } else {
            // Handle GET request to view pending leave requests
            $leaveApprovalModel = new DepOfficeModel();
            $requests = $leaveApprovalModel->getAllLeaveRequests();

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

            // Initialize the HoldModel to retrieve any holds for the current user
            $holdsModel = new HoldModel();
            $hold = $holdsModel->getHoldByuserId($_SESSION['user_id']);

            // Initialize the HoldModel to retrieve any holds for the current user
            $resignsModel = new ResignModel();
            $resign = $resignsModel->getResignByuserId($_SESSION['user_id']);

            // Initialize the LeaveType model and retrieve all leave types
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            // Load the approval view
            require 'src/views/leave/offices-d/approvals.php';
        }
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
                    header("Location: /elms/pending");
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
                header("Location: /elms/pending");
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
                    header("Location: /elms/pending");
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
                header("Location: /elms/pending");
                exit();
            }
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getdhapproved($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepOfficeModel();
        $requests = $leaveRequestModel->getdhrejected($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/offices-d/rejected.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new DepOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/offices-d/viewLeave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function viewLeaveDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new DepOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/offices-d/viewLeaveDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function delete($id)
    {
        $deleteLeaveRequest = new DepOfficeModel();
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
        header("Location: /elms/dofficeLeave");
        exit();
    }
}
