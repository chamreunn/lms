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
    <div class="col-lg-12">
        <div class="card mb-3">
            <div class="card-header">
                <h4 class="text-primary mb-0">ព័ត៌មានលម្អិតអំពីច្បាប់</h4>
                <!-- Cancel Button -->
                <?php if ($request['status'] !== 'Approved'): ?>
                    <div class="ms-auto mb-0">
                        <button class="btn btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#cancelModal">បោះបង់សំណើ</button>
                    </div>

                    <div class="modal modal-blur fade" id="cancelModal" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content">
                                <div class="modal-status bg-danger"></div>
                                <form action="/elms/leave-delete" method="POST">
                                    <div class="modal-body text-center py-4 mb-0">
                                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
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
                                    <div class="modal-footer">
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
                <?php elseif ($request['status'] == 'Approved'): ?>
                    <div class="mb-3 ms-auto row">
                        <div class="col-sm-8">
                            <div class="dropdown">
                                <button class="btn btn-outline-success dropdown-toggle" type="button"
                                    id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                    ទាញយកច្បាប់ឈប់សម្រាក
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <li>
                                        <span class="dropdown-header">ទាញយក បោះពុម្ព</span>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" onclick="printContents(<?= $request['id'] ?>)" href="#">
                                            <!-- Download SVG icon from http://tabler-icons.io/i/settings -->
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
                                            <!-- Download SVG icon from http://tabler-icons.io/i/edit -->
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
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">ប្រភេទច្បាប់ :</th>
                            <td><span
                                    class="badge badge-primary <?= $request['color'] ?>"><?= $request['leave_type_name'] ?></span>
                            </td>
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
                                <span class="mx-2">
                                    <?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">ដល់កាលបរិច្ឆេទ :</th>
                            <td>
                                <span class="text-primary">
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
                                <span class="mx-2">
                                    <?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">រយៈពេល :</th>
                            <td>
                                <span class="text-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-24">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 0 0 5.998 8.485m12.002 -8.485a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5" />
                                        <path
                                            d="M12 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
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
                                <?php if ($request['attRequired'] === 'Yes'): ?>
                                    <?php if (empty($request['attachment'])): ?>
                                        <span class="text-danger">សូមភ្ជាប់ឯកសារភ្ជាប់</span>
                                    <?php else: ?>
                                        <a href="<?= $request['attachment'] ?>" target="_blank">មើលឯកសារ</a>
                                    <?php endif; ?>
                                <?php else: ?>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-user">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
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
                    <?php if (empty($request['approvals'])): ?>
                        <div class="d-flex flex-column w-100 justify-content-center align-items-center mb-2">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <span class="text-muted">មិនទាន់មានការអនុម័ត</span>
                        </div>
                    <?php else: ?>
                        <!-- Actual items -->
                        <?php foreach ($request['approvals'] as $index => $approval): ?>
                            <?php
                            $isLast = $index == count($request['approvals']) - 1;
                            $isWaiting = !$isLast && $request['approvals'][$index + 1]['status'] == 'Pending';
                            ?>
                            <div class="col-md-4">
                                <div
                                    class="card mb-3 <?= $approval['status'] == 'Rejected' ? 'border-danger' : ($approval['status'] == 'Approved' ? 'border-success' : ($approval['status'] == 'Cancelled' ? 'border-secondary' : 'border-warning')) ?>">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <img src="<?= $approval['profile'] ?>" class="avatar rounded-circle me-3" alt=""
                                                style="object-fit: cover;">
                                            <div>
                                                <h5 class="mb-0"><?= $approval['approver_name'] ?></h5>
                                                <small
                                                    class="text-muted"><?= translateDateToKhmer($approval['updated_at'], 'j F, Y h:i A') ?></small>
                                            </div>
                                            <span
                                                class="badge ms-auto <?= $approval['status'] == 'Pending' ? 'bg-warning' : ($approval['status'] == 'Approved' ? 'bg-success' : ($approval['status'] == 'Rejected' ? 'bg-danger' : 'bg-secondary')) ?>">
                                                <i
                                                    class="status-icon <?= $approval['status'] == 'Pending' ? 'ti ti-clock' : ($approval['status'] == 'Approved' ? 'ti ti-check-circle' : ($approval['status'] == 'Rejected' ? 'ti ti-x-circle' : 'ti ti-slash-circle')) ?>"></i>
                                                <?= $approval['status'] ?>
                                            </span>
                                        </div>
                                        <div class="mb-0">
                                            <textarea id="remarks-<?= $index ?>" class="form-control" rows="3"
                                                disabled><?= $approval['remarks'] ?? "មិនមានមតិយោបល់" ?></textarea>
                                        </div>
                                    </div>
                                    <?php if ($request['status'] !== 'Approved'): ?>
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
                                        style="width: 168px; padding-left: 50px" />
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
                            style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;">
                            <strong class="h3">កម្មវត្ថុ៖</strong> <span
                                style="text-indent: 50px;">សំណើសុំច្បាប់ឈប់សម្រាកចំនួន
                                <?= convertToKhmerNumerals($request['num_date']) ?>ថ្ងៃ ដោយគិតចាប់ពីថ្ងៃទី
                                <?= translateDateToKhmer($request['start_date'], 'd') ?> ខែ
                                <?= translateDateToKhmer($request['start_date'], 'F') ?> ឆ្នាំ
                                <?= translateDateToKhmer($request['start_date'], 'Y') ?> ដល់ថ្ងៃទី
                                <?= translateDateToKhmer($request['end_date'], 'd') ?> ខែ
                                <?= translateDateToKhmer($request['end_date'], 'F') ?> ឆ្នាំ
                                <?= translateDateToKhmer($request['end_date'], 'Y') ?>
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
                                    <div class="col-6"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['office_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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
                            <div class="col-6 mb-2"
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
                                    <div class="col-6 mb-2"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['office_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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
                                    <div class="col-6 mb-2"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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
                                    <div class="col-6 mb-2"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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

                            <!-- Deputy Of Unit Approvals -->
                            <?php if (!empty($request['dunit'])): ?>
                                <?php foreach ($request['dunit'] as $approval): ?>
                                    <div class="col-6 mb-2"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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

                            <!-- Head Of Unit Approvals -->
                            <?php if (!empty($request['unit'])): ?>
                                <?php foreach ($request['unit'] as $approval): ?>
                                    <div class="col-6 mb-2"
                                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align: justify; text-align: center;">
                                        <p style="margin-bottom: 5px;">
                                            រាជធានីភ្នំពេញ ថ្ងៃទី
                                            <?= translateDateToKhmer($approval['updated_at'] ?? '', 'd') ?>
                                            ខែ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'F') ?>
                                            ឆ្នាំ <?= translateDateToKhmer($approval['updated_at'] ?? '', 'Y') ?>
                                        </p>
                                        <p class="fw-bold" style="margin-bottom: 0;">
                                            <?= htmlspecialchars(($approval['position_name']) . "" . ($request['department_name'] ?? 'Unknown Position'), ENT_QUOTES, 'UTF-8') ?>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

<div class="modal modal-blur fade" id="apply-leave" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/apply-leave" enctype="multipart/form-data">
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
                                        <?= $leavetype['name'] ?> (<?= $leavetype['duration'] ?>ថ្ងៃ)
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
                            <div class="col-12 mb-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="signature"
                                        onchange="toggleFileInput(this, 'signatureFile')" checked>
                                    <label class="form-check-label cursor-pointer" for="signature">
                                        ហត្ថលេខា<span class="text-red fw-bold mx-1">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mb-3" id="signatureFile">
                                <label id="displayName" for="signature_file"
                                    class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                                    <span class="p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                        </svg>
                                    </span>
                                    <span>ហត្ថលេខា</span>
                                </label>
                                <input type="file" name="signature" id="signature_file" accept="image/png" required
                                    hidden onchange="displayFileName('signature_file', 'displayName')" />
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-send mx-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 14l11 -11" />
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

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