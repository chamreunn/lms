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
                        case 'Deputy Head Of Office':
                            $leaveRequestModel = new LeaveApproval();
                            $requestscount = $leaveRequestModel->countPendingRequestsForApprover();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'offices-d/sidebar.php';
                            break;
                        case 'Head Of Office':
                            $leaveRequestModel = new HeadOfficeModel();
                            $pendingCount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'offices-h/sidebar.php';
                            break;
                        case 'Deputy Head Of Department':
                            $leaveRequestModel = new DepDepartmentModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'departments-d/sidebar.php';
                            break;
                        case 'Head Of Department':
                            $leaveRequestModel = new HeadDepartmentModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $leavetypeModel = new Leavetype();
                            $leavetypes = $leavetypeModel->getAllLeavetypes();
                            require 'departments-h/sidebar.php';
                            break;
                        case 'Deputy Head Of Unit 1':
                            $leaveRequestModel = new DepUnit1Model();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'unit1-d/sidebar.php';
                            break;
                        case 'Deputy Head Of Unit 2':
                            $leaveRequestModel = new DepUnit2Model();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'unit2-d/sidebar.php';
                            break;
                        case 'Head Of Unit':
                            $leaveRequestModel = new HeadUnitModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            require 'unit-h/sidebar.php';
                            break;
                        default:
                            // For any unexpected role, include a default sidebar
                            include('users/sidebar.php');
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