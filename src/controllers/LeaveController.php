<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/LeaveApproval.php';
require_once 'src/models/Leavetype.php';

class LeaveController
{
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $leave_type_id = $_POST['leave_type_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $remarks = $_POST['remarks'];

            // Handle file upload for attachment
            $attachment = $_FILES['attachment'];
            $attachment_name = $attachment['name'];
            $attachment_tmp_name = $attachment['tmp_name'];
            $attachment_error = $attachment['error'];
            $attachment_size = $attachment['size'];
            $allowed_attachment_extensions = ['docx', 'pdf'];

            if ($attachment_name) {
                $attachment_ext = strtolower(pathinfo($attachment_name, PATHINFO_EXTENSION));
                if (in_array($attachment_ext, $allowed_attachment_extensions) && $attachment_error === UPLOAD_ERR_OK) {
                    // Check file size (e.g., limit to 2MB)
                    if ($attachment_size <= 2097152) {
                        move_uploaded_file($attachment_tmp_name, 'public/uploads/leave_attachments/' . $attachment_name);
                    } else {
                        $_SESSION['error'] = [
                            'title' => "File Error",
                            'message' => "Attachment file size exceeds 2MB limit."
                        ];
                        header("Location: /elms/apply-leave");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = [
                        'title' => "File Error",
                        'message' => "Invalid attachment file type or upload error."
                    ];
                    header("Location: /elms/apply-leave");
                    exit();
                }
            }

            // Handle file upload for signature
            $signature = $_FILES['signature'];
            $signature_name = $signature['name'];
            $signature_tmp_name = $signature['tmp_name'];
            $signature_error = $signature['error'];
            $signature_size = $signature['size'];
            $allowed_signature_extensions = ['png'];

            if ($signature_name) {
                $signature_ext = strtolower(pathinfo($signature_name, PATHINFO_EXTENSION));
                if (in_array($signature_ext, $allowed_signature_extensions) && $signature_error === UPLOAD_ERR_OK) {
                    // Check file size (e.g., limit to 1MB)
                    if ($signature_size <= 1048576) {
                        move_uploaded_file($signature_tmp_name, 'public/uploads/signatures/' . $signature_name);
                    } else {
                        $_SESSION['error'] = [
                            'title' => "File Error",
                            'message' => "Signature file size exceeds 1MB limit."
                        ];
                        header("Location: /elms/apply-leave");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = [
                        'title' => "File Error",
                        'message' => "Invalid signature file type or upload error."
                    ];
                    header("Location: /elms/apply-leave");
                    exit();
                }
            }

            // Fetch leave type details including duration from database
            $leaveTypeModel = new Leavetype();
            $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id);

            if (!$leaveType) {
                // Handle error if leave type id does not exist
                $_SESSION['error'] = [
                    'title' => "Leave Type Error",
                    'message' => "Invalid leave type selected."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            $leave_type_duration = $leaveType['duration'];

            // Calculate duration in business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $this->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare duration_days with leave_type_duration
            if ($duration_days > $leave_type_duration) {
                // Handle error if duration exceeds leave type duration
                $_SESSION['error'] = [
                    'title' => "Duration Error",
                    'message' => "You cannot request leave exceeding the type's duration of " . $leave_type_duration . " days."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Create leave request
            $leaveRequestModel = new LeaveRequest();
            $leaveRequestModel->create($user_id, $leave_type_id, $leaveType['name'], $start_date, $end_date, $remarks, $duration_days, $attachment_name, $signature_name);

            // Notify manager (Implement notification logic here)

            $_SESSION['success'] = [
                'title' => "ច្បាប់ឈប់សម្រាក",
                'message' => "សំណើត្រូវបានបង្កើតដោយជោគជ័យ។"
            ];
            header("Location: /elms/leave-requests");
            exit();
        } else {
            require 'src/views/leave/apply.php';
        }
    }

    private function calculateBusinessDays(DateTime $start_date, DateTime $end_date)
    {
        $business_days = 0;
        $current_date = clone $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = $current_date->format('N');
            if ($day_of_week < 6) { // Monday to Friday are business days
                $business_days++;
            }
            $current_date->modify('+1 day');
        }

        return $business_days;
    }

    public function viewRequests()
    {
        $leaveRequestModel = new LeaveRequest();
        $requests = $leaveRequestModel->getRequestsByUserId($_SESSION['user_id']);

        require 'src/views/leave/requests.php';
    }

    public function viewDetail()
    {
        if (isset($_GET['leave_id'])) {
            $leaveRequestModel = new LeaveRequest();
            $leave_id = (int)$_GET['leave_id'];
            $request = $leaveRequestModel->getRequestById($leave_id);

            if ($request) {
                require 'src/views/leave/viewleave.php';
                return;
            }
        }
        // If request not found or leave_id is not provided, redirect or show error
        header('Location: /elms/requests');
        exit();
    }

    public function approve()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $request_id = $_POST['request_id'];
            $status = $_POST['status'];
            $remarks = $_POST['remarks'];
            $approver_id = $_SESSION['user_id'];

            $leaveRequestModel = new LeaveApproval();
            $leaveRequestModel->submitApproval($request_id, $approver_id, $status, $remarks);

            // Notify employee
            // Here you would implement the notification logic
            $_SESSION['success'] = [
                'title' => "សំណើច្បាប់",
                'message' => "សំណើច្បាប់ត្រូវបាន " . $status
            ];
            header('location: /elms/pending');
        } else {
            $leaveRequestModel = new LeaveApproval();
            $requests = $leaveRequestModel->getPendingRequestsForApprover($_SESSION['user_id']);

            require 'src/views/leave/approvals.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new LeaveApproval();
        $requests = $leaveRequestModel->getdhapproved($_SESSION['user_id']);

        require 'src/views/leave/approved.php';
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
        header("Location: /elms/dashboard");
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
}
