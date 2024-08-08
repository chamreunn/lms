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

$title = "ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ";
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
ob_start();
?>
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mx-1">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo $title ?? "" ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <!-- <input type="search" class="form-control d-inline-block w-9 me-3" placeholder="ស្វែងរកនាយកដ្ឋាន…" id="customSearch" /> -->
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle d-none d-sm-inline-block" href="/elms/apply-leave" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M16 19h6" />
                                    <path d="M19 16v6" />
                                </svg>
                                <span>បង្កើតសំណើ</span>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a data-bs-toggle="modal" data-bs-target="#apply-leave" class="dropdown-item" href="/elms/apply-leave">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                    <path d="M16 3v4" />
                                                    <path d="M8 3v4" />
                                                    <path d="M4 11h16" />
                                                    <path d="M19 22v.01" />
                                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                                </svg>
                                            </span>
                                            ច្បាប់ឈប់សម្រាក
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-late-in" class="dropdown-item" href="/elms/late_in_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                                                    <path d="M19 22v-6" />
                                                    <path d="M22 19l-3 -3l-3 3" />
                                                    <path d="M12 7v5l2.5 2.5" />
                                                </svg>
                                            </span>
                                            លិខិតចូលយឺត
                                        </a>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-late-out" class="dropdown-item" href="/elms/late_out_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                                                    <path d="M16 19h6" />
                                                    <path d="M19 16v6" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                            </span>
                                            លិខិតចេញយឺត
                                        </a>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-left-before" class="dropdown-item" href="/elms/late_out_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                                                    <path d="M16 22l5 -5" />
                                                    <path d="M21 21.5v-4.5h-4.5" />
                                                    <path d="M12 7v5l2 2" />
                                                </svg>
                                            </span>
                                            លិខិតចេញមុន
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-mission" class="dropdown-item" href="/elms/rejected">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                                            បេសកកម្ម
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a href="/elms/apply-leave" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                            <div class="dropdown-menu">
                                <div class="dropdown-menu-columns">
                                    <div class="dropdown-menu-column">
                                        <a data-bs-toggle="modal" data-bs-target="#apply-leave" class="dropdown-item" href="/elms/apply-leave">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                    <path d="M16 3v4" />
                                                    <path d="M8 3v4" />
                                                    <path d="M4 11h16" />
                                                    <path d="M19 22v.01" />
                                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                                </svg>
                                            </span>
                                            ច្បាប់ឈប់សម្រាក
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-late-in" class="dropdown-item" href="/elms/late_in_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                                                    <path d="M19 22v-6" />
                                                    <path d="M22 19l-3 -3l-3 3" />
                                                    <path d="M12 7v5l2.5 2.5" />
                                                </svg>
                                            </span>
                                            លិខិតចូលយឺត
                                        </a>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-late-out" class="dropdown-item" href="/elms/late_out_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                                                    <path d="M16 19h6" />
                                                    <path d="M19 16v6" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                            </span>
                                            លិខិតចេញយឺត
                                        </a>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-left-before" class="dropdown-item" href="/elms/late_out_request">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                                                    <path d="M16 22l5 -5" />
                                                    <path d="M21 21.5v-4.5h-4.5" />
                                                    <path d="M12 7v5l2 2" />
                                                </svg>
                                            </span>
                                            លិខិតចេញមុន
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a data-bs-toggle="modal" data-bs-target="#apply-mission" class="dropdown-item" href="/elms/rejected">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                                            បេសកកម្ម
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
?>
<?php
// Assuming $requests is an array of all leave requests
$requestsPerPage = 10; // Number of requests to display per page
$totalRequests = count($requests);
$totalPages = ceil($totalRequests / $requestsPerPage);

// Get the current page from the URL, default to page 1 if not set
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); // Ensure the current page is within bounds

// Calculate the starting index of the requests to display
$startIndex = ($currentPage - 1) * $requestsPerPage;
$paginatedRequests = array_slice($requests, $startIndex, $requestsPerPage);

