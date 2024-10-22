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
                            name="start_date" id="mission_start" autocomplete="off" />
                    </div>
                </div>
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
                        <input class="form-control date-picker" placeholder="ដល់កាលបរិច្ឆេទ" type="text" name="end_date"
                            id="date" autocomplete="off" />
                    </div>
                </div>
                <div class="col mb-3">
                    <button type="submit" class="btn w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                        <span>ស្វែងរក</span>
                    </button>
                </div>
                <div class="col mb-3">
                    <a href="/elms/mission" type="reset" class="btn w-100 btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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

    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th>ល.រ</th>
                    <th>ឈ្មោះមន្ត្រី</th>
                    <th class="d-none d-sm-table-cell">ឈ្មោះបេសកកម្ម</th>
                    <th class="d-none d-sm-table-cell">ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">ដល់កាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">រយៈពេល</th>
                    <th class="d-none d-sm-table-cell">ឯកសារភ្ជាប់</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($missions)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានបេសកកម្មនៅឡើយ។</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($missions as $key => $mission): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td>
                                <div class="d-flex">
                                    <img src="<?= $_SESSION['user_profile'] ?>" class="avatar" style="object-fit: cover;"
                                        alt="">
                                    <div class="d-flex flex-column mx-2">
                                        <h4 class="mx-0 mb-1 text-primary">
                                            <?= $_SESSION['user_khmer_name'] ?>
                                        </h4>
                                        <span class="text-muted"><?= $_SESSION['email'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell"><?= $mission['missionName'] ?></td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($mission['start_date'], 'D,j F Y') ?>
                            </td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($mission['end_date'], 'D,j F Y') ?></td>
                            <td class="d-none d-sm-table-cell"><?= $mission['num_date'] ?>ថ្ងៃ</td>
                            <td class="d-none text-truncate d-sm-table-cell">
                                <a data-bs-toggle="tooltip" target="_blank" data-bs-placement="auto"
                                    title="<?= $mission['attachment'] ?>"
                                    href="public/uploads/missions_attachments/<?= $mission['attachment'] ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                    </svg>
                                    ឯកសារភ្ជាប់
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
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>



<?php include('src/common/footer.php'); ?>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function (data, escape) {
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