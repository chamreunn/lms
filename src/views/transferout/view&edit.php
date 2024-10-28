<?php
$title = "ពិនិត្យលិខិតផ្ទេរចេញ";
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
                        <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Main container for displaying and editing hold request -->
<div class="container-xl">
    <div class="row g-3">

        <!-- Transfer Request Details -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between">
                    <h3 class="card-title text-primary"><?= $title ?></h3>
                    <span class="badge <?= $getTransferouts[0]['status'] === 'approved' ? 'bg-success'
                        : ($getTransferouts[0]['status'] === 'rejected' ? 'bg-danger' : 'bg-warning') ?>">
                        <?= $getTransferouts[0]['status'] === 'approved' ? 'បានអនុម័ត'
                            : ($getTransferouts[0]['status'] === 'rejected' ? 'មិនអនុម័ត' : 'កំពុងរង់ចាំ') ?>
                    </span>
                </div>

                <!-- Transfer Details Form -->
                <form action="/elms/edit-transferout" method="POST" enctype="multipart/form-data">
                    <div class="card-body">
                        <input type="hidden" name="holdId" value="<?= $getTransferouts[0]['id'] ?? '' ?>">
                        <input type="hidden" name="approverId" value="<?= $getTransferouts[0]['approver_id'] ?? '' ?>">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['token'] ?? '' ?>">

                        <!-- Transfer Information Section -->
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary">ពី</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">នាយកដ្ឋាន</label>
                                    <input type="text" class="form-control" name="from_department"
                                        value="<?= $getTransferouts[0]['from_department_name'] ?? '' ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ការិយាល័យ</label>
                                    <input type="text" class="form-control" name="from_office"
                                        value="<?= $getTransferouts[0]['from_office_name'] ?? '' ?>" disabled>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary">ទៅកាន់</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">នាយកដ្ឋាន</label>
                                    <input type="text" class="form-control" name="to_department"
                                        value="<?= $getTransferouts[0]['to_department_name'] ?? '' ?>" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">ការិយាល័យ</label>
                                    <input type="text" class="form-control" name="to_office"
                                        value="<?= $getTransferouts[0]['to_office_name'] ?? '' ?>" disabled>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Details -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">កាលបរិច្ឆេទស្នើ</label>
                            <input type="text" class="form-control"
                                value="<?= $getTransferouts[0]['created_at'] ?? '' ?>" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label fw-bold text-primary">មូលហេតុ</label>
                            <textarea class="form-control" id="reason" name="reason"
                                required><?= $getTransferouts[0]['reason'] ?? '' ?></textarea>
                        </div>

                        <!-- Attachments Section -->
                        <div class="mb-3">
                            <label class="form-label fw-bold text-primary">Attachments</label>
                            <input type="file" class="form-control" id="attachment" name="attachment[]"
                                accept=".pdf,.docx" multiple>

                            <?php if (!empty($getTransferouts[0]['attachment'])): ?>
                                <p class="mt-3">Current Attachments:</p>
                                <ul class="list-group">
                                    <?php
                                    $attachments = explode(',', $getTransferouts[0]['attachment']);
                                    foreach ($attachments as $attachment): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="public/uploads/transferout-attachments/<?= $attachment ?>"
                                                target="_blank"><?= $attachment ?></a>
                                            <!-- Delete button that triggers the modal -->
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="attachmentId<?= $attachment ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
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
                                        <!-- Confirmation Modal for File Removal -->
                                        <div class="modal modal-blur fade" id="attachmentId<?= $attachment ?>">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title text-danger" id="confirmRemoveModalLabel">Confirm
                                                            File
                                                            Deletion</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Are you sure you want to delete this attachment?</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="button" class="btn btn-danger"
                                                            id="confirmDeleteButton">Delete</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>

                            <!-- Button to trigger the modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#attachmentModal">
                                Manage Attachments
                            </button>

                            <!-- Attachment Modal Form -->
                            <div class="modal fade" id="attachmentModal" tabindex="-1"
                                aria-labelledby="attachmentModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form method="post"
                                            action="index.php?controller=TransferoutController&action=updateAttachments"
                                            enctype="multipart/form-data">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="attachmentModalLabel">Manage Attachments
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Existing Attachments -->
                                                <?php if (!empty($getTransferouts['attachment'])): ?>
                                                    <p>Existing Attachments:</p>
                                                    <ul class="list-group">
                                                        <?php
                                                        $attachments = explode(',', $getTransferouts['attachment']);
                                                        foreach ($attachments as $attachment): ?>
                                                            <li
                                                                class="list-group-item d-flex justify-content-between align-items-center">
                                                                <a href="public/uploads/transferout-attachments/<?= $attachment ?>"
                                                                    target="_blank"><?= $attachment ?></a>
                                                                <input type="checkbox" name="delete_attachments[]"
                                                                    value="<?= $attachment ?>"> Delete
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                <?php endif; ?>
                                                <!-- Hidden input to keep track of existing attachments -->
                                                <input type="hidden" name="existing_attachments"
                                                    value="<?= implode(',', $attachments) ?>">

                                                <!-- New Attachment Upload -->
                                                <div class="mt-3">
                                                    <label for="new_attachments" class="form-label">Add New
                                                        Attachments</label>
                                                    <input type="file" name="new_attachments[]" class="form-control"
                                                        accept=".pdf,.docx" multiple>
                                                </div>
                                                <input type="hidden" name="transferout_id"
                                                    value="<?= $getTransferouts[0]['id'] ?>">
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card-footer">
                        <?php if ($getTransferouts[0]['status'] !== 'approved'): ?>
                            <button type="submit" class="btn btn-primary w-100">Edit</button>
                        <?php else: ?>
                            <button type="button" class="btn btn-secondary w-100" disabled>Edit (Approved)</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Approval Tracking Section -->
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="text-primary mb-0">Approval Tracking</h3>
                </div>
                <div class="card-body">
                    <ul class="steps steps-vertical">
                        <?php if (!empty($getTransferouts) && is_array($getTransferouts)): ?>
                            <?php foreach ($getTransferouts as $approvalStep): ?>
                                <li class="step-item <?= $approvalStep['approval_status'] === 'approved' ? 'active' : '' ?>">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= $approvalStep['profile'] ?>" class="avatar me-3" alt="Profile"
                                            style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <strong class="text-primary">
                                                <?= $approvalStep['approver_name'] ?? 'Unknown' ?>
                                            </strong>
                                            <span class="badge <?= $approvalStep['approval_status'] === 'approved' ? 'bg-success'
                                                : ($approvalStep['approval_status'] === 'rejected' ? 'bg-danger'
                                                    : 'bg-warning') ?>">
                                                <?= $approvalStep['approval_status'] === 'approved' ? 'Approved'
                                                    : ($approvalStep['approval_status'] === 'rejected' ? 'Rejected' : 'Pending') ?>
                                            </span>
                                            <div class="text-muted">
                                                <small>Date: <?= $approvalStep['approved_at'] ?? '-' ?></small>
                                            </div>
                                            <?php if (!empty($approvalStep['comment'])): ?>
                                                <div><strong>Comment:</strong> <?= $approvalStep['comment'] ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center py-3 text-muted">No approvals yet.</p>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>