<?php
require_once 'src/models/Notification.php';
require_once 'src/models/offices-d/DepOfficeModel.php';

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

            // Handle file upload for attachment
            $attachment_name = $leaveRequestModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Handle file upload for signature
            $signature_name = $leaveRequestModel->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signature_name === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Fetch leave type details including duration from database
            $leaveTypeModel = new Leavetype();
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

            // Calculate duration in business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $leaveRequestModel->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare duration_days with leave_type_duration
            if ($duration_days > $leave_type_duration) {
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
                ];
                header("Location: /elms/dashboard");
                exit();
            }

            // Fetch the user's office details
            $userDoffice = $userModel->getEmailLeaderHOApi($user_id, $_SESSION['token']);
            if (!$userDoffice || $userDoffice['http_code'] !== 200 || empty($userDoffice['emails'])) {
                error_log("API Response: " . print_r($userDoffice, true));
                $_SESSION['error'] = [
                    'title' => "Office Error",
                    'message' => "Unable to find office details. Please contact support."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            $managerEmail = $userDoffice['emails'];

            // Convert array to comma-separated string if necessary
            if (is_array($managerEmail)) {
                $managerEmail = implode(',', $managerEmail);
            }

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
                $attachment_name,
                $signature_name
            );

            if (!$leaveRequestId) {
                $_SESSION['error'] = [
                    'title' => "Leave Request Error",
                    'message' => "Failed to create leave request. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Send email notification
            if (!$leaveRequestModel->sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType['name'])) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Log user activity
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            // Set success message and redirect to the leave overview
            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
            ];
            header("Location: /elms/dofficeLeave");
            exit();
        } else {
            header("Location: /elms/dashboard");
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
            $requiredFields = ['request_id', 'status', 'remarks', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];
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
            $leaveApproval = new DepOfficeModel();
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

            // Handle file upload for manager's signature
            $signaturePath = $leaveApproval->handleFileUpload($_FILES['manager_signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signaturePath === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks, $signaturePath);

                // Fetch office details using API
                $userModel = new User();
                $userHoffice = $userModel->getEmailLeaderHOApi($_SESSION['user_id'], $_SESSION['token']);

                if (!$userHoffice || $userHoffice['http_code'] !== 200 || empty($userHoffice['emails'])) {
                    throw new Exception("Unable to find office details. Please contact support.");
                }

                // Convert emails array to string if necessary
                $managerEmail = $userHoffice['emails'];
                if (is_array($managerEmail)) {
                    $managerEmail = implode(',', $managerEmail); // Convert array to comma-separated string
                }

                // Send email notification
                if (!$leaveApproval->sendEmailNotificationToHOffice($managerEmail, $message, $request_id, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)) {
                    throw new Exception("Notification email could not be sent. Please try again.");
                }

                // Send email back to the user
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
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " .  $managerEmail
                ];
                header('location: /elms/pending');
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
                header("Location: /elms/apply-leave");
                exit();
            }
        } else {
            // Handle GET request
            $leaveApprovalModel = new DepOfficeModel();
            $requests = $leaveApprovalModel->getAllLeaveRequests();
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();

            require 'src/views/leave/offices-d/approvals.php';
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
            $leave_id = (int)$_GET['leave_id'];
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
