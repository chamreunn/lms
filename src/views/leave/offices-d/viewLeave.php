<!-- src/views/leave/viewleave.php -->
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

$title = "ពិនិត្យមើលច្បាប់ឈប់សម្រាក";
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
?>

<?php if (isset($request['attachment_error']) && $request['attachment_error']) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>សម្គាល់:</strong> <?= $request['attachment_error'] ?>
        <form action="/elms/uploadAttachment" method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="leave_id" value="<?= $request['id'] ?>">
            <div class="input-group">
                <input type="file" class="form-control" id="attachment" name="attachment" required>
                <button type="submit" class="btn btn-primary">បញ្ចូលឯកសារ</button>
            </div>
        </form>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="text-primary mb-0">ព័ត៌មានលម្អិតអំពីច្បាប់</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">ប្រភេទច្បាប់ :</th>
                            <td><span class="badge badge-primary <?= $request['color'] ?>"><?= $request['leave_type_name'] ?></span></td>
                        </tr>
                        <tr>
                            <th scope="row">ស្ថានភាព :</th>
                            <td>
                                <span class="badge 
                                    <?php
                                    $attachmentRequired = $request['attRequired'] == 'YES';
                                    $attachmentMissing = empty($request['attachment']);

                                    // Determine the status to display
                                    $statusToDisplay = $request['status'];

                                    // If attachment is required but missing, override the status to 'Pending'
                                    if ($attachmentRequired && $attachmentMissing) {
                                        $statusToDisplay = 'Pending';
                                    }
                                    if ($statusToDisplay == 'Pending') {
                                        echo 'bg-warning';
                                    } elseif ($statusToDisplay == 'Approved') {
                                        echo 'bg-success';
                                    } elseif ($statusToDisplay == 'Rejected') {
                                        echo 'bg-danger';
                                    } else {
                                        echo 'bg-secondary';
                                    }
                                    ?>">
                                    <?= $statusToDisplay ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">ចាប់ពីកាលបរិច្ឆេទ :</th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M8 15h2v2h-2z" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">ដល់កាលបរិច្ឆេទ :</th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                        <path d="M16 3l0 4" />
                                        <path d="M8 3l0 4" />
                                        <path d="M4 11l16 0" />
                                        <path d="M8 15h2v2h-2z" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">រយៈពេល :</th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-24">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 0 0 5.998 8.485m12.002 -8.485a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5" />
                                        <path d="M12 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                        <path d="M18 15v2a1 1 0 0 0 1 1h1" />
                                        <path d="M21 15v6" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <?= $request['num_date'] ?> ថ្ងៃ
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">មូលហេតុ :</th>
                            <td><?= $request['remarks'] ?></td>
                        </tr>
                        <tr>
                            <th scope="row">ឯកសារភ្ជាប់ :</th>
                            <td>
                                <?php if ($request['attRequired'] === 'Yes') : ?>
                                    <?php if (empty($request['attachment'])) : ?>
                                        <span class="text-danger">សូមភ្ជាប់ឯកសារភ្ជាប់</span>
                                    <?php else : ?>
                                        <a href="<?= $request['attachment'] ?>" target="_blank">មើលឯកសារ</a>
                                    <?php endif; ?>
                                <?php else : ?>
                                    <span class="text-muted">មិនត្រូវការភ្ជាប់ឯកសារ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Contact Information Section -->
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="text-primary mb-0">ទំនាក់ទំនង</h4>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">ឈ្មោះ :</th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <?= $request['khmer_name'] ?>
                                </span>
                            </td>
                        </tr>
                        <!-- Contact Name -->
                        <tr>
                            <th scope="row">
                                ទំនាក់ទំនង :
                            </th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <?= $request['phone_number'] ?>
                                </span>
                            </td>
                        </tr>

                        <!-- Contact Name -->
                        <tr>
                            <th scope="row">
                                អាសយដ្ឋានអ៊ីម៉ែល :
                            </th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                </span>
                                <span class="mx-2">
                                    <a href="mailto:<?= $request['email'] ?>" class="text-primary">
                                        <?= $request['email'] ?>
                                    </a>
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Steps Vertical -->
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title text-primary mb-0">ការអនុម័ត</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <?php if (empty($request['approvals'])) : ?>
                        <div class="d-flex flex-column w-100 justify-content-center align-items-center mb-2">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <span class="text-muted">មិនទាន់មានការអនុម័ត</span>
                        </div>
                    <?php else : ?>
                        <!-- Actual items -->
                        <?php foreach ($request['approvals'] as $index => $approval) : ?>
                            <?php
                            $isLast = $index == count($request['approvals']) - 1;
                            $isWaiting = !$isLast && $request['approvals'][$index + 1]['status'] == 'Pending';
                            ?>
                            <div class="col-md-4">
                                <div class="card mb-3 <?= $approval['status'] == 'Rejected' ? 'border-danger' : ($approval['status'] == 'Approved' ? 'border-success' : ($approval['status'] == 'Cancelled' ? 'border-secondary' : 'border-warning')) ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?= $approval['profile'] ?>" class="avatar rounded-circle me-3" alt="" style="object-fit: cover;">
                                            <div>
                                                <h5 class="mb-0"><?= $approval['approver_name'] ?></h5>
                                                <small class="text-muted"><?= translateDateToKhmer($approval['updated_at'], 'j F, Y h:i A') ?></small>
                                            </div>
                                            <span class="badge ms-auto <?= $approval['status'] == 'Pending' ? 'bg-warning' : ($approval['status'] == 'Approved' ? 'bg-success' : ($approval['status'] == 'Rejected' ? 'bg-danger' : 'bg-secondary')) ?>">
                                                <i class="status-icon <?= $approval['status'] == 'Pending' ? 'ti ti-clock' : ($approval['status'] == 'Approved' ? 'ti ti-check-circle' : ($approval['status'] == 'Rejected' ? 'ti ti-x-circle' : 'ti ti-slash-circle')) ?>"></i>
                                                <?= $approval['status'] ?>
                                            </span>
                                        </div>
                                        <div class="mb-0">
                                            <textarea id="remarks-<?= $index ?>" class="form-control" rows="3" disabled><?= $approval['remarks'] ?? "មិនមានមតិយោបល់" ?></textarea>
                                        </div>
                                    </div>
                                    <?php if ($request['status'] !== 'Approved') : ?>
                                        <!-- Additional content for pending approval -->
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
                        <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;"><strong class="h3">កម្មវត្ថុ៖</strong> សំណើសុំច្បាប់ឈប់សម្រាកចំនួន <?= $request['num_date'] ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['end_date'], 'Y') ?></p>
                        <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;"><strong class="h3">មូលហេតុ៖</strong> <?= $request['remarks'] ?> ។</p>
                        <p style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;">
                            តបតាមកម្មវត្ថុខាងលើ ខ្ញុំសូមគោរពជម្រាបជូន លោកប្រធាននាយកដ្ឋាន មេត្តាជ្រាបដ៏ខ្ពង់ខ្ពស់ថា៖ខ្ញុំបាទ/ នាងខ្ញុំឈ្មោះ<?= $request['khmer_name'] ?>កើតថ្ងៃទី <?= translateDateToKhmer($request['dob'], 'd') ?> ខែ <?= translateDateToKhmer($request['dob'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['dob'], 'Y') ?> មានតួនាទីជា <?= $request['position_name'] ?> នៃ <?= $request['office_name'] ?> នៃ <?= $request['department_name'] ?> ខ្ញុំសូមគោរពស្នើសុំការអនុញ្ញាតច្បាប់ចំនួន <?= translateDateToKhmer($request['num_date'], 'd') ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ <?= translateDateToKhmer($request['end_date'], 'Y') ?>
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
                                <h3 class="mb-0"><?= $request['khmer_name'] ?></h3>
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

<?php include('src/common/footer.php'); ?>