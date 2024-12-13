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
$pretitle = "ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ";
$title = "ពិនិត្យមើលច្បាប់ឈប់សម្រាក";
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

<?php if (isset($request['attachment_error']) && $request['attachment_error']): ?>
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
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="text-primary mb-0">ព័ត៌មានលម្អិតអំពីច្បាប់</h3>
            </div>
            <div class="card-body text-center">
                <img class="avatar avatar-md mb-3" src="<?= $request['profile'] ?>" alt="Profile Image"
                    style="object-fit: cover;">
                <h3 class="text-primary"><?= $request['khmer_name'] ?></h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <tbody>
                        <tr>
                            <th>ប្រភេទច្បាប់ :</th>
                            <td>
                                <span
                                    class="badge bg-primary <?= $request['color'] ?>"><?= $request['leave_type_name'] ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>ស្ថានភាព :</th>
                            <td>
                                <?php
                                $attachmentRequired = $request['attRequired'] == 'YES';
                                $attachmentMissing = empty($request['attachment']);
                                $statusToDisplay = $request['status'];

                                if ($attachmentRequired && $attachmentMissing) {
                                    $statusToDisplay = 'Pending';
                                }
                                ?>
                                <span
                                    class="badge <?= $statusToDisplay == 'Pending' ? 'bg-warning' : ($statusToDisplay == 'Approved' ? 'bg-success' : ($statusToDisplay == 'Rejected' ? 'bg-danger' : 'bg-secondary')) ?>">
                                    <?= $statusToDisplay ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>ចាប់ពីកាលបរិច្ឆេទ :</th>
                            <td>
                                <span class="text-primary">
                                    <i class="ti ti-calendar"></i>
                                    <?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>ដល់កាលបរិច្ឆេទ :</th>
                            <td>
                                <span class="text-primary">
                                    <i class="ti ti-calendar"></i>
                                    <?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>រយៈពេល :</th>
                            <td>
                                <span class="text-primary">
                                    <i class="ti ti-clock"></i> <?= $request['num_date'] ?> ថ្ងៃ
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>មូលហេតុ :</th>
                            <td><?= $request['remarks'] ?></td>
                        </tr>
                        <tr>
                            <th>ឯកសារភ្ជាប់ :</th>
                            <td>
                                <?php if ($request['attRequired'] === 'Yes'): ?>
                                    <?php if (empty($request['attachment'])): ?>
                                        <span class="text-danger">សូមភ្ជាប់ឯកសារភ្ជាប់</span>
                                    <?php else: ?>
                                        <a href="public/uploads/leave_attachments/<?= $request['attachment'] ?>" target="_blank"
                                            class="text-primary">មើលឯកសារ</a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">មិនត្រូវការភ្ជាប់ឯកសារ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>ទំនាក់ទំនង :</th>
                            <td><?= $request['phone_number'] ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-light">
                <?php if ($request['status'] !== 'Approved'): ?>

                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <div class="card-title">
                            <h3 class="text-primary mb-0">ការអនុម័ត</h3>
                        </div>
                        <div class="ms-auto">
                            <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                data-bs-target="#cancelModal">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-cancel">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                                    <path d="M18.364 5.636l-12.728 12.728" />
                                </svg>
                                <span class="btn-responsive-text">បោះបង់សំណើ</span>
                            </button>
                            <!-- Modal for cancel request -->
                            <div class="modal modal-blur fade" id="cancelModal" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-status bg-danger"></div>
                                        <form action="/elms/leave-delete" method="POST">
                                            <div class="modal-body text-center py-4 mb-0">
                                                <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon mb-2 text-danger icon-lg">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 9v4"></path>
                                                    <path
                                                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                    </path>
                                                    <path d="M12 16h.01"></path>
                                                </svg>
                                                <h5 class="modal-title fw-bold text-danger">បោះបង់សំណើច្បាប់</h5>
                                                <p class="mb-0">តើអ្នកប្រាកដទេថានិងបោះបង់សំណើច្បាប់នេះ?</p>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <div class="w-100">
                                                    <div class="row">
                                                        <div class="col">
                                                            <button type="button" class="btn w-100"
                                                                data-bs-dismiss="modal">បោះបង់</button>
                                                        </div>
                                                        <div class="col">
                                                            <button type="submit"
                                                                class="btn btn-danger ms-auto w-100">យល់ព្រម</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php elseif ($request['status'] == 'Approved'): ?>
                    <div class="w-100 d-flex align-items-center justify-content-between">
                        <div class="card-title">
                            <h3 class="text-primary mb-0">ការអនុម័ត</h3>
                        </div>
                        <div class="ms-auto">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-success dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    ទាញយក
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <span class="dropdown-header">ទាញយក បោះពុម្ព</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" onclick="printContents(<?= $request['id'] ?>)" href="#">
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
                                            <span class="mx-2">បោះពុម្ព</span>
                                        </a>
                                    </li>
                                    <li class="dropdown-divider mt-0 mb-0"></li>
                                    <li>
                                        <a class="dropdown-item mb-0" href="#"
                                            onclick="Export2Word('page-contents<?= $request['id'] ?>', 'ច្បាប់ឈប់សម្រាក <?= $request['khmer_name'] ?>');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                <path d="M7 11l5 5l5 -5" />
                                                <path d="M12 4l0 12" />
                                            </svg>
                                            <span class="mx-2">ទាញយក (WORD)</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>
            </div>

            <div class="card-body">
                <ul class="steps steps-counter steps-vertical mb-0">
                    <?php if (empty($request['approvals'])): ?>
                        <div class="d-flex flex-column w-100 justify-content-center align-items-center mb-3">
                            <img src="public/img/icons/svgs/empty.svg" alt="No approvals" style="object-fit:cover;">
                            <p class="text-muted mt-2">មិនទាន់មានការអនុម័ត</p>
                        </div>
                    <?php else: ?>

                        <?php
                        // Check if the overall request status is 'Approved'
                        $isRequestApproved = trim($request['status']) === 'Approved';

                        // If the request is approved, display the top confirmation step
                        if ($isRequestApproved): ?>
                            <!-- Final step if the request is fully Approved (Displayed at the top) -->
                            <li class="step-item steps-green active">
                                <div class="d-flex align-items-center mb-3">
                                    <div>
                                        <h4 class="text-success mb-0">ច្បាប់ឈប់សម្រាកនេះត្រូវបានអនុម័ត</h4>
                                        <small class="text-success">
                                            <strong>អនុម័តនៅ:</strong>
                                            <?= translateDateToKhmer($request['updated_at'], 'j F Y h:i A') ?>
                                        </small>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php
                        // Find the last non-approved step if the request is not fully approved
                        $lastPendingOrRejectedIndex = -1;
                        if (!$isRequestApproved) {
                            foreach ($request['approvals'] as $index => $approval) {
                                if ($approval['status'] !== 'Approved') {
                                    $lastPendingOrRejectedIndex = $index;
                                }
                            }
                        }
                        ?>

                        <?php foreach ($request['approvals'] as $index => $approval): ?>
                            <?php
                            // If the request is approved, all steps are active
                            if ($isRequestApproved) {
                                $isActive = true;
                            } else {
                                // Only the last non-approved step is active if the request is not approved
                                $isActive = $index == $lastPendingOrRejectedIndex;
                            }
                            ?>

                            <li
                                class="step-item <?= $isActive ? 'active' : ($approval['status'] == 'Rejected' ? 'step-item-danger' : ($approval['status'] == 'Pending' ? 'step-item-warning' : '')) ?>">
                                <div class="d-flex align-items-center mb-2">
                                    <img src="<?= !empty($approval['profile']) ? $approval['profile'] : ''; ?>"
                                        class="avatar me-3" alt="Profile" style="object-fit: cover;">
                                    <div>
                                        <div class="h5 mb-0 d-flex">
                                            <h4 class="text-primary mb-0"><?= $approval['approver_name'] ?>
                                            </h4>
                                            <span
                                                class="badge mb-0 bg-<?= $approval['status'] == 'Pending' ? 'warning' : ($approval['status'] == 'Approved' ? 'success' : 'danger') ?> ms-2">
                                                <?= $approval['status'] ?>
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <?= translateDateToKhmer($approval['updated_at'], 'j F Y h:i A') ?>
                                        </small>
                                    </div>
                                </div>

                                <!-- Display remarks if available -->
                                <?php if (!empty($approval['remarks'])): ?>
                                    <div class="text-secondary mt-2">
                                        <strong>មតិយោបល់:</strong> <?= $approval['remarks'] ?>
                                    </div>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="col-xl-12 col-md-8 col-12 mb-md-0 mb-4 mt-3" hidden>
    <div id="page-contents<?= $request['id'] ?>" class="card invoice-preview-card" style="height: 100vh">
        <div class="card-body">
            <div class="page-container hidden-on-narrow">
                <div class="pdf-page size-a4">
                    <div class="pdf-header">
                        <center class="invoice-number"
                            style="font-family: khmer mef2;color: #2F5496;font-size: 20px; margin-top: -2px;">
                            ព្រះរាជាណាចក្រកម្ពុជា<br>
                            ជាតិ សាសនា ព្រះមហាក្សត្រ
                        </center>
                    </div>
                    <div class="page-body">
                        <div class="mb-xl-0 mb-2">
                            <div class="for"
                                style="font-family: khmer mef2; margin-top: -20px; font-size:20px; position: relative; color: #2F5496;">
                                <span class="company-logo">
                                    <img src="public/img/icons/brands/logo2.png" class="mb-3"
                                        style="width: 168px; padding-left: 48px" />
                                </span>
                                <p style="font-size: 14px; margin-bottom: 0;">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                                <p
                                    style="font-size: 14px; text-indent: 40px; margin-bottom: 0; padding-bottom: 0; line-height:30px;">
                                    អង្គភាពសវនកម្មផ្ទៃក្នុង <br>
                                <p style="font-size: 14px; text-indent: 25px;">លេខ:.......................អ.ស.ផ.</p>
                                </p>
                            </div>
                        </div>
                        <center style="text-align: center; font-family: khmer mef2; font-size: 19px; margin-top: -50px"
                            class="mb-3">
                            សូមគោរពជូន
                        </center>
                        <center style="text-align: center; font-family: khmer mef2; font-size: 19px;" class="mb-3">
                            លោកប្រធាន<?= $request['department_name'] ?>
                        </center>
                        <p
                            style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;white-space: nowrap;">
                            <strong class="h3">កម្មវត្ថុ៖</strong> <span>សំណើសុំច្បាប់ឈប់សម្រាកចំនួន
                                <?= convertToKhmerNumerals($request['num_date']) ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី
                                <?= translateDateToKhmer($request['start_date'], 'd') ?>
                                ខែ<?= translateDateToKhmer($request['start_date'], 'F') ?>
                                ឆ្នាំ<?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី
                                <?= translateDateToKhmer($request['end_date'], 'd') ?>
                                ខែ<?= translateDateToKhmer($request['end_date'], 'F') ?>
                                ឆ្នាំ<?= translateDateToKhmer($request['end_date'], 'Y') ?>
                            </span>
                        </p>
                        <p
                            style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;">
                            <strong class="h3">មូលហេតុ៖</strong> <?= $request['remarks'] ?> ។
                        </p>
                        <p
                            style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;">
                            តបតាមកម្មវត្ថុខាងលើ ខ្ញុំសូមគោរពជម្រាបជូន លោកប្រធាននាយកដ្ឋាន
                            មេត្តាជ្រាបដ៏ខ្ពង់ខ្ពស់ថា៖ខ្ញុំបាទ/ នាងខ្ញុំឈ្មោះ <?= $request['khmer_name'] ?> កើតថ្ងៃទី
                            <?= translateDateToKhmer($request['dob'], 'd') ?> ខែ
                            <?= translateDateToKhmer($request['dob'], 'F') ?> ឆ្នាំ
                            <?= translateDateToKhmer($request['dob'], 'Y') ?> មានតួនាទីជា
                            <?= $request['position_name'] ?> នៃ <?= $request['office_name'] ?> នៃ
                            <?= $request['department_name'] ?> ខ្ញុំសូមគោរពស្នើសុំការអនុញ្ញាតច្បាប់ចំនួន
                            <?= convertToKhmerNumerals($request['num_date']) ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី
                            <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ
                            <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ
                            <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី
                            <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ
                            <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ
                            <?= translateDateToKhmer($request['end_date'], 'Y') ?>
                            ដូចមូលហេតុ និងកាលបរិច្ឆេទក្នុងកម្មវត្ថុខាងលើ។
                        </p>
                        <p style="font-family: khmer mef1; font-size:16px; text-align:justify; text-indent: 50px;">
                            សេចក្តីដូចបានជម្រាបជូនខាងលើ សូម លោកប្រធាននាយកដ្ឋាន មេត្តាពិនិត្យ
                            និងសម្រេចអនុញ្ញាតច្បាប់ដោយក្តីអនុគ្រោះ។
                        </p>
                        <p style="font-family: khmer mef1; font-size:16px; text-align:justify; text-indent: 50px;">
                            សូម <b>លោកប្រធាននាយកដ្ឋាន </b> មេត្តាទទួលនូវការគោរពដ៏ខ្ពង់ខ្ពស់អំពីខ្ញុំ ។
                        </p>
                        <div class="row">
                            <!-- Department Office Approvals -->
                            <?php if (!empty($request['doffice'])): ?>
                                <?php foreach ($request['doffice'] as $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            អនុប្រធាន<?= htmlspecialchars(($request['office_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?php
                                            switch ($approval['approver_status'] ?? '') {
                                                case 'Approved':
                                                    echo 'បានអនុម័ត'; // Khmer for 'Approved'
                                                    break;
                                                case 'Rejected':
                                                    echo 'មិនអនុម័ត'; // Khmer for 'Rejected'
                                                    break;
                                                case 'Mission':
                                                    echo 'បេសកកម្ម'; // Khmer for 'Rejected'
                                                    break;
                                                case 'On Leave':
                                                    echo 'ច្បាប់'; // Khmer for 'Rejected'
                                                    break;
                                                default:
                                                    echo 'ស្ថានភាពមិនស្គាល់'; // Khmer for 'Unknown Status'
                                                    break;
                                            }
                                            ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Staff Information -->
                            <div class="col-6 mb-3"
                                style="font-family: khmer mef1; font-size: 18px; line-height: 30px; text-align: justify; text-align: center;">
                                <p style="margin-bottom: 0;">
                                    រាជធានីភ្នំពេញ ថ្ងៃទី <?= translateDateToKhmer($request['created_at'], 'd') ?>
                                    ខែ <?= translateDateToKhmer($request['created_at'], 'F') ?>
                                    ឆ្នាំ <?= translateDateToKhmer($request['created_at'], 'Y') ?>
                                </p>
                                <h3 class="mb-3">មន្ត្រីជំនាញ</h3>
                                <h3 class="mb-0">
                                    <?= htmlspecialchars($request['khmer_name'] ?? 'Unknown Name', ENT_QUOTES, 'UTF-8') ?>
                                </h3>
                            </div>

                            <!-- Office Head Approvals -->
                            <?php if (!empty($request['hoffice'])): ?>
                                <?php foreach ($request['hoffice'] as $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            ប្រធាន<?= htmlspecialchars(($request['office_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?php
                                            switch ($approval['approver_status'] ?? '') {
                                                case 'Approved':
                                                    echo 'បានអនុម័ត'; // Khmer for 'Approved'
                                                    break;
                                                case 'Rejected':
                                                    echo 'មិនអនុម័ត'; // Khmer for 'Rejected'
                                                    break;
                                                case 'Mission':
                                                    echo 'បេសកកម្ម'; // Khmer for 'Rejected'
                                                    break;
                                                case 'On Leave':
                                                    echo 'ច្បាប់'; // Khmer for 'Rejected'
                                                    break;
                                                default:
                                                    echo 'ស្ថានភាពមិនស្គាល់'; // Khmer for 'Unknown Status'
                                                    break;
                                            }
                                            ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Department Head Approvals -->
                            <?php if (!empty($request['ddepartment'])): ?>
                                <?php foreach ($request['ddepartment'] as $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            អនុប្រធាន<?= htmlspecialchars(($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?php
                                            switch ($approval['approver_status'] ?? '') {
                                                case 'Approved':
                                                    echo 'បានអនុម័ត'; // Khmer for 'Approved'
                                                    break;
                                                case 'Rejected':
                                                    echo 'មិនអនុម័ត'; // Khmer for 'Rejected'
                                                    break;
                                                case 'Mission':
                                                    echo 'បេសកកម្ម'; // Khmer for 'Rejected'
                                                    break;
                                                case 'On Leave':
                                                    echo 'ច្បាប់'; // Khmer for 'Rejected'
                                                    break;
                                                default:
                                                    echo 'ស្ថានភាពមិនស្គាល់'; // Khmer for 'Unknown Status'
                                                    break;
                                            }
                                            ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Head Department Approvals -->
                            <?php if (!empty($request['hdepartment'])): ?>
                                <?php foreach ($request['hdepartment'] as $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            ប្រធាន<?= htmlspecialchars(($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?= match ($approval['approver_status'] ?? '') {
                                                'Approved' => 'បានអនុម័ត',
                                                'Rejected' => 'មិនអនុម័ត',
                                                'Mission' => 'បេសកកម្ម',
                                                'On Leave' => 'ច្បាប់',
                                                default => 'ស្ថានភាពមិនស្គាល់',
                                            } ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Deputy Of Unit Approvals -->
                            <?php if (!empty($request['dunit'])): ?>
                                <?php foreach ($request['dunit'] as $index => $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?= match ($approval['approver_status'] ?? '') {
                                                'Approved' => 'បានអនុម័ត',
                                                'Rejected' => 'មិនអនុម័ត',
                                                'Mission' => 'បេសកកម្ម',
                                                'On Leave' => 'ច្បាប់',
                                                default => 'ស្ថានភាពមិនស្គាល់',
                                            } ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <!-- Head Of Unit Approvals -->
                            <?php if (!empty($request['unit'])): ?>
                                <?php foreach ($request['unit'] as $approval): ?>
                                    <div class="col-6 mb-3"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['doupdated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            ប្រធាន<?= htmlspecialchars(($approval['position_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <h3
                                            class="<?= ($approval['approver_status'] ?? '') === 'Approved' ? 'text-success' : 'text-danger' ?>">
                                            <?= match ($approval['approver_status'] ?? '') {
                                                'Approved' => 'បានអនុម័ត',
                                                'Rejected' => 'មិនអនុម័ត',
                                                'Mission' => 'បេសកកម្ម',
                                                'On Leave' => 'ច្បាប់',
                                                default => 'ស្ថានភាពមិនស្គាល់',
                                            } ?>
                                        </h3>
                                        <h3 class="mb-0">
                                            <?= htmlspecialchars($approval['approver_name'] ?? 'Unknown Approver', ENT_QUOTES, 'UTF-8') ?>
                                        </h3>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                body { font-family: Khmer-mef1, Khmer-mef2; }
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