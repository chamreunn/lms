<?php
$title = "លិខិតចេញយឺត";
ob_start();
?>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#apply-late-out"
                            class="btn btn-primary d-none d-sm-inline-block">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            បន្ថែមថ្មី
                        </a>
                        <a href="/elms/apply-leave" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#apply-late-out" aria-expanded="false">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <line x1="12" y1="5" x2="12" y2="19"></line>
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    // Return an empty string or a default value if the date is null or empty
    if (empty($date)) {
        return '';
    }

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

    // Check if the provided date is valid
    $timestamp = strtotime($date);
    if ($timestamp === false) {
        return ''; // Return an empty string or a default value if the date is invalid
    }

    // Get the English day and month names
    $englishDay = date('D', $timestamp);
    $englishMonth = date('F', $timestamp);

    // Translate English day and month names to Khmer
    $translatedDay = $days[$englishDay] ?? $englishDay;
    $translatedMonth = $months[$englishMonth] ?? $englishMonth;

    // Format the date in English
    $formattedDate = date($format, $timestamp);

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

function convertToKhmerNumerals($number)
{
    $arabicNumerals = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    $khmerNumerals = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

    return str_replace($arabicNumerals, $khmerNumerals, $number);
}
?>
<!-- display office  -->
<div class="col-12">
    <div class="card rounded-3">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
        </div>

        <div class="card-body border-bottom">
            <form class="mb-0" action="/elms/overtimeout" method="POST">
                <div class="row align-items-center">
                    <div class="col-lg-4 mb-3">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path
                                        d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                                    </path>
                                    <path d="M16 3v4"></path>
                                    <path d="M8 3v4"></path>
                                    <path d="M4 11h16"></path>
                                    <path d="M11 15h1"></path>
                                    <path d="M12 15v3"></path>
                                </svg>
                            </span>
                            <input class="form-control date-picker" placeholder="កាលបរិច្ឆេទចាប់ពី" type="text"
                                name="start_date" autocomplete="off" />
                        </div>
                    </div>
                    <div class="col-lg-4 mb-3">
                        <select type="text" class="form-select" id="select-status" name="status" tabindex="-1">
                            <option class="text-muted" selected disabled>ស្ថានភាព</option>
                            <option value="Pending"
                                data-custom-properties="&lt;span class=&quot;badge bg-warning&quot;">Pending</option>
                            <option value="Approved"
                                data-custom-properties="&lt;span class=&quot;badge bg-success&quot;">Approved</option>
                            <option value="Rejected"
                                data-custom-properties="&lt;span class=&quot;badge bg-danger&quot;">Rejected</option>
                            <option value="Canceled"
                                data-custom-properties="&lt;span class=&quot;badge bg-secondary&quot;">Canceled</option>
                        </select>
                    </div>
                    <div class="col mb-3">
                        <button type="submit" class="btn w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                <path d="M21 21l-6 -6" />
                            </svg>
                            <span>ស្វែងរក</span>
                        </button>
                    </div>
                    <div class="col mb-3">
                        <a href="/elms/overtimeout" type="reset" class="btn w-100 btn-danger">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-rotate-clockwise">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                            </svg>
                            <span>សម្អាត</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <?php if (empty($getovertimeout)): ?>
            <div class="text-center mb-3">
                <img src="public/img/icons/svgs/empty.svg" alt="">
                <p>មិនទាន់មានការិយាល័យនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                <a href="#" data-bs-toggle="modal" data-bs-target="#apply-late-out" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <line x1="12" y1="5" x2="12" y2="19" />
                        <line x1="5" y1="12" x2="19" y2="12" />
                    </svg>
                    បន្ថែមថ្មី
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table id="officeTable" class="table card-table table-vcenter text-nowrap datatable">
                    <thead>
                        <tr>
                            <th class="d-none d-xl-table-cell">ល.រ</th>
                            <th>ឈ្មោះមន្រ្តី</th>
                            <th class="d-none d-xl-table-cell">កាលបរិច្ឆេទយឺត</th>
                            <th class="d-none d-xl-table-cell">ម៉ោង</th>
                            <th class="d-none d-xl-table-cell">រយៈពេលយឺត</th>
                            <th class="d-none d-xl-table-cell">មូលហេតុ</th>
                            <th class="d-none d-xl-table-cell">ស្នើនៅ</th>
                            <th>ស្ថានភាព</th>
                            <th class="d-none d-xl-table-cell">សកម្មភាព</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($getovertimeout as $key => $getlate): ?>
                            <tr>
                                <td class="d-none d-xl-table-cell"><?= $key + 1 ?></td>
                                <td>
                                    <div class="d-flex">
                                        <!-- Plus button for showing hidden data on small screens -->
                                        <button class="btn d-sm-none btn-link p-0 me-2" data-bs-toggle="collapse"
                                            data-bs-target="#details<?= $getlate['id'] ?>" aria-expanded="false"
                                            aria-controls="details<?= $getlate['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="icon icon-tabler icon-tabler-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 5v14m-7 -7h14"></path>
                                            </svg>
                                        </button>

                                        <img src="<?= $_SESSION['user_profile'] ?>" class="avatar" style="object-fit: cover;"
                                            alt="">
                                        <div class="d-flex flex-column mx-2">
                                            <h4 class="mx-0 mb-1 text-primary">
                                                <?= $_SESSION['user_khmer_name'] ?>
                                            </h4>
                                            <span class="text-muted"><?= $_SESSION['email'] ?></span>
                                        </div>
                                    </div>

                                    <!-- Collapsed content for small screens -->
                                    <div id="details<?= $getlate['id'] ?>" class="collapse d-sm-block d-xl-none mt-2">
                                        <div class="mb-1 text-red"><strong>ម៉ោង:</strong> <?= $getlate['late_out'] ?></div>
                                        <div class="mb-1 text-red"><strong>រយៈពេល:</strong> <?= $getlate['late'] ?> នាទី</div>
                                        <div class="mb-1"><strong>ថ្ងៃខែឆ្នាំ:</strong> <?= $getlate['date'] ?></div>
                                        <div class="mb-1"><strong>មូលហេតុ:</strong> <?= $getlate['reasons'] ?></div>
                                        <div class="mb-1"><strong>ស្នើនៅ:</strong> <?= $getlate['created_at'] ?></div>
                                        <div class="mb-0">
                                            <?php if ($getlate['status'] == 'Approved'): ?>
                                                <a href="#" onclick="printContents(<?= $getlate['id'] ?>)"
                                                    class="icon me-2 edit-btn text-danger" data-bs-toggle="tooltip" title="Print"
                                                    data-bs-target="#edit<?= $getlate['id'] ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                        <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                        <path
                                                            d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                                    </svg>
                                                </a>
                                                <a href="#"
                                                    onclick="Export2Word('page-contents<?= $getlate['id'] ?>', 'word-content<?= $getlate['id'] ?>');"
                                                    class="icon me-2 edit-btn" data-bs-toggle="tooltip" title="Export to Word">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M7 11l5 5l5 -5" />
                                                        <path d="M12 4l0 12" />
                                                    </svg>
                                                </a>
                                            <?php else: ?>
                                                <a href="#" class="icon me-2 edit-btn text-info"
                                                    data-bs-target="#editlateouts<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                        <path d="M16 5l3 3" />
                                                    </svg>
                                                </a>
                                                <a href="#" class="icon me-2 edit-btn text-danger"
                                                    data-bs-target="#deleteMissions<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </a>

                                                <!-- edit late out responsive  -->
                                                <div class="modal modal-blur fade" id="editlateouts<?= $getlate['id'] ?>" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-md">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"><strong>កែប្រែសំណើចូលយឺត</strong></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <form method="POST" action="/elms/edit_lateout" enctype="multipart/form-data">
                                                                <input type="hidden" name="lateId" value="<?= $getlate['id'] ?>">
                                                                <div class="modal-body">
                                                                    <div class="row">
                                                                        <div class="col-lg-12 mb-3">
                                                                            <label for="lateindate" class="form-label">កាលបរិច្ឆេទ<span
                                                                                    class="text-danger mx-1 fw-bold">*</span></label>
                                                                            <div class="input-icon">
                                                                                <span class="input-icon-addon">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                                        width="24" height="24" viewBox="0 0 24 24"
                                                                                        stroke-width="2" stroke="currentColor" fill="none"
                                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                                        </path>
                                                                                        <rect x="4" y="5" width="16" height="16" rx="2">
                                                                                        </rect>
                                                                                        <line x1="16" y1="3" x2="16" y2="7"></line>
                                                                                        <line x1="8" y1="3" x2="8" y2="7"></line>
                                                                                        <line x1="4" y1="11" x2="20" y2="11"></line>
                                                                                        <rect x="8" y="15" width="2" height="2"></rect>
                                                                                    </svg>
                                                                                </span>
                                                                                <input type="text" autocomplete="off"
                                                                                    value="<?= $getlate['date'] ?>"
                                                                                    class="form-control date-picker" name="date">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-lg-12 mb-3">
                                                                            <label class="form-label">ម៉ោង<span
                                                                                    class="text-danger mx-1 fw-bold">*</span></label>
                                                                            <div class="input-icon">
                                                                                <span class="input-icon-addon">
                                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                                        stroke="currentColor" stroke-width="2"
                                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                        <path
                                                                                            d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                                                        <path d="M12 7v5l.5 .5" />
                                                                                        <path
                                                                                            d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                                                        <path d="M15 21v-6" />
                                                                                    </svg>
                                                                                </span>
                                                                                <input type="text" autocomplete="off"
                                                                                    value="<?= $getlate['late_out'] ?>" placeholder="ម៉ោង"
                                                                                    class="form-control time-picker" name="time">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 mb-3">
                                                                            <label for="reason" class="form-label">មូលហេតុ<span
                                                                                    class="text-danger mx-1 fw-bold">*</span></label>
                                                                            <textarea autocomplete="off" placeholder="មូលហេតុ"
                                                                                class="form-control" id="reason"
                                                                                name="reason"><?= $getlate['reasons'] ?></textarea>
                                                                        </div>
                                                                        <div class="col-lg-6">
                                                                            <label class="form-check cursor-pointer">
                                                                                <input class="form-check-input" type="checkbox" name="agree"
                                                                                    <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                                                                                <span class="form-check-label">យល់ព្រមលើការបញ្ចូល<span
                                                                                        class="text-danger fw-bold mx-1">*</span></span>
                                                                            </label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <div class="w-100">
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <button type="button" class="btn w-100"
                                                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                                            </div>
                                                                            <div class="col">
                                                                                <button type="submit" class="btn w-100 btn-primary ms-auto">
                                                                                    បញ្ចូន
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- delete  -->
                                                <div class="modal modal-blur fade" id="deleteMissions<?= $getlate['id'] ?>"
                                                    tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-status bg-danger"></div>
                                                            <form action="/elms/late-out-delete" method="POST">
                                                                <div class="modal-body text-center py-4 mb-0">
                                                                    <input type="hidden" name="id" value="<?= $getlate['id'] ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                        stroke-width="2" stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        class="icon mb-2 text-danger icon-lg">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                        <path d="M12 9v4"></path>
                                                                        <path
                                                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                        </path>
                                                                        <path d="M12 16h.01"></path>
                                                                    </svg>
                                                                    <h5 class="modal-title fw-bold text-danger">លុបការចូលយឺត</h5>
                                                                    <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបការចូលយឺតនេះ?</p>
                                                                </div>
                                                                <div class="modal-footer bg-light border-top">
                                                                    <div class="w-100">
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <button type="button" class="btn w-100"
                                                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                                            </div>
                                                                            <div class="col">
                                                                                <button type="submit"
                                                                                    class="btn btn-danger ms-auto w-100">បាទ /
                                                                                    ចា៎</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="d-none d-xl-table-cell"><?= $getlate['date'] ?></td>
                                <td class="d-none d-xl-table-cell text-red"><?= $getlate['late_out'] ?></td>
                                <td class="d-none d-xl-table-cell text-red"><?= $getlate['late'] ?>នាទី</td>
                                <td class="d-none d-xl-table-cell"><?= $getlate['reasons'] ?></td>
                                <td class="d-none d-xl-table-cell"><?= $getlate['created_at'] ?></td>
                                <td>
                                    <div
                                        class="badge <?php echo $a = $getlate['status'] == 'Pending' ? 'bg-warning' : ($getlate['status'] == 'Approved' ? 'bg-success' : 'bg-danger'); ?> ">
                                        <?= $getlate['status']; ?>
                                    </div>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <?php if ($getlate['status'] == 'Approved'): ?>
                                        <a href="#" onclick="printContents(<?= $getlate['id'] ?>)"
                                            class="icon me-2 edit-btn text-danger" data-bs-toggle="tooltip" title="Print"
                                            data-bs-target="#edit<?= $getlate['id'] ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                <path
                                                    d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                            </svg>
                                        </a>
                                        <a href="#"
                                            onclick="Export2Word('page-contents<?= $getlate['id'] ?>', 'word-content<?= $getlate['id'] ?>');"
                                            class="icon me-2 edit-btn" data-bs-toggle="tooltip" title="Export to Word">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 11l5 5l5 -5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                        </a>
                                        <a href="#" class="icon me-2 edit-btn text-danger"
                                            data-bs-target="#deleteMissions<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </a>

                                        <!-- delete  -->
                                        <div class="modal modal-blur fade" id="deleteMissions<?= $getlate['id'] ?>" tabindex="-1"
                                            role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-status bg-danger"></div>
                                                    <form action="/elms/late-out-delete" method="POST">
                                                        <div class="modal-body text-center py-4 mb-0">
                                                            <input type="hidden" name="id" value="<?= $getlate['id'] ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon mb-2 text-danger icon-lg">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M12 9v4"></path>
                                                                <path
                                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                </path>
                                                                <path d="M12 16h.01"></path>
                                                            </svg>
                                                            <h5 class="modal-title fw-bold text-danger">លុបការចូលយឺត</h5>
                                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបការចូលយឺតនេះ?</p>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top">
                                                            <div class="w-100">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <button type="button" class="btn w-100"
                                                                            data-bs-dismiss="modal">បោះបង់</button>
                                                                    </div>
                                                                    <div class="col">
                                                                        <button type="submit"
                                                                            class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <a href="#" class="icon me-2 edit-btn text-info"
                                            data-bs-target="#editlateout<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                <path d="M16 5l3 3" />
                                            </svg>
                                        </a>
                                        <a href="#" class="icon me-2 edit-btn text-danger"
                                            data-bs-target="#deleteMission<?= $getlate['id'] ?>" data-bs-toggle="modal">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7l16 0" />
                                                <path d="M10 11l0 6" />
                                                <path d="M14 11l0 6" />
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                            </svg>
                                        </a>

                                        <!-- edit late out  -->
                                        <div class="modal modal-blur fade" id="editlateout<?= $getlate['id'] ?>" tabindex="-1"
                                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-md">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"><strong>កែប្រែសំណើចូលយឺត</strong></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form method="POST" action="/elms/edit_lateout" enctype="multipart/form-data">
                                                        <input type="hidden" name="lateId" value="<?= $getlate['id'] ?>">
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-lg-12 mb-3">
                                                                    <label for="lateindate" class="form-label">កាលបរិច្ឆេទ<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                                </path>
                                                                                <rect x="4" y="5" width="16" height="16" rx="2">
                                                                                </rect>
                                                                                <line x1="16" y1="3" x2="16" y2="7"></line>
                                                                                <line x1="8" y1="3" x2="8" y2="7"></line>
                                                                                <line x1="4" y1="11" x2="20" y2="11"></line>
                                                                                <rect x="8" y="15" width="2" height="2"></rect>
                                                                            </svg>
                                                                        </span>
                                                                        <input type="text" autocomplete="off"
                                                                            value="<?= $getlate['date'] ?>"
                                                                            class="form-control date-picker" name="date">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12 mb-3">
                                                                    <label class="form-label">ម៉ោង<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                <path
                                                                                    d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                                                <path d="M12 7v5l.5 .5" />
                                                                                <path
                                                                                    d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                                                <path d="M15 21v-6" />
                                                                            </svg>
                                                                        </span>
                                                                        <input type="text" autocomplete="off"
                                                                            value="<?= $getlate['late_out'] ?>" placeholder="ម៉ោង"
                                                                            class="form-control time-picker" name="time">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 mb-3">
                                                                    <label for="reason" class="form-label">មូលហេតុ<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <textarea autocomplete="off" placeholder="មូលហេតុ"
                                                                        class="form-control" id="reason"
                                                                        name="reason"><?= $getlate['reasons'] ?></textarea>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <label class="form-check cursor-pointer">
                                                                        <input class="form-check-input" type="checkbox" name="agree"
                                                                            <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                                                                        <span class="form-check-label">យល់ព្រមលើការបញ្ចូល<span
                                                                                class="text-danger fw-bold mx-1">*</span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <div class="w-100">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <button type="button" class="btn w-100"
                                                                            data-bs-dismiss="modal">បោះបង់</button>
                                                                    </div>
                                                                    <div class="col">
                                                                        <button type="submit" class="btn w-100 btn-primary ms-auto">
                                                                            បញ្ចូន
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- delete  -->
                                        <div class="modal modal-blur fade" id="deleteMission<?= $getlate['id'] ?>" tabindex="-1"
                                            role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-status bg-danger"></div>
                                                    <form action="/elms/late-out-delete" method="POST">
                                                        <div class="modal-body text-center py-4 mb-0">
                                                            <input type="hidden" name="id" value="<?= $getlate['id'] ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon mb-2 text-danger icon-lg">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M12 9v4"></path>
                                                                <path
                                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                </path>
                                                                <path d="M12 16h.01"></path>
                                                            </svg>
                                                            <h5 class="modal-title fw-bold text-danger">លុបការចូលយឺត</h5>
                                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបការចូលយឺតនេះ?</p>
                                                        </div>
                                                        <div class="modal-footer bg-light border-top">
                                                            <div class="w-100">
                                                                <div class="row">
                                                                    <div class="col">
                                                                        <button type="button" class="btn w-100"
                                                                            data-bs-dismiss="modal">បោះបង់</button>
                                                                    </div>
                                                                    <div class="col">
                                                                        <button type="submit"
                                                                            class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4" hidden>
                                <div id="page-contents<?= $getlate['id'] ?>" class="card invoice-preview-card"
                                    style="height: 100vh">
                                    <div class="card-body">
                                        <div class="page-container hidden-on-narrow">
                                            <div class="pdf-page size-a4">
                                                <div class="pdf-header">
                                                    <center class="invoice-number"
                                                        style="font-family: khmer mef2;color: #2F5496;font-size: 20px; margin-top: -5px;">
                                                        ព្រះរាជាណាចក្រកម្ពុជា<br>
                                                        ជាតិ សាសនា ព្រះមហាក្សត្រ
                                                    </center>
                                                </div>
                                                <div class="from">
                                                    <div class="mb-xl-0 mb-4">
                                                        <div class="for"
                                                            style="font-family: khmer mef2; font-size:20px; position: relative; color: #2F5496;">
                                                            <span class="company-logo">
                                                                <img src="public/img/icons/brands/logo2.png" class="mb-3"
                                                                    style="width: 180px; padding-left: 38px" />
                                                            </span>
                                                            <p style="font-size: 14px;">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                                                            <p
                                                                style="font-size: 14px; text-indent: 40px; top: 0; line-height:30px;">
                                                                អង្គភាពសវនកម្មផ្ទៃក្នុង <br>
                                                            <p style="font-size: 14px; text-indent: 25px;">
                                                                លេខ:.......................អ.ស.ផ.</p>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <center
                                                        style="text-align: center; font-family: khmer mef2; font-size: 19px;"
                                                        class="mb-3">
                                                        ពាក្យស្នើសុំបញ្ជាក់ពិការចេញពីបំពេញការងារយឺតយ៉ាវ
                                                    </center>
                                                    <p
                                                        style="font-family: khmer mef1; font-size:18px; line-height: 35px; text-align:justify; text-indent: 50px;">
                                                        ខ្ញុំបាទ / នាងខ្ញុំឈ្មោះ <span
                                                ><?= $getlate['khmer_name'] ?></span> មានតួនាទីជា
                                                        <sp><?= $_SESSION['position'] ?></sp> នៃ <span
                                                ><?= $_SESSION['departmentName'] ?></span>
                                                        បានមកបំពេញការងារយឺតពេលកំណត់នៅថ្ងៃទី <span
                                                ><?= translateDateToKhmer($getlate['date'], 'd') ?> ខែ
                                                            <span
                                                    ><?= translateDateToKhmer($getlate['date'], 'F') ?>
                                                                ឆ្នាំ <span
                                                        ><?= translateDateToKhmer($getlate['date'], 'Y') ?>
                                                                </span> វេលាម៉ោង <span
                                                        ><?= convertToKhmerNumerals($getlate['late_out'] . "នាទី") ?></span>
                                                                ហើយខ្ញុំសូមបញ្ជាក់ពីមូលហេតុដែលខ្ញុំបាទ/នាងខ្ញុំមកបំពេញការងារយឺតយ៉ាវដោយមូលហេតុ
                                                                <span
                                                                    class="fw-bolder"><?= $getlate['reasons'] ?></span>។ដូចនេះសូមមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមានខ្ញុំបាទ/នាងខ្ញុំក្នុងបញ្ជីវត្តមានរបស់មន្ត្រីនៃអង្គភាពសវនកម្មផ្ទៃក្នុងនៃ
                                                                <span class="fw-bolder">អ.ស.ហ.</span>នៅថ្ងៃទី <span
                                                ><?= translateDateToKhmer($getlate['date'], 'd') ?> ខែ
                                                            <span
                                                    ><?= translateDateToKhmer($getlate['date'], 'F') ?>
                                                                ឆ្នាំ <span
                                                        ><?= translateDateToKhmer($getlate['date'], 'Y') ?>
                                                                </span>
                                                                            គឺតាមមូលហេតុខាងលើនេះ។
                                                    </p>
                                                    <p
                                                        style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                        សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ពិនិត្យ និងចាត់ចែងតាមការគួរ
                                                    </p>
                                                    <p
                                                        style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                        សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ទទួលនូវការរាប់អានពីខ្ញុំ។
                                                    </p>
                                                    <div class="row">
                                            <!-- Staff Information -->
                                            <div class="col-6 mb-2 mt-3"
                                                style="font-family: 'khmer mef1'; font-size: 18px; line-height: 20px; text-align: justify;">
                                                <div style="text-align: center;">
                                                    <p>បានឃើញ និងឯកភាពថា</p>
                                                </div>
                                                <p style="white-space: nowrap; text-align: center;">
                                                    ឈ្មោះ <?= $getlate['khmer_name'] ?> តួនាទីជា <?= $_SESSION['position'] ?>
                                                </p>
                                                <p style="white-space: nowrap; text-align: center;">
                                                    នៃ <?= $_SESSION['departmentName'] ?>
                                                </p>
                                                <p>បានចេញពីបំពេញការងារវេលាម៉ោង <?= convertToKhmerNumerals($getlate['late_out'] . "នាទី") ?></p>
                                                <p style="text-align: center;">
                                                    រាជធានីភ្នំពេញ ថ្ងៃទី
                                                    <?= translateDateToKhmer($getlate['updated_at'], 'd') ?>
                                                    ខែ <?= translateDateToKhmer($getlate['updated_at'], 'F') ?>
                                                    ឆ្នាំ <?= translateDateToKhmer($getlate['updated_at'], 'Y') ?>
                                                </p>
                                                <h3 style="text-align: center;">មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</h3>
                                                <p style="text-align: center;
                                                    class="mb-2 <?= ($getlate['approval_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                                    <?php
                                                    switch ($getlate['approval_status'] ?? '') {
                                                        case 'Approved':
                                                            echo 'បានអនុម័ត'; // Khmer for 'Approved'
                                                            break;
                                                        case 'Rejected':
                                                            echo 'មិនអនុម័ត'; // Khmer for 'Rejected'
                                                            break;
                                                        default:
                                                            echo 'ស្ថានភាពមិនស្គាល់'; // Khmer for 'Unknown Status'
                                                            break;
                                                    }
                                                    ?>
                                                </p>
                                                <h3 style="text-align: center;"><?= $getlate['approver_name'] ?></h3>
                                            </div>

                                            <div class="col-6 mb-2"
                                                style="font-family: khmer mef1; font-size: 18px; line-height: 30px; text-align: justify; text-align: center;">
                                                <p style="margin-bottom: 0;">
                                                    រាជធានីភ្នំពេញ ថ្ងៃទី
                                                    <?= translateDateToKhmer($getlate['created_at'], 'd') ?>
                                                    ខែ <?= translateDateToKhmer($getlate['created_at'], 'F') ?>
                                                    ឆ្នាំ <?= translateDateToKhmer($getlate['created_at'], 'Y') ?>
                                                </p>
                                                <h3 class="mb-3">អ្នកស្នើសុំ</h3>
                                                <h3 class="mb-0">
                                                    <?= htmlspecialchars($getlate['khmer_name'] ?? 'Unknown Name', ENT_QUOTES, 'UTF-8') ?>
                                                </h3>
                                            </div>
                                        </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex align-items-center rounded-bottom-3">
                <ul id="custom-pagination" class="pagination m-0 ms-auto"></ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('src/common/footer.php'); ?>
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

