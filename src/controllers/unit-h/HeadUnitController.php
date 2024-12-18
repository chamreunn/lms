<?php
require_once 'src/models/unit1-d/DepUnit1Model.php';
require_once 'src/models/Leavetype.php';

class HeadUnitController
{

    private $pdo;

    private $table_name = "leave_requests";

    private $approval = "leave_approvals";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userModel = new User();
            $HeadDepartmentModel = new HeadUnitModel();

            // Retrieve session information
            $user_id = $_SESSION['user_id'] ?? null;
            $user_email = $_SESSION['email'] ?? null;
            $position = $_SESSION['position'] ?? null;
            $department = $_SESSION['departmentName'] ?? null;
            $token = $_SESSION['token'] ?? null;
            $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;
            $leave = 1;

            // Check if essential session data is present
            if (!$user_id || !$position || !$token || !$user_khmer_name) {
                $_SESSION['error'] = [
                    'title' => "Session Issue",
                    'message' => "Session information is missing. Please log in again."
                ];
                header("Location: /elms/login");
                exit();
            }

            // Retrieve POST data
            $leave_type_id = $_POST['leave_type_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $remarks = $_POST['remarks'] ?? null;

            // Check required fields
            if (!$leave_type_id || !$start_date || !$end_date) {
                $_SESSION['error'] = [
                    'title' => "Invalid Information",
                    'message' => "Please fill out all required fields."
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            try {
                // Convert dates to DateTime objects
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);

                // Validate date range
                if ($datetime_end < $datetime_start) {
                    throw new Exception("End date cannot be earlier than start date.");
                }

                // Handle attachment upload
                $attachment_name = $HeadDepartmentModel->handleFileUpload(
                    $_FILES['attachment'],
                    ['docx', 'pdf'],
                    2097152,
                    'public/uploads/leave_attachments/'
                );
                if ($attachment_name === false) {
                    throw new Exception("Failed to upload attachment. Please try again.");
                }

                // Retrieve leave type details
                $leaveTypeModel = new Leavetype();
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }

                // Calculate duration in business days
                $duration_days = $HeadDepartmentModel->calculateBusinessDays($datetime_start, $datetime_end);

                // Check if duration exceeds allowed leave type duration
                $leave_type_duration = $leaveType['duration'];
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("Selected leave type allows up to $leave_type_duration days.");
                }

                // First, try updating the API with leave information
                $updateToApi = $HeadDepartmentModel->updateToApi($user_id, $start_date, $end_date, $leave, $token);

                // Check if API update is successful
                // if ($updateToApi['status'] !== 200) {
                //     throw new Exception("Failed to update leave in the API. " . ($updateToApi['error'] ?? 'Unknown error.'));
                // }

                // If the API update is successful, create leave request in the database
                $leaveRequestId = $HeadDepartmentModel->create(
                    $user_id,
                    $user_email,
                    $leave_type_id,
                    $position,
                    $department,
                    $leaveType['name'],
                    $start_date,
                    $end_date,
                    $remarks,
                    $duration_days,
                    $attachment_name
                );

                // Check if leave request was created successfully in the database
                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request.");
                }

