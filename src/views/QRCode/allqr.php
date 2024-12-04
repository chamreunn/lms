<?php
$pretitle = "ទំព័រដើម";
$title = "All QR Codes";
include('src/common/header.php');
?>

<?php if (!empty($getQRs)): ?>
    <div class="d-flex justify-content-end align-items-center mb-3">
        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteAllModal">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 7l16 0" />
                <path d="M10 11l0 6" />
                <path d="M14 11l0 6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
            </svg>
            <span>លុបទាំងអស់</span>
        </button>
    </div>

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
                                class="img-fluid rounded" style="max-width: 150px;" data-bs-toggle="modal"
                                data-bs-target="#qrModal<?= $qr['id'] ?>">
                        </div>
                    </div>
                    <div class="card-footer bg-light w-100">
                        <div class="row g-3">
                            <div class="col">
                                <!-- Delete Action -->
                                <a href="#" class="btn btn-outline-danger w-100" data-bs-target="#deleteQr<?= $qr['id'] ?>"
                                    data-bs-toggle="modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                    </svg>
                                    <span>លុប</span>
                                </a>
                            </div>
                            <div class="col">
                                <!-- Open Modal for Download -->
                                <button class="btn btn-primary w-100" data-bs-toggle="modal"
                                    data-bs-target="#qrModal<?= $qr['id'] ?>">
                                    ពិនិត្យមើល
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Delete Modal -->
            <div class="modal modal-blur fade" id="deleteQr<?= $qr['id'] ?>" tabindex="-1" role="dialog" aria-modal="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-status bg-danger"></div>
                        <form action="/elms/deleteQR" method="POST">
                            <div class="modal-body text-center py-4 mb-0">
                                <input type="hidden" name="id" value="<?= $qr['id'] ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon mb-2 text-danger icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 9v4"></path>
                                    <path
                                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                    </path>
                                    <path d="M12 16h.01"></path>
                                </svg>
                                <h5 class="modal-title fw-bold text-danger">លុប <?= $title ?></h5>
                                <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុប <strong class="text-danger"><?= $title ?></strong> នេះ?
                                </p>
                            </div>
                            <div class="modal-footer bg-light border-top">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-danger ms-auto w-100">យល់ព្រម</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modal for QR View & Download -->
            <div class="modal modal-blur fade" id="qrModal<?= $qr['id'] ?>" tabindex="-1"
                aria-labelledby="qrModalLabel<?= $qr['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="qrModalLabel<?= $qr['id'] ?>">QR Code -
                                <?= htmlspecialchars($qr['user_name']) ?>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- A5 Poster -->
                            <div class="poster card">
                                <div class="card-status-top bg-primary" style="height: 10px;"></div>
                                <div
                                    class="card-body d-flex flex-column align-items-center text-center justify-content-between h-100">
                                    <div class="card-stamp mt-2">
                                        <div class="card-stamp-icon bg-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M7 17l0 .01" />
                                                <path
                                                    d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M7 7l0 .01" />
                                                <path
                                                    d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M17 7l0 .01" />
                                                <path d="M14 14l3 0" />
                                                <path d="M20 14l0 .01" />
                                                <path d="M14 14l0 3" />
                                                <path d="M14 20l3 0" />
                                                <path d="M17 17l3 0" />
                                                <path d="M20 17l0 3" />
                                            </svg>
                                        </div>
                                    </div>
                                    <!-- Logo Section -->
                                    <div class="mb-2">
                                        <img src="public/img/icons/brands/Login_logo_350.png" alt="Logo"
                                            style="max-width: 150px;" class="img-fluid">
                                    </div>
                                    <div class="mb-0 mt-0">
                                        <p class="text-primary fs-3 fw-bold">
                                            <span class="text-danger"
                                                style="font-family:system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">QR
                                                Code</span>សម្រាប់ស្កេនវត្តមានប្រចាំថ្ងៃរបស់អ្នក
                                        </p>
                                    </div>
                                    <!-- QR Code Section -->
                                    <div class="mb-0 position-relative d-flex flex-column align-items-center">
                                        <div class="qr-wrapper position-relative" style="border-radius: 12px;">
                                            <!-- Angled Corners -->
                                            <div class="position-absolute top-0 start-0"
                                                style="border-left: 5px solid #000; border-top: 5px solid #000; width: 40px; height: 40px;">
                                            </div>
                                            <div class="position-absolute top-0 end-0"
                                                style="border-right: 5px solid #000; border-top: 5px solid #000; width: 40px; height: 40px;">
                                            </div>
                                            <div class="position-absolute bottom-0 start-0"
                                                style="border-left: 5px solid #000; border-bottom: 5px solid #000; width: 40px; height: 40px;">
                                            </div>
                                            <div class="position-absolute bottom-0 end-0"
                                                style="border-right: 5px solid #000; border-bottom: 5px solid #000; width: 40px; height: 40px;">
                                            </div>
                                            <!-- QR Code -->
                                            <div class="text-center">
                                                <img src="data:image/png;base64,<?= htmlspecialchars($qr['image']) ?>"
                                                    alt="QR Code" class="img-fluid" style="max-width: 300px;">
                                            </div>
                                        </div>
                                        <!-- User Name -->
                                        <h1 class="text-primary mt-3"><?= htmlspecialchars($qr['user_name']) ?></h1>
                                    </div>

                                    <!-- User Name Section -->
                                    <footer>
                                        <small class="text-danger"><em>*
                                                សូមទំនាក់ទំនងមកការិ.ព័ត៌មានវិទ្យាប្រសិនបើលោកអ្នកមានបញ្ហាក្នុងការប្រើប្រាស់ប្រព័ន្ធ</em></small>
                                    </footer>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <!-- Modified Download Button to Trigger PDF Download -->
                            <button type="button" class="btn btn-success downloadPosterQR">Download as PDF</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="text-center py-5">
        <img src="public/img/icons/svgs/empty.svg" alt="">
        <p class="text-muted">មិនមានទិន្នន័យ។</p>
    </div>
<?php endif; ?>

<!-- Delete All Modal -->
<div class="modal modal-blur fade" id="deleteAllModal" tabindex="-1" aria-labelledby="deleteAllModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4 mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon mb-2 text-danger icon-lg">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 9v4"></path>
                    <path
                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                    </path>
                    <path d="M12 16h.01"></path>
                </svg>
                <h5 class="modal-title fw-bold text-danger">លុបទាំងអស់</h5>
                <p class="mb-0">តើអ្នកប្រាកដទេថានិង <strong class="text-danger">លុបទាំងអស់</strong> ?
                    ប្រសិនបើលុបលោកអ្នកមិនអាចយកមកវិញបានទេ ។
                </p>
            </div>
            <div class="modal-footer bg-light border-top">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                        </div>
                        <div class="col">
                            <form action="/elms/dlallqr" method="POST">
                                <button type="submit" class="btn btn-danger ms-auto w-100">យល់ព្រម</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