<style>
    /* Hide the default pagination controls */
    #officeTable_paginate {
        display: none;
    }
</style>
<!-- pagination footer  -->
<script>
    $(document).ready(function () {
        var table = $('#officeTable').DataTable({
            "paging": true,
            "searching": false,
            "info": false,
            "lengthChange": false,
            "pageLength": 8,
            "language": {
                "paginate": {
                    "previous": "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M15 6l-6 6l6 6'></path></svg> prev",
                    "next": "next <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M9 6l6 6l-6 6'></path></svg>"
                }
            },
            "drawCallback": function (settings) {
                var api = this.api();
                var start = api.page.info().start + 1;
                var end = api.page.info().end;
                var total = api.page.info().recordsTotal;

                $('#showing-start').text(start);
                $('#showing-end').text(end);
                $('#total-entries').text(total);

                // Create custom pagination
                var paginationHtml = '';
                var currentPage = api.page.info().page;
                var totalPages = api.page.info().pages;

                paginationHtml += '<li class="page-item ' + (currentPage === 0 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" tabindex="-1" aria-disabled="' + (currentPage === 0 ? 'true' : 'false') + '" data-page="prev">';
                paginationHtml += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">';
                paginationHtml += '<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                paginationHtml += '<path d="M15 6l-6 6l6 6"></path>';
                paginationHtml += '</svg> </a></li>';

                for (var i = 0; i < totalPages; i++) {
                    paginationHtml += '<li class="page-item ' + (currentPage === i ? 'active' : '') + '">';
                    paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
                }

                paginationHtml += '<li class="page-item ' + (currentPage === totalPages - 1 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="next"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">';
                paginationHtml += '<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                paginationHtml += '<path d="M9 6l6 6l-6 6"></path>';
                paginationHtml += '</svg></a></li>';

                $('#custom-pagination').html(paginationHtml);

                // Add click event to custom pagination links
                $('#custom-pagination .page-link').on('click', function (e) {
                    e.preventDefault();
                    var pageNum = $(this).data('page');
                    if (pageNum === 'prev') {
                        table.page('previous').draw('page');
                    } else if (pageNum === 'next') {
                        table.page('next').draw('page');
                    } else {
                        table.page(parseInt(pageNum)).draw('page');
                    }
                });
            }
        });

        // Custom search bar functionality
        $('#customSearch').on('keyup', function () {
            table.search(this.value).draw();
        });
    });
</script>