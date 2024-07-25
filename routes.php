<?php
// Enable error reporting for troubleshooting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define the base URL based on the hosting environment
$base_url = '/elms';

// Include necessary controllers
require_once 'src/controllers/AuthController.php';
require_once 'src/controllers/LeaveController.php';
require_once 'src/controllers/NotificationController.php';
require_once 'src/controllers/DepartmentController.php';
require_once 'src/controllers/OfficeController.php';
require_once 'src/controllers/RoleController.php';
require_once 'src/controllers/PositionController.php';
require_once 'src/controllers/UserController.php';
require_once 'src/controllers/DashboardController.php';
require_once 'src/controllers/HeadOfficeLeaveController.php';
require_once 'src/controllers/DepDepartController.php';
require_once 'src/controllers/HeadDepartController.php';
require_once 'src/controllers/DepUnit1Controller.php';
require_once 'src/controllers/LateController.php';

// Parse the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Check the base URL and request URI
if (strpos($uri, $base_url) !== 0) {
    header("HTTP/1.0 404 Not Found");
    require 'src/views/errors/404.php';
    exit();
}

// Function to handle session checks and execution
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

// Define the routes
$routes = [
    '/' => function() { (new AuthController())->login(); },
    '/login' => function() { (new AuthController())->login(); },
    '/logout' => function() { session_destroy(); header("Location: /elms/login"); exit(); },
    '/apply-leave' => function() { checkSessionAndExecute(fn() => (new LeaveController())->apply()); },
    '/leave-requests' => function() { checkSessionAndExecute(fn() => (new LeaveController())->viewRequests()); },
    '/pending' => function() { checkSessionAndExecute(fn() => (new LeaveController())->approve()); },
    '/headofficepending' => function() { checkSessionAndExecute(fn() => (new HeadOfficeLeaveController())->approve()); },
    '/headofficeapproved' => function() { checkSessionAndExecute(fn() => (new HeadOfficeLeaveController())->approved()); },
    '/depdepartmentpending' => function() { checkSessionAndExecute(fn() => (new DepDepartController())->approve()); },
    '/depdepartmentapproved' => function() { checkSessionAndExecute(fn() => (new DepDepartController())->approved()); },
    '/headdepartmentpending' => function() { checkSessionAndExecute(fn() => (new HeadDepartController())->approve()); },
    '/headdepartmentapproved' => function() { checkSessionAndExecute(fn() => (new HeadDepartController())->approved()); },
    '/depunit1pending' => function() { checkSessionAndExecute(fn() => (new DepUnit1Controller())->approve()); },
    '/depunit1approved' => function() { checkSessionAndExecute(fn() => (new DepUnit1Controller())->approved()); },
    '/deputit1rejected' => function() { checkSessionAndExecute(fn() => (new DepUnit1Controller())->approved()); },
    '/approved' => function() { checkSessionAndExecute(fn() => (new LeaveController())->approved()); },
    '/leave-calendar' => function() { checkSessionAndExecute(fn() => (new LeaveController())->viewCalendar()); },
    '/leave-cancel' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
                (new LeaveController())->cancel($_POST['id'], $_POST['status']);
            } else {
                http_response_code(400);
                echo 'Missing parameters.';
            }
        });
    },
    '/view-leave-detail' => function() { checkSessionAndExecute(fn() => (new LeaveController())->viewDetail()); },
    '/leave-delete' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                (new LeaveController())->delete($_POST['id']);
            }
        });
    },
    '/notifications' => function() { checkSessionAndExecute(fn() => (new NotificationController())->index()); },
    '/dashboard' => function() { checkSessionAndExecute(fn() => (new DashboardController())->show()); },
    '/roles' => function() { checkSessionAndExecute(fn() => (new RoleController())->index()); },
    '/documents' => function() { checkSessionAndExecute(fn() => (new LateController())->index()); },
    '/late_in_request' => function() { checkSessionAndExecute(fn() => (new LateController())->requestLateIn()); },
    '/late_out_request' => function() { checkSessionAndExecute(fn() => (new LateController())->requestLateOut()); },
    '/overtimein' => function() { checkSessionAndExecute(fn() => (new LateController())->overtimein()); },
    '/overtimeout' => function() { checkSessionAndExecute(fn() => (new LateController())->overtimeout()); },
    '/create_late' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new LateController())->store($_POST['name'], $_POST['color']);
            }
        });
    },
    '/apply_latein' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new LateController())->createLateIn($_POST['date'], $_POST['time'], $_POST['reason']);
            }
        });
    },
    '/apply_lateout' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new LateController())->createLateOut($_POST['date'], $_POST['time'], $_POST['reason']);
            }
        });
    },
    '/update_late' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                (new LateController())->update($_POST['id'], $_POST['name'], $_POST['color']);
            }
        });
    },
    '/delete_latein' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                (new LateController())->delete($_POST['id']);
            }
        });
    },
    '/delete_lateout' => function() {
        checkSessionAndExecute(function() {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
                (new LateController())->delete($_POST['id']);
            }
        });
    },
    '/departments' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->index()); },
    '/department_create' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->create()); },
    '/department_store' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->store()); },
    '/department_edit' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->edit()); },
    '/department_update' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->update()); },
    '/department_delete' => function() { checkSessionAndExecute(fn() => (new DepartmentController())->delete()); },
    '/offices' => function() { checkSessionAndExecute(fn() => (new OfficeController())->index()); },
    '/office_create' => function() { checkSessionAndExecute(fn() => (new OfficeController())->create()); },
    '/office_store' => function() { checkSessionAndExecute(fn() => (new OfficeController())->store()); },
    '/office_edit' => function() { checkSessionAndExecute(fn() => (new OfficeController())->edit()); },
    '/office_update' => function() { checkSessionAndExecute(fn() => (new OfficeController())->update()); },
    '/office_delete' => function() { checkSessionAndExecute(fn() => (new OfficeController())->delete()); },
    '/positions' => function() { checkSessionAndExecute(fn() => (new PositionController())->index()); },
    '/position_create' => function() { checkSessionAndExecute(fn() => (new PositionController())->create()); },
    '/position_store' => function() { checkSessionAndExecute(fn() => (new PositionController())->store()); },
    '/position_edit' => function() { checkSessionAndExecute(fn() => (new PositionController())->edit()); },
    '/position_update' => function() { checkSessionAndExecute(fn() => (new PositionController())->update()); },
    '/position_delete' => function() { checkSessionAndExecute(fn() => (new PositionController())->delete()); },
    '/users' => function() { checkSessionAndExecute(fn() => (new UserController())->index()); },
    '/user_create' => function() { checkSessionAndExecute(fn() => (new UserController())->create()); },
    '/user_store' => function() { checkSessionAndExecute(fn() => (new UserController())->store()); },
    '/user_edit' => function() { checkSessionAndExecute(fn() => (new UserController())->edit()); },
    '/user_update' => function() { checkSessionAndExecute(fn() => (new UserController())->update()); },
    '/user_delete' => function() { checkSessionAndExecute(fn() => (new UserController())->delete()); },
    '/user_profile' => function() { checkSessionAndExecute(fn() => (new UserController())->profile()); },
    '/update_password' => function() { checkSessionAndExecute(fn() => (new UserController())->updatePassword()); },
];

// Route the request
$routePath = str_replace($base_url, '', $uri);
if (isset($routes[$routePath])) {
    $routes[$routePath]();
} else {
    // Redirect to 404 page for non-existent routes
    header("HTTP/1.0 404 Not Found");
    require 'src/views/errors/404.php';
    exit();
}