                // Log user activity
                $activity = "Requested leave.";
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Set success message
                $_SESSION['success'] = [
                    'title' => "Success",
                    'message' => "Leave request created successfully."
                ];
                header("Location: /elms/hunitLeave");
                exit();

            } catch (Exception $e) {
                // Log the error and set an error message for the user
                error_log("Error during leave request: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }
        } else {
            // Load the leave request view if not a POST request
            require 'src/views/leave/unit-h/myLeave.php';
        }
    }


    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new HeadUnitModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/unit-h/myLeave.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new HeadUnitModel();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        require 'src/views/leave/unit-h/myLeave.php';
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
                    header("Location: /elms/dunit1pending");
                    exit();
                }
            }

            // Create approval record
            $Model = new HeadUnitModel();
            // Retrieve POST data
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
            $approver_id = $_SESSION['user_id'];
            $message = $_SESSION['user_khmer_name'] . " បាន " . $status . " ច្បាប់ឈប់សម្រាក។";
            $username = $uname . " បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $leave = 1 ?? null;

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                $updateToApi = $Model->updateToApi($user_id, $start_date, $end_date, $leave, $_SESSION['token']);

                if (!$updateToApi) {
                    throw new Exception("Failed to update leave to API. " . ($updateToApi['error'] ?? 'Unknown error'));
                }
                // Create approval record
                $leaveApproval = new HeadUnitModel();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details using API
                $userModel = new User();

                // Send email back to the user
                if (!$Model->sendEmailBackToUser($uEmail, $_SESSION['user_khmer_name'], $request_id, $status, $updatedAt, $remarks)) {
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
                    'message' => "អនុម័តបានជោគជ័យ។"
                ];
                header('location: /elms/hunitpending');
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
                header("Location: /elms/hunitpending");
                exit();
            }
        } else {
            // Handle GET request
            $leaveRequestModel = new HeadUnitModel();
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

            // Initialize the backWork to retrieve transfer-out details for the current user
            $backworkModel = new BackworkModel();
            $backworks = $backworkModel->getBackworkByUserId($_SESSION['user_id']);

            $resignsModel = new ResignModel();
            $resigns = $resignsModel->getResignByuserId($_SESSION['user_id']);

            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();
            require 'src/views/leave/unit-h/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new HeadUnitModel();
        $requests = $leaveRequestModel->getapproved($_SESSION['user_id']);

        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/unit-h/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new HeadUnitModel();
        $requests = $leaveRequestModel->getrejected($_SESSION['user_id']);

        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/unit-h/rejected.php';
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
        header("Location: /elms/hunitLeave");
        exit();
    }

    public function displayAttendances()
    {
        $userModel = new User();
        $adminModel = new AdminModel();
        $userAttendances = $userModel->getAllUserAttendance($_SESSION['token']);
        $gettodaylatecount = $adminModel->getTodayLateCount('Approved');

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

        require 'src/views/attendence/HeadUnitAllAttendance.php';
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
        require 'src/views/leave/unit-h/adminTodayLate.php';
    }

    public function headunitViewCalendar()
    {
        // Load the models to fetch leave and holiday data
        $leaveRequestModel = new HeadUnitModel();
        $leaves = $leaveRequestModel->getLeadersOnLeave(); // Get leaves
        $calendarModel = new CalendarModel();
        $getHolidays = $calendarModel->getHolidayCDay(); // Get holidays

        // Load the view and pass the fetched data
        require 'src/views/leave/calendar.php';
    }

    public function action()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Extract session and form data
            $approverId = $_SESSION['user_id'];
            $approverName = $_SESSION['user_khmer_name'];

            $holdId = $_POST['holdId'];
            $uId = $_POST['uId'];
            $action = $_POST['status'];
            $comment = $_POST['comment'] ?? '';

            $actionAt = date('Y-m-d h:i:s A');

            $title = "លិខិតព្យួរការងារ";

            try {
                // Ensure a valid connection and start transaction
                if (!$this->pdo->inTransaction()) {
                    $this->pdo->beginTransaction();
                }

                // Create a DepOfficeModel instance and submit approval
                $leaveApproval = new HeadUnitModel();
                $userModel = new User();
                $leaveApproval->updateHoldApproval($holdId, $approverId, $action, $comment);

                // Send notifications
                $userModel->sendDocBackToUser($title, $uId, $approverName, $action, $actionAt, $comment);

                // Define notification details
                $notificationMessageToUser = $approverName . " បាន " . $action . "លើលិខិតព្យួរការងារ";
                $notificationProfile = $_SESSION['user_profile'];

                // Create the in-app notification
                $notificationModel = new NotificationModel();
                $notificationModel->createNotification(
                    $uId,            // Target user ID (requestor)
                    $title,
                    $notificationMessageToUser,
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
                header("Location: /elms/hunitpending");
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
                header("Location: /elms/hunitpending");
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

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $transferoutapproval = new HeadUnitModel();

                $transferoutapproval->updateTransferoutApproval($userId, $transferoutId, $approverId, $action, $comment);

                if ($transferoutapproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/hunitpending");
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
                header("Location: /elms/hunitpending");
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

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $backworkApproval = new HeadUnitModel();

                $backworkApproval->updateTransBackworkApproval($userId, $backworkId, $approverId, $action, $comment);

                if ($backworkApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតព្យួរការងារ",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតព្យួរការងាររួចរាល់។"
                    ];
                    header("Location: /elms/hunitpending");
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
                header("Location: /elms/dunit1pending");
                exit();
            }
        }
    }

    public function actionresign()
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
                $resignApproval = new HeadUnitModel();

                $resignApproval->updateTransResignApproval($userId, $resignId, $approverId, $action, $comment);

                if ($resignApproval) {
                    // Log the error and set error message
                    $_SESSION['success'] = [
                        'title' => "លិខិតលាឈប់",
                        'message' => "អ្នកបាន " . $action . " លើលិខិតលិខិតលាឈប់រួចរាល់។"
                    ];
                    header("Location: /elms/hunitpending");
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
                header("Location: /elms/hunitpending");
                exit();
            }
        }
    }
}
