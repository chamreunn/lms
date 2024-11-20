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
    <title><?= $title ?? "No Title" ?></title>
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
        /* Default (Light Mode) Styles */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            /* Light background */
            backdrop-filter: blur(8px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .bouncing-balls {
            display: flex;
            gap: 10px;
        }

        .ball {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: #3498db;
            /* Light blue balls */
            animation: bounce 0.6s infinite alternate;
        }

        .ball:nth-child(1) {
            animation-delay: 0s;
        }

        .ball:nth-child(2) {
            animation-delay: 0.2s;
        }

        .ball:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            0% {
                transform: translateY(0);
            }

            100% {
                transform: translateY(-30px);
            }
        }

        /* Dark Mode Styles */
        body.dark-theme .loader-wrapper {
            background-color: rgba(0, 0, 0, 0.8);
            /* Dark background */
            backdrop-filter: blur(8px);
        }

        body.dark-theme .ball {
            background-color: #ffffff;
            /* White balls for dark mode */
        }

        /* Dark theme loader text (optional) */
        body.dark-theme .loader-wrapper::after {
            content: "Loading...";
            color: #ffffff;
            font-size: 18px;
            position: absolute;
            bottom: 10px;
        }
    </style>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

    <!-- map  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

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
        <div class="bouncing-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
        </div>
    </div>

    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>

    <div class="page">

        <!-- Navbar -->
        <div class="sticky-top">
            <?php
            if (isset($_SESSION['user_id']) && !isset($_SESSION['blocked_user'])) {
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
                            require 'navbar.php';
                            require 'admin/sidebar.php';
                            break;
                        case 'Deputy Head Of Office':
                            // Load the LeaveApproval model to get leave request counts
                            $leaveRequestModel = new LeaveApproval();
                            $pendingRequestsCount = $leaveRequestModel->countPendingRequestsForApprover();
                            $approvedRequestsCount = $leaveRequestModel->approvedCount();
                            $rejectedRequestsCount = $leaveRequestModel->rejectedCount();

                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $pendingRequestsCount + $pendingHoldsCount;

                            // Load the AdminModel to get late counts
                            $adminModel = new AdminModel();
                            $totalLateCount = $adminModel->getLateinCount();
                            $latesTodayCount = $adminModel->getLateCountToday();

                            // Sidebar and navbar templates
                            require 'navbar.php';
                            require 'offices-d/sidebar.php';
                            break;
                        case 'Head Of Office':
                            $userModel = new User();
                            $leaveRequestModel = new HeadOfficeModel();
                            $pendingCount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            $depoffice = $userModel->getEmailLeaderDOApi($_SESSION['user_id'], $_SESSION['token']);
                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);
                            $totalPendingCount = $pendingCount + $pendingHoldsCount;
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'offices-h/sidebar.php';
                            break;
                        case 'Deputy Head Of Department':
                            $leaveRequestModel = new DepDepartmentModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();
                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $requestscount + $pendingHoldsCount;
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'departments-d/sidebar.php';
                            break;
                        case 'Head Of Department':
                            $userModel = new User();
                            $leaveRequestModel = new HeadDepartmentModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();

                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $requestscount + $pendingHoldsCount;

                            $leavetypeModel = new Leavetype();
                            $leavetypes = $leavetypeModel->getAllLeavetypes();
                            $depdepart = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'departments-h/sidebar.php';
                            break;
                        case 'Deputy Head Of Unit 1':
                            $leaveRequestModel = new DepUnit1Model();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();

                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $requestscount + $pendingHoldsCount;
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'unit1-d/sidebar.php';
                            break;
                        case 'Deputy Head Of Unit 2':
                            $leaveRequestModel = new DepUnit2Model();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();

                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $requestscount + $pendingHoldsCount;
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'unit2-d/sidebar.php';
                            break;
                        case 'Head Of Unit':
                            $leaveRequestModel = new HeadUnitModel();
                            $requestscount = $leaveRequestModel->pendingCount();
                            $approvedCount = $leaveRequestModel->approvedCount();
                            $rejectedCount = $leaveRequestModel->rejectedCount();

                            // Load the HoldModel to get count of pending hold requests
                            $holdModel = new HoldModel();
                            $pendingHoldsCount = $holdModel->countPendingHoldsByUserId($_SESSION['user_id']);

                            // Total pending count combining leave requests and holds
                            $totalPendingCount = $requestscount + $pendingHoldsCount;

                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'unit-h/sidebar.php';
                            break;
                        case 'superadmin':
                            // sidebar and navbar 
                            require 'navbar.php';
                            require 'superadmin/sidebar.php';
                            break;
                        default:
                            // For any unexpected role, include a default sidebar
                            // sidebar and navbar 
                            require 'navbar.php';
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
            <?= $pageheader ?? "" ?>
            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">