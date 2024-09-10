<?php
require_once 'src/models/LeaveRequest.php';
require_once 'src/models/lates/LateModel.php';
require_once 'src/models/Notification.php';
require_once 'src/models/unit2-d/DepUnit2Model.php';
require_once 'src/models/unit1-d/DepUnit1Model.php';
require_once 'src/models/unit-h/HeadUnitModel.php';

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
            $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $limit = 10; // Number of items per page
            $offset = ($page - 1) * $limit;

            // Fetch paginated leave requests and total count
            $leaves = $this->leaveRequestModel->getLeaveByUser($_SESSION['user_id']);
            $totalRequests = $this->leaveRequestModel->getTotalRequestsByUserId($_SESSION['user_id']);
            $totalPages = ceil($totalRequests / $limit);

            // Add fetched data to the $data array
            $data['leaves'] = $leaves;
            $data['page'] = $page;
            $data['totalPages'] = $totalPages;

            switch ($role) {
                case 'NULL':
                    $leaveRequestModel = new LeaveApproval();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $notification = new Notification();
                    $leavetypes = new Leavetype();
                    $missionCount = new MissionModel();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $gettoday = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetype = $leavetypes->getLeaveTypeById($_SESSION['user_id']);
                    $leavetypes = $leavetypes->getAllLeavetypes();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    require 'src/views/dashboard/users/dashboard.php';
                    break;
                case 'Deputy Head Of Office':
                    $leaveRequestModel = new DepDepartmentModel();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $notification = new Notification();
                    $leavetypeModel = new Leavetype();
                    $getAllMission = new MissionModel();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $getUserApprove = $countRequestModel->leaveUserApproved($_SESSION['token']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $getMissionCount = $getAllMission->missionCount($_SESSION['user_id']);
                    $getMissionToday = $getAllMission->missionsToday($_SESSION['user_id'], $_SESSION['token']);
                    require 'src/views/dashboard/offices-d/dashboard.php';
                    break;
                case 'Head Of Office':
                    $leaveRequestModel = new HeadOfficeModel();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $getUserApprove = $countRequestModel->leaveUserApproved($_SESSION['token']);
                    $notification = new Notification();
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypeModel = new Leavetype();
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    require 'src/views/dashboard/offices-h/dashboard.php';
                    break;
                case 'Deputy Head Of Department':
                    $leaveRequestModel = new DepDepartmentModel();
                    $lateModel = new LateModel();
                    $notification = new Notification();
                    $leavetypeModel = new Leavetype();
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    require 'src/views/dashboard/departments-d/dashboard.php';
                    break;
                case 'Head Of Department':
                    $leaveRequestModel = new HeadDepartmentModel();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $notification = new Notification();
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypeModel = new Leavetype();
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    require 'src/views/dashboard/departments-h/dashboard.php';
                    break;
                case 'Deputy Head Of Unit 1':
                    $leaveRequestModel = new DepUnit1Model();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $leavetypeModel = new Leavetype();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    require 'src/views/dashboard/unit1-d/dashboard.php';
                    break;
                case 'Deputy Head Of Unit 2':
                    $leaveRequestModel = new DepUnit2Model();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $leavetypeModel = new Leavetype();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    require 'src/views/dashboard/unit2-d/dashboard.php';
                    break;
                case 'Head Of Unit':
                    $leaveRequestModel = new HeadUnitModel();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $leavetypeModel = new Leavetype();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    require 'src/views/dashboard/unit-h/dashboard.php';
                    break;
                case 'Admin':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $gettoday = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $notification = new Notification();
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypes = new Leavetype();
                    $leavetype = $leavetypes->getLeaveTypeById($_SESSION['user_id']);
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/admin/dashboard.php';
                    break;
                case 'Super Admin':
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $lateModel = new LateModel();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $gettoday = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $notification = new Notification();
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypes = new Leavetype();
                    $leavetype = $leavetypes->getLeaveTypeById($_SESSION['user_id']);
                    $leaveRequestModel = new LeaveApproval();
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    require 'src/views/dashboard/superAdmin/dashboard.php';
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
