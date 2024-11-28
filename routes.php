<?php
// Check if a session is active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

// Define your base URL
$base_url = '/elms'; // Set your base URL if your application is located under a subdirectory

// Include necessary controllers
$controllers = [
    'src/controllers/auth/AuthController.php',
    'src/controllers/users/LeaveController.php',
    'src/controllers/NotificationController.php',
    'src/controllers/DepartmentController.php',
    'src/controllers/OfficeController.php',
    'src/controllers/RoleController.php',
    'src/controllers/PositionController.php',
    'src/controllers/UserController.php',
    'src/controllers/DashboardController.php',
    'src/controllers/offices-h/HeadOfficeController.php',
    'src/controllers/departments-d/DepDepartmentController.php',
    'src/controllers/departments-h/HeadDepartmentController.php',
    'src/controllers/unit1-d/DepUnit1Controller.php',
    'src/controllers/unit2-d/DepUnit2Controller.php',
    'src/controllers/unit-h/HeadUnitController.php',
    'src/controllers/lates/LateController.php',
    'src/controllers/SettingController.php',
    'src/controllers/missions/MissionController.php',
    'src/controllers/offices-d/DepOfficeController.php',
    'src/controllers/admin/AdminController.php',
    'src/controllers/calendar/CalendarController.php',
    'src/controllers/telegram/TelegramController.php',
    'src/controllers/hold/HoldController.php',
    'src/controllers/transferout/TransferoutController.php',
    'src/controllers/resign/ResignController.php',
    'src/controllers/backwork/BackworkController.php',
    'src/controllers/attendance/AttendanceController.php',
    'src/controllers/qrcode/QrcodeController.php',
];

// Require all controllers
foreach ($controllers as $controller) {
    require_once $controller;
}

