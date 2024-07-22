<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/DepUnit1Leave.php';
require_once 'src/models/Leavetype.php';

class DepUnit1Controller
{
    public function apply()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_SESSION['user_id'];
            $leave_type_id = $_POST['leave_type_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $remarks = $_POST['remarks'];
            $attachment = $_FILES['attachment']['name'];

            // Handle file upload
            if ($attachment) {
                move_uploaded_file($_FILES['attachment']['tmp_name'], 'public/uploads/leave_attachments/' . $attachment);
            }

            // Fetch leave type details including duration from database
            $leaveTypeModel = new Leavetype();
            $leaveType = $leaveTypeModel->getLeaveTypeById($leave_type_id); // Adjust this method according to your implementation

            if (!$leaveType) {
                // Handle error if leave type id does not exist
                $_SESSION['error'] = [
                    'title' => "Leave Type Error",
                    'message' => "Invalid leave type selected."
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            $leave_type_duration = $leaveType['duration']; // Assuming 'duration' is the column name in your leave_types table

            // Calculate duration in business days between start_date and end_date
            $datetime_start = new DateTime($start_date);
            $datetime_end = new DateTime($end_date);
            $duration_days = $this->calculateBusinessDays($datetime_start, $datetime_end);

            // Compare duration_days with leave_type_duration
            if ($duration_days > $leave_type_duration) {
                // Handle error if duration exceeds leave type duration
                $_SESSION['error'] = [
                    'title' => "រយៈពេល",
                    'message' => "អ្នកមិនអាចស្នើច្បាប់ឈប់សម្រាកបានទេ។ ប្រភេទច្បាប់នេះមានរៈពេល " . $leave_type_duration . "ថ្ងៃ"
                ];
                header("Location: /elms/apply-leave");
                exit();
            }

            // Create leave request
            $leaveRequestModel = new LeaveRequest();
            $leaveRequestModel->create($user_id, $leave_type_id, $leaveType['name'], $start_date, $end_date, $remarks, $duration_days, $attachment);

            // Notify manager
            // Here you would implement the notification logic

            $_SESSION['success'] = [
                'title' => "ច្បាប់ឈប់សម្រាក",
                'message' => "សំណើត្រូវបានបង្កើតដោយជោគជ័យ។"
            ];
            header("Location: /elms/apply-leave");
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

            $leaveRequestModel = new DepUnit1Leave();
            $leaveRequestModel->submitApproval($request_id, $approver_id, $status, $remarks);

            // Notify employee
            // Here you would implement the notification logic
            $_SESSION['success'] = [
                'title' => "សំណើច្បាប់",
                'message' => "សំណើច្បាប់ត្រូវបាន " . $status
            ];
            header('location: /elms/depunit1pending');
        } else {
            $leaveRequestModel = new DepUnit1Leave();
            $requests = $leaveRequestModel->getPendingRequestsForApprover($_SESSION['user_id']);

            require 'src/views/leave/depunit1approval.php';
        }
    }

    public function approved()
    {
        $leaveRequestModel = new DepUnit1Leave();
        $requests = $leaveRequestModel->gethapproved($_SESSION['user_id']);

        require 'src/views/leave/depunit1approved.php';
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
