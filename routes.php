<?php
// Check if a session is active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}
// Define your base URL
$base_url = '/elms'; // Set your base URL if your application is located under a subdirectory

// Include necessary controllers
require_once 'src/controllers/auth/AuthController.php';
require_once 'src/controllers/users/LeaveController.php';
require_once 'src/controllers/NotificationController.php';
require_once 'src/controllers/DepartmentController.php';
require_once 'src/controllers/OfficeController.php';
require_once 'src/controllers/RoleController.php';
require_once 'src/controllers/PositionController.php';
require_once 'src/controllers/UserController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/offices-h/HeadOfficeController.php';
require_once 'src/controllers/departments-d/DepDepartmentController.php';
require_once 'src/controllers/departments-h/HeadDepartmentController.php';
require_once 'src/controllers/unit1-d/DepUnit1Controller.php';
require_once 'src/controllers/unit2-d/DepUnit2Controller.php';
require_once 'src/controllers/unit-h/HeadUnitController.php';
require_once 'src/controllers/lates/LateController.php';
require_once 'src/controllers/SettingController.php';
require_once 'src/controllers/missions/MissionController.php';
require_once 'src/controllers/offices-d/DepOfficeController.php';
require_once 'src/controllers/admin/AdminController.php';

$uri = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

switch ($uri) {
    case $base_url . '/':
    case $base_url . '/login':
        $controller = new AuthController();
        $controller->login();
        break;
    case $base_url . '/logout':
        // Clear session data
        session_unset(); // Unset all session variables

        // Destroy the session
        session_destroy();

        // Optionally, you might want to explicitly remove the session cookie
        if (ini_get(option: "session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        sleep(seconds: 1);
        header(header: "Location: $base_url/login");
        exit();
    case $base_url . '/apply-leave':
        checkSessionAndExecute(callback: function (): void {
            $controller = new LeaveController();
            $controller->apply();
        });
        break;

    case $base_url . '/dof-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->apply();
        });
        break;
    case $base_url . '/hof-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->apply();
        });
        break;
    case $base_url . '/ddep-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new DepDepartmentController();
            $controller->apply();
        });
        break;
    case $base_url . '/hod-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartmentController();
            $controller->apply();
        });
        break;
    case $base_url . '/du1-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->apply();
        });
        break;
    case $base_url . '/du2-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new DepUnit2Controller();
            $controller->apply();
        });
        break;
    case $base_url . '/hunit-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new HeadUnitController();
            $controller->apply();
        });
        break;
    case $base_url . '/my-leaves':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->myLeaves();
            }
        });
        break;
    case $base_url . '/dofficeLeave':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/hofficeLeave':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/ddepartmentLeave':
        checkSessionAndExecute(function () {
            $controller = new DepDepartmentController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/hdepartmentLeave':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartmentController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/dunit1Leave':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/dunit2Leave':
        checkSessionAndExecute(function () {
            $controller = new DepUnit2Controller();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/hunitLeave':
        checkSessionAndExecute(function () {
            $controller = new HeadUnitController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/adminLeave':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewRequestsWithFilters();
            } else {
                $controller->viewRequests();
            }
        });
        break;
    case $base_url . '/admin-apply-leave':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->apply();
        });
        break;
    case $base_url . '/adminpending':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            // Check if action is set in the query parameters
            $action = $_GET['action'] ?? 'latein'; // Default to 'latein' if no action is set

            switch ($action) {
                case 'latein':
                    $controller->getPendingLate();
                    break;
                case 'lateout':
                    $controller->getPendingLateOut();
                    break;
                case 'leaveearly':
                    $controller->getPendingLeaveEarly();
                    break;
                case 'allLate':
                    $controller->getAllLate();
                    break;
                default:
                    $controller->getPendingLate(); // Default case
            }
        });
        break;
    case $base_url . '/adminapprovelate':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->approved();
        });
        break;
    case $base_url . '/pending':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->pending();
        });
        break;

    case $base_url . '/headofficepending':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->pending();
        });
        break;
    case $base_url . '/headofficeapproval':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->approved();
        });
        break;
    case $base_url . '/headofficerejected':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->rejected();
        });
        break;
    case $base_url . '/depdepartmentpending':
        checkSessionAndExecute(function () {
            $controller = new DepDepartmentController();
            $controller->pending();
        });
        break;
    case $base_url . '/depdepartmentapproved':
        checkSessionAndExecute(function () {
            $controller = new DepDepartmentController();
            $controller->approved();
        });
        break;
    case $base_url . '/depdepartmentrejected':
        checkSessionAndExecute(function () {
            $controller = new DepDepartmentController();
            $controller->rejected();
        });
        break;
    case $base_url . '/headdepartmentpending':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartmentController();
            $controller->approve();
        });
        break;
    case $base_url . '/headdepartmentapproved':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartmentController();
            $controller->approved();
        });
        break;
    case $base_url . '/headdepartmentrejected':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartmentController();
            $controller->rejected();
        });
        break;
    case $base_url . '/hunitpending':
        checkSessionAndExecute(function () {
            $controller = new HeadUnitController();
            $controller->pending();
        });
        break;
    case $base_url . '/hunitapproved':
        checkSessionAndExecute(function () {
            $controller = new HeadUnitController();
            $controller->approved();
        });
        break;
    case $base_url . '/hunitrejected':
        checkSessionAndExecute(function () {
            $controller = new HeadUnitController();
            $controller->rejected();
        });
        break;
    case $base_url . '/dunit1pending':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->pending();
        });
        break;
    case $base_url . '/dunit1approved':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->approved();
        });
        break;
    case $base_url . '/dunit1rejected':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->rejected();
        });
        break;

    case $base_url . '/dunit2pending':
        checkSessionAndExecute(function () {
            $controller = new DepUnit2Controller();
            $controller->pending();
        });
        break;
    case $base_url . '/dunit2approved':
        checkSessionAndExecute(function () {
            $controller = new DepUnit2Controller();
            $controller->approved();
        });
        break;
    case $base_url . '/dunit2rejected':
        checkSessionAndExecute(function () {
            $controller = new DepUnit2Controller();
            $controller->rejected();
        });
        break;

    case $base_url . '/approved':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->approved();
        });
        break;
    case $base_url . '/rejected':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->rejected();
        });
        break;
    case $base_url . '/leave-calendar':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->viewCalendar();
        });
        break;
    case $base_url . '/view-leave-detail':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->viewDetail();
        });
        break;
    case $base_url . '/view-leave-detail-d':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->viewLeaveDetail();
        });
        break;
    case $base_url . '/view-leave-detail-h':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->viewLeaveDetail();
        });
        break;
    case $base_url . '/hoffice-view-leave':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeController();
            $controller->viewDetail();
        });
        break;
    case $base_url . '/view-leave':
        checkSessionAndExecute(function () {
            $controller = new DepOfficeController();
            $controller->viewDetail();
        });
        break;
    case '/elms/uploadAttachment':
        $controller = new LeaveController();
        $controller->uploadAttachment();
        break;
    case $base_url . '/leave-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LeaveController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
    case $base_url . '/hoffice-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new HeadOfficeController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/doffice-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new DepOfficeController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/hdepart-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new HeadDepartmentController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/dunit1-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new DepUnit1Controller();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/dunit2-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new DepUnit2Controller();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/admin-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new AdminController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/my-account':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->index();
        });
        break;
    case $base_url . '/view_detail':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->viewDetail();
        });
        break;
    case $base_url . '/update-email':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->updateEmail();
        });
        break;
    case $base_url . '/update-password':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->updatePassword();
        });
        break;
    case $base_url . '/edit_user_detail':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->editUserDetail();
        });
        break;
    case $base_url . '/setting_security':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->security();
        });
        break;
    case $base_url . '/activity':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->activity();
        });
        break;
    case $base_url . '/markasread':
        checkSessionAndExecute(function () {
            $controller = new NotificationController();
            $controller->markasread();
        });
        break;
    case $base_url . '/notifyAlls':
        checkSessionAndExecute(function () {
            $controller = new NotificationController();
            $controller->viewAllNotify();
        });
        break;
    case $base_url . '/change-profile-picture':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->updateProfilePicture();
        });
        break;
    case $base_url . '/reset-profile-picture':
        checkSessionAndExecute(function () {
            $controller = new SettingController();
            $controller->resetProfilePicture();
        });
        break;
    case $base_url . '/notifications':
        checkSessionAndExecute(function () {
            $controller = new NotificationController();
            $controller->index();
        });
        break;
    case $base_url . '/notificationDetail':
        checkSessionAndExecute(function () {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $controller = new NotificationController();
                $controller->viewDetail($id);
            } else {
                // Handle the case where 'id' is not present in the URL
                echo "Notification ID is missing.";
            }
        });
        break;
    case $base_url . '/mission':
        checkSessionAndExecute(function () {
            $controller = new MissionController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewMissionWithFilters();
            } else {
                $controller->index();
            }
        });
        break;
    case $base_url . '/apply-mission':
        checkSessionAndExecute(function () {
            $controller = new MissionController();
            $controller->create();
        });
        break;
    case $base_url . '/update-mission':
        checkSessionAndExecute(function () {
            $controller = new MissionController();
            $controller->update($_POST['mission_id']);
        });
        break;
    case $base_url . '/delete-mission':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new MissionController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/dashboard':
        checkSessionAndExecute(function () {
            $controller = new DashboardController();
            $controller->show();
        });
        break;
    case $base_url . '/roles':
        checkSessionAndExecute(function () {
            $controller = new RoleController();
            $controller->index();
            require 'src/views/roles/roles.php';
        });
        break;
    case $base_url . '/documents':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            $controller->index();
        });
        break;
    case $base_url . '/actionLate':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->ActionLate();
        });
        break;
    case $base_url . '/actionLateOut':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->ActionLateOut();
        });
        break;
    case $base_url . '/actionLeaveEarly':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->ActionLeaveEarly();
        });
        break;
    case $base_url . '/late_in_request':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            $controller->requestLateIn();
        });
        break;
    case $base_url . '/late_out_request':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            $controller->requestLateOut();
        });
        break;
    case $base_url . '/late-in-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LateController();
                $controller->deleteLateIn($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/late-out-delete':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LateController();
                $controller->deleteLateOut($_POST['id']);
            } else {
                header(header: "Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/leaveearly-delete':
        checkSessionAndExecute(callback: function (): void {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LateController();
                $controller->deleteLeaveEarly(id: $_POST['id']);
            } else {
                header(header: "Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/viewLateDetail':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->viewLateDetail();
        });
        break;
    case $base_url . '/viewLateDetailLateOut':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->viewLateDetailLateOut();
        });
        break;
    case $base_url . '/viewLateDetailLeaveEarly':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->viewLateDetailLeaveEarly();
        });
        break;
    case $base_url . '/viewLateDetailAllLate':
        checkSessionAndExecute(function () {
            $controller = new AdminController();
            $controller->viewLateDetailAllLate();
        });
        break;
    case $base_url . '/overtimein':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewOvertimeInWithFilters();
            } else {
                $controller->overtimein();
            }
        });
        break;
    case $base_url . '/overtimeout':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewoverTimeOutWithFilters();
            } else {
                $controller->overtimeout();
            }
        });
        break;
    case $base_url . '/leaveearly':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->viewLeaveEarlyWithFilters();
            } else {
                $controller->leaveearly();
            }
        });
        break;
    case $base_url . '/create_late':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new LateController();
                $controller->store($_POST['name'], $_POST['color']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/apply_latein':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new LateController();
                $controller->createLateIn($_POST['date'], $_POST['time'], $_POST['reason']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/apply_lateout':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new LateController();
                $controller->createLateOut($_POST['date'], $_POST['time'], $_POST['reason']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/apply_leaveearly':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new LateController();
                $controller->createLeaveEarly($_POST['date'], $_POST['time'], $_POST['reason']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/update_late':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new LateController();
                $controller->update($_POST['id'], $_POST['name'], $_POST['color']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/delete-late-in':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LateController();
                $controller->deleteLateIn($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/user_index':
        checkSessionAndExecute(function () {
            $userController = new UserController();
            $userController->index();
        });
        break;
    case $base_url . '/create_user':
        checkSessionAndExecute(function () {
            $userController = new UserController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/elms/create_user') {
                $userController->create();
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/edit_user':
        checkSessionAndExecute(function () {
            $userController = new UserController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $userController->update();
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/create_role':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new RoleController();
                $controller->store($_POST['name'], $_POST['description']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/edit_role':
        checkSessionAndExecute(function () {
            if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
                $controller = new RoleController();
                $controller->edit($_GET['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/update_role':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new RoleController();
                $controller->update($_POST['id'], $_POST['name'], $_POST['description']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/delete_role':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new RoleController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/delete_user':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['user_id'])) {
                $controller = new UserController();
                $controller->delete($_POST['user_id']);
            } else {
                header("Location: /elms/user_index");
                exit();
            }
        });
        break;
    case $base_url . '/positions':
        checkSessionAndExecute(function () {
            $controller = new PositionController();
            $controller->index();
            require 'src/views/positions/positions.php';
        });
        break;
    case $base_url . '/create_position':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new PositionController();
                $controller->store($_POST['name'], $_POST['color']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/edit_position':
        checkSessionAndExecute(function () {
            if (isset($_SESSION['user_id']) && isset($_GET['id'])) {
                $controller = new PositionController();
                $controller->edit($_GET['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/update_position':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new PositionController();
                $controller->update($_POST['id'], $_POST['name'], $_POST['color']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/delete_position':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new PositionController();
                $controller->delete($_POST['id']);
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/department':
        checkSessionAndExecute(function () {
            require 'src/views/department/department.php';
        });
        break;
    case $base_url . '/create_department':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new DepartmentController();
                $controller->store();
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/office':
        checkSessionAndExecute(function () {
            $userController = new UserController();
            $userController->index();
            require 'src/views/office/office.php';
        });
        break;
    case $base_url . '/create_office':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                $controller = new OfficeController();
                $controller->store();
            } else {
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/leavetype':
        checkSessionAndExecute(function () {
            require 'src/views/leave/leavetype.php';
        });
        break;
    case $base_url . '/create_leavetype':
        checkSessionAndExecute(function () {
            require 'src/action/create_leavetype.php';
        });
        break;
    case $base_url . '/settings':
        checkSessionAndExecute(function () {
            require 'src/views/settings/settings.php';
        });
        break;
    case $base_url . '/error_page':
        checkSessionAndExecute(function () {
            require 'src/views/errors/error_page.php';
        });
        break;
    default:
        // Redirect to 404 page for non-existent routes
        header("HTTP/1.0 404 Not Found");
        require 'src/views/errors/404.php';
        exit();
}

function checkSessionAndExecute($callback)
{
    global $base_url;
    if (isset($_SESSION['user_id'])) {
        $callback();
    } else {
        header("Location: $base_url/login");
        exit();
    }
}

function checkRoleAndExecute($roleViews)
{
    global $base_url;
    if (isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
        if (isset($roleViews[$role])) {
            require $roleViews[$role];
        } else {
            header("Location: $base_url/login");
            exit();
        }
    } else {
        header("Location: $base_url/login");
        exit();
    }
}
