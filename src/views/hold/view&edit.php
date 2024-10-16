<?php
$title = "ពិនិត្យលិខិតព្យួរ";
require_once 'src/common/header.php';
?>

<!-- Page header -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    លិខិតផ្សេងៗ
                </div>
                <h2 class="page-title">
                    <?= $title ?? ""; ?>
                </h2>
            </div>
            <!-- Breadcrumb -->
            <div class="col-auto ms-auto">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/elms/dashboard">ទំព័រដើម</a></li>
                        <li class="breadcrumb-item active" aria-current="page">ពិនិត្យលិខិតព្យួរ</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Main container for displaying and editing hold request -->
<div class="container-xl">
    <div class="row g-3">
        <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between">
                    <h3 class="card-title text-primary">ព័ត៌មានលម្អិតលិខិតព្យួរ</h3>
                    <span
                        class="mx-1 <?= $getHoldById[0]['status'] == 'approved' ? 'badge bg-success' : ($getHoldById[0]['status'] == 'rejected' ? 'badge bg-danger' : 'badge bg-warning') ?>">
                        <?php
                        if ($getHoldById[0]['status'] == 'approved') {
                            echo 'បានអនុម័ត';
                        } elseif ($getHoldById[0]['status'] == 'rejected') {
                            echo 'មិនអនុម័ត';
                        } else {
                            echo 'កំពុងរង់ចាំ';
                        }
                        ?>
                    </span>
                </div>

                <!-- Edit form displaying current hold request data -->
                <form action="/elms/edit-hold" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <!-- Hidden input to store the request ID -->
                        <input type="hidden" name="request_id" value="<?= $getHoldById[0]['id'] ?? '' ?>">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="start_date"
                                        class="form-label fw-bold text-primary">ថ្ងៃចាប់ផ្តើម</label>
                                    <input type="text" class="form-control date-picker" id="start_date"
                                        name="start_date" value="<?= $getHoldById[0]['start_date'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label fw-bold text-primary">ថ្ងៃបញ្ចប់</label>
                                    <input type="text" class="form-control date-picker" id="end_date" name="end_date"
                                        value="<?= $getHoldById[0]['end_date'] ?? '' ?>" required>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-primary">រយៈពេល</label>
                                    <input type="text" class="form-control" id="duration"
                                        value="<?= $getHoldById[0]['duration'] ?? '' ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label fw-bold text-primary">មូលហេតុ</label>
                            <textarea class="form-control" id="reason" name="reason"
                                required><?= $getHoldById[0]['reason'] ?? '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label fw-bold text-primary">ឯកសារភ្ជាប់</label>
                            <input type="file" class="form-control" id="attachment" name="attachment"
                                accept=".pdf,.docx">
                            <!-- Display current attachment if exists -->
                            <?php if (!empty($getHoldById[0]['attachment'])): ?>
                                <p class="mt-3">ឯកសារបច្ចុប្បន្ន៖
                                    <a href="public/uploads/hold-attachments/<?= $getHoldById[0]['attachment'] ?>"
                                        target="_blank"><?= $getHoldById[0]['attachment'] ?></a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <!-- Submit and cancel buttons -->
                    <div class="card-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <a href="/elms/hold" class="btn w-100">បោះបង់</a>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">ធ្វើការកែប្រែ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tracking Section -->
        <div class="col-lg-8 col-sm-12 col-md-12">
            <!-- Process Tracker -->
            <div class="card border-0">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <h3 class="text-primary mb-0">តាមដានលិខិតព្យួរ</h3>
                </div>
                <div class="card-body">
                    <ul class="steps steps-vertical">
                        <?php if (!empty($getHoldById) && is_array($getHoldById)): ?>
                            <?php foreach ($getHoldById as $approvalStep): ?>
                                <li class="step-item <?= $approvalStep['approval_status'] == 'Approved' ? 'active' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <!-- Profile Image -->
                                        <img src="<?= $approvalStep['profile'] ?>" class="avatar me-3" alt="Profile"
                                            style="object-fit: cover;">
                                        <div>
                                            <div class="mb-2">
                                                <strong
                                                    class="h4 text-primary m-0"><?= $approvalStep['approver_name'] ?? 'Unknown' ?></strong>
                                                <span class="mx-1 <?= $approvalStep['approval_status'] == 'approved' ? 'badge bg-success'
                                                    : ($approvalStep['approval_status'] == 'rejected' ? 'badge bg-danger'
                                                        : ($approvalStep['approval_status'] == 'mission' ? 'badge bg-lime'
                                                            : ($approvalStep['approval_status'] == 'leave' ? 'badge bg-info'
                                                                : 'badge bg-warning'))) ?>">
                                                    <?php
                                                    if ($approvalStep['approval_status'] == 'approved') {
                                                        echo 'បានអនុម័ត';
                                                    } elseif ($approvalStep['approval_status'] == 'rejected') {
                                                        echo 'មិនអនុម័ត';
                                                    } elseif ($approvalStep['approval_status'] == 'mission') {
                                                        echo 'បេសកកម្ម';
                                                    } elseif ($approvalStep['approval_status'] == 'leave') {
                                                        echo 'សម្រាក';
                                                    } else {
                                                        echo 'កំពុងរង់ចាំ';
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="text-secondary">
                                                <div class="text-muted">
                                                    <strong>កាលបរិច្ឆេទ:</strong>
                                                    <?= $approvalStep['approved_at'] ?? '-' ?>
                                                </div>
                                                <?php if (!empty($approvalStep['comment'])): ?>
                                                    <div class="mt-2">
                                                        <strong>មតិយោបល់:</strong> <?= $approvalStep['comment'] ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-info-circle text-muted fa-2x" width="48" height="48"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="12" cy="12" r="9" />
                                    <line x1="12" y1="8" x2="12.01" y2="8" />
                                    <polyline points="11 12 12 12 12 16 13 16" />
                                </svg>
                                <p class="mt-3">គ្មានការអនុម័តសម្រាប់លិខិតព្យួរនេះទេ។</p>
                            </div>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>