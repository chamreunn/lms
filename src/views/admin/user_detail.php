<?php
$title = $userDetails['khmer_name'];
include('src/common/header.php');
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

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div>ថ្ងៃនេះ</div>
                </div>
                <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="d-flex align-items-center text-center mb-0">
                    <img class="avatar avatar-xl" style="object-fit: cover;" src="<?= $userDetails['profile_picture'] ?>" alt="Profile Picture">
                    <div class="col-sm-12 mx-3 mb-0 text-start">
                        <h4 class="text-primary"><?= $userDetails['khmer_name'] ?></h4>
                        <p class="text-muted"><?= $userDetails['email'] ?></p>
                        <span class="badge <?= $userDetails['pcolor'] ?>"><?= $userDetails['position_name'] ?></span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12">
                <div class="datagrid">
                    <div class="datagrid-item">
                        <div class="datagrid-title">នាយកដ្ឋាន</div>
                        <div class="datagrid-content text-primary fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-skyscraper">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21l18 0" />
                                <path d="M5 21v-14l8 -4v18" />
                                <path d="M19 21v-10l-6 -4" />
                                <path d="M9 9l0 .01" />
                                <path d="M9 12l0 .01" />
                                <path d="M9 15l0 .01" />
                                <path d="M9 18l0 .01" />
                            </svg>
                            <?= $userDetails['department_name'] ?>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">ការិយាល័យ</div>
                        <div class="datagrid-content text-primary fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-building-estate">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 21h18" />
                                <path d="M19 21v-4" />
                                <path d="M19 17a2 2 0 0 0 2 -2v-2a2 2 0 1 0 -4 0v2a2 2 0 0 0 2 2z" />
                                <path d="M14 21v-14a3 3 0 0 0 -3 -3h-4a3 3 0 0 0 -3 3v14" />
                                <path d="M9 17v4" />
                                <path d="M8 13h2" />
                                <path d="M8 9h2" />
                            </svg>
                            <?= $userDetails['office_name'] ?>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">ទំនាក់ទំនង</div>
                        <div class="datagrid-content text-primary fw-bold">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-scan">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 9a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                <path d="M4 8v-2a2 2 0 0 1 2 -2h2" />
                                <path d="M4 16v2a2 2 0 0 0 2 2h2" />
                                <path d="M16 4h2a2 2 0 0 1 2 2v2" />
                                <path d="M16 20h2a2 2 0 0 0 2 -2v-2" />
                                <path d="M8 16a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2" />
                            </svg>
                            <span><?= $userDetails['phone_number'] ?></span>
                        </div>
                    </div>
                    <div class="datagrid-item">
                        <div class="datagrid-title">ថ្ងៃខែឆ្នាំកំណើត</div>
                        <div class="datagrid-content text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-cake">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 20h18v-8a3 3 0 0 0 -3 -3h-12a3 3 0 0 0 -3 3v8z" />
                                <path d="M3 14.803c.312 .135 .654 .204 1 .197a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1a2.4 2.4 0 0 0 2 -1a2.4 2.4 0 0 1 2 -1a2.4 2.4 0 0 1 2 1a2.4 2.4 0 0 0 2 1c.35 .007 .692 -.062 1 -.197" />
                                <path d="M12 4l1.465 1.638a2 2 0 1 1 -3.015 .099l1.55 -1.737z" />
                            </svg>
                            <?= $userDetails['date_of_birth'] ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-card">
    <div class="col-12 mb-3">
        <div class="row row-cards">
            <!-- All Leave Requests Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-primary">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-user">
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
                                    <?= $getleavecounts . "ច្បាប់" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Letters Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-success">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
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
                                    <?= $getovertimeincount . "លិខិត" ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-warning">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
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
                                    <?= $getovertimeincount . "លិខិត" ?? "" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Likes Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-stamp">
                        <div class="card-stamp-icon bg-indigo">
                            <!-- Download SVG icon from http://tabler-icons.io/i/bell -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
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
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3 p-2">
    <ul class="nav nav-pills mb-0" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                    <path d="M11 15h1" />
                    <path d="M12 15v3" />
                </svg>
                <span class="fw-bolder">ច្បាប់ឈប់សម្រាក</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                    <path d="M19 22v-6" />
                    <path d="M22 19l-3 -3l-3 3" />
                    <path d="M12 7v5l2.5 2.5" />
                </svg>
                <span class="fw-bolder">លិខិតចូលយឺត​</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                    <path d="M16 19h6" />
                    <path d="M19 16v6" />
                    <path d="M12 7v5l3 3" />
                </svg>
                <span class="fw-bolder">លិខិតចេញយឺត</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                    <path d="M16 22l5 -5" />
                    <path d="M21 21.5v-4.5h-4.5" />
                    <path d="M12 7v5l2 2" />
                </svg>
                <span class="fw-bolder">លិខិតចេញមុន</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-share">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h16" />
                    <path d="M16 22l5 -5" />
                    <path d="M21 21.5v-4.5h-4.5" />
                </svg>
                <span class="fw-bolder">បេសកកម្ម</span>
            </button>
        </li>
    </ul>
