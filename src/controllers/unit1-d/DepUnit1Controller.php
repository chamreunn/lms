<?php

require_once 'src/models/unit1-d/DepUnit1Model.php';
require_once 'src/models/Leavetype.php';

class DepUnit1Controller
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
            try {
                $userModel = new User();
                $HeadDepartmentModel = new DepUnit1Model();

                // Fetch session details
                $user_id = $_SESSION['user_id'] ?? null;
                $user_email = $_SESSION['email'] ?? null;
                $position = $_SESSION['position'] ?? null;
                $department = $_SESSION['departmentName'] ?? null;
                $token = $_SESSION['token'] ?? null;
                $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;
                $leave = '1' ?? "NULL";

                // Check if essential session data is available
                if (!$user_id || !$position || !$token || !$user_khmer_name) {
                    throw new Exception("Session information is missing. Please log in again.");
                }

                // Fetch POST data with null fallback if not provided
                $leave_type_id = $_POST['leave_type_id'] ?? null;
                $start_date = $_POST['start_date'] ?? null;
                $end_date = $_POST['end_date'] ?? null;
                $remarks = $_POST['remarks'] ?? null;

                // Validate required fields
                if (!$leave_type_id || !$start_date || !$end_date) {
                    throw new Exception("Please fill out all required fields.");
                }

                // Handle messages
                $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";
                $status = "On Leave";

                // Handle file upload for attachment
                $attachment_name = $HeadDepartmentModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
                if ($attachment_name === false) {
                    throw new Exception("មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត");
                }

                // Check if the end date is after the start date
                if (new DateTime($end_date) < new DateTime($start_date)) {
                    throw new Exception("ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត");
                }

                // Fetch leave type details
                $leaveTypeModel = new Leavetype();
                $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
                if (!$leaveType) {
                    throw new Exception("Invalid leave type selected.");
                }

                // Calculate the duration in business days
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $HeadDepartmentModel->calculateBusinessDays($datetime_start, $datetime_end);

                // Compare the calculated duration with the allowed duration for the selected leave type
                $leave_type_duration = $leaveType['duration'];
                if ($duration_days > $leave_type_duration) {
                    throw new Exception("ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត");
                }

                // Fetch office details based on department
                $userDoffice = $userModel->getEmailLeaderHUApi($user_id, $token);
                if (empty($userDoffice['ids']) || empty($userDoffice['emails'])) {
                    throw new Exception("Unable to find Head Of Unit details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = $userDoffice['ids'][0];
                $managerEmail = $userDoffice['emails'][0];
                $managerName = $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0];
                $link = "https://leave.iauoffsa.us/elms/hunitpending";

                // Check if the manager is on leave or mission
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                // Create leave request in the database
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

                if (!$leaveRequestId) {
                    throw new Exception("Failed to create leave request. Please try again.");
                }

                // If the manager is on leave or mission, update approval and API
                if ($isManagerOnLeave || $isManagerOnMission) {
                    $status = $isManagerOnLeave ? "On Leave" : "Mission";
                    $remarksText = $isManagerOnLeave ? "ច្បាប់" : "បេសកកម្ម";
                    $HeadDepartmentModel->updateApproval($leaveRequestId, $managerId, $status, $remarksText);
                    $HeadDepartmentModel->updateToApi($user_id, $start_date, $end_date, $leave, $token);

                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "ច្បាប់របស់អ្នកត្រូវបានអនុម័ត។"
                    ];
                    header("Location: /elms/dunit1Leave");
                    exit();
                }

                // Send notifications
                $userModel->sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link);
                $HeadDepartmentModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name']);

                // Log user activity
                $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

                // Create notification for the user
                $notificationModel = new Notification();
                $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

                // Set success message and redirect
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                ];
                header("Location: /elms/dunit1Leave");
                exit();

            } catch (Exception $e) {
                // Handle errors
                $_SESSION['error'] = [
                    'title' => "Error",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/dunit1Leave");
                exit();
            }
        } else {
            require 'src/views/leave/unit1-d/myLeave.php';
        }
    }


    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new DepUnit1Model();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters, $_SESSION['token']);

        require 'src/views/leave/unit1-d/myLeave.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id'], $_SESSION['token']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        require 'src/views/leave/unit1-d/myLeave.php';
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Define the required POST fields for validation
            $requiredFields = ['request_id', 'status', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];

            // Validate that all required fields are provided
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

            // Retrieve POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'] ?? ''; // Optional field
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
            $token = $_SESSION['token'];
            $leave = '1';  // Assuming '1' represents leave status
            $departmentName = $_SESSION['departmentName'] ?? null; // URL to direct the manager

            try {
                // Start the transaction
                $this->pdo->beginTransaction();

                // Create approval record
                $leaveApproval = new DepUnit1Model();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office/manager details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHUApi($approver_id, $token);

                if (!is_array($userHoffice) || !isset($userHoffice['ids'])) {
                    throw new Exception("Unable to find department details. Please contact support.");
                }

                // Use the first available manager's ID and email
                $managerId = $userHoffice['ids'][0] ?? null;
                $managerEmail = $userHoffice['emails'][0] ?? null;
                $managerName = isset($userHoffice['lastNameKh'][0]) && isset($userHoffice['firstNameKh'][0])
                    ? $userHoffice['lastNameKh'][0] . ' ' . $userHoffice['firstNameKh'][0]
                    : null;
                $link = "https://leave.iauoffsa.us/elms/hunitpending";

                if (!$managerId || !$managerEmail) {
                    throw new Exception("No valid manager details found.");
                }

                // Check if the manager is on leave or on a mission today
                $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
                $isManagerOnMission = $userModel->isManagerOnMission($managerId);

                if ($isManagerOnLeave || $isManagerOnMission) {
                    $action = $isManagerOnLeave ? "On Leave" : "Mission";
                    $remarksText = $isManagerOnLeave ? "ច្បាប់" : "បេសកកម្ម";

                    // Update approval status
                    $leaveApproval->updateApproval($request_id, $managerId, $action, $remarksText);

                    // Update the status to the API
                    $leaveApproval->updateToApi($user_id, $start_date, $end_date, $leave, $token);

                    // Notify the user
                    $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "ច្បាប់របស់អ្នកត្រូវបានអនុម័ត។"
                    ];
                } else {
                    // Send notification to the next manager via Telegram or other methods
                    $userModel->sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link);
                    $userModel->sendBackToUser($user_id, $uname, $start_date, $end_date, $duration_days, $uremarks, $status);

                    // Send email notification to the manager
                    if (!$leaveApproval->sendEmailNotification($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                        throw new Exception("Notification email could not be sent. Please try again.");
                    }

                    // Send email notification to the user
                    if (!$leaveApproval->sendEmailBackToUser($uEmail, $_SESSION['user_khmer_name'], $request_id, $status, $updatedAt, $remarks)) {
                        throw new Exception("Notification email to user could not be sent. Please try again.");
                    }

                    // Create a user notification
                    $notificationModel = new Notification();
                    $notificationModel->createNotification($user_id, $approver_id, $request_id, $message);

                    // Log the approver's activity
                    $activity = "បាន " . $status . " ច្បាប់ឈប់សម្រាក " . $uname;
                    $userModel->logUserActivity($approver_id, $activity, $_SERVER['REMOTE_ADDR']);

                    $_SESSION['success'] = [
                        'title' => "សំណើច្បាប់",
                        'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerName
                    ];
                }

                // Commit the transaction
                $this->pdo->commit();

                // Redirect to the pending page with success message
                header('Location: /elms/dunit1pending');
                exit();
            } catch (Exception $e) {
                // Rollback the transaction on failure
                $this->pdo->rollBack();

                // Log the error and set error message
                error_log("Error: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបញ្ជូនសំណើ: " . $e->getMessage()
                ];
                header("Location: /elms/dunit1pending");
                exit();
            }
        } else {
            // Handle the GET request to load pending requests
            $leaveRequestModel = new DepUnit1Model();
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

            require 'src/views/leave/unit1-d/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getapproved($_SESSION['user_id']);

        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/unit1-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getrejected($_SESSION['user_id']);

        $leavetypeModel = new Leavetype();
        $leavetypes = $leavetypeModel->getAllLeavetypes();

        require 'src/views/leave/unit1-d/rejected.php';
    }

    public function du1ViewCalendar()
    {
        // Load the models to fetch leave and holiday data
        $leaveRequestModel = new DepUnit1Model();
        $leaves = $leaveRequestModel->getLeadersOnLeave(); // Get leaves
        $calendarModel = new CalendarModel();
        $getHolidays = $calendarModel->getHolidayCDay(); // Get holidays

        // Load the view and pass the fetched data
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
        header("Location: /elms/dunit1Leave");
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

    public function action()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Extract session and form data
            $approverId = $_SESSION['user_id'];
            $approverName = $_SESSION['user_khmer_name'];

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
                $leaveApproval = new DepUnit1Model();
                $userModel = new User();

                $leaveApproval->updateHoldApproval($approverId, $holdId, $action, $comment);
                // Recursive manager delegation
                $leaveApproval->delegateManager($leaveApproval, $userModel, $holdId, $approverId, $action);

                // Send notifications
                $userModel->sendDocBackToUser($title, $uId, $approverName, $action, $actionAt, $comment);
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
                $notificationMessageToUser = $approverName . " បាន " . $action . "លើលិខិតព្យួរការងារ";
                $notificationProfile = $_SESSION['user_profile'];
                $notificationLink = ($_SERVER['SERVER_NAME'] === '127.0.0.1')
                    ? 'http://127.0.0.1/elms/dunit1pending'
                    : 'https://leave.iauoffsa.us/elms/dunit1pending';

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
                header("Location: /elms/dunit1pending");
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
                header("Location: /elms/dunit1pending");
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
                $transferoutapproval = new DepUnit1Model();
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
                    header("Location: /elms/dunit1pending");
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
                $backworkApproval = new DepUnit1Model();
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
                    header("Location: /elms/dunit1pending");
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
            $department = $_SESSION['departmentName'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create a DepOfficeModel instance and submit approval
                $resignApproval = new DepUnit1Model();
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
                    header("Location: /elms/dunit1pending");
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
}
