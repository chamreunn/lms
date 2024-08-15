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

$title = "បេសកកម្ម";
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mission">
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
                        </button>
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

<div class="card">
    <div class="card-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="header-title mb-0 text-muted"><?= $title ?></h4>
        </div>
    </div>

    <div class="card-body border-bottom">
        <form class="mb-0" action="/elms/mission" method="POST">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-3">
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
                        <input class="form-control date-picker" placeholder="កាលបរិច្ឆេទចាប់ពី" type="text" name="start_date" id="mission_start" autocomplete="off" />
                    </div>
                </div>
                <div class="col-lg-4 mb-3">
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
                        <input class="form-control date-picker" placeholder="ដល់កាលបរិច្ឆេទ" type="text" name="end_date" id="date" autocomplete="off" />
                    </div>
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
                    <a href="/elms/mission" type="reset" class="btn w-100 btn-danger">
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
                    <th class="d-none d-sm-table-cell">ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">ដល់កាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">រយៈពេល</th>
                    <th class="d-none d-sm-table-cell">ឯកសារភ្ជាប់</th>
                    <th class="text-center">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($missions)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានសំណើនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោម ឬស្តាំដៃខាងលើ</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mission">
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
                            </button>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($missions as $mission) : ?>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <img src="<?= $mission['profile_picture'] ?>" class="avatar" style="object-fit: cover;" alt="">
                                    <div class="d-flex flex-column mx-2">
                                        <h4 class="mx-0 mb-1 text-primary">
                                            <?= $mission['khmer_name'] ?>
                                        </h4>
                                        <span class="text-muted"><?= $mission['email'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($mission['start_date'], 'D,j F Y') ?></td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($mission['end_date'], 'D,j F Y') ?></td>
                            <td class="d-none d-sm-table-cell"><?= $mission['num_date'] ?>ថ្ងៃ</td>
                            <td class="d-none text-truncate d-sm-table-cell">
                                <a data-bs-toggle="tooltip" data-bs-placement="auto" title="<?= $mission['attachment'] ?>" href="public/uploads/missions_attachments/<?= $mission['attachment'] ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                    </svg>
                                    ឯកសារភ្ជាប់
                                </a>
                            </td>
                            <td class="p-0 text-center">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#edit-mission<?= $mission['id'] ?>" class="icon me-0 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= htmlspecialchars($mission['id']) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                    </svg>
                                </a>
                                <a href="#" class="d-sm-none" title="លុប" data-bs-toggle="collapse" data-bs-target="#collapseRequest<?= $mission['id'] ?>" aria-expanded="false" aria-controls="collapseRequest<?= $mission['id'] ?>">
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
                                <div class="collapse" id="collapseRequest<?= $mission['id'] ?>">
                                    <table class="table mb-0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong>ចាប់ពីកាលបរិច្ឆេទ : </strong>
                                                    <span> <?= translateDateToKhmer($mission['start_date'], 'D,j F Y') ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>ដល់កាលបរិច្ឆេទ : </strong>
                                                    <span><?= translateDateToKhmer($mission['end_date'], 'D,j F Y') ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>រយៈពេល : </strong>
                                                    <span><?= $mission['num_date'] ?>ថ្ងៃ</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>រយៈពេល : </strong>
                                                    <span><?= $mission['attachment'] ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit Mission -->
                        <div class="modal modal-blur fade" id="edit-mission<?= $mission['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" position="document">
                                <div class="modal-content">
                                    <!-- Set the form action to the update endpoint -->
                                    <form action="/elms/update-mission" method="POST" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title">កែសម្រួលលិខិតបេសកកម្ម</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Hidden field to pass the mission ID -->
                                            <input type="hidden" name="mission_id" value="<?= $mission['id'] ?>">

                                            <div class="mb-3">
                                                <label for="emission_start" class="form-label">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                                            <rect x="8" y="15" width="2" height="2"></rect>
                                                        </svg>
                                                    </span>
                                                    <!-- Pre-fill the input with the current start date -->
                                                    <input type="text" value="<?= $mission['start_date'] ?>" class="form-control" id="emission_start" name="start_date" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="emission_end" class="form-label">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                            <path d="M12 7v5l.5 .5" />
                                                            <path d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                            <path d="M15 21v-6" />
                                                        </svg>
                                                    </span>
                                                    <!-- Pre-fill the input with the current end date -->
                                                    <input type="text" value="<?= $mission['end_date'] ?>" class="form-control" id="emission_end" name="end_date" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">រយៈពេល<span class="text-danger mx-1 fw-bold">*</span></label>
                                                <div class="input-icon">
                                                    <span class="input-icon-addon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                            <path d="M12 7v5l.5 .5" />
                                                            <path d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                            <path d="M15 21v-6" />
                                                        </svg>
                                                    </span>
                                                    <!-- Pre-fill the input with the current end date -->
                                                    <input disabled type="text" value="<?= $mission['num_date'] ?>ថ្ងៃ" class="form-control" id="emission_end" name="end_date" required>
                                                </div>
                                            </div>
                                            <?php if (!empty($mission['attachment'])) : ?>
                                                <div class="mb-3">
                                                    <label class="form-label">ឯកសារដែលភ្ជាប់មកជាមួយ:</label>
                                                    <a href="public/uploads/missions_attachments/<?= htmlspecialchars($mission['attachment']) ?>" target="_blank" class="btn btn-outline-secondary w-100 text-start">
                                                        <span class="p-1">
                                                            <!-- Attachment Icon SVG -->
                                                        </span>
                                                        <span><?= htmlspecialchars($mission['attachment']) ?></span>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                            <div class="mb-3">
                                                <label for="emissionDoc" class="form-label">ឯកសារភ្ជាប់ថ្មី (ជម្រើស)</label>
                                                <label id="missionName" for="emissionDoc" class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                                                    <span class="p-1">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                                        </svg>
                                                    </span>
                                                    <span id="emissionName">ជ្រើសឯកសារថ្មី</span>
                                                </label>
                                                <input type="file" name="missionDoc" id="emissionDoc" accept=".pdf, .docx, .xlsx" hidden onchange="displayFileName('emissionDoc', 'emissionName')" />
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-check cursor-pointer">
                                                    <input class="form-check-input" type="checkbox" name="agree" checked required>
                                                    <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span class="text-danger fw-bold mx-1">*</span></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light border-top">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                    </div>
                                                    <div class="col">
                                                        <button type="submit" class="btn w-100 btn-primary ms-auto">កែប្រែ</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- delete  -->
                        <div class="modal modal-blur fade" id="deleteModal<?= htmlspecialchars($mission['id']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-status bg-danger"></div>
                                    <form action="/elms/delete-mission" method="POST">
                                        <div class="modal-body text-center py-4 mb-0">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($mission['id']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 9v4"></path>
                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                                <path d="M12 16h.01"></path>
                                            </svg>
                                            <h5 class="modal-title fw-bold text-danger">លុបបេសកកម្ម</h5>
                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបបេសកកម្មនេះ?</p>
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
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Apply Mission -->
<div class="modal modal-blur fade" id="mission" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply-mission" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">បង្កើតលិខិតថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label date-picker">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទ" class="form-control date-picker" id="mission_start" name="start_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label date-picker">ដល់កាលបរិចេ្ឆទ<span class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="ដល់កាលបរិចេ្ឆទ" class="form-control date-picker" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="createMissionDoc" class="form-label">ឯកសារភ្ជាប់</label>
                        <label id="createMissionName" for="createMissionDoc" class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                            <span class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                </svg>
                            </span>
                            <span>ឯកសារភ្ជាប់</span>
                        </label>
                        <input type="file" name="missionDoc" id="createMissionDoc" accept=".pdf, .docx, .xlsx" required hidden onchange="displayFileName('createMissionDoc', 'createMissionName')" />
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span class="text-danger fw-bold mx-1">*</span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">បញ្ចូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

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
    function displayFileName(inputId, labelId) {
        var inputElement = document.getElementById(inputId);
        var labelElement = document.getElementById(labelId);

        if (inputElement.files && inputElement.files.length > 0) {
            var fileName = inputElement.files[0].name;
            labelElement.textContent = fileName;
        } else {
            labelElement.textContent = "ជ្រើសឯកសារថ្មី"; // Default text
        }
    }
</script>