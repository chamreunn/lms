<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

require_once 'src/models/unit1-d/DepUnit1Model.php';
require_once 'src/models/Leavetype.php';

class DepUnit2Controller
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
            $HeadDepartmentModel = new DepUnit2Model();

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
                $_SESSION['error'] = [
                    'title' => "Session Error",
                    'message' => "Session information is missing. Please log in again."
                ];
                header("Location: /elms/login");
                exit();
            }

            // Fetch POST data with null fallback if not provided
            $leave_type_id = $_POST['leave_type_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $remarks = $_POST['remarks'] ?? null;

            // Validate required fields
            if (!$leave_type_id || !$start_date || !$end_date) {
                $_SESSION['error'] = [
                    'title' => "Invalid Input",
                    'message' => "Please fill out all required fields."
                ];
                header("Location: /elms/dunit2Leave");
                exit();
            }

            // Handle messages
            $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            $leaveRemarks = "ច្បាប់";
            $status = "On Leave";

            // Handle file upload for attachment
            $attachment_name = $HeadDepartmentModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/dunit2Leave");
                exit();
            }


            if (new DateTime($end_date) < new DateTime($start_date)) {
                $_SESSION['error'] = [
                    'title' => "កំហុសកាលបរិច្ឆេទ",
                    'message' => "ថ្ងៃបញ្ចប់មិនអាចតូចជាងថ្ងៃចាប់ផ្ដើម។ សូមពិនិត្យម្តងទៀត"
                ];
                header("Location: /elms/my-leaves");
                exit();
            }

            // Fetch leave type details including duration from the database
            $leaveTypeModel = new Leavetype();
            $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
            if (!$leaveType) {
                $_SESSION['error'] = [
                    'title' => "Leave Type Error",
                    'message' => "Invalid leave type selected."
                ];
                header("Location: /elms/dunit2Leave");
                exit();
            }

            // Calculate the duration in business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $HeadDepartmentModel->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare the calculated duration with the allowed duration for the selected leave type
            $leave_type_duration = $leaveType['duration'];
            if ($duration_days > $leave_type_duration) {
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
                ];
                header("Location: /elms/dunit2Leave");
                exit();
            }

            // Fetch office details based on department
            $userDoffice = $userModel->getEmailLeaderHUApi($user_id, $token);
            if (empty($userDoffice) || empty($userDoffice['ids']) || empty($userDoffice['emails'])) {
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find Head Of Unit details or no emails found. Please contact support."
                ];
                header("Location: /elms/dunit2Leave");
                exit();
            }

            // Use the first available manager's ID and email
            $managerId = !empty($userDoffice['ids']) ? $userDoffice['ids'][0] : null;
            $managerEmail = !empty($userDoffice['emails']) ? $userDoffice['emails'][0] : null;
            $managerName = !empty($userDoffice['lastNameKh']) && !empty($userDoffice['firstNameKh'])
                ? $userDoffice['lastNameKh'][0] . ' ' . $userDoffice['firstNameKh'][0]
                : null;
            $link = "https://leave.iauoffsa.us/elms/hunitpending";

            if (!$managerId || !$managerEmail) {
                throw new Exception("No valid manager details found.");
            }

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
                $attachment_name,
            );

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/dunit2Leave");
                exit();
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
                header("Location: /elms/dunit2Leave");
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
            header("Location: /elms/dunit2Leave");
            exit();
        } else {
            require 'src/views/leave/unit2-d/myLeave.php';
        }
    }

    public function viewRequestsWithFilters()
    {
        $leaveRequestModel = new DepUnit2Model();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/unit2-d/myLeave.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepUnit2Model();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
        $leaveType = new Leavetype();
        $leavetypes = $leaveType->getAllLeavetypes();
        require 'src/views/leave/unit2-d/myLeave.php';
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
            // Validate required POST fields
            $requiredFields = ['request_id', 'status', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $_SESSION['error'] = [
                        'title' => "Invalid Input",
                        'message' => "Missing required fields. Please try again."
                    ];
                    header("Location: /elms/dunit2pending");
                    exit();
                }
            }

            // Retrieve POST data
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'] ?? ''; // Optional remarks field
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
            $leave = '1'; // Assuming '1' represents leave status
            $token = $_SESSION['token'];

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                // Create approval record
                $leaveApproval = new DepUnit2Model();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office/manager details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHUApi($_SESSION['user_id'], $token);

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
                    // Notify the next manager via Telegram or other methods
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

                    // Create user notification
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

                // Commit transaction
                $this->pdo->commit();

                // Set success message and redirect to the pending page
                $_SESSION['success'] = [
                    'title' => "សំណើច្បាប់",
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
                ];
                header('Location: /elms/dunit2pending');
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
                header("Location: /elms/dunit2pending");
                exit();
            }
        } else {
            // Handle GET request
            $leaveRequestModel = new DepUnit2Model();
            $requests = $leaveRequestModel->getAllLeaveRequests();
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/unit2-d/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepUnit2Model();
        $requests = $leaveRequestModel->getapproved($_SESSION['user_id']);

        require 'src/views/leave/unit2-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepUnit2Model();
        $requests = $leaveRequestModel->getrejected($_SESSION['user_id']);

        require 'src/views/leave/unit2-d/rejected.php';
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
        header("Location: /elms/dunit2Leave");
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
}
