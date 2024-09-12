<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

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

            // ទាញយកព័ត៌មានពី session
            $user_id = $_SESSION['user_id'] ?? null;
            $user_email = $_SESSION['email'] ?? null;
            $position = $_SESSION['position'] ?? null;
            $department = $_SESSION['departmentName'] ?? null;
            $token = $_SESSION['token'] ?? null;
            $user_khmer_name = $_SESSION['user_khmer_name'] ?? null;

            // ពិនិត្យមើលថាតើព័ត៌មាន session សំខាន់ៗមានដែរឬទេ
            if (!$user_id || !$position || !$token || !$user_khmer_name) {
                $_SESSION['error'] = [
                    'title' => "បញ្ហា Session",
                    'message' => "ព័ត៌មាន session មិនមានទេ។ សូមចូលគណនីម្ដងទៀត។"
                ];
                header("Location: /elms/login");
                exit();
            }

            // ទាញយកព័ត៌មាន POST ប្រសិនបើមិនបានផ្ដល់នោះទេនឹងទទួលបាន null ជំនួសវិញ
            $leave_type_id = $_POST['leave_type_id'] ?? null;
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $remarks = $_POST['remarks'] ?? null;

            // ផ្ទៀងផ្ទាត់វាលដែលត្រូវបំពេញ
            if (!$leave_type_id || !$start_date || !$end_date) {
                $_SESSION['error'] = [
                    'title' => "ព័ត៌មានមិនត្រឹមត្រូវ",
                    'message' => "សូមបំពេញវាលដែលចាំបាច់ទាំងអស់។"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // បំលែងកាលបរិច្ឆេទទៅជា DateTime ដើម្បីធ្វើការប្រៀបធៀប
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);

            // ពិនិត្យមើលថាតើកាលបរិច្ឆេទបញ្ចប់តិចជាងកាលបរិច្ឆេទចាប់ផ្តើមឬទេ
            if ($datetime_end < $datetime_start) {
                $_SESSION['error'] = [
                    'title' => "បញ្ហាកាលបរិច្ឆេទ",
                    'message' => "កាលបរិច្ឆេទបញ្ចប់មិនអាចតិចជាងកាលបរិច្ឆេទចាប់ផ្តើមបានទេ។ សូមជ្រើសរើសរយៈពេលត្រឹមត្រូវ។"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // គ្រប់គ្រងសារផ្ញើ
            $message = "$user_khmer_name បានស្នើសុំច្បាប់ឈប់សម្រាក។";
            $activity = "បានស្នើសុំច្បាប់ឈប់សម្រាក។";

            // គ្រប់គ្រងការផ្ទុកឯកសារភ្ជាប់
            $attachment_name = $HeadDepartmentModel->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 2097152, 'public/uploads/leave_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // គ្រប់គ្រងការផ្ទុកហត្ថលេខា
            $signature_name = $HeadDepartmentModel->handleFileUpload($_FILES['signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signature_name === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // ទាញយកព័ត៌មានប្រភេទច្បាប់ រួមទាំងរយៈពេលពីមូលដ្ឋានទិន្នន័យ
            $leaveTypeModel = new Leavetype();
            $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);
            if (!$leaveType) {
                $_SESSION['error'] = [
                    'title' => "បញ្ហាប្រភេទច្បាប់",
                    'message' => "ប្រភេទច្បាប់មិនត្រឹមត្រូវ"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // គណនារយៈពេលក្នុងថ្ងៃធ្វើការ រវាងកាលបរិច្ឆេទចាប់ផ្តើម និងកាលបរិច្ឆេទបញ្ចប់
            $duration_days = $HeadDepartmentModel->calculateBusinessDays($datetime_start, $datetime_end);

            // ប្រៀបធៀបទៅនឹងរយៈពេលសំរាប់ប្រភេទច្បាប់ដែលបានជ្រើសរើស
            $leave_type_duration = $leaveType['duration'];
            if ($duration_days > $leave_type_duration) {
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "ប្រភេទច្បាប់ឈប់សម្រាកនេះមានរយៈពេល " . $leave_type_duration . " ថ្ងៃ។ សូមពិនិត្យមើលប្រភេទច្បាប់ដែលអ្នកបានជ្រើសរើសម្តងទៀត"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // បង្កើតសំណើច្បាប់ទៅមូលដ្ឋានទិន្នន័យ
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
                    'title' => "បញ្ហាសំណើច្បាប់",
                    'message' => "មិនអាចបង្កើតសំណើច្បាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                ];
                header("Location: /elms/hunitLeave");
                exit();
            }

            // កត់ត្រាសកម្មភាពអ្នកប្រើ
            $userModel->logUserActivity($user_id, $activity, $_SERVER['REMOTE_ADDR']);

            // កំណត់សារជោគជ័យ និងបញ្ជូនទៅទំព័រផ្សេងទៀត
            $_SESSION['success'] = [
                'title' => "ជោគជ័យ",
                'message' => "បង្កើតសំណើច្បាប់បានជោគជ័យ។"
            ];
            header("Location: /elms/hunitLeave");
            exit();
        } else {
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
            $requiredFields = ['request_id', 'status', 'remarks', 'uremarks', 'uname', 'uemail', 'leaveType', 'user_id', 'start_date', 'end_date', 'duration'];
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

            // Handle file upload for manager's signature
            $signaturePath = $Model->handleFileUpload($_FILES['manager_signature'], ['png'], 1048576, 'public/uploads/signatures/');
            if ($signaturePath === false) {
                $_SESSION['error'] = [
                    'title' => "ហត្ថលេខា",
                    'message' => "មិនអាចបញ្ចូលហត្ថលេខាបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header('location: /elms/dunit1pending');
                exit();
            }

            // Start transaction
            try {
                $this->pdo->beginTransaction();

                // Create approval record
                $leaveApproval = new DepUnit1Model();
                $updatedAt = $leaveApproval->submitApproval($request_id, $approver_id, $status, $remarks, $signaturePath);

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
                    'message' => "កំពុងបញ្ជូនទៅកាន់ " . $managerEmail
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
            $leaveRequestModel = new HeadUnitModel();
            $requests = $leaveRequestModel->getAllLeaveRequests();
            $leavetypeModel = new Leavetype();
            $leavetypes = $leavetypeModel->getAllLeavetypes();
            require 'src/views/leave/unit-h/pending.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new HeadUnitModel();
        $requests = $leaveRequestModel->getapproved($_SESSION['user_id']);

        require 'src/views/leave/unit-h/approved.php';
    }

    public function rejected()
    {
        $leaveRequestModel = new HeadUnitModel();
        $requests = $leaveRequestModel->getrejected($_SESSION['user_id']);

        require 'src/views/leave/unit-h/rejected.php';
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
