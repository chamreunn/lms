<?php
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
require_once 'src/controllers/LeavetypeController.php';
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    // Define Khmer translations for days and months
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

    // Define Khmer numerals
    $numerals = [
        '0' => '០',
        '1' => '១',
        '2' => '២',
        '3' => '៣',
        '4' => '៤',
        '5' => '៥',
        '6' => '៦',
        '7' => '៧',
        '8' => '៨',
        '9' => '៩'
    ];

    // Get the English day and month names
    $englishDay = date('D', strtotime($date));
    $englishMonth = date('F', strtotime($date));

    // Translate English day and month names to Khmer
    $translatedDay = $days[$englishDay] ?? $englishDay;
    $translatedMonth = $months[$englishMonth] ?? $englishMonth;

    // Format the date in English
    $formattedDate = date($format, strtotime($date));

    // Replace day and month with Khmer
    $translatedDate = str_replace(
        [$englishDay, $englishMonth],
        [$translatedDay, $translatedMonth],
        $formattedDate
    );

    // Replace Arabic numerals with Khmer numerals
    $translatedDate = strtr($translatedDate, $numerals);

    return $translatedDate;
}
?>

<?php if ($gettoday): ?>
    <div class="row row-cards mb-3">
        <div class="col-md-6 col-lg-12">
            <div class="card bg-success-lt">
                <div class="card-stamp">
                    <div class="card-stamp-icon bg-yellow">
                        <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path
                                d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6">
                            </path>
                            <path d="M9 17v1a3 3 0 0 0 6 0v-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="card-body">
                    <h3 class="card-title">
                        <!-- <div id="real-time-clock"></div> -->
                    </h3>
                    <p class="text-secondary">អ្នកមានច្បាប់ថ្ងៃនេះ</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($getPendingCounts)): ?>
    <div class="col">
        <div class="alert alert-warning alert-dismissible mb-3" role="alert">
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
                <a href="/elms/adminpending" class="text-warning">
                    អ្នកមានសំណើសំណើចំនួន <strong
                        class="badge bg-red text-red-fg ms-2 fw-bolder"><?= $getPendingCounts ?></strong> នៅមិនទាន់អនុម័ត
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
                <a href="/elms/adminLeave" class="card card-sm">
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
                                    លិខិតចូលយឺត
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
                                    លិខិតចេញយឺត
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

                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="col">
        <div class="card-header d-flex align-items-center justify-content-between mb-3">
            <h3 class="card-title">បង្កើតសំណើ</h3>
        </div>
    </div>
</div>

<div class="row row-card mb-3">
    <div class="col-lg-3 mb-3" data-aos="zoomin" data-aos-delay="100">
        <a href="" data-bs-toggle="modal" data-bs-target="#do-apply"
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
            <strong>សំណើច្បាប់ឈប់សម្រាក</strong>
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