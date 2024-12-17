<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/offices-h/HeadOfficeModel.php';
require_once 'src/models/Leavetype.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HeadOfficeController
{
    private $pdo;
    protected $table_name = "leave_requests";
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();
            $leaveTypeModel = new Leavetype();
            $headOfficeModel = new HeadOfficeModel();

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
            $transfer = $_POST['transferId'];

            // Validate required fields
            $requiredFields = ['leave_type_id', 'start_date', 'end_date'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = [
                        'title' => "Input Error",
                        'message' => "Missing required fields. Please fill out all fields."
                    ];
                    header("Location: /elms/apply-leave");
                    exit();
                }
            }

            // Date validation
            if (new DateTime($end_date) < new DateTime($start_date)) {
                $_SESSION['error'] = [
                    'title' => "កំហុសកាលបរិច្ឆេទ",
                    'message' => "ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត"
                ];
                header("Location: /elms/my-leaves");
                exit();
            }

            // Handle file upload for attachment
            $attachment_name = $headOfficeModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Fetch leave type details including duration
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

            // Calculate business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $headOfficeModel->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare duration_days with leave_type_duration
            if ($duration_days > $leave_type_duration) {
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
                ];
                header("Location: /elms/dashboard");
                exit();
            }

            // Create leave request
            $leaveRequestId = $headOfficeModel->create(
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
                header("Location: /elms/apply-leave");
                exit();
            }

            // Determine approving managers with dynamic links
            $managerApis = [
                'getEmailLeaderDDApi' => ['column' => 'dhead_department', 'link' => 'https://leave.iauoffsa.us/elms/depdepartmentpending'],
                'getEmailLeaderHDApi' => ['column' => 'head_department', 'link' => 'https://leave.iauoffsa.us/elms/headdepartmentpending'],
                'getEmailLeaderDHU1Api' => ['column' => 'dhead_unit', 'link' => 'https://leave.iauoffsa.us/elms/dunit1pending'],
                'getEmailLeaderDHU2Api' => ['column' => 'dhead_unit', 'link' => 'https://leave.iauoffsa.us/elms/dunit2pending'],
                'getEmailLeaderHUApi' => ['column' => 'head_unit', 'link' => 'https://leave.iauoffsa.us/elms/hunitpending']
            ];

            $approvingManagerId = null;
            $approvingManagerEmail = null;
            $approvingManagerName = null;

            foreach ($managerApis as $apiMethod => $details) {
                $columnToUpdate = $details['column'];
                $link = $details['link'];

                $managerDetails = $userModel->$apiMethod($user_id, $_SESSION['token']);
                if (!$managerDetails || empty($managerDetails['ids'])) {
                    continue; // Skip if no managers found
                }

                foreach ($managerDetails['ids'] as $index => $managerId) {
                    $managerEmail = $managerDetails['emails'][$index] ?? null;
                    $managerName = $managerDetails['lastNameKh'][$index] . ' ' . $managerDetails['firstNameKh'][$index];

                    // Check if the manager is available
                    $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                    $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                    if ($isManagerOnLeave || $isManagerOnMission) {
                        // Set appropriate status
                        $approvalStatus = $isManagerOnLeave ? "On Leave" : "Mission";

                        // Update table_approval with "On Leave" or "Mission"
                        $leaveApproval = new DepOfficeModel();
                        $leaveApproval->updateApproval($leaveRequestId, $managerId, $approvalStatus, $remarks);

                        // Update table_name to "Approved" in the relevant column
                        $stmt = $this->pdo->prepare(
                            "UPDATE {$this->table_name} SET $columnToUpdate = 'Approved' WHERE id = ?"
                        );
                        $stmt->execute([$leaveRequestId]);

                        // Notify manager via Telegram
                        $userModel->sendTelegramNotification(
                            $userModel,
                            $managerId,
                            $start_date,
                            $end_date,
                            $duration_days,
                            $remarks,
                            $leaveRequestId,
                            $link
                        );
                    } else {
                        // Assign the first available manager
                        $approvingManagerId = $managerId;
                        $approvingManagerEmail = $managerEmail;
                        $approvingManagerName = $managerName;
                        break 2; // Exit loops if a valid manager is found
                    }
                }
            }

            if (!$approvingManagerId) {
                throw new Exception("No available managers for approval. Please contact support.");
            }

            if (
                !
                $headOfficeModel->sendEmailNotification(
                    $approvingManagerEmail,
                    $message,
                    $leaveRequestId,
                    $start_date,
                    $end_date,
                    $duration_days,
                    $remarks,
                    $leaveType['name']
                )
            ) {
                throw new Exception("Notification email could not be sent. Please try again.");
            }

            // Create user activity log and notifications
            $notificationModel = new Notification();
            $notificationModel->createNotification($approvingManagerId, $user_id, $leaveRequestId, $message);
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ {$approvingManagerName}"
            ];
            header("Location: /elms/hofficeLeave");
            exit();
        } else {
            // If the request method is not POST, redirect to the dashboard
            header("Location: /elms/dashboard");
            exit();
        }
    }

    public function viewRequests()
    {
        $leaveRequestModel = new HeadOfficeModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();
        $userModel = new User();
        $depoffice = $userModel->getEmailLeaderDOApi($_SESSION['user_id'], $_SESSION['token']);

        require 'src/views/leave/offices-h/myLeave.php';
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new HeadOfficeModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/offices-h/myLeave.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new HeadOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);

            if ($request) {
                require 'src/views/leave/offices-h/viewLeave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function pending()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Required POST fields
            $requiredFields = ['request_id', 'status', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];

            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = [
                        'title' => "Invalid Input",
                        'message' => "Missing required field: $field. Please try again."
                    ];
                    header("Location: /elms/apply-leave");
                    exit();
                }
            }

            // Retrieve POST data
            $data = [
                'request_id' => $_POST['request_id'],
                'status' => $_POST['status'],
                'remarks' => $_POST['remarks'] ?? '',
                'uremarks' => $_POST['uremarks'] ?? '',
                'uname' => $_POST['uname'],
                'uemail' => $_POST['uemail'],
                'leaveType' => $_POST['leaveType'],
                'user_id' => $_POST['user_id'],
                'start_date' => $_POST['start_date'],
                'end_date' => $_POST['end_date'],
                'duration_days' => $_POST['duration'],
                'approver_id' => $_SESSION['user_id'],
                'department' => $_SESSION['departmentName'],
            ];

            // Prepare additional variables
            $link = "https://leave.iauoffsa.us/elms/";
            $message = $_SESSION['user_khmer_name'] . " បាន " . $data['status'] . " ច្បាប់ឈប់សម្រាក។";

            try {
                $this->pdo->beginTransaction();

                // Submit approval
                $leaveApproval = new HeadOfficeModel();
                $leaveApproval->submitApproval($data['request_id'], $data['approver_id'], $data['status'], $data['remarks']);

                // Determine the appropriate manager API based on the department
                $managerApis = $this->determineManagerApis($data['department']);
                $userModel = new User();

                $approvingManager = $this->findAvailableManager($managerApis, $userModel, $data);

                if (!$approvingManager) {
                    throw new Exception("No available managers for approval. Please contact support.");
                }

                // Notify next approver via Telegram
                $userModel->sendTelegramNextManager(
                    $approvingManager['id'],
                    $data['uname'] . " បានស្នើសុំច្បាប់ឈប់សម្រាក។",
                    $data['start_date'],
                    $data['end_date'],
                    $data['duration_days'],
                    $data['uremarks'],
                    $data['status'],
                    $link
                );

                // Notify the user about the submission
                $userModel->sendBackToUser(
                    $data['user_id'],
                    $data['uname'],
                    $data['start_date'],
                    $data['end_date'],
                    $data['duration_days'],
                    $data['remarks'],
                    $data['status']
                );

                // Create notification
                $notificationModel = new Notification();
                $notificationModel->createNotification(
                    $data['user_id'],
                    $data['approver_id'],
                    $data['request_id'],
                    $message
                );

                // Log the user's activity
                $activity = "បាន " . $data['status'] . " ច្បាប់ឈប់សម្រាក " . $data['uname'];
                $userModel->logUserActivity($data['approver_id'], $activity, $_SERVER['REMOTE_ADDR']);

                $this->pdo->commit();

                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $approvingManager['name']
                ];
                header('location: /elms/headofficepending');
                exit();
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/headofficepending");
                exit();
            }
        } else {
            // Handle GET request
            $leaveApprovalModel = new HeadOfficeModel();
            $requests = $leaveApprovalModel->getAllLeaveRequests();

            // Fetch additional data
            $userModel = new User();
            $approver = $userModel->getApproverByRole(
                $userModel,
                $_SESSION['user_id'],
                $_SESSION['token'],
                $_SESSION['role'],
                $_SESSION['departmentName']
            );

            $holdsModel = new HoldModel();
            $hold = $holdsModel->getHoldByuserId($_SESSION['user_id']);

            $transferoutModel = new TransferoutModel();
            $transferouts = $transferoutModel->getTransferoutByUserId($_SESSION['user_id']);

            $resignsModel = new ResignModel();
            $resigns = $resignsModel->getResignByuserId($_SESSION['user_id']);

            $backworkModel = new BackworkModel();
            $backworks = $backworkModel->getBackworkByUserId($_SESSION['user_id']);

            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/offices-h/pending.php';
        }
    }

    private function determineManagerApis($department)
    {
        $managerApis = [
            'getEmailLeaderDDApi' => 'dhead_department',
            'getEmailLeaderHDApi' => 'head_department',
            'getEmailLeaderDHU1Api' => 'dhead_unit',
            'getEmailLeaderDHU2Api' => 'dhead_unit',
            'getEmailLeaderHUApi' => 'head_unit'
        ];

        if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
            unset($managerApis['getEmailLeaderDHU2Api']);
        } else {
            unset($managerApis['getEmailLeaderDHU1Api']);
        }

        return $managerApis;
    }

    private function findAvailableManager($managerApis, $userModel, $data)
    {
        foreach ($managerApis as $apiMethod => $columnToUpdate) {
            $managerDetails = $userModel->$apiMethod($data['user_id'], $_SESSION['token']);
            if (!$managerDetails || empty($managerDetails['ids'])) {
                continue;
            }

            foreach ($managerDetails['ids'] as $index => $managerId) {
                $managerEmail = $managerDetails['emails'][$index] ?? null;
                $managerName = $managerDetails['lastNameKh'][$index] . ' ' . $managerDetails['firstNameKh'][$index];

                if ($userModel->isManagerOnLeaveToday($managerId)) {
                    // Update the status to "On Leave" in the database
                    $leaveApproval = new HeadOfficeModel();
                    $leaveApproval->updatePendingApproval($data['request_id'], $managerId, "On Leave", "ច្បាប់");

                    // Update the specific column in the database
                    $stmt = $this->pdo->prepare(
                        "UPDATE {$this->table_name} SET $columnToUpdate = 'Approved' WHERE id = ?"
                    );
                    $stmt->execute([$data['request_id']]);
                } elseif ($userModel->isManagerOnMission($managerId)) {
                    // Update the status to "On Mission" in the database
                    $leaveApproval = new HeadOfficeModel();
                    $leaveApproval->updatePendingApproval($data['request_id'], $managerId, "On Mission", "បេសកកម្ម");

                    // Update the specific column in the database
                    $stmt = $this->pdo->prepare(
                        "UPDATE {$this->table_name} SET $columnToUpdate = 'Approved' WHERE id = ?"
                    );
                    $stmt->execute([$data['request_id']]);
                } else {
                    // Return the first available manager
                    return [
                        'id' => $managerId,
                        'email' => $managerEmail,
                        'name' => $managerName
                    ];
                }
            }
        }

        return null;
    }


    public function approved()
    {
        $leaveRequestModel = new HeadOfficeModel();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/offices-h/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new HeadOfficeModel();
        $requests = $leaveRequestModel->gethrejected($_SESSION['user_id']);

        require 'src/views/leave/offices-h/rejected.php';
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
        header("Location: /elms/hofficeLeave");
        exit();
    }

    public function viewLeaveDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new HeadOfficeModel();
            $leave_id = (int) $_GET['leave_id'];
            $leavetypeModel = new Leavetype();
            $userModel = new User();

            // Fetch leave request details
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);

            // Fetch department office leader information
            $depoffice = $userModel->getEmailLeaderDOApi($_SESSION['user_id'], $_SESSION['token']);

            // Fetch leave types
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            // Check if the request is valid
            if ($request) {
                require 'src/views/leave/offices-h/viewLeaveDetail.php';
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

            // Extract session and form data
            $approverId = $_SESSION['user_id'];
            $approverName = $_SESSION['user_khmer_name'];
            $department = $_SESSION['departmentName'];

            $holdId = $_POST['holdId'];
            $nextApproverId = $_POST['approverId'];
            $uId = $_POST['uId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'] ?? '';

            // User request details
            $uName = $_POST['uName'] ?? '';
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $duration = $_POST['duration'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $actionAt = date('Y-m-d h:i:s A');

            $title = "លិខិតព្យួរការងារ";

            try {
                // Ensure a valid connection and start transaction
                if (!$this->pdo->inTransaction()) {
                    $this->pdo->beginTransaction();
                }

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new HeadOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $leaveApproval->updateHoldApproval($approverId, $holdId, $action, $comment);
                // Recursive manager delegation
                $leaveApproval->delegateManager($leaveApproval, $userModel, $managers, $holdId, $approverId);
                // Send notifications
                $userModel->sendDocBackToUser($title, $uId, $approverName, $action, $comment, $actionAt);
                $userModel->sendDocToNextApprover(
                    $title,
                    $comment,
                    $actionAt,
                    $nextApproverId,
                    $approverName,
                    $uName,
                    $action,
                    $start_date,
                    $end_date,
                    $duration,
                    $reason
                );

                // Define notification details
                $notificationMessageToUser = $approverName . " បាន " . $action . "ស្នើលិខិតព្យួរការងារ";
                $notificationProfile = $_SESSION['user_profile'];
                $notificationLink = ($_SERVER['SERVER_NAME'] === '127.0.0.1')
                    ? 'http://127.0.0.1/elms/headofficepending'
                    : 'https://leave.iauoffsa.us/elms/pending';

                // Create the in-app notification
                $notificationModel = new NotificationModel();
                $notificationModel->createNotification(
                    $uId,            // Target user ID (requestor)
                    $title,
                    $notificationMessageToUser,
                    $notificationProfile
                );

                // Notify the next approver
                $notificationModel->createNotification(
                    $nextApproverId, // Target user ID (next approver)
                    $title,
                    $notificationMessageToUser,
                    $notificationLink,
                    $notificationProfile
                );

                // Commit transaction
                if ($this->pdo->inTransaction()) {
                    $this->pdo->commit();
                }

                // Success message
                $_SESSION['success'] = [
                    'title' => $title,
                    'message' => "អ្នកបាន " . htmlspecialchars($action) . " លើលិខិតព្យួរការងាររួចរាល់។"
                ];
                header("Location: /elms/headofficepending");
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
                header("Location: /elms/headofficepending");
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
                $transferoutapproval = new HeadOfficeModel();
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
                    header("Location: /elms/headofficepending");
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
                header("Location: /elms/headofficepending");
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
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $resignApproval = new HeadOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $resignApproval->updateResignApproval($userId, $resignId, $action, $comment);

                // Recursive manager delegation
                $resignApproval->delegateResignManager($resignApproval, $userModel, $managers, $resignId, $userId);

                if ($resignApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតលាឈប់",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតលិខិតលាឈប់រួចរាល់។"
                    ];
                    header("Location: /elms/headofficepending");
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
                header("Location: /elms/headofficepending");
                exit();
            }
        }
    }

    public function actionback()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Get values from form and session
            $userId = $_SESSION['user_id'];
            $backworkId = $_POST['backworkId'];
            $approverId = $_POST['approverId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'];
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $backworkApproval = new HeadOfficeModel();
                $userModel = new User();

                if (in_array($department, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $managers = 'getEmailLeaderDHU1Api';
                } else {
                    $managers = 'getEmailLeaderDHU2Api';
                }

                $backworkApproval->updateTransBackworkApproval($userId, $backworkId, $action, $comment);
                // Recursive manager delegation
                $backworkApproval->delegateManagerBackwork($backworkApproval, $userModel, $managers, $backworkId, $userId);

                if ($backworkApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/headofficepending");
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
                header("Location: /elms/headofficepending");
                exit();
            }
        }
    }
}
