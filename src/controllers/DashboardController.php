<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/LateModel.php';

class DashboardController
{
    private $leaveRequestModel;

    public function __construct()
    {
        $this->leaveRequestModel = new LeaveRequest();
    }

    public function show()
    {
        $this->renderDashboard();
    }

    public function dashBoardLeave()
    {
        $this->renderDashboard();
    }

    public function getLeaveCountById()
    {
        $count = $this->leaveRequestModel->getLeaveCountById($_SESSION['user_id']);
        $this->renderDashboard(['count' => $count]);
    }

    public function viewUserApprovedByTeam()
    {
        $this->renderDashboard();
    }

    private function renderDashboard($data = [])
    {
        if (isset($_SESSION['role'])) {
            $role = $_SESSION['role'];

            // Get pagination parameters
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = 10; // Number of items per page
            $offset = ($page - 1) * $limit;

            // Fetch paginated leave requests and total count
            $leaves = $this->leaveRequestModel->getPaginatedRequestsByUserId($_SESSION['user_id'], $limit, $offset);
            $totalRequests = $this->leaveRequestModel->getTotalRequestsByUserId($_SESSION['user_id']);
            $totalPages = ceil($totalRequests / $limit);

            // Add fetched data to the $data array
            $data['leaves'] = $leaves;
            $data['page'] = $page;
            $data['totalPages'] = $totalPages;

            switch ($role) {
                case 'User':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $gettoday = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    require 'src/views/dashboard/user.php';
                    break;
                case 'Deputy Head Of Office':
                    $leaveRequestModel = new DepDepartLeave();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    require 'src/views/dashboard/office_manager.php';
                    break;
                case 'Head Of Department':
                    require 'src/views/dashboard/head_department.php';
                    break;
                case 'Head Of Office':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/head_office_manager.php';
                    break;
                case 'Deputy Head Of Department':
                    $leaveRequestModel = new DepDepartLeave();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/deputy_department.php';
                    break;
                case 'Deputy Of Unit 1':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/deputyunit_1.php';
                    break;
                case 'Admin':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/admin.php';
                    break;
                default:
                    header("Location: /elms/login");
                    exit();
            }
        } else {
            header("Location: /elms/login");
            exit();
        }
    }
}
