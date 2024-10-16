<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

date_default_timezone_set('Asia/Bangkok');
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

        /* Full-Page Loader with Blur */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            border: 6px solid #f3f3f3;
            border-radius: 50%;
            border-top: 6px solid #3498db;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Page content blur when loader is visible */
        body.loading .page {
            filter: blur(8px);
        }
    </style>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

    <!-- spinner button  -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all forms
            const forms = document.querySelectorAll('form');

            forms.forEach(function (form) {
                // Attach event listener for form submission
                form.addEventListener('submit', function (event) {
                    const submitBtn = form.querySelector('button[type="submit"]');

                    // Check if the spinner already exists; if not, create and append it
                    if (!submitBtn.querySelector('.spinner-border')) {
                        const spinner = document.createElement('span');
                        spinner.classList.add('spinner-border', 'spinner-border-sm', 'mx-2');
                        spinner.setAttribute('role', 'status');
                        spinner.setAttribute('aria-hidden', 'true');
                        spinner.style.display = 'none';
                        submitBtn.appendChild(spinner);
                    }

                    // Show the spinner
                    const spinner = submitBtn.querySelector('.spinner-border');
                    spinner.style.display = 'inline-block';

                    // Disable the button after a slight delay to allow form submission
                    setTimeout(function () {
                        submitBtn.setAttribute('disabled', 'true');
                    }, 50); // Delay the button disable by 50ms, giving the form time to submit
                });
            });
        });
    </script>
</head>

<body class="loading">
    <!-- Loader HTML -->
    <div id="loader-wrapper" class="loader-wrapper">
        <div class="loader"></div>
    </div>

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
                            $leaveRequestModel = new LeaveApproval();
                            $requestscount = $leaveRequestModel->countPendingRequestsForApprover();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $getPendingCounts = $adminModel->getLateinCount();
                            $latesToday = $adminModel->getLateCountToday();
                            $AllLate = $adminModel->getAllLate();
                            // sidebar count 
                            $getAllMissionCount = $adminModel->getMissionsTodayCount();
                            $getLeaveTodayCount = $adminModel->getLeaveTodayCount();
                            $getPendingCount = $adminModel->getLateCountByStatus('Pending');
                            // end sidebar count 
                            require 'admin/sidebar.php';
                            break;
                        case 'Deputy Head Of Office':
                            $leaveRequestModel = new LeaveApproval();
                            $requestscount = $leaveRequestModel->countPendingRequestsForApprover();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $adminModel = new AdminModel();
                            $getPendingCounts = $adminModel->getLateinCount();
                            $latesToday = $adminModel->getLateCountToday();
                            require 'offices-d/sidebar.php';
                            break;
                        case 'Head Of Office':
                            $userModel = new User();
                            $leaveRequestModel = new HeadOfficeModel();
                            $pendingCount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $depoffice = $userModel->getEmailLeaderDOApi($_SESSION['user_id'], $_SESSION['token']);
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
                            $userModel = new User();
                            $leaveRequestModel = new HeadDepartmentModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $leavetypeModel = new Leavetype();
                            $leavetypes = $leavetypeModel->getAllLeavetypes();
                            $depdepart = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);
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