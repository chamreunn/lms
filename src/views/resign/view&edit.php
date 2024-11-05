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

                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">ឯកសារភ្ជាប់</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#addMore"
                                class="btn btn-primary w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                បន្ថែម
                            </button>

                            <?php if (!empty($getResignById[0]['attachments'])): ?>
                                <p class="mt-3">ឯកសារបច្ចុប្បន្ន៖</p>
                                <ul class="list-group mb-3">
                                    <?php
                                    // Explode the attachments string into an array
                                    $attachments = explode(',', $getResignById[0]['attachments']);
                                    foreach ($attachments as $attachment): // Loop through each attachment
                                        // Generate a unique ID for the modal using the attachment name
                                        $attachmentId = md5($attachment);
                                    ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <a href="public/uploads/resign-attachments/<?= htmlspecialchars($attachment) ?>"
                                                    target="_blank">
                                                    <?= htmlspecialchars($attachment) ?>
                                                </a>
                                            </div>
                                            <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#deleteAttachment<?= $attachmentId ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash mx-0">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </li>

                                        <!-- Delete Confirmation Modal -->
                                        <div class="modal fade" id="deleteAttachment<?= $attachmentId ?>" tabindex="-1"
                                            aria-labelledby="modalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                                <div class="modal-content">
                                                    <form action="/elms/deleteResignAttachment" method="POST">
                                                        <div class="modal-body text-center py-4 mb-0">
                                                            <input type="hidden" name="attachment"
                                                                value="<?= htmlspecialchars($attachment) ?>">
                                                            <input type="hidden" name="id"
                                                                value="<?= htmlspecialchars($getResignById[0]['id'] ?? '') ?>">
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
                                                            <h5 class="modal-title fw-bold text-danger">លុបឯកសារ</h5>
                                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបឯកសារ <span
                                                                    class="text-red fw-bold"><?= htmlspecialchars($attachment) ?></span>
                                                                នេះ?</p>
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
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="text-center">
                                    <img src="public/img/icons/svgs/empty.svg" alt="">
                                    <p>មិនមានឯកសារភ្ជាប់</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- add resign attachment modal  -->
        <div class="modal modal-blur fade" id="addMore" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-status bg-primary"></div>
                    <form action="/elms/addMoreAttachmentResign" method="POST" enctype="multipart/form-data">
                        <div class="modal-body text-center py-4 mb-0">
                            <input type="hidden" name="id" value="<?= $getResignById[0]['id'] ?? '' ?>">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon text-primary icon-lg icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                            </svg>
                            <h5 class="modal-title fw-bold text-primary">បន្ថែមឯកសារភ្ជាប់</h5>
                            <div class="input-group">
                                <ul class="selected-files text-start mb-3"></ul>
                                <label for="attachments"
                                    class="btn btn-primary file-label w-100 rounded">ជ្រើសរើសឯកសារភ្ជាប់</label>
                                <input type="file" name="moreAttachment[]" id="attachments" class="file-input d-none"
                                    multiple accept=".docx, .xlsx, .pdf">
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col">
                                        <button type="submit"
                                            class="btn btn-outline-primary ms-auto w-100">យល់ព្រម</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
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