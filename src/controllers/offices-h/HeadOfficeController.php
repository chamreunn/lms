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

            $leaveRemarks = "ច្បាប់";
            $status = "On Leave";
            $mission = "Mission";
            $missionRemarks = "បេសកកម្ម";

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

            // Fetch the user's office details from API
            $userDoffice = $userModel->getEmailLeaderDDApi($user_id, $_SESSION['token']);
            if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                error_log("API Response: " . print_r($userDoffice, true));
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find office details. Please contact support."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Use the first available manager's ID and email
            $managerId = !empty($userDoffice['ids']) ? $userDoffice['ids'][0] : null;
            $managerEmail = !empty($userDoffice['emails']) ? $userDoffice['emails'][0] : null;
            $managerName = !empty($userDoffice['lastNameKh']) && !empty($userDoffice['firstNameKh'])
                ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                : null;
            $link = "https://leave.iauoffsa.us/elms/depdepartmentpending";

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
            );

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            if ($isManagerOnLeave || $isManagerOnMission) {
                // Submit approval for manager on leave
                $updatedAt = $headOfficeModel->updateApproval($leaveRequestId, $managerId, $isManagerOnLeave ? $status : $mission, $isManagerOnLeave ? $leaveRemarks : $missionRemarks);

                // Fetch backup manager details
                $backupManager = $userModel->getEmailLeaderHDApi($user_id, $_SESSION['token']);
                if (!$backupManager || empty($backupManager['emails'])) {
                    throw new Exception("Both the primary and backup managers are unavailable. Please contact support.");
                }

                // Update manager details to backup manager's info
                $managerId = !empty($backupManager['ids']) ? $backupManager['ids'][0] : null;
                $managerEmail = $backupManager['emails'][0];
                $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                $link = "https://leave.iauoffsa.us/elms/headdepartmentpending";

                $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                // Send email notification to the manager
                if (!$headOfficeModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
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
                if (!$headOfficeModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
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

            // Set success message and redirect to leave overview
            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
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

            // Create approval record
            $leaveApproval = new HeadOfficeModel();

            // Retrieve POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'] ?? '';  // Use default empty string if not provided
            $uremarks = $_POST['uremarks'] ?? ''; // Use default empty string if not provided
            $uname = $_POST['uname'];
            $uEmail = $_POST['uemail'];
            $leaveType = $_POST['leaveType'];
            $user_id = $_POST['user_id']; // ID of the user who applied for leave
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $duration_days = $_POST['duration'];
            $approver_id = $_SESSION['user_id'];

            // Prepare messages
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // Define constants for leave remarks and actions
            $leaveRemarks = "ច្បាប់";
            $action = "On Leave";
            $mission = "Mission";
            $missionRemarks = "បេសកកម្ម";

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                // Submit approval
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);

                if (!$userHoffice || $userHoffice['http_code'] !== 200 || empty($userHoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = $userHoffice['ids'][0] ?? null;
                $managerEmail = $userHoffice['emails'][0] ?? null;
                $managerName = isset($userHoffice['lastNameKh'][0]) && isset($userHoffice['firstNameKh'][0])
                    ? $userHoffice['lastNameKh'][0] . ' ' . $userHoffice['firstNameKh'][0]
                    : null;

                // Check if the manager is on leave today using the leave_requests table
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                // If the manager is unavailable, fetch backup manager details
                if ($isManagerOnLeave || $isManagerOnMission) {
                    $updatedAt = $leaveApproval->updatePendingApproval(
                        $request_id,
                        $managerId,
                        $isManagerOnLeave ? $action : $mission,
                        $isManagerOnLeave ? $leaveRemarks : $missionRemarks
                    );

                    $backupManager = $userModel->getEmailLeaderHDApi($user_id, $_SESSION['token']);
                    if (!$backupManager || empty($backupManager['emails'])) {
                        throw new Exception("Both primary and backup managers are unavailable.");
                    }

                    // Update to backup manager's details
                    $managerId = $backupManager['ids'][0] ?? null;
                    $managerEmail = $backupManager['emails'][0];
                    $managerName = $backupManager['lastNameKh'][0] . ' ' . $backupManager['firstNameKh'][0];
                    $link = "https://leave.iauoffsa.us/elms/headdepartmentpending";
                } else {
                    $link = "https://leave.iauoffsa.us/elms/depdepartmentpending";
                }

                // Send notifications
                $userModel->sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link);
                $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                // Send email notifications
                if (!$leaveApproval->sendEmailNotificationToHOffice($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    throw new Exception("Notification email could not be sent to the manager. Please try again.");
                }

                if (!$leaveApproval->sendEmailBackToUser($uEmail, $_SESSION['user_khmer_name'], $request_id, $status, $updatedAt, $remarks)) {
                    throw new Exception("Notification email to user could not be sent. Please try again.");
                }

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                // Log the user's activity
                $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Commit transaction
                $this->pdo->commit();

                // Set success message and redirect to the pending page
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header('location: /elms/headofficepending');
                exit();
            } catch (Exception $e) {
                // Rollback transaction in case of failure
                $this->pdo->rollBack();

                // Log error and set error message
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
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/offices-h/pending.php';
        }
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
            $request = $leaveRequestModel->getRequestById($leave_id, $_SESSION['token']);
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            if ($request) {
                require 'src/views/leave/offices-h/viewLeaveDetail.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }
}
