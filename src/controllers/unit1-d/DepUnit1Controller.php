<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

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

            $userModel = new User();
            $HeadDepartmentModel = new DepUnit1Model();

            // Fetch session details
            $user_id = $_SESSION['user_id'] ?? null;
            $user_email = $_SESSION['email'] ?? null;
            $position = $_SESSION['position'] ?? null;
            $department = $_SESSION['departmentName'] ?? null;
            $token = $_SESSION['token'] ?? null;
            $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;

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
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Handle messages
            $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // Handle file upload for attachment
            $attachment_name = $HeadDepartmentModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Handle file upload for signature
            $signature_name = $HeadDepartmentModel->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signature_name === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/dunit1Leave");
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
                header("Location: /elms/dunit1Leave");
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
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Fetch office details based on department
            $userDoffice = $userModel->getEmailLeaderHUApi($user_id, $token);
            if (empty($userDoffice) || empty($userDoffice['ids']) || empty($userDoffice['emails'])) {
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find Head Of Unit details or no emails found. Please contact support."
                ];
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Prepare email details
            $managerEmail = is_array($userDoffice['emails']) ? implode(',', $userDoffice['emails']) : $userDoffice['emails'];

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
                $signature_name
            );

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Log user activity
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            // Send email notification
            if (!$HeadDepartmentModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/dunit1Leave");
                exit();
            }

            // Create notification for the user
            $notificationModel = new Notification();
            $notificationModel->createNotification($userDoffice['ids'], $user_id, $leaveRequestId, $message);

            // Set success message and redirect
            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
            ];
            header("Location: /elms/dunit1Leave");
            exit();
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

        $requests = $leaveRequestModel->getRequestsByFilters($user_id, $filters);

        require 'src/views/leave/unit1-d/myLeave.php';
    }

    public function viewRequests()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);
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
            $Model = new DepUnit1Model();
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

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                // Create approval record
                $leaveApproval = new DepUnit1Model();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks);

                // Fetch office details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHUApi($_SESSION['user_id'], $_SESSION['token']);

                if (!is_array($userHoffice) || !isset($userHoffice['ids'])) {
                    throw new Exception("Unable to find Department details. Please contact support.");
                }

                // Convert emails array to string if necessary
                $managerEmail = $userHoffice['emails'];
                if (is_array($managerEmail)) {
                    $managerEmail = implode(',', $managerEmail); // Convert array to comma-separated string
                }

                // Send email notification
                if (!$Model->sendEmailNotification($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

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
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " .  $managerEmail
                ];
                header('location: /elms/dunit1pending');
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
                header("Location: /elms/dunit1pending");
                exit();
            }
        } else {
            // Handle GET request
            $leaveRequestModel = new DepUnit1Model();
            $requests = $leaveRequestModel->getAllLeaveRequests();
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/unit1-d/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getapproved($_SESSION['user_id']);

        require 'src/views/leave/unit1-d/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new DepUnit1Model();
        $requests = $leaveRequestModel->getrejected($_SESSION['user_id']);

        require 'src/views/leave/unit1-d/rejected.php';
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
}