asyncHandler(function () {
    global $base_url;

    $uri = parse_url(url: $_SERVER['REQUEST_URI'], component: PHP_URL_PATH);

    switch ($uri) {
        case $base_url . '/':
        case $base_url . '/login':
            (new AuthController())->login();
            break;

        case $base_url . '/logout':
            // Handle logout: clear session and redirect to login
            session_unset();
            session_destroy();

            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
            }

            header("Location: $base_url/login");
            exit();

        case $base_url . '/apply-leave':
            checkSessionAndExecute(function () {
                $controller = new LeaveController();
                $controller->apply();
            });
            break;
        case $base_url . '/telegramConnect':
            checkSessionAndExecute(function () {
                $controller = new TelegramController();
                $controller->telegramAuth($_SESSION['BotUsername']);
            });
            break;
        case $base_url . '/telegramDisconnect':
            checkSessionAndExecute(function () {
                $controller = new TelegramController();
                $controller->telegramDisconnect();
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
        case $base_url . '/my-attendances':
            checkSessionAndExecute(function () {
                $controller = new LeaveController();
                $controller->displayAttendances();
            });
            break;
        case $base_url . '/all-attendances':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->displayAttendances();
            });
            break;
        case $base_url . '/admin-attendances':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->displayAllAttendances();
            });
            break;
        case $base_url . '/filter-attendances':
            checkSessionAndExecute(function () {
                $controller = new LeaveController();
                $controller->filterAttendence();
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
        case $base_url . '/hunit-delete':
            checkSessionAndExecute(function () {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                    $controller = new HeadUnitController();
                    $controller->delete($_POST['id']);
                } else {
                    header("Location: /elms/login");
                    exit();
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
        case $base_url . '/adminleaves':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->getLeaveFilter();
                } else {
                    $controller->getAllLeaves();
                }
            });
            break;
        case $base_url . '/adminleavetoday':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->getLeaveFilter();
                } else {
                    $controller->getAllLeaveToday();
                }
            });
            break;
        case $base_url . '/adminmissions':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->getMissionTodayFilter();
                } else {
                    $controller->getAllMissions();
                }
            });
            break;
        case $base_url . '/admintodaymissions':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->getMissionFilter();
                } else {
                    $controller->getAllMissionTodays();
                }
            });
            break;
        case $base_url . '/adminpending':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->getPendingLate();
            });
            break;
        case $base_url . '/adminapproved':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $controller->getApprovedLateFilter();
                } else {
                    $controller->getApprovedLate();
                }
            });
            break;
        case $base_url . '/admintodaylate':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->getTodayLate();
            });
            break;
        case $base_url . '/headunittodaylate':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->getTodayLate();
            });
            break;
        case $base_url . '/adminrejected':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->getRejectedLate();
            });
            break;
        case $base_url . '/adminApprovedLeave':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->viewAllLeave();
            });
            break;
        case $base_url . '/AllLeavesAdmin':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->AllLeaves();
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
        case $base_url . '/actionhold':
            checkSessionAndExecute(function () {
                $controller = new DepOfficeController();
                $controller->action();
            });
            break;
        case $base_url . '/actiontransferout':
            checkSessionAndExecute(function () {
                $controller = new DepOfficeController();
                $controller->actiontransferout();
            });
            break;
        case $base_url . '/actionback':
            checkSessionAndExecute(function () {
                $controller = new DepOfficeController();
                $controller->actionback();
            });
            break;
        case $base_url . '/hoactionhold':
            checkSessionAndExecute(function () {
                $controller = new HeadOfficeController();
                $controller->action();
            });
            break;
        case $base_url . '/hoactiontransferout':
            checkSessionAndExecute(function () {
                $controller = new HeadOfficeController();
                $controller->actionTransferout();
            });
            break;
        case $base_url . '/hoactionback':
            checkSessionAndExecute(function () {
                $controller = new HeadOfficeController();
                $controller->actionback();
            });
            break;
        case $base_url . '/ddactionhold':
            checkSessionAndExecute(function () {
                $controller = new DepDepartmentController();
                $controller->action();
            });
            break;
        case $base_url . '/ddactiontransferout':
            checkSessionAndExecute(function () {
                $controller = new DepDepartmentController();
                $controller->actionTransferout();
            });
            break;
        case $base_url . '/ddactionback':
            checkSessionAndExecute(function () {
                $controller = new DepDepartmentController();
                $controller->actionback();
            });
            break;
        case $base_url . '/hdactionhold':
            checkSessionAndExecute(function () {
                $controller = new HeadDepartmentController();
                $controller->action();
            });
            break;
        case $base_url . '/hdactiontransferout':
            checkSessionAndExecute(function () {
                $controller = new HeadDepartmentController();
                $controller->actionTransferout();
            });
            break;
        case $base_url . '/hdactionback':
            checkSessionAndExecute(function () {
                $controller = new HeadDepartmentController();
                $controller->actionback();
            });
            break;
        case $base_url . '/du1actionhold':
            checkSessionAndExecute(function () {
                $controller = new DepUnit1Controller();
                $controller->action();
            });
            break;
        case $base_url . '/du1actiontransferout':
            checkSessionAndExecute(function () {
                $controller = new DepUnit1Controller();
                $controller->actionTransferout();
            });
            break;
        case $base_url . '/du1actionback':
            checkSessionAndExecute(function () {
                $controller = new DepUnit1Controller();
                $controller->actionback();
            });
            break;
        case $base_url . '/du2actionhold':
            checkSessionAndExecute(function () {
                $controller = new DepUnit2Controller();
                $controller->action();
            });
            break;

        case $base_url . '/huactionhold':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->action();
            });
            break;
        case $base_url . '/huactiontransferout':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->actionTransferout();
            });
            break;
        case $base_url . '/huactionback':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->actionback();
            });
            break;
        case $base_url . '/actionresign':
            checkSessionAndExecute(function () {
                $controller = new DepOfficeController();
                $controller->actionResign();
            });
            break;
        case $base_url . '/hoactionresign':
            checkSessionAndExecute(function () {
                $controller = new HeadOfficeController();
                $controller->actionResign();
            });
            break;
        case $base_url . '/ddactionresign':
            checkSessionAndExecute(function () {
                $controller = new DepDepartmentController();
                $controller->actionResign();
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
        case $base_url . '/department-calendar':
            checkSessionAndExecute(function () {
                $controller = new CalendarController();
                $controller->viewCalendar();
            });
            break;
        case $base_url . '/du1-calendar':
            checkSessionAndExecute(function () {
                $controller = new DepUnit1Controller();
                $controller->du1ViewCalendar();
            });
            break;
        case $base_url . '/du2-calendar':
            checkSessionAndExecute(function () {
                $controller = new DepUnit2Controller();
                $controller->du2ViewCalendar();
            });
            break;
        case $base_url . '/headunit-calendar':
            checkSessionAndExecute(function () {
                $controller = new HeadUnitController();
                $controller->headunitViewCalendar();
            });
            break;
        case $base_url . '/holidays':
            checkSessionAndExecute(function () {
                $controller = new CalendarController();
                $controller->index();
            });
            break;
        case $base_url . '/allqr':
            checkSessionAndExecute(function () {
                $controller = new QrcodeController();
                $controller->index();
            });
            break;
        case $base_url . '/qrcode':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->indexQR();
            });
            break;
        case $base_url . '/generateQR':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->generate();
            });
            break;
        case $base_url . '/deleteQR':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->deleteQR();
            });
            break;
        case $base_url . '/ipaddress':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->actionIP();
            });
            break;
        case $base_url . '/attendanceCheck':
            checkSessionAndExecute(function () {
                $controller = new AttendanceController();
                $controller->index();
            });
            break;
        case $base_url . '/actionCheck':
            checkSessionAndExecute(function () {
                $controller = new AttendanceController();
                $controller->action();
            });
            break;
        case $base_url . '/createHoliday':
            checkSessionAndExecute(function () {
                $controller = new CalendarController();
                $controller->create();
            });
            break;
        case $base_url . '/updateHoliday':
            checkSessionAndExecute(function () {
                $controller = new CalendarController();
                $controller->update();
            });
            break;
        case $base_url . '/deleteHoliday':
            checkSessionAndExecute(function () {
                $controller = new CalendarController();
                $controller->delete();
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
        case $base_url . '/view-leave-detail-dd':
            checkSessionAndExecute(function () {
                $controller = new DepDepartmentController();
                $controller->viewLeaveDetail();
            });
            break;
        case $base_url . '/view-leave-detail-dh':
            checkSessionAndExecute(function () {
                $controller = new HeadDepartmentController();
                $controller->viewLeaveDetail();
            });
            break;
        case $base_url . '/hoffice-view-leave':
            checkSessionAndExecute(function () {
                $controller = new HeadOfficeController();
                $controller->viewDetail();
            });
            break;
        case $base_url . '/admin-view-leave':
            checkSessionAndExecute(function () {
                $controller = new AdminController();
                $controller->viewLeavesDetail();
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
        case $base_url . '/ddepart-delete':
            checkSessionAndExecute(function () {
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                    $controller = new DepDepartmentController();
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
        case $base_url . '/edit_latein':
            checkSessionAndExecute(function () {
                $userController = new LateController();
                $userController->editLateIn();
            });
            break;
        case $base_url . '/edit_lateout':
            checkSessionAndExecute(function () {
                $userController = new LateController();
                $userController->editLateOut();
            });
            break;
        case $base_url . '/edit_leaveearly':
            checkSessionAndExecute(function () {
                $userController = new LateController();
                $userController->editLeaveEarly();
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
        case $base_url . '/hold':
            checkSessionAndExecute(function () {
                $holdController = new HoldController();
                $holdController->index();
            });
            break;
        case $base_url . '/apply-hold':
            checkSessionAndExecute(function () {
                $holdController = new HoldController();
                $holdController->create();
            });
            break;
        case $base_url . '/edit-hold':
            checkSessionAndExecute(function () {
                $holdController = new HoldController();
                $holdController->update();
            });
            break;
        case $base_url . '/view&edit-hold':
            checkSessionAndExecute(function () {
                if (isset($_GET['holdId']) && !empty($_GET['holdId'])) {
                    $holdController = new HoldController();
                    $holdController->view($_GET['holdId']);
                } else {
                    // Handle error: no valid holdId
                    $_SESSION['error'] = "No valid hold ID provided!";
                    header("Location: /elms/hold");
                    exit();
                }
            });
            break;
        case $base_url . '/exportFile':
            checkSessionAndExecute(function () {
                $controller = new HoldController();
                $controller->export();
            });
            break;
        case $base_url . '/exportTransferoutDoc':
            checkSessionAndExecute(function () {
                $controller = new TransferoutController();
                $controller->export();
            });
            break;
        case $base_url . '/exportBackworkDoc':
            checkSessionAndExecute(function () {
                $controller = new BackworkController();
                $controller->export();
            });
            break;
        case $base_url . '/delete-hold':
            checkSessionAndExecute(function () {
                $controller = new HoldController();
                $controller->delete();
            });
            break;
        case $base_url . '/holdApproved':
            checkSessionAndExecute(function () {
                $controller = new HoldController();
                $controller->holdApproved();
            });
            break;
        case $base_url . '/holdRejected':
            checkSessionAndExecute(function () {
                $controller = new HoldController();
                $controller->holdRejected();
            });
            break;
        case $base_url . '/addMoreAttachmentHold':
            checkSessionAndExecute(function () {
                $holdController = new HoldController();
                $holdController->addMoreAttachment();
            });
            break;
        case $base_url . '/deleteHoldAttachment':
            checkSessionAndExecute(function () {
                $controller = new HoldController();
                $controller->removeAttachments();
            });
            break;
        case $base_url . '/transferout':
            checkSessionAndExecute(function () {
                $holdController = new TransferoutController();
                $holdController->index();
            });
            break;
        case $base_url . '/apply-transferout':
            checkSessionAndExecute(function () {
                $holdController = new TransferoutController();
                $holdController->create();
            });
            break;
        case $base_url . '/addMoreAttachment':
            checkSessionAndExecute(function () {
                $holdController = new TransferoutController();
                $holdController->addMoreAttachment();
            });
            break;
        case $base_url . '/view&edit-transferout':
            checkSessionAndExecute(function () {
                if (isset($_GET['transferId']) && !empty($_GET['transferId'])) {
                    $holdController = new TransferoutController();
                    $holdController->view($_GET['transferId']);
                } else {
                    // Handle error: no valid holdId
                    $_SESSION['error'] = "No valid hold ID provided!";
                    header("Location: /elms/hold");
                    exit();
                }
            });
            break;
        case $base_url . '/delete-transferout':
            checkSessionAndExecute(function () {
                $controller = new TransferoutController();
                $controller->delete();
            });
            break;
        case $base_url . '/deleteTranoutAttachment':
            checkSessionAndExecute(function () {
                $controller = new TransferoutController();
                $controller->removeAttachments();
            });
            break;
        case $base_url . '/resign':
            checkSessionAndExecute(function () {
                $resignController = new ResignController();
                $resignController->index();
            });
            break;
        case $base_url . '/apply-resign':
            checkSessionAndExecute(function () {
                $resignController = new ResignController();
                $resignController->create();
            });
            break;
        case $base_url . '/delete-resign':
            checkSessionAndExecute(function () {
                $resignController = new ResignController();
                $resignController->delete();
            });
            break;
        case $base_url . '/view&edit-resign':
            checkSessionAndExecute(function () {
                if (isset($_GET['resignId']) && !empty($_GET['resignId'])) {
                    $resignController = new ResignController();
                    $resignController->view($_GET['resignId']);
                } else {
                    // Handle error: no valid holdId
                    $_SESSION['error'] = "No valid hold ID provided!";
                    header("Location: /elms/resign");
                    exit();
                }
            });
            break;
        case $base_url . '/edit-resign':
            checkSessionAndExecute(function () {
                $resignController = new ResignController();
                $resignController->update();
            });
            break;
        case $base_url . '/addMoreAttachmentResign':
            checkSessionAndExecute(function () {
                $resignController = new ResignController();
                $resignController->addMoreAttachment();
            });
            break;
        case $base_url . '/deleteResignAttachment':
            checkSessionAndExecute(function () {
                $controller = new ResignController();
                $controller->removeAttachments();
            });
            break;
        case $base_url . '/backwork':
            checkSessionAndExecute(function () {
                $holdController = new BackworkController();
                $holdController->index();
            });
            break;
        case $base_url . '/apply-backwork':
            checkSessionAndExecute(function () {
                $backworkController = new BackworkController();
                $backworkController->create();
            });
            break;
        case $base_url . '/delete-backwork':
            checkSessionAndExecute(function () {
                $backworkController = new BackworkController();
                $backworkController->delete();
            });
            break;
        case $base_url . '/view&edit-backwork':
            checkSessionAndExecute(function () {
                if (isset($_GET['backworkId']) && !empty($_GET['backworkId'])) {
                    $backworkController = new BackworkController();
                    $backworkController->view($_GET['backworkId']);
                } else {
                    // Handle error: no valid holdId
                    $_SESSION['error'] = "No valid hold ID provided!";
                    header("Location: /elms/backwork");
                    exit();
                }
            });
            break;
        case $base_url . '/edit-backwork':
            checkSessionAndExecute(function () {
                $backworkController = new BackworkController();
                $backworkController->update();
            });
            break;
        case $base_url . '/addMoreAttachmentBackwork':
            checkSessionAndExecute(function () {
                $backworkController = new BackworkController();
                $backworkController->addMoreAttachment();
            });
            break;
        case $base_url . '/deleteBackworkAttachment':
            checkSessionAndExecute(function () {
                $backworkController = new BackworkController();
                $backworkController->removeAttachments();
            });
            break;
        case $base_url . '/verify-2fa':
            checkSessionAndExecute(function () {
                $settingController = new settingController();
                $settingController->create2fa();
            });
            break;
        case $base_url . '/disable-2fa':
            checkSessionAndExecute(function () {
                $settingController = new settingController();
                $settingController->disable2fa();
            });
            break;
        case $base_url . '/verifyAuth2Fa':
            // Redirect to 404 page for non-existent routes
            checkSessionAndExecute(function () {
                $settingController = new settingController();
                $settingController->verifyAuth2Fa();
            });
            break;
        case $base_url . '/v2faCode':
            require 'src/views/errors/2fa.php';
            break;
        case $base_url . '/block_page':
            // Redirect to custom blocked page
            header("HTTP/1.0 404 Not Found");
            require 'src/views/errors/block_page.php';
            break;
        case $base_url . '/usage':
            // Redirect to custom blocked page
            require 'src/views/errors/usage.php';
            break;

        case $base_url . '/404':
            // Redirect to the 404 page for non-existent routes
            header("HTTP/1.0 404 Not Found");
            require 'src/views/errors/404.php';
            break;

        default:
            // Handle any other cases
            header("HTTP/1.0 404 Not Found");
            require 'src/views/errors/404.php';
            die(); // Default fallback to 404
    }

});

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
