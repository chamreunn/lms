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
                    // $leaveRequestModel = new LeaveApproval();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $notification = new Notification();
                    $leavetypes = new Leavetype();
                    $missionCount = new MissionModel();
                    $userModel = new User();
                    $userController = new AdminModel();
                    $userDetails = $userController->getUserByIdAPI($_SESSION['user_id'], $_SESSION['token']);
                    $getAllManagers = $userModel->getEmailLeaderHUApi($_SESSION['user_id'], $_SESSION['token']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leaves = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetype = $leavetypes->getLeaveTypeById($_SESSION['user_id']);
                    $leavetypes = $leavetypes->getAllLeavetypes();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $currentDate = date('Y-m-d');
                    $getQRcode = $userModel->getQRcodeByUserId($_SESSION['user_id']);
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/users/dashboard.php';
                    break;
                case 'Deputy Head Of Office':
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $notification = new Notification();
                    $leavetypeModel = new Leavetype();
                    $getAllMission = new MissionModel();
                    $leaveRequestModel = new DepDepartmentModel();
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $getQRcode = $userModel->getQRcodeByUserId($_SESSION['user_id']);
                    $leaves = $leaveRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $getUserApprove = $leaveRequestModel->leaveUserApproved($_SESSION['token']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $getMissionToday = $getAllMission->missionsToday($_SESSION['user_id'], $_SESSION['token']);
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);
                    // Load the LeaveApproval model to get leave request counts
                    $leaveRequestModel = new LeaveApproval();
                    $pendingRequestsCount = $leaveRequestModel->countPendingRequestsForApprover();

                    // Load the HoldModel to get count of pending hold requests
                    $holdModel = new HoldModel();
                    $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                    // Total pending count combining leave requests and holds
                    $totalPendingCount = $pendingRequestsCount + $pendingHoldsCount;
                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/offices-d/dashboard.php';
                    break;
                case 'Head Of Office':
                    $leaveRequestModel = new HeadOfficeModel();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $notification = new Notification();
                    $leavetypeModel = new Leavetype();
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $leaves = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $getUserApprove = $leaveRequestModel->leaveUserApproved($_SESSION['token']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    $currentDate = date('Y-m-d');
                    $getQRcode = $userModel->getQRcodeByUserId($_SESSION['user_id']);
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/offices-h/dashboard.php';
                    break;
                case 'Deputy Head Of Department':
                    $leaveRequestModel = new DepDepartmentModel();
                    $lateModel = new LateModel();
                    $notification = new Notification();
                    $leavetypeModel = new Leavetype();
                    $countRequestModel = new LeaveRequest();
                    $getUserApprove = $countRequestModel->leaveUserApprovedByDepartment($_SESSION['token']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/departments-d/dashboard.php';
                    break;
                case 'Head Of Department':
                    $leaveRequestModel = new HeadDepartmentModel();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $getUserApprove = $countRequestModel->leaveUserApprovedByDepartment($_SESSION['token']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $countRequestModel = new LeaveRequest();
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $notification = new Notification();
                    $getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
                    $leavetypeModel = new Leavetype();
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $depdepart = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/departments-h/dashboard.php';
                    break;
                case 'Deputy Head Of Unit 1':
                    $leaveRequestModel = new DepUnit1Model();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $leavetypeModel = new Leavetype();
                    $leaves = $countRequestModel->getTodayLeaveById($_SESSION['user_id']);
                    $getUserApprove = $leaveRequestModel->leaveUserApproved($_SESSION['token']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
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
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/unit2-d/dashboard.php';
                    break;
                case 'Head Of Unit':
                    $leaveRequestModel = new HeadUnitModel();
                    $lateModel = new LateModel();
                    $countRequestModel = new LeaveRequest();
                    $leavetypeModel = new Leavetype();
                    $getUserApprove = $leaveRequestModel->leaveUserApproved($_SESSION['token']);
                    $getMissionToday = $leaveRequestModel->getMissions($_SESSION['token']);
                    $getLateIn = $leaveRequestModel->getLateIn($_SESSION['token']);
                    $getLateOut = $leaveRequestModel->getLateOut($_SESSION['token']);
                    $getLeaveEarly = $leaveRequestModel->getLeaveEarly($_SESSION['token']);
                    $getuserapproves = $leaveRequestModel->getUserApproveByTeam($_SESSION['user_id']);
                    $getovertimeincounts = $lateModel->getOvertimeinCount($_SESSION['user_id']);
                    $getovertimeoutcounts = $lateModel->getOvertimeoutCount($_SESSION['user_id']);
                    $getcountrequestbyid = $countRequestModel->countRequestsByUserId($_SESSION['user_id']);
                    $missionCount = new MissionModel();
                    $getMissionCount = $missionCount->missionCount($_SESSION['user_id']);
                    $leavetypes = $leavetypeModel->getAllLeavetypes();
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/unit-h/dashboard.php';
                    break;
                case 'Admin':
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
                    $userModel = new User();
                    // Get approver based on role and department
                    $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
                    $offices = $userModel->getAllOfficeApi($_SESSION['token']);

                    // Check for errors in the departments API response
                    if ($departments['http_code'] !== 200) {
                        $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
                        $departments['data'] = []; // Fallback to empty data if there's an error
                    }

                    // Check for errors in the offices API response
                    if ($offices['http_code'] !== 200) {
                        $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
                        $offices['data'] = []; // Fallback to empty data if there's an error
                    }
                    require 'src/views/dashboard/admin/dashboard.php';
                    break;
                case 'superadmin':
                    $superAdminModel = new AdminModel();
                    $allUserDetails = $superAdminModel->getAllUsersFromAPI();
                    $userDetails = $superAdminModel->getUserByIdAPI($_SESSION['user_id'], $_SESSION['token']);

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
                    $userModel = new User();
                    $currentDate = date('Y-m-d');
                    $todayAttendance = $userModel->todayAttendanceByUseridApi($_SESSION['user_id'], $currentDate, $_SESSION['token'], );
                    require 'src/views/dashboard/s-admin/dashboard.php';
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
