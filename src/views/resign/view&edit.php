<?php
$title = "ពិនិត្យលិខិតលាឈប់";
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
                        <li class="breadcrumb-item active" aria-current="page">ពិនិត្យលិខិតលាឈប់</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Main container for displaying and editing hold request -->
<div class="container-xl">
    <div class="row g-3">
        <!-- Hold Request Details -->
        <div class="col-lg-4 col-sm-12 col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between">
                    <h3 class="card-title text-primary">ព័ត៌មានលម្អិតលិខិតលាឈប់</h3>
                </div>

                <!-- Edit form displaying current hold request data -->
                <form action="/elms/edit-resign" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <!-- Hidden input to store the request ID -->
                        <input type="hidden" name="resignId" value="<?= $getResignById[0]['id'] ?? '' ?>">
                        <!-- Hidden input to store the request ID -->
                        <input type="hidden" name="approverId" value="<?= $getResignById[0]['approver_id'] ?? '' ?>">

                        <!-- CSRF token (if your framework uses it) -->
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <div class="mb-3">
                            <label for="workexperience" class="form-label fw-bold text-primary">បទពិសោធន៍</label>
                            <textarea class="form-control" id="workexperience" name="workexperience"
                                required><?= $getResignById[0]['workexperience'] ?? '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label fw-bold text-primary">មូលហេតុ</label>
                            <textarea class="form-control" id="reason" name="reason"
                                required><?= $getResignById[0]['reason'] ?? '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="attachment" class="form-label fw-bold text-primary">ឯកសារភ្ជាប់</label>
                            <input type="file" class="form-control" id="attachment" name="attachment[]"
                                accept=".pdf,.docx" multiple>
                            <?php if (!empty($getResignById[0]['attachment'])): ?>
                                <p class="mt-3">ឯកសារបច្ចុប្បន្ន៖</p>
                                <ul class="list-group">
                                    <?php
                                    $attachments = explode(',', $getResignById[0]['attachment']);
                                    foreach ($attachments as $attachment): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="public/uploads/hold-attachments/<?= $attachment ?>" target="_blank">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                                </svg>
                                                <?= $attachment ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                        </div>
                    </div>

                    <!-- Submit and cancel buttons -->

                    <div class="card-footer">
                        <div class="w-100">
                            <div class="row">
                                <div class="col">
                                    <button type="submit" class="btn btn-primary w-100">កែប្រែ</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Approval Tracking Section -->
        <div class="col-lg-8 col-sm-12 col-md-12">
            <div class="card border-0">
                <div class="card-header bg-light d-flex align-items-center justify-content-between">
                    <h3 class="text-primary mb-0">តាមដានលិខិតលាឈប់</h3>
                </div>
                <div class="card-body">
                    <ul class="steps steps-vertical">
                        <?php if (!empty($getResignById) && is_array($getResignById)): ?>
                            <?php foreach ($getResignById as $approvalStep): ?>
                                <li class="step-item <?= $approvalStep['approval_status'] == 'approved' ? 'active' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <!-- Profile Image -->
                                        <img src="<?= $approvalStep['profile'] ?>" class="avatar me-3" alt="Profile"
                                            style="object-fit: cover;">
                                        <div>
                                            <div class="mb-2">
                                                <strong class="h4 text-primary m-0">
                                                    <?= $approvalStep['approver_name'] ?? 'Unknown' ?>
                                                </strong>
                                                <span class="mx-1 <?= $approvalStep['approval_status'] == 'approved' ? 'badge bg-success'
                                                                        : ($approvalStep['approval_status'] == 'rejected' ? 'badge bg-danger'
                                                                            : ($approvalStep['approval_status'] == 'mission' ? 'badge bg-danger-lt'
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