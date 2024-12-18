<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

$title = "ទំព័រដើម";
include('src/common/header.php');
// Ensure the necessary data is available
$count = isset($data['count']) ? $data['count'] : 0;
$leaves = isset($data['leaves']) ? $data['leaves'] : [];
$page = isset($data['page']) ? $data['page'] : 1;
$totalPages = isset($data['totalPages']) ? $data['totalPages'] : 1;
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    $days = [
        'Mon' => 'ច័ន្ទ',
        'Tue' => 'អង្គារ',
        'Wed' => 'ពុធ',
        'Thu' => 'ព្រហស្បតិ៍',
        'Fri' => 'សុក្រ',
        'Sat' => 'សៅរ៍',
        'Sun' => 'អាទិត្យ'
    ];
    $months = [
        'January' => 'មករា',
        'February' => 'កុម្ភៈ',
        'March' => 'មីនា',
        'April' => 'មេសា',
        'May' => 'ឧសភា',
        'June' => 'មិថុនា',
        'July' => 'កក្កដា',
        'August' => 'សីហា',
        'September' => 'កញ្ញា',
        'October' => 'តុលា',
        'November' => 'វិច្ឆិកា',
        'December' => 'ធ្នូ'
    ];

    $translatedDay = $days[date('D', strtotime($date))];
    $translatedMonth = $months[date('F', strtotime($date))];
    $translatedDate = str_replace(
        [date('D', strtotime($date)), date('F', strtotime($date))],
        [$translatedDay, $translatedMonth],
        date($format, strtotime($date))
    );

    return $translatedDate;
}
?>
<!-- scan attendance  -->
<?php if (!empty($todayAttendance)): ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-auto">
                    <img src="<?= $_SESSION['user_profile'] ?>" alt="" style="object-fit: cover;" class="avatar avatar-lg">
                </div>
                <div class="col-auto px-3">
                    <h2><?= $_SESSION['user_khmer_name'] ?? 'No Name' ?></h2>
                    <div class="text-muted">
                        <span class="badge <?= $_SESSION['position_color'] ?>"><?= $_SESSION['position'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="row row-cards">
                    <?php
                    // Check if attendance record exists
                    if (isset($todayAttendance[0])) {
                        $attendance = $todayAttendance[0]; // Access the first attendance record
                        if ($attendance['leave'] !== '1' && $attendance['mission'] !== '1') {
                            ?>
                            <!-- check in  -->
                            <div class="col-sm-6 col-lg-6">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary-lt text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-login">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M21 12h-13l3 -3" />
                                                        <path d="M11 15l-3 -3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="fw-bolder font-weight-medium 
                                        <?php
                                        if (isset($attendance['checkIn']) && $attendance['checkIn'] > '09:00:00') {
                                            echo 'text-danger'; // Red for late check-in
                                        }
                                        ?>">
                                                    <?= $attendance['checkIn'] ?? '--:--:--' ?>
                                                </div>
                                                <div class="text-secondary">
                                                    ម៉ោងចូល
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- check out  -->
                            <div class="col-sm-6 col-lg-6">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-red-lt text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M9 12h12l-3 -3" />
                                                        <path d="M18 15l3 -3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="fw-bolder font-weight-medium 
                                        <?php
                                        if (isset($attendance['checkOut'])) {
                                            if ($attendance['checkOut'] > '17:30:00') {
                                                echo 'text-danger'; // Red for late check-out
                                            } elseif ($attendance['checkOut'] < '16:00:00') {
                                                echo 'text-info'; // Blue for early check-out
                                            }
                                        }
                                        ?>">
                                                    <?= $attendance['checkOut'] ?? '--:--:--' ?>
                                                </div>
                                                <div class="text-secondary">
                                                    ម៉ោងចេញ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } elseif ($attendance['leave'] == '1') {
                            ?>
                            <div class="col-sm-12 col-lg-12">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col text-center">
                                                <h1 class="text-danger fw-bolder">
                                                    ច្បាប់ឈប់សម្រាក
                                                </h1>
                                                <p class="text-muted mb-0">អ្នកបានដាក់ច្បាប់ឈប់សម្រាកសម្រាប់ថ្ងៃនេះរបស់លោកអ្នក។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } elseif ($attendance['mission'] == '1') {
                            ?>
                            <div class="col-sm-12 col-lg-12">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col text-center">
                                                <h1 class="text-danger fw-bolder">
                                                    បេសកកម្ម
                                                </h1>
                                                <p class="text-muted mb-0">អ្នកមានបេសកកម្មថ្ងៃនេះ។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="col-sm-12 col-lg-12">
                            <div class="card card-sm bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <h1 class="text-warning">
                                                មិនទាន់មានការកត់ត្រាវត្តមានទេ។
                                            </h1>
                                            <p class="text-muted mb-0">
                                                សូមចុចប៊ូតុងខាងក្រោមដើម្បីកត់ត្រាវត្តមានប្រចាំថ្ងៃរបស់លោកអ្នក</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="mt-3 d-flex gap-3 align-items-center justify-content-center">
                <form action="/elms/actionCheck" method="POST">
                    <div class="mb-3" hidden>
                        <div class="map" style="height: 400px; width: 100%;"></div>
                        <input type="text" class="form-control" id="latitude" name="latitude" value="">
                        <input type="text" class="form-control" id="longitude" name="longitude" value="">
                        <input type="text" class="form-control" name="uid" value="<?= $_SESSION['user_id'] ?>">
                        <input type="text" class="form-control" name="userId"
                            value="<?= $_SESSION['user_id'] ?? 'No User Id Found' ?>">
                        <input type="text" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                        <input type="text" class="form-control" name="check" value="<?= date('H:i:s') ?>">
                        <input type="text" class="form-control" id="deviceId" name="device_id" value="">
                        <input type="text" class="form-control" id="ipAddress" name="ip_address" value="">
                    </div>
                    <button type="submit" id="checkInButton" class="btn btn-primary" disabled>
                        កំពុងពិនិត្យទីតាំង...
                    </button>
                </form>
                <p class="mb-0">ឬ</p>
                <button class="btn btn-primary" id="scanQrButton">
                    <span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-scan">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                            <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                            <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                            <path d="M5 12l14 0" />
                        </svg>
                    </span>
                    ស្កេនវត្តមាន
                </button>
            </div>

            <!-- QR Reader -->
            <div id="cameraWrapper" class="modal-blur camera-wrapper" style="display: none;">
                <div class="camera-container bg-primary-lt">
                    <div id="reader" style="width: 100%; height: 100%;"></div>
                    <!-- Stop Scanning Button -->
                    <button class="btn btn-danger mt-3" id="stopScanButton" style="display: none;">បោះបង់ការស្កេន
                    </button>
                </div>
            </div>

            <!-- QR Result -->
            <p id="qrResult" class="text-success fw-bold" style="font-size: 1.2rem;"></p>
        </div>
    </div>
<?php elseif (!empty($getQRcode)): ?>
    <div class="card d-block d-sm-block d-md-block d-lg-block mb-3">
        <div class="card-body">
            <div class="col-12 text-center">
                <div class="empty text-center mb-0">
                    <div class="d-flex flex-column justify-content-center align-items-center text-center">
                        <dotlottie-player src="https://lottie.host/9e679f3e-9b16-48a1-9830-33bcda19a9dd/IcjLvDCpRH.lottie"
                            background="transparent" speed="1" style="width: 100px;" loop autoplay>
                        </dotlottie-player>
                        <h3 class="mb-0">សូមចុចប៊ូតុង <span class="text-danger">កត់ត្រាវត្តមាន</span> ឬ <span
                                class="text-danger">ស្កេនវត្តមាន</span>
                            ខាងក្រោមដើម្បីកត់ត្រាវត្តមានប្រចាំថ្ងៃរបស់លោកអ្នក</h3>
                    </div>
                </div>

                <div class="mb-3 d-flex gap-3 align-items-center justify-content-center">
                    <form action="/elms/actionCheck" method="POST">
                        <div class="mb-3" hidden>
                            <div class="map" style="height: 400px; width: 100%;"></div>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="">
                            <input type="text" class="form-control" id="longitude" name="longitude" value="">
                            <input type="text" class="form-control" name="uid" value="<?= $_SESSION['user_id'] ?>">
                            <input type="text" class="form-control" name="userId"
                                value="<?= $_SESSION['user_id'] ?? 'No User Id Found' ?>">
                            <input type="text" class="form-control" name="date" value="<?= date('Y-m-d') ?>">
                            <input type="text" class="form-control" name="check" value="<?= date('H:i:s') ?>">
                            <input type="text" class="form-control" id="deviceId" name="device_id" value="">
                            <input type="text" class="form-control" id="ipAddress" name="ip_address" value="">
                        </div>
                        <button type="submit" id="checkInButton" class="btn btn-primary" disabled>
                            កំពុងពិនិត្យទីតាំង...
                        </button>
                    </form>
                    <p class="mb-0">ឬ</p>
                    <button class="btn btn-primary" id="scanQrButton">
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-scan">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
                                <path d="M4 17v1a2 2 0 0 0 2 2h2" />
                                <path d="M16 4h2a2 2 0 0 1 2 2v1" />
                                <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
                                <path d="M5 12l14 0" />
                            </svg>
                        </span>
                        ស្កេនវត្តមាន
                    </button>
                </div>

                <!-- QR Reader -->
                <div id="cameraWrapper" class="modal-blur camera-wrapper" style="display: none;">
                    <div class="camera-container bg-primary-lt">
                        <div id="reader" style="width: 100%; height: 100%;"></div>
                        <!-- Stop Scanning Button -->
                        <button class="btn btn-danger mt-3" id="stopScanButton" style="display: none;">បោះបង់ការស្កេន
                        </button>
                    </div>
                </div>

                <!-- QR Result -->
                <p id="qrResult" class="text-success fw-bold" style="font-size: 1.2rem;"></p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- alert leave count  -->
<?php if ($totalPendingCount > 0): ?>
    <div class="col">
        <div class="alert alert-info alert-dismissible mb-3" role="alert">
            <div class="d-flex">
                <div>
                    <!-- Download SVG icon from http://tabler-icons.io/i/info-circle -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon alert-icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                        <path d="M12 9h.01"></path>
                        <path d="M11 12h1v4h1"></path>
                    </svg>
                </div>
                <a href="/elms/headdepartmentpending">
                    អ្នកមានសំណើដែលមិនទាន់អនុម័តចំនួន <span
                        class="badge bg-red text-red-fg ms-2 fw-bolder"><?= $totalPendingCount ?></span>
                </a>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    </div>
<?php endif; ?>

<div class="row row-card">
    <div class="col-12 mb-3">
        <div class="row row-cards">
            <!-- All Leave Requests Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="/elms/hdepartmentLeave" class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-primary">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                            </svg>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium mx-0">
                                    ច្បាប់ឈប់សម្រាកទាំងអស់
                                </div>
                                <div class="text-primary fw-bolder">
                                    <?= $getcountrequestbyid . "ច្បាប់" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Late Letters Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="/elms/overtimein" class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-success">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                <path d="M12 7v5l2 2" />
                                <path d="M19 22v.01" />
                                <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                            </svg>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                        <path d="M12 7v5l2 2" />
                                        <path d="M19 22v.01" />
                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    សំណើចូលយឺត
                                </div>
                                <div class="text-green fw-bolder">
                                    <?= $getovertimeincounts . "លិខិត" ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Missions Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="/elms/overtimeout" class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-warning">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h12" />
                                <path d="M20 14l2 2h-3" />
                                <path d="M20 18l2 -2" />
                                <path d="M19 16a3 3 0 1 0 2 5.236" />
                            </svg>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h12" />
                                        <path d="M20 14l2 2h-3" />
                                        <path d="M20 18l2 -2" />
                                        <path d="M19 16a3 3 0 1 0 2 5.236" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    សំណើចេញយឺត
                                </div>
                                <div class="text-warning fw-bolder">
                                    <?= $getovertimeoutcounts . "លិខិត" ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Likes Card -->
            <div class="col-sm-6 col-lg-3">
                <a href="/elms/mission" class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-indigo">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path d="M16 3l0 4" />
                                <path d="M8 3l0 4" />
                                <path d="M4 11l16 0" />
                                <path d="M8 15h2v2h-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-indigo text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M8 15h2v2h-2z" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    បេសកកម្ម
                                </div>
                                <div class="text-indigo fw-bolder">
                                    <?= $getMissionCount . "លិខិត" ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (count($getUserApprove) > 0): ?>
    <div class="col-12 mb-3">
        <h3 class="mb-3">ច្បាប់ឈប់សម្រាកថ្ងៃនេះ</h3>
        <div class="card">
            <div class="list-group list-group-flush overflow-auto" style="max-height: 35rem">
                <?php foreach ($getUserApprove as $request): ?>
                    <a href="/elms/view-leave-detail?leave_id=<?= $request['leave_request_id'] ?>"
                        class="list-group-item list-group-item-action border-left-light">
                        <!-- <div class="list-group-item"> -->
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <img class="avatar" src="<?= $request['profile'] ?>" style="object-fit: cover;" />
                            </div>
                            <div class="col text-truncate">
                                <h4 class="text-body text-success d-block"><?= $request['user_name'] ?></h4>
                                <small class="text-muted text-truncate mt-n1">ឈប់សម្រាក ចំនួន <strong
                                        class="text-success"><?= $request['num_date'] ?></strong> ថ្ងៃ ចាប់ពី <strong
                                        class="text-success"><?= $request['start_date'] ?></strong> ដល់ <strong
                                        class="text-success"><?= $request['end_date'] ?></strong>
                                </small>
                            </div>
                        </div>
                        <!-- </div> -->
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="d-flex mb-3">
    <h3 class="mb-0">បង្កើតសំណើ</h3>
</div>

<div class="row row-card mb-3">
    <div class="col-lg-3 mb-3" data-aos="zoomin" data-aos-delay="100">
        <a href="" data-bs-toggle="modal" data-bs-target="#head-of-department"
            class="card card-link card-link-pop text-primary p-5 d-flex align-items-center justify-content-center">
            <div class="avatar avatar-md mb-3 bg-primary-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                    <path d="M7 14h.013" />
                    <path d="M10.01 14h.005" />
                    <path d="M13.01 14h.005" />
                    <path d="M16.015 14h.005" />
                    <path d="M13.015 17h.005" />
                    <path d="M7.01 17h.005" />
                    <path d="M10.01 17h.005" />
                </svg>
            </div>
            <strong>ច្បាប់ឈប់សម្រាក</strong>
        </a>

    </div>

    <div class="col-lg-3 mb-3" data-aos="zoomin" data-aos-delay="200">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-late-in"
            class="card card-link card-link-pop text-success p-5 d-flex align-items-center justify-content-center">
            <div class="avatar avatar-md mb-3 bg-success-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                    <path d="M19 22v-6" />
                    <path d="M22 19l-3 -3l-3 3" />
                    <path d="M12 7v5l2.5 2.5" />
                </svg>
            </div>
            <strong>សំណើចូលយឺត</strong>
        </a>
    </div>

    <div class="col-lg-3 mb-3" data-aos="zoomin" data-aos-delay="300">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-late-out"
            class="card card-link card-link-pop p-5 text-warning d-flex align-items-center justify-content-center">
            <div class="avatar avatar-md mb-3 bg-warning-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h12" />
                    <path d="M20 14l2 2h-3" />
                    <path d="M20 18l2 -2" />
                    <path d="M19 16a3 3 0 1 0 2 5.236" />
                </svg>
            </div>
            <strong>សំណើចេញយឺត</strong>
        </a>
    </div>

    <div class="col-lg-3 mb-3" data-aos="zoomin" data-aos-delay="400">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-leaveearly"
            class="card card-link card-link-pop p-5 text-warning d-flex align-items-center justify-content-center">
            <div class="avatar avatar-md mb-3 bg-warning-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M12 7v5l3 3" />
                </svg>
            </div>
            <strong>សំណើចេញមុន</strong>
        </a>
    </div>
</div>

<div class="hr-text text-primary fs-large">លិខិតរដ្ឋបាលផ្សេងៗ</div>

<div class="row g-3 row-card">
    <!-- Card 1 -->
    <div class="col-lg-3" data-aos="zoom-in" data-aos-delay="500">
        <a href="" data-bs-toggle="modal" data-bs-target="#hold"
            class="card card-link card-link-pop text-primary p-5 d-flex align-items-center justify-content-center hover-shadow">
            <div class="card-stamp">
                <div class="card-stamp-icon bg-vk">
                    <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-user-pause">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                        <path d="M17 17v5" />
                        <path d="M21 17v5" />
                    </svg>
                </div>
            </div>
            <div class="avatar avatar-md mb-3 bg-primary-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-pause">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                    <path d="M17 17v5" />
                    <path d="M21 17v5" />
                </svg>
            </div>
            <strong class="mt-2">លិខិតព្យួរ</strong>
        </a>
    </div>

    <!-- Card 2 -->
    <div class="col-lg-3" data-aos="zoom-in" data-aos-delay="600">
        <a href="" data-bs-toggle="modal" data-bs-target="#transferout"
            class="card card-link card-link-pop text-info p-5 d-flex align-items-center justify-content-center hover-shadow">
            <div class="card-stamp">
                <div class="card-stamp-icon bg-info">
                    <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-user-share">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                        <path d="M16 22l5 -5" />
                        <path d="M21 21.5v-4.5h-4.5" />
                    </svg>
                </div>
            </div>
            <div class="avatar avatar-md mb-3 bg-info-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-share">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                    <path d="M16 22l5 -5" />
                    <path d="M21 21.5v-4.5h-4.5" />
                </svg>
            </div>
            <strong class="mt-2">លិខិតផ្ទេរចេញ</strong>
        </a>
    </div>

    <!-- Card 3 -->
    <div class="col-lg-3" data-aos="zoom-in" data-aos-delay="700">
        <a href="" data-bs-toggle="modal" data-bs-target="#resign"
            class="card card-link card-link-pop text-danger p-5 d-flex align-items-center justify-content-center hover-shadow">
            <div class="card-stamp">
                <div class="card-stamp-icon bg-danger">
                    <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-user-cancel">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                        <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                        <path d="M17 21l4 -4" />
                    </svg>
                </div>
            </div>
            <div class="avatar avatar-md mb-3 bg-danger-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-cancel">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                    <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    <path d="M17 21l4 -4" />
                </svg>
            </div>
            <strong class="mt-2">លិខិតលារឈប់</strong>
        </a>
    </div>

    <!-- Card 4 -->
    <div class="col-lg-3" data-aos="zoom-in" data-aos-delay="800">
        <a href="" data-bs-toggle="modal" data-bs-target="#backwork"
            class="card card-link card-link-pop text-success p-5 d-flex align-items-center justify-content-center hover-shadow">
            <div class="card-stamp">
                <div class="card-stamp-icon bg-success">
                    <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                        <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                    </svg>
                </div>
            </div>
            <div class="avatar avatar-md mb-3 bg-success-lt">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                </svg>
            </div>
            <strong class="mt-2">លិខិតបន្តការងារ</strong>
        </a>
    </div>
</div>

<?php include('src/common/footer.php'); ?>