</div>

<div class="card">
    <div class="card-body">
        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                <div class="header">
                    <h3 class="mb-3">ច្បាប់ឈប់សម្រាក</h3>
                </div>
                <?php if (empty($requests)) : ?>
                    <div class="text-center">
                        <img src="public/img/icons/svgs/empty.svg" alt="">
                        <p class="text-muted">មិនទាន់មានសំណើច្បាប់ឈប់សម្រាក។</p>
                    </div>
                <?php else : ?>
                    <table class="table table-vcenter table-bordered text-nowrap datatable w-100">
                        <thead>
                            <tr>
                                <th>ឈ្មោះមន្ត្រី</th>
                                <th class="d-none d-sm-table-cell">ប្រភេទច្បាប់</th>
                                <th class="d-none d-sm-table-cell">ចាប់ពីកាលបរិច្ឆេទ</th>
                                <th class="d-none d-sm-table-cell">ដល់កាលបរិច្ឆេទ</th>
                                <th class="d-none d-sm-table-cell">រយៈពេល</th>
                                <th class="d-none d-sm-table-cell">មូលហេតុ</th>
                                <th class="text-center">សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $request) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <img src="<?= $request['user_profile'] ?>" class="avatar" style="object-fit: cover;" alt="">
                                            <div class="d-flex flex-column mx-2">
                                                <h4 class="mx-0 mb-1 text-primary">
                                                    <?= $request['user_name'] ?>
                                                    <strong class="badge
                                    <?= $request['status'] == 'Pending' ? 'bg-warning' : '' ?>
                                    <?= $request['status'] == 'Approved' ? 'bg-success' : '' ?>
                                    <?= $request['status'] == 'Rejected' ? 'bg-danger' : '' ?>
                                    <?= $request['status'] == 'Cancelled' ? 'bg-secondary' : '' ?> me-1">
                                                        <?= htmlspecialchars($request['status']) ?>
                                                    </strong>
                                                </h4>
                                                <span class="text-muted"><?= $request['user_email'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        <div class="badge <?= htmlspecialchars($request['color']) ?>"><?= htmlspecialchars($request['leave_type_name']) ?></div>
                                    </td>
                                    <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($request['start_date'], 'D,j F Y') ?></td>
                                    <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($request['end_date'], 'D,j F Y') ?></td>
                                    <td class="d-none d-sm-table-cell"><?= $request['duration'] ?>ថ្ងៃ</td>
                                    <td class="d-none d-sm-table-cell">
                                        <span class="text-truncate" data-bs-placement="top" data-bs-toggle="tooltip" title="<?= htmlspecialchars($request['remarks']) ?>"><?= htmlspecialchars($request['remarks']) ?></span>
                                    </td>
                                    <td class="p-0 text-center">
                                        <a href="/elms/view-leave-detail?leave_id=<?= htmlspecialchars($request['id']) ?>" title="ពិនិត្យមើល" data-bs-placement="auto" data-bs-toggle="tooltip" class="icon me-0 edit-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                        <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= htmlspecialchars($request['id']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                            </svg>
                                        </a>
                                        <a href="#" class="d-sm-none" title="បង្ហាញបន្ថែម" data-bs-toggle="collapse" data-bs-target="#collapseRequest<?= $request['id'] ?>" aria-expanded="false" aria-controls="collapseRequest<?= $request['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dots">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>

                                <tr class="d-sm-none">
                                    <td colspan="7" class="p-0">
                                        <div class="collapse" id="collapseRequest<?= $request['id'] ?>">
                                            <table class="table mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <strong>ប្រភេទច្បាប់ : </strong>
                                                            <div class="badge <?= htmlspecialchars($request['color']) ?>"><?= htmlspecialchars($request['leave_type']) ?></div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>ចាប់ពីកាលបរិច្ឆេទ : </strong>
                                                            <span> <?= translateDateToKhmer($request['start_date'], 'D,j F Y') ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>ដល់កាលបរិច្ឆេទ : </strong>
                                                            <span><?= translateDateToKhmer($request['end_date'], 'D,j F Y') ?></span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>រយៈពេល : </strong>
                                                            <span><?= $request['num_date'] ?>ថ្ងៃ</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <strong>មូលហេតុ : </strong>
                                                            <span class="text-truncate" data-bs-placement="top" data-bs-toggle="tooltip" title="<?= htmlspecialchars($request['remarks']) ?>"><?= htmlspecialchars($request['remarks']) ?></span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>

                                <!-- delete  -->
                                <div class="modal modal-blur fade" id="deleteModal<?= htmlspecialchars($request['id']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-status bg-danger"></div>
                                            <form action="/elms/leave-delete" method="POST">
                                                <div class="modal-body text-center py-4 mb-0">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 9v4"></path>
                                                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                                        <path d="M12 16h.01"></path>
                                                    </svg>
                                                    <h5 class="modal-title fw-bold text-danger">លុបសំណើច្បាប់</h5>
                                                    <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបសំណើច្បាប់នេះ?</p>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <div class="w-100">
                                                        <div class="row">
                                                            <div class="col">
                                                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                            </div>
                                                            <div class="col">
                                                                <button type="submit" class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                <div class="header">
                    <h3 class="mb-3">លិខិតចូលយឺត</h3>
                </div>
                <?php if (empty($getlatein)) : ?>
                    <div class="text-center">
                        <img src="public/img/icons/svgs/empty.svg" alt="">
                        <p class="text-muted">មិនទាន់មានសំណើច្បាប់ឈប់សម្រាក។</p>
                    </div>
                <?php else : ?>
                    <table id="officeTable" class="table table-vcenter table-bordered text-nowrap datatable w-100">
                        <thead>
                            <tr>
                                <th>ឈ្មោះមន្រ្តី</th>
                                <th>កាលបរិច្ឆេទយឺត</th>
                                <th>ម៉ោង</th>
                                <th>រយៈពេលយឺត</th>
                                <th>មូលហេតុ</th>
                                <th>ស្នើនៅ</th>
                                <th>ស្ថានភាព</th>
                                <th>សកម្មភាព</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($getlatein as $getlate) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex">
                                            <img src="<?= $getlate['profile'] ?>" class="avatar" style="object-fit: cover;" alt="">
                                            <div class="d-flex flex-column mx-2">
                                                <h4 class="mx-0 mb-1 text-primary"><?= $getlate['khmer_name'] ?></h4>
                                                <span class="text-muted"><?= $getlate['email'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $getlate['date'] ?></td>
                                    <td><?= $getlate['late_in'] ?></td>
                                    <td><?= $getlate['late'] ?> នាទី</td>
                                    <td><?= $getlate['reasons'] ?></td>
                                    <td><?= $getlate['created_at'] ?></td>
                                    <td>
                                        <div class="badge <?php echo $a = $getlate['status'] == 'Pending' ? 'bg-warning' : ($getlate['status'] == 'Approved' ? 'bg-success' : 'bg-danger'); ?> "><?= $getlate['status']; ?></div>
                                    </td>
                                    <td>
                                        <?php if ($getlate['status'] == 'Approved') : ?>
                                            <a href="#" onclick="printContents(<?= $getlate['id'] ?>)" class="icon me-2 edit-btn text-danger" data-bs-toggle="tooltip" title="Print" data-bs-target="#edit<?= $getlate['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                                </svg>
                                            </a>
                                            <a href="#" onclick="Export2Word('page-contents<?= $getlate['id'] ?>', 'word-content<?= $getlate['id'] ?>');" class="icon me-2 edit-btn" data-bs-toggle="tooltip" title="Export to Word">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 11l5 5l5 -5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                            </a>
                                            <a href="#" class="icon me-2 edit-btn text-danger" data-bs-target="#deleteMission<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </a>

                                            <!-- delete  -->
                                            <div class="modal modal-blur fade" id="deleteMission<?= $getlate['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-status bg-danger"></div>
                                                        <form action="/elms/leave-delete" method="POST">
                                                            <div class="modal-body text-center py-4 mb-0">
                                                                <input type="hidden" name="missionId" value="<?= $getlate['id'] ?>">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                    <path d="M12 9v4"></path>
                                                                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                                                    <path d="M12 16h.01"></path>
                                                                </svg>
                                                                <h5 class="modal-title fw-bold text-danger">លុបការចូលយឺត</h5>
                                                                <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបការចូលយឺតនេះ?</p>
                                                            </div>
                                                            <div class="modal-footer bg-light border-top">
                                                                <div class="w-100">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                                        </div>
                                                                        <div class="col">
                                                                            <button type="submit" class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <a href="#" class="icon me-2 edit-btn text-secondary text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                                </svg>
                                            </a>
                                            <a href="#" class="icon me-2 edit-btn text-secondary text-muted">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                    <path d="M7 11l5 5l5 -5" />
                                                    <path d="M12 4l0 12" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4" hidden>
                                    <div id="page-contents<?= $getlate['id'] ?>" class="card invoice-preview-card" style="height: 100vh">
                                        <div class="card-body">
                                            <div class="page-container hidden-on-narrow">
                                                <div class="pdf-page size-a4">
                                                    <div class="pdf-header">
                                                        <center class="invoice-number" style="font-family: khmer mef2;color: #2F5496;font-size: 20px; margin-top: -5px;">ព្រះរាជាណាចក្រកម្ពុជា<br>
                                                            ជាតិ សាសនា ព្រះមហាក្សត្រ
                                                        </center>
                                                    </div>
                                                    <div class="from">
                                                        <div class="mb-xl-0 mb-4">
                                                            <div class="for" style="font-family: khmer mef2; font-size:20px; position: relative; color: #2F5496;">
                                                                <span class="company-logo">
                                                                    <img src="public/img/icons/brands/logo2.png" class="mb-3" style="width: 150px; padding-left: 30px" />
                                                                </span>
                                                                <p style="font-size: 14px;">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                                                                <p style="font-size: 14px; text-indent: 40px; top: 0; line-height:30px;">អង្គភាពសវនកម្មផ្ទៃក្នុង <br>
                                                                <p style="font-size: 14px; text-indent: 25px;">លេខ:.......................អ.ស.ផ.</p>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <center style="text-align: center; font-family: khmer mef2; font-size: 19px;" class="mb-3">
                                                            ពាក្យស្នើសុំបញ្ជាក់ពិការចូលបំពេញការងារយឺតយ៉ាវ
                                                        </center>
                                                        <p style="font-family: khmer mef1; font-size:18px; line-height: 30px; text-align:justify; text-indent: 50px;">
                                                            ខ្ញុំបាទ / នាងខ្ញុំឈ្មោះ <span class="fw-bold"><?= $getlate['khmer_name'] ?></span> មានតួនាទីជា <span class="fw-bold"><?= $getlate['position_name'] ?></span> នៃ <span class="fw-bold"><?= $getlate['department_name'] ?></span> បានមកបំពេញការងារយឺតពេលកំណត់នៅថ្ងៃទី <span class="fw-bold"><?= date('d', strtotime($getlate['date'])) ?> ខែ <span class="fw-bold"><?= translateDateToKhmer($getlate['date'], 'F') ?> ឆ្នាំ <span class="fw-bold"><?= date('Y', strtotime($getlate['date'])) ?>
                                                                    </span> វេលាម៉ោង <span class="fw-bold"><?= $getlate['late_in'] . "នាទី" ?></span> ហើយខ្ញុំសូមបញ្ជាក់ពីមូលហេតុដែលខ្ញុំបាទ/នាងខ្ញុំមកបំពេញការងារយឺតយ៉ាវដោយមូលហេតុ <span class="fw-bolder"><?= $getlate['reasons'] ?></span>។ដូចនេះសូមមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមានខ្ញុំបាទ/នាងខ្ញុំក្នុងបញ្ជីវត្តមានរបស់មន្ត្រីនៃអង្គភាពសវនកម្មផ្ទៃក្នុងនៃ <span class="fw-bolder">អ.ស.ហ.</span>នៅថ្ងៃទី <span class="fw-bold"><?= date('d', strtotime($getlate['date'])) ?> ខែ <span class="fw-bold"><?= translateDateToKhmer($getlate['date'], 'F') ?> ឆ្នាំ <span class="fw-bold"><?= date('Y', strtotime($getlate['date'])) ?> គឺតាមមូលហេតុខាងលើនេះ។
                                                        </p>
                                                        <p style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                            សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ពិនិត្យ និងចាត់ចែងតាមការគួរ
                                                        </p>
                                                        <p style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                            សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ទទួលនូវការរាប់អានពីខ្ញុំ។
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
            <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">...</div>
            <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">...</div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

<!-- card&list view  -->
<script>
    // Function to handle switching views and saving the preference
    function switchView(view) {
        if (view === 'card') {
            document.getElementById('cardView').classList.remove('d-none');
            document.getElementById('listView').classList.add('d-none');
            document.getElementById('cardViewBtn').classList.add('active');
            document.getElementById('listViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'card');
        } else if (view === 'list') {
            document.getElementById('cardView').classList.add('d-none');
            document.getElementById('listView').classList.remove('d-none');
            document.getElementById('listViewBtn').classList.add('active');
            document.getElementById('cardViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'list');
        }
    }

    // Event listeners for buttons
    document.getElementById('cardViewBtn').addEventListener('click', function() {
        switchView('card');
    });

    document.getElementById('listViewBtn').addEventListener('click', function() {
        switchView('list');
    });

    // On page load, set the view based on the user's previous choice
    window.onload = function() {
        const preferredView = localStorage.getItem('preferredView') || 'card';
        switchView(preferredView);
    };
</script>

<!-- khmer number  -->
<script>
    function convertToKhmerNumerals(num) {
        const khmerNumerals = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
        return num.toString().split('').map(digit => khmerNumerals[digit]).join('');
    }

    function updateDateTime() {
        const clockElement = document.getElementById('real-time-clock');
        const currentTime = new Date();

        // Define Khmer arrays for days of the week and months.
        const daysOfWeek = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
        const dayOfWeek = daysOfWeek[currentTime.getDay()];

        const months = ['មករា', 'កុម្ភៈ', 'មិនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        const month = months[currentTime.getMonth()];

        const day = convertToKhmerNumerals(currentTime.getDate());
        const year = convertToKhmerNumerals(currentTime.getFullYear());

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
        const khmerHours = convertToKhmerNumerals(hours);
        const khmerMinutes = convertToKhmerNumerals(currentTime.getMinutes().toString().padStart(2, '0'));
        const khmerSeconds = convertToKhmerNumerals(currentTime.getSeconds().toString().padStart(2, '0'));

        // Construct the date and time string in the desired Khmer format.
        const dateTimeString = `${dayOfWeek}, ${day} ${month} ${year} ${khmerHours}:${khmerMinutes}:${khmerSeconds} ${period}`;
        clockElement.textContent = dateTimeString;
    }

    // Update the date and time every second (1000 milliseconds).
    setInterval(updateDateTime, 1000);

    // Initial update.
    updateDateTime();
</script>