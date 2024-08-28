<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?php echo $title ?? "No Title" ?></title>
    <link rel="icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <!-- CSS -->
    <link href="public/dist/css/tabler.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-flags.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-payments.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-vendors.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/demo.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/libs/animate/animate.css?1668287865" rel="stylesheet" />
    <link href="public/dist/libs/litepicker/dist/css/plugins/multiselect.js.css?1668287865" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <style>
        .sortable:hover {
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>

    <div class="page">
        <!-- Navbar -->
        <div class="sticky-top">
            <?php
            if (isset($_SESSION['user_id']) && !isset($_SESSION['blocked_user'])) {
                include('navbar.php');
                // Determine which sidebar to include based on the user's role
                if (isset($_SESSION['role'])) {
                    $role = $_SESSION['role'];
                    switch ($role) {
                        case 'Admin':
                            $adminModel = new AdminModel();
                            $getPendingCounts = $adminModel->getLateinCount();
                            $latesToday = $adminModel->getLateCountToday();
                            include('admin_sidebar.php');
                            break;
                        case 'User':
                            include('user_sidebar.php');
                            break;
                        case 'Deputy Head Of Office':
                            $leaveRequestModel = new LeaveApproval();
                            $requestscount = $leaveRequestModel->countPendingRequestsForApprover();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            include('office_manager_sidebar.php');
                            break;
                        case 'Head Of Office':
                            $leaveRequestModel = new HeadOfficeLeave();
                            $pendingCount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            include('head_office_manager_sidebar.php');
                            break;
                        case 'Deputy Head Of Department':
                            $leaveRequestModel = new DepDepartLeave();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->rejectedCount();
                            $rejectedCount = $leaveRequestModel->approvedCount();
                            include('deputy_department_sidebar.php');
                            break;
                        case 'Head Of Department':
                            $leaveRequestModel = new HeadDepartLeave();
                            $pendingCount = $leaveRequestModel->pendingCount($_SESSION['user_id']);
                            $approvedCount = $leaveRequestModel->approvedCount($_SESSION['user_id']);
                            $rejectedCount = $leaveRequestModel->rejectedCount($_SESSION['user_id']);
                            $leavetypeModel = new Leavetype();
                            $leavetypes = $leavetypeModel->getAllLeavetypes();
                            include('head_department_sidebar.php');
                            break;
                        case 'Deputy Of Unit 1':
                            include('deputy_unit_sidebar_1.php');
                            break;
                        case 'Deputy Of Unit 2':
                            include('deputy_unit_sidebar_2.php');
                            break;
                        default:
                            // For any unexpected role, include a default sidebar
                            include('sidebar.php');
                            break;
                    }
                } else {
                    // Default sidebar if role is not set
                    include('sidebar.php');
                }
            }
            ?>
        </div>

        <!-- Include Alert -->
        <?php include('src/common/alert.php'); ?>

        <div class="page-wrapper">
            <!-- Page header -->
            <?php echo $pageheader ?? "" ?>
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">