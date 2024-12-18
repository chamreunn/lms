<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}
require_once 'src/models/Notification.php';
date_default_timezone_set('Asia/Bangkok');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
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

    <!-- qr code style  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

    <!-- map  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- camera  -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"
        integrity="sha512-r6rDA7W6ZeQhvl8S7yRVQUKVHdexq+GAlNkNNqVC7YyIV+NwqCTJe2hDWCiffTyRNOeGEzRRJ9ifvRm/HCzGYg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- lottie animation icon loop  -->
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <!-- Include AOS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
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
    <style>
        /* Loader Wrapper */
        .loader-wrapper {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            backdrop-filter: blur(10px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Spinner Container */
        .spinner-container {
            position: relative;
            width: 150px;
            height: 150px;
        }

        /* Spinner */
        .spinner {
            width: 100%;
            height: 100%;
            border: 5px solid rgba(52, 152, 219, 0.3);
            border-top-color: #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* Logo Loader */
        .logo-loader {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 80px;
            height: 80px;
            background-image: url('public/img/icons/brands/logo2.png');
            background-size: contain;
            background-repeat: no-repeat;
            transform: translate(-50%, -50%);
        }

        /* Spin Animation */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        /* Adjust sizes for small screens */
        @media (max-width: 768px) {
            .spinner-container {
                width: 100px;
                height: 100px;
            }

            .spinner {
                border-width: 3px;
            }

            .logo-loader {
                width: 50px;
                height: 50px;
            }
        }

        /* Light Theme Loader Background */
        body:not(.theme-dark):not([data-theme="dark"]) .loader-wrapper {
            background-color: rgba(255, 255, 255, 0.7);
            /* Light theme background with opacity */
            backdrop-filter: blur(10px);
            /* Apply a blur effect */
        }


        /* Dark Mode for prefers-color-scheme */
        @media (prefers-color-scheme: dark) {
            .loader-wrapper {
                background-color: rgba(26, 34, 52, 0.7);
                /* Dark background for loader */
                backdrop-filter: blur(10px);
                /* Apply a blur effect */
            }
        }
    </style>

    <div id="loader-wrapper" class="loader-wrapper">
        <div class="spinner-container">
            <div class="spinner"></div>
            <div class="logo-loader"></div>
        </div>
    </div>

    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>

    <div class="page">

        <!-- Navbar -->
        <div class="sticky-top">
            <?php if (isset($_SESSION['user_id']) && !isset($_SESSION['blocked_user'])) {
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
                            // notifications 
                            $allnotifications = new NotificationModel();
                            $notifications = $allnotifications->getUserNotifications($_SESSION['user_id']);
                            // Sidebar and navbar templates
                            require 'navbar.php';
                            require 'offices-d/sidebar.php';
                            break;
                        case 'Head Of Office':
                            $userModel = new User();
                            $adminModel = new AdminModel();
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
                            $adminModel = new AdminModel();
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
                            $adminModel = new AdminModel();
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
                            $adminModel = new AdminModel();
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
                            $adminModel = new AdminModel();
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
                            $adminModel = new AdminModel();

                            require 'navbar.php';
                            require 'unit-h/sidebar.php';
                            break;
                        case 'superadmin':
                            // admin section 
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
                            require 'navbar.php';
                            require 'superadmin/sidebar.php';
                            break;
                        default:
                            // admin section 
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
                            // notifications 
                            $allnotifications = new NotificationModel();
                            $notifications = $allnotifications->getUserNotifications($_SESSION['user_id']);
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
                    <div class="page-header d-print-none mt-0 mb-3">
                        <div class="col-12">
                            <div class="row g-2 align-items-center">
                                <div class="col">
                                    <!-- Page pre-title -->
                                    <a href="./." class="page-pretitle text-decoration-none mb-1">
                                        <?= $pretitle ?? "" ?>
                                    </a>
                                    <h2 class="page-title">
                                        <?= htmlspecialchars($title ?? ""); ?>
                                    </h2>
                                </div>

                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <?= $customButton ?? "" ?>
                                    </div>
                                </div>

                                <!-- Page title actions -->
                                <div class="col-auto ms-auto d-print-none">
                                    <div class="btn-list">
                                        <h3 class="text-primary mb-0" id="real-time-clock"></h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    