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
    date_default_timezone_set(timezoneId: 'Asia/Bangkok');
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
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">

                </div>
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? ""); ?>
                </h2>
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

<div class="row">
    <?php if (!empty($leaves)): ?>
        <?php foreach ($leaves as $leave): ?>
            <div class="col-12">
                <div class="alert alert-success" role="alert">
                    <!-- SVG for icon -->
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
                    <a href="/elms/view-leave-detail?leave_id=<?= $leave['leave_request_id'] ?>"
                        class="text-success text-decoration-none" data-bs-placement="top" data-bs-toggle="tooltip"
                        title="កាលបរិច្ឆេទចាប់ពី <?= translateDateToKhmer($leave['start_date'], 'd F Y') ?> ដល់ <?= translateDateToKhmer($leave['end_date'], 'd F Y') ?>">ច្បាប់ឈប់សម្រាករបស់អ្នកត្រូវបាន
                        <strong>អនុម័ត</strong></a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- alert leave count  -->
<?php if ($requestscount > 0): ?>
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
                <a href="/elms/pending">
                    អ្នកមានច្បាប់ដែលមិនទាន់អនុម័តចំនួន <span
                        class="badge bg-red text-red-fg ms-2 fw-bolder"><?= $requestscount ?></span>
                </a>
            </div>
            <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
        </div>
    </div>
<?php endif; ?>

<?php if (count($getMissionToday) > 0): ?>
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
                <div>
                    <a href="/elms/mission">
                        អ្នកមានបេសកម្មនៅថ្ងៃនេះ <span
                            class="badge bg-red text-red-fg ms-2 fw-bolder"><?= count($getMissionToday) ?></span>
                    </a>
                    <ul class="list-unstyled mt-2 mb-0">
                        <?php foreach ($getMissionToday as $mission): ?>
                            <li>
                                <!-- <strong>បេសកម្ម:</strong> <?= $mission['mission_name'] ?><br> -->
                                <strong>កាលបរិច្ឆេទចាប់ផ្តើម:</strong> <?= $mission['start_date'] ?><br>
                                <strong>កាលបរិច្ឆេទបញ្ចប់:</strong> <?= $mission['end_date'] ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
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
                <a href="/elms/dofficeLeave" class="card card-sm">
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
                                        class="text-success"><?= $request['end_date'] ?></strong></small>
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
    <div class="col-lg-3 mb-3">
        <a href="" data-bs-toggle="modal" data-bs-target="#office-apply-leave"
            class="card card-link card-link-pop text-primary p-5 d-flex align-items-center justify-content-center">
            <div class="avatar mb-3 bg-primary-lt">
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

    <div class="col-lg-3 mb-3">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-late-in"
            class="card card-link card-link-pop text-success p-5 d-flex align-items-center justify-content-center">
            <div class="avatar mb-3 bg-success-lt">
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

    <div class="col-lg-3 mb-3">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-late-out"
            class="card card-link card-link-pop p-5 text-warning d-flex align-items-center justify-content-center">
            <div class="avatar mb-3 bg-warning-lt">
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

    <div class="col-lg-3 mb-3">
        <a href="" data-bs-toggle="modal" data-bs-target="#apply-leaveearly"
            class="card card-link card-link-pop p-5 text-warning d-flex align-items-center justify-content-center">
            <div class="avatar mb-3 bg-warning-lt">
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

    <div class="col-lg-3 mb-3">
        <a href="" data-bs-toggle="modal" data-bs-target="#mission"
            class="card card-link card-link-pop p-5 d-flex align-items-center justify-content-center text-indigo">
            <div class="avatar mb-3 bg-indigo-lt">
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
            <strong>បេសកកម្ម</strong>
        </a>
    </div>
</div>

<?php include('src/common/footer.php'); ?>
<!-- Modal Apply Leave -->
<div class="modal modal-blur fade" id="office-apply-leave" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/dof-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control date-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <!-- <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                        </span> -->
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="attachment"
                                        onchange="toggleFileInput(this, 'attachmentFile')">
                                    <label class="form-check-label cursor-pointer" for="attachment">
                                        ឯកសារភ្ជាប់
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mb-3" id="attachmentFile" style="display: none;">
                                <label id="attachmentDisplayName" for="attachment_file"
                                    class="btn w-100 text-start p-3 bg-light">
                                    ឯកសារភ្ជាប់
                                </label>
                                <input type="file" name="attachment" id="attachment_file" class="form-control" hidden
                                    onchange="displayFileName('attachment_file', 'attachmentDisplayName')" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">
                        <span>បង្កើតសំណើ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- timezone  -->