?>
<div class="card rounded-3">
    <div class="card-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="header-title mb-0 text-muted"><?= $title ?></h4>
        </div>
    </div>

    <div class="card-body border-bottom">
        <form class="mb-0" action="/elms/leave-requests" method="POST">
            <div class="row align-items-center">
                <div class="col-lg-3 mb-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <path d="M4 11h16"></path>
                                <path d="M11 15h1"></path>
                                <path d="M12 15v3"></path>
                            </svg>
                        </span>
                        <input class="form-control" placeholder="កាលបរិច្ឆេទចាប់ពី" type="text" name="start_date" id="start_date" autocomplete="off" />
                    </div>
                </div>
                <div class="col-lg-3 mb-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"></path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <path d="M4 11h16"></path>
                                <path d="M11 15h1"></path>
                                <path d="M12 15v3"></path>
                            </svg>
                        </span>
                        <input class="form-control" placeholder="ដល់កាលបរិច្ឆេទ" type="text" name="end_date" id="date" autocomplete="off" />
                    </div>
                </div>
                <div class="col-lg-3 mb-3">
                    <select type="text" class="form-select" id="select-status" name="status" tabindex="-1">
                        <option class="text-muted" selected disabled>ស្ថានភាព</option>
                        <option value="Pending" data-custom-properties="&lt;span class=&quot;badge bg-warning&quot;">Pending</option>
                        <option value="Approved" data-custom-properties="&lt;span class=&quot;badge bg-success&quot;">Approved</option>
                        <option value="Rejected" data-custom-properties="&lt;span class=&quot;badge bg-danger&quot;">Rejected</option>
                        <option value="Canceled" data-custom-properties="&lt;span class=&quot;badge bg-secondary&quot;">Canceled</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <button type="submit" class="btn w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                        <span>ស្វែងរក</span>
                    </button>
                </div>
                <div class="col mb-3">
                    <a href="/elms/leave-requests" type="reset" class="btn w-100 btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-rotate-clockwise">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                        </svg>
                        <span>សម្អាត</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th>ឈ្មោះមន្ត្រី</th>
                    <th class="d-none d-sm-table-cell">ប្រភេទច្បាប់</th>
                    <th class="d-none d-sm-table-cell">ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">ដល់កាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">រយៈពេល</th>
                    <th class="d-none d-sm-table-cell">មូលហេតុ</th>
                    <th>សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paginatedRequests)) : ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានសំណើនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោម ឬស្តាំដៃខាងលើ</p>
                            <div class="dropdown">
                                <a class="btn btn-primary dropdown-toggle d-none d-sm-inline-block" href="/elms/apply-leave" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M16 19h6" />
                                        <path d="M19 16v6" />
                                    </svg>
                                    <span>បង្កើតសំណើ</span>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a data-bs-toggle="modal" data-bs-target="#apply-leave" class="dropdown-item" href="/elms/apply-leave">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M19 22v.01" />
                                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                                    </svg>
                                                </span>
                                                ច្បាប់ឈប់សម្រាក
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-late-in" class="dropdown-item" href="/elms/late_in_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                                                        <path d="M19 22v-6" />
                                                        <path d="M22 19l-3 -3l-3 3" />
                                                        <path d="M12 7v5l2.5 2.5" />
                                                    </svg>
                                                </span>
                                                លិខិតចូលយឺត
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-late-out" class="dropdown-item" href="/elms/late_out_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                                                        <path d="M16 19h6" />
                                                        <path d="M19 16v6" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </span>
                                                លិខិតចេញយឺត
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-left-before" class="dropdown-item" href="/elms/late_out_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                                                        <path d="M16 22l5 -5" />
                                                        <path d="M21 21.5v-4.5h-4.5" />
                                                        <path d="M12 7v5l2 2" />
                                                    </svg>
                                                </span>
                                                លិខិតចេញមុន
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-mission" class="dropdown-item" href="/elms/rejected">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                                                បេសកកម្ម
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a href="/elms/apply-leave" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="dropdown" aria-expanded="false">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                </a>
                                <div class="dropdown-menu">
                                    <div class="dropdown-menu-columns">
                                        <div class="dropdown-menu-column">
                                            <a data-bs-toggle="modal" data-bs-target="#apply-leave" class="dropdown-item" href="/elms/apply-leave">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M19 22v.01" />
                                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                                    </svg>
                                                </span>
                                                ច្បាប់ឈប់សម្រាក
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-late-in" class="dropdown-item" href="/elms/late_in_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                                                        <path d="M19 22v-6" />
                                                        <path d="M22 19l-3 -3l-3 3" />
                                                        <path d="M12 7v5l2.5 2.5" />
                                                    </svg>
                                                </span>
                                                លិខិតចូលយឺត
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-late-out" class="dropdown-item" href="/elms/late_out_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                                                        <path d="M16 19h6" />
                                                        <path d="M19 16v6" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </span>
                                                លិខិតចេញយឺត
                                            </a>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-left-before" class="dropdown-item" href="/elms/late_out_request">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                                                        <path d="M16 22l5 -5" />
                                                        <path d="M21 21.5v-4.5h-4.5" />
                                                        <path d="M12 7v5l2 2" />
                                                    </svg>
                                                </span>
                                                លិខិតចេញមុន
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a data-bs-toggle="modal" data-bs-target="#apply-mission" class="dropdown-item" href="/elms/rejected">
                                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                                                បេសកកម្ម
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php else : ?>
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
                            <td class="p-0">
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
                                <?php if ($request['status'] == 'Approved') : ?>
                                    <a href="#" title="ទាញយក" data-bs-placement="right" class="icon delete-btn text-success" data-bs-toggle="modal" data-bs-target="#download<?= htmlspecialchars($request['id']) ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 11l5 5l5 -5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <a href="#" class="d-sm-none" title="លុប" data-bs-toggle="collapse" data-bs-target="#collapseRequest<?= $request['id'] ?>" aria-expanded="false" aria-controls="collapseRequest<?= $request['id'] ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
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

                        <!-- download  -->
                        <div class="modal modal-blur fade" id="download<?= htmlspecialchars($request['id']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title mb-0">ទាញយក</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3 mt-0">
                                            <label class="form-label fw-bolder">ឈ្មោះរបាយការណ៍<span class="text-danger fw-bold mx-1">*</span></label>
                                            <input type="text" class="form-control" name="filename" value="ច្បាប់ឈប់សម្រាក">
                                        </div>
                                        <label class="form-label fw-bolder">ទាញយកជា<span class="text-danger fw-bold mx-1">*</span></label>
                                        <div class="form-selectgroup-boxes row">
                                            <div class="col-lg-12 mb-3">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="report-type" value="<?= $request['id'] ?>" class="form-selectgroup-input" checked="">
                                                    <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1" style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-word">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2" />
                                                                    <path d="M9 12l1.333 5l1.667 -4l1.667 4l1.333 -5" />
                                                                </svg>
                                                                <span class="mx-1">WORD</span>
                                                            </span>
                                                            <span class="d-block text-secondary"></span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                            <div class="col-lg-12">
                                                <label class="form-selectgroup-item">
                                                    <input type="radio" name="report-type" value="<?= $request['id'] ?>" class="form-selectgroup-input">
                                                    <span class="form-selectgroup-label d-flex align-items-center p-3">
                                                        <span class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </span>
                                                        <span class="form-selectgroup-label-content">
                                                            <span class="form-selectgroup-title strong mb-1" style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-type-pdf">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
                                                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6" />
                                                                    <path d="M17 18h2" />
                                                                    <path d="M20 15h-3v6" />
                                                                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z" />
                                                                </svg>
                                                                <span class="mx-1">PDF</span>
                                                            </span>
                                                            <span class="d-block text-secondary"></span>
                                                        </span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                </div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-danger ms-auto w-100">ទាញយក</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4" hidden>
                            <div id="page-contents<?= $request['id'] ?>" class="card invoice-preview-card" style="height: 100vh">
                                <div class="card-body">
                                    <div class="page-container hidden-on-narrow">
                                        <div class="pdf-page size-a4">
                                            <div class="pdf-header">
                                                <center class="invoice-number" style="font-family: khmer mef2;color: #2F5496;font-size: 20px; margin-top: -2px;">ព្រះរាជាណាចក្រកម្ពុជា<br>
                                                    ជាតិ សាសនា ព្រះមហាក្សត្រ
                                                </center>
                                            </div>
                                            <div class="page-body">
                                                <div class="mb-xl-0 mb-2">
                                                    <div class="for" style="font-family: khmer mef2; margin-top: -20px; font-size:20px; position: relative; color: #2F5496;">
                                                        <span class="company-logo">
                                                            <img src="public/img/icons/brands/logo2.png" class="mb-3" style="width: 168px; padding-left: 50px" />
                                                        </span>
                                                        <p style="font-size: 14px; margin-bottom: 0;">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                                                        <p style="font-size: 14px; text-indent: 40px; margin-bottom: 0; padding-bottom: 0; line-height:30px;">អង្គភាពសវនកម្មផ្ទៃក្នុង <br>
                                                        <p style="font-size: 14px; text-indent: 25px;">លេខ:.......................អ.ស.ផ.</p>
                                                        </p>
                                                    </div>
                                                </div>
                                                <center style="text-align: center; font-family: khmer mef2; font-size: 19px; margin-top: -50px" class="mb-3">
                                                    សូមគោរពជូន
                                                </center>
                                                <center style="text-align: center; font-family: khmer mef2; font-size: 19px;" class="mb-3">
                                                    លោកប្រធាន<?= $request['department_name'] ?>
                                                </center>
                                                <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;"><strong class="h3">កម្មវត្ថុ៖</strong> សំណើសុំច្បាប់ឈប់សម្រាកចំនួន <?= translateDateToKhmer($request['num_date'], 'd') ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['end_date'], 'Y') ?></p>
                                                <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;"><strong class="h3">មូលហេតុ៖</strong> <?= $request['remarks'] ?> ។</p>
                                                <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;">
                                                    តបតាមកម្មវត្ថុខាងលើ ខ្ញុំសូមគោរពជម្រាបជូន លោកប្រធាននាយកដ្ឋាន មេត្តាជ្រាបដ៏ខ្ពង់ខ្ពស់ថា៖ខ្ញុំបាទ/ នាងខ្ញុំឈ្មោះ<?= $request['user_name'] ?>កើតថ្ងៃទី <?= translateDateToKhmer($request['dob'], 'd') ?> ខែ <?= translateDateToKhmer($request['dob'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['dob'], 'Y') ?> មានតួនាទីជា <?= $request['position_name'] ?> នៃ <?= $request['office_name'] ?> នៃ <?= $request['department_name'] ?> ខ្ញុំសូមគោរពស្នើសុំការអនុញ្ញាតច្បាប់ចំនួន <?= translateDateToKhmer($request['num_date'], 'd') ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['end_date'], 'Y') ?>
                                                    ដូចមូលហេតុ និងកាលបរិច្ឆេទក្នុងកម្មវត្ថុខាងលើ។
                                                </p>
                                                <p style="font-family: khmer mef1; font-size:16px; text-align:justify; text-indent: 50px;">
                                                    សេចក្តីដូចបានជម្រាបជូនខាងលើ សូម លោកប្រធាននាយកដ្ឋាន មេត្តាពិនិត្យ និងសម្រេចអនុញ្ញាតច្បាប់ដោយក្តីអនុគ្រោះ។
                                                </p>
                                                <p style="font-family: khmer mef1; font-size:16px; text-align:justify; text-indent: 50px;">
                                                    សូម <b>លោកប្រធាននាយកដ្ឋាន </b> មេត្តាទទួលនូវការគោរពដ៏ខ្ពង់ខ្ពស់អំពីខ្ញុំ ។
                                                </p>
                                                <div class="row">
                                                    <?php foreach ($request['hoffice'] as $approval) : ?>
                                                        <div class="col" style="font-family: khmer mef1; font-size:16px; line-height: 30px; text-align:justify; text-align:center;">
                                                            <p style="margin-bottom: 0;">គួរឯកភាព, គោរពស្នើសុំការសម្រេចពី</p>
                                                            <p style="margin-bottom: 0;">លោកប្រធានការិយាល័យ</p>
                                                            <p style="margin-bottom: 5px;">រាជធានីភ្នំពេញ ថ្ងៃទី <?= translateDateToKhmer($approval['updated_at'], 'd') ?> ខែ <?= translateDateToKhmer($approval['updated_at'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'], 'Y') ?></p>
                                                            <h3 style="margin-bottom: 0;"><?= $request['office_name'] ?></h3>
                                                            <h3 class="mb-3">ប្រធាន</h3>
                                                            <img style="width: 200px;" src="public/uploads/signatures/<?= $approval['signature'] ?>" class="mb-3"></img>
                                                            <h3 class="mb-0"><?= $approval['approver_name'] ?></h3>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="col" style="font-family: khmer mef1; font-size:18px; line-height: 30px; text-align:justify; text-align:center;">
                                                        <p style="margin-bottom: 0;">រាជធានីភ្នំពេញ ថ្ងៃទី <?= translateDateToKhmer($approval['created_at'], 'd') ?> ខែ <?= translateDateToKhmer($approval['created_at'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($approval['created_at'], 'Y') ?></p>
                                                        <h3 class="mb-3">មន្ត្រីជំនាញ</h3>
                                                        <img style="width: 200px;" src="public/uploads/signatures/<?= $request['signature'] ?>" class="mb-3"></img>
                                                        <h3 class="mb-0"><?= $request['user_name'] ?></h3>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col">
                                                        <?php foreach ($request['hdepartment'] as $approval) : ?>
                                                            <div class="col ms-auto" style="font-family: khmer mef1; font-size:18px; line-height: 30px; text-align:justify; text-align:center;">
                                                                <p style="margin-bottom: 0;">ឯកភាពតាមសំណើ</p>
                                                                <p style="margin-bottom: 5px;">រាជធានីភ្នំពេញ ថ្ងៃទី <?= translateDateToKhmer($approval['updated_at'], 'd') ?> ខែ <?= translateDateToKhmer($approval['updated_at'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'], 'Y') ?></p>
                                                                <h3 class="mb-3">ប្រធាននាយកដ្ឋាន</h3>
                                                                <img style="width: 200px;" src="public/uploads/signatures/<?= $approval['signature'] ?>" class="mb-3" />
                                                                <h3 class="mb-0"><?= $approval['approver_name'] ?></h3>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                                        <div class="modal-footer bg-light border-top">
                                            <div class="w-100 mt-3">
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
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end rounded-3">
        <ul class="pagination mb-0">
            <!-- Previous Page Link -->
            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                <a class="page-link mx-1" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                    </svg>
                </a>
            </li>
            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <!-- Next Page Link -->
            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php include('src/common/footer.php'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize TomSelect
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: new Date(),
            monthSelectorType: "static",
            nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>',
            prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>',
            locale: 'km' // Set locale to Khmer
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

    });
</script>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));
    });
    // @formatter:on
</script>

<script>
    // Function to print the contents
    function printContents(id) {
        var printContent = document.getElementById('page-contents' + id).innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Function to export the table data to a Word document
    function Export2Word(elementId, filename = '') {
        var preHtml = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office'
              xmlns:w='urn:schemas-microsoft-com:office:word'
              xmlns='http://www.w3.org/TR/REC-html40'>
        <head>
            <meta charset='utf-8'>
            <title>Export HTML To Doc</title>
            <style>
                body { font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>`;
        var postHtml = `</body></html>`;
        var html = preHtml + document.getElementById(elementId).innerHTML + postHtml;

        var blob = new Blob(['\ufeff', html], {
            type: 'application/msword'
        });

        // Create a download link element
        var downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;

            // Setting the file name
            downloadLink.download = filename;

            // Triggering the function
            downloadLink.click();

            // Clean up the URL object after download
            URL.revokeObjectURL(url);
        }

        document.body.removeChild(downloadLink);
    }
</script>