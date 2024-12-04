<?php
$pretitle = "ទំព័រដើម";
$title = "All QR Codes";
include('src/common/header.php');
?>
<div class="d-flex justify-content-end align-items-center mb-3">
    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
        Delete All
    </button>
</div>
<?php if (!empty($getQRs)): ?>
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($getQRs as $index => $qr): ?>
            <div class="col">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h6 class="text-secondary mb-0">#<?= htmlspecialchars($index + 1) ?></h6>
                        <small class="text-muted"><?= htmlspecialchars($qr['created_at'] ?? 'Unknown') ?></small>
                    </div>
                    <div class="card-body text-center">
                        <!-- Profile Section -->
                        <div class="mb-3">
                            <img src="<?= $qr['profile'] ?>" class="rounded-circle border border-2" alt="Profile Picture"
                                width="60" height="60">
                        </div>
                        <h5 class="card-title text-primary"><?= htmlspecialchars($qr['user_name'] ?? 'Unknown') ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($qr['user_email'] ?? 'Unknown') ?></p>

                        <!-- QR Code -->
                        <div class="mb-3">
                            <img src="data:image/png;base64,<?= htmlspecialchars($qr['image']) ?>" alt="QR Code"
                                class="img-fluid rounded" style="max-width: 150px;">
                        </div>
                    </div>
                    <div class="card-footer bg-light w-100">
                        <div class="row g-3">
                            <div class="col">
                                <!-- Delete Action -->
                                <button class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#deleteQrModal<?= $qr['id'] ?>">
                                    Delete
                                </button>
                            </div>
                            <div class="col">
                                <!-- Download Action -->
                                <form action="/elms/downloadqr" method="get">
                                    <input type="hidden" name="qr_id" value="<?= htmlspecialchars($qr['id']) ?>">
                                    <input type="hidden" name="user_name" value="<?= htmlspecialchars($qr['user_name']) ?>">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Download QR
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <p class="text-muted">No data found.</p>
    </div>
<?php endif; ?>

<!-- Delete All Modal -->
<div class="modal fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteAllModalLabel">Confirm Delete All</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete all QR codes? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="delete_all_qr.php" method="post">
                    <button type="submit" class="btn btn-danger">Delete All</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Individual Delete Modals -->
<?php foreach ($getQRs as $qr): ?>
    <div class="modal fade" id="deleteQrModal<?= $qr['id'] ?>" tabindex="-1"
        aria-labelledby="deleteQrModalLabel<?= $qr['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger" id="deleteQrModalLabel<?= $qr['id'] ?>">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete the QR code for <?= htmlspecialchars($qr['user_name'] ?? 'Unknown') ?>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form action="delete_qr.php" method="post">
                        <input type="hidden" name="qr_id" value="<?= $qr['id'] ?>">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>


<?php include('src/common/footer.php'); ?>