<script>
    function updateDateTime() {
        const clockElement = document.getElementById('real-time-clock');
        const currentTime = new Date();

        // Define Khmer arrays for days of the week and months.
        const daysOfWeek = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
        const dayOfWeek = daysOfWeek[currentTime.getDay()];

        const months = ['មករា', 'កុម្ភៈ', 'មិនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        const month = months[currentTime.getMonth()];

        const day = currentTime.getDate();
        const year = currentTime.getFullYear();

        // Calculate and format hours, minutes, seconds, and time of day in Khmer.
        let hours = currentTime.getHours();
        let period;

        if (hours >= 5 && hours < 12) {
            period = 'ព្រឹក'; // Khmer for AM (morning)
        } else if (hours >= 12 && hours < 17) {
            period = 'រសៀល'; // Khmer for afternoon
        } else if (hours >= 17 && hours < 20) {
            period = 'ល្ងាច'; // Khmer for evening
        } else {
            period = 'យប់'; // Khmer for night
        }

        hours = hours % 12 || 12;
        const minutes = currentTime.getMinutes().toString().padStart(2, '0');
        const seconds = currentTime.getSeconds().toString().padStart(2, '0');

        // Construct the date and time string in the desired Khmer format.
        const dateTimeString = `${dayOfWeek}, ${day} ${month} ${year} ${hours}:${minutes}:${seconds} ${period}`;
        clockElement.textContent = dateTimeString;
    }

    // Update the date and time every second (1000 milliseconds).
    setInterval(updateDateTime, 1000);

    // Initial update.
    updateDateTime();
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Initialize TomSelect
        new TomSelect("#leave_type", {
            copyClassesToDropdown: false,
            dropdownClass: "dropdown-menu ts-dropdown",
            optionClass: "dropdown-item",
            controlInput: "<input>",
            render: {
                item: function (data, escape) {
                    return data.customProperties ?
                        `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                        `<div>${escape(data.text)}</div>`;
                },
                option: function (data, escape) {
                    return data.customProperties ?
                        `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                        `<div>${escape(data.text)}</div>`;
                },
            },
        });

        document.getElementById('leave_type').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var leaveTypeName = selectedOption.getAttribute('data-leave-name');
            document.getElementById('leave_type_name').value = leaveTypeName;
        });

        // Initialize Litepicker for dates
        const litepickerOptions = {
            singleMode: true,
            format: "YYYY-MM-DD",
            plugins: ['multiselect'],
            // minDate: new Date().toISOString().split('T')[0], // Prevent selection of past dates
            lang: 'kh', // Set language to Khmer
            buttonText: {
                previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        };

        new Litepicker({
            element: document.getElementById("lstart_date"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("lend_date"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("start_date"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("date"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("end_date"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("lateindate"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("lateoutdate"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("leftbefore"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("mission_start"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("mission_end"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("emission_start"),
            ...litepickerOptions
        });
        new Litepicker({
            element: document.getElementById("emission_end"),
            ...litepickerOptions
        });

        // Initialize Flatpickr for time input
        flatpickr("#time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i", // Time in HH:MM format
            time_24hr: false,
            defaultHour: 12,
            defaultMinute: 0,
            locale: 'km' // Set locale to Khmer for time as well
        });

        // Initial setup to ensure signature file input is visible if checkbox is checked
        const signatureCheckbox = document.getElementById('signature');
        if (signatureCheckbox.checked) {
            document.getElementById('signatureFile').style.display = 'block';
        }
    });

    function toggleFileInput(checkbox, fileInputId) {
        var fileInput = document.getElementById(fileInputId);
        fileInput.style.display = checkbox.checked ? 'block' : 'none';
    }

    function displayFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const fileNameLabel = document.getElementById(labelId);
        fileNameLabel.textContent = input.files[0] ? input.files[0].name : '';
    }
</script>