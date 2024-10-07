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

<div class="row g-3">
    <!-- Law Information Section -->
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
                                        <a href="<?= $request['attachment'] ?>" target="_blank"
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
                <h3 class="text-primary mb-0">ការអនុម័ត</h3>
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
                                    <img src="<?= !empty($approval['profile']) ? $approval['profile'] : 'public/img/icons/svgs/default_profile.png'; ?>"
                                        class="avatar me-3" alt="Profile" style="object-fit: cover;">
                                    <div>
                                        <div class="h5 mb-0 d-flex">
                                            <h4 class="text-primary mb-0"><?= htmlspecialchars($approval['approver_name']) ?>
                                            </h4>
                                            <span
                                                class="badge mb-0 bg-<?= $approval['status'] == 'Pending' ? 'warning' : ($approval['status'] == 'Approved' ? 'success' : 'danger') ?> ms-2">
                                                <?= htmlspecialchars($approval['status']) ?>
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
                                        <strong>មតិយោបល់:</strong> <?= htmlspecialchars($approval['remarks']) ?>
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

<?php include('src/common/footer.php'); ?>