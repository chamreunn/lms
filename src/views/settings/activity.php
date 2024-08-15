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

ob_start();
$title = "គណនីរបស់ខ្ញុំ";
include('src/common/header.php');
?>
<!-- Page header -->
<div class="row g-2 align-items-center mb-3">
    <div class="col">
        <h2 class="page-title">
            គណនីរបស់ខ្ញុំ
        </h2>
    </div>
</div>
<!-- Page body -->
<div class="card">
    <div class="row g-0">
        <div class="col-3 d-none d-md-block border-end">
            <div class="card-body">
                <h4 class="subheader">គណនីរបស់ខ្ញុំ</h4>
                <div class="list-group list-group-transparent">
                    <a href="/elms/my-account" class="list-group-item list-group-item-action d-flex align-items-center">គណនីរបស់ខ្ញុំ</a>
                    <a href="/elms/activity" class="list-group-item list-group-item-action d-flex align-items-center active">សកម្មភព</a>
                </div>
            </div>
        </div>
        <div class="col d-flex flex-column">
            <div class="col-12">
                <div class="card-body">
                    <h2 class="mb-4">សកម្មភាព</h2>
                    <?php if (empty($activities)) : ?>
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <img src="public/img/icons/svgs/empty.svg" alt="មិនមានសកម្មភាព">
                            </div>
                            <h2 class="text-muted">មិនមានសកម្មភាពបានកត់ត្រាទេ</h2>
                            <p class="text-muted">សកម្មភាពរបស់អ្នកនឹងបង្ហាញនៅទីនេះ។</p>
                        </div>
                    <?php else : ?>
                        <div class="divide-y">
                            <?php foreach ($activities as $activity) : ?>
                                <div>
                                    <div class="row">
                                        <div class="col-auto">
                                            <img class="avatar" src="<?= $activity['profile_picture'] ?>" alt="" style="object-fit: cover;">
                                        </div>
                                        <div class="col">
                                            <div class="text-truncate mb-1">
                                                <strong><?= $activity['khmer_name'] ?></strong> <?= $activity['action'] ?>
                                            </div>
                                            <div class="text-secondary"><?= $activity['created_at'] ?></div>
                                        </div>
                                        <div class="col-auto align-self-center">
                                            <div class="badge bg-primary"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<?php include('src/common/footer.php'); ?>