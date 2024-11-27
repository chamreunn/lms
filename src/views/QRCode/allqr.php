<?php
$pretitle = "ទំព័រដើម";
$title = "All QR Codes";
include('src/common/header.php');
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">QR Code Details</h3>
    </div>
    <?php if (!empty($getQRs)): ?>
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Account</th>
                        <th>QR Code</th>
                        <th>Name</th>
                        <th>URL</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($getQRs as $index => $qr): ?>
                        <tr>
                            <td><?= htmlspecialchars($index + 1) ?></td>
                            <td width="300">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <img src="<?= $qr['profile'] ?>" class="avatar" alt="">
                                    </div>
                                    <div class="col-auto">
                                        <h3 class="mb-0"><?= htmlspecialchars($qr['user_name'] ?? 'Unknown') ?></h3>
                                        <p class="mb-0"><?= htmlspecialchars($qr['user_email'] ?? 'Unknown') ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <img src="data:image/png;base64,<?= htmlspecialchars($qr['image']) ?>" alt="QR Code"
                                    width="100">
                            </td>
                            <td width="100"><?= htmlspecialchars($qr['name'] ?? 'Unknown') ?></td>
                            <td width="20"><a href="<?= htmlspecialchars($qr['url']) ?>"
                                    target="_blank"><?= htmlspecialchars($qr['url']) ?></a></td>
                            <td><?= htmlspecialchars($qr['created_at'] ?? 'Unknown') ?></td>
                            <td>
                                <div class="d-flex">
                                    <a href="#" data-bs-target="#deleteQr<?= $qr['id'] ?>" data-bs-toggle="modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash text-danger fw-bolder">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </a>
                                    <a href="#" class="mx-2" data-bs-target="#downloadQr<?= $qr['id'] ?>"
                                        data-bs-toggle="modal">
                                        <button class="btn btn-primary btn-sm">ទាញយក QR</button>
                                    </a>
                                </div>
                                <!-- Delete Modal -->
                                <div class="modal modal-blur fade" id="deleteQr<?= $qr['id'] ?>" tabindex="-1" role="dialog"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-status bg-danger"></div>
                                            <form action="/elms/deleteQR" method="POST">
                                                <div class="modal-body text-center py-4 mb-0">
                                                    <input type="hidden" name="id" value="<?= $qr['id'] ?>">
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
                                                    <h5 class="modal-title fw-bold text-danger">លុប <?= $title ?></h5>
                                                    <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុប <strong
                                                            class="text-danger"><?= $title ?></strong> នេះ?</p>
                                                </div>
                                                <div class="modal-footer bg-light border-top">
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

                                <!-- Download Modal -->
                                <div class="modal modal-blur fade" id="downloadQr<?= $qr['id'] ?>" tabindex="-1" role="dialog"
                                    aria-modal="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Download QR Code</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <img src="data:image/png;base64,<?= htmlspecialchars($qr['image']) ?>"
                                                    alt="QR Code" class="img-fluid">
                                            </div>
                                            <div class="modal-footer bg-light justify-content-center">
                                                <a href="data:image/png;base64,<?= htmlspecialchars($qr['image']) ?>"
                                                    download="qr_code_<?= $qr['id'] ?>.png" class="btn btn-primary">
                                                    <span>ទាញយក</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-download mx-2 me-0">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M7 11l5 5l5 -5" />
                                                        <path d="M12 4l0 12" />
                                                    </svg>
                                                </a>
                                                <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="card-body">
            no data found.
        </div>
    <?php endif; ?>
</div>

<?php include('src/common/footer.php'); ?>