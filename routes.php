<?php
// Check if a session is active before starting a new one
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}
// Define your base URL
$base_url = '/elms'; // Set your base URL if your application is located under a subdirectory

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

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

switch ($uri) {
    case $base_url . '/':
    case $base_url . '/login':
        $controller = new AuthController();
        $controller->login();
        break;
    case $base_url . '/logout':
        session_destroy();
        header("Location: $base_url/login");
        exit();
    case $base_url . '/apply-leave':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->apply();
        });
        break;
    case $base_url . '/leave-requests':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->viewRequests();
        });
        break;
    case $base_url . '/pending':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->approve();
        });
        break;
    case $base_url . '/headofficepending':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeLeaveController();
            $controller->approve();
        });
        break;
    case $base_url . '/headofficeapproved':
        checkSessionAndExecute(function () {
            $controller = new HeadOfficeLeaveController();
            $controller->approved();
        });
        break;
    case $base_url . '/depdepartmentpending':
        checkSessionAndExecute(function () {
            $controller = new DepDepartController();
            $controller->approve();
        });
        break;
    case $base_url . '/depdepartmentapproved':
        checkSessionAndExecute(function () {
            $controller = new DepDepartController();
            $controller->approved();
        });
        break;
    case $base_url . '/headdepartmentpending':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartController();
            $controller->approve();
        });
        break;
    case $base_url . '/headdepartmentapproved':
        checkSessionAndExecute(function () {
            $controller = new HeadDepartController();
            $controller->approved();
        });
        break;
    case $base_url . '/depunit1pending':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->approve();
        });
        break;
    case $base_url . '/depunit1approved':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->approved();
        });
        break;
    case $base_url . '/deputit1rejected':
        checkSessionAndExecute(function () {
            $controller = new DepUnit1Controller();
            $controller->approved();
        });
        break;
    case $base_url . '/approved':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->approved();
        });
        break;
    case $base_url . '/leave-calendar':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->viewCalendar();
        });
        break;
    case $base_url . '/leave-cancel':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
                // Ensure that required parameters are set
                if (isset($_POST['id']) && isset($_POST['status'])) {
                    $id = $_POST['id'];
                    $status = $_POST['status'];

                    // Instantiate the controller and call the cancel method
                    $controller = new LeaveController();
                    $controller->cancel($id, $status);
                } else {
                    // Handle missing parameters
                    http_response_code(400); // Bad Request
                    echo 'Missing parameters.';
                }
            } else {
                // Redirect to login if not a POST request or session user ID is not set
                header("Location: /elms/login");
                exit();
            }
        });
        break;
    case $base_url . '/view-leave-detail':
        checkSessionAndExecute(function () {
            $controller = new LeaveController();
            $controller->viewDetail();
        });
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
        break;
    case $base_url . '/notifications':
        checkSessionAndExecute(function () {
            $controller = new NotificationController();
            $controller->index();
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
    case $base_url . '/overtimein':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            $controller->overtimein();
        });
        break;
    case $base_url . '/overtimeout':
        checkSessionAndExecute(function () {
            $controller = new LateController();
            $controller->overtimeout();
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
    case $base_url . '/delete_latein':
        checkSessionAndExecute(function () {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['id'])) {
                $controller = new LateController();
                $controller->delete($_POST['id']);
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
            include 'src/views/users/index.php';
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