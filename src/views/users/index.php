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
$title = "បង្កើតគណនី";
include('create_user_modal.php');
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
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <input type="search" class="form-control d-inline-block w-9 me-3" value="Search user…" />
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-user">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            New user
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
?>

<div class="row row-cards">
    <?php if (!empty($paginated_users)): ?>
        <?php foreach ($paginated_users as $key => $value): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card rounded-3 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <span class="avatar avatar-xl mb-3"
                            style="background-image: url(https://hrms.iauoffsa.us/images/<?= $value['image'] ?? "default_image.svg" ?>); object-fit: cover;"></span>
                        <h3 class="m-0 mb-1">
                            <form action="<?= '/elms/view_detail' ?>" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $value['id'] ?>">
                                <a href="<?= '/elms/view_detail?user_id=' . urlencode($value['id']) ?>">
                                    <?= htmlspecialchars($value['lastNameKh'] . " " . $value['firstNameKh'] ?? '') ?>
                                </a>
                            </form>
                        </h3>
                        <div class="mb-2">
                            <span class="badge <?= $value['position_color'] ?? '' ?>"><?= htmlspecialchars($value['role']) ?></span>
                        </div>
                        <div class="text-muted mb-2">
                            <?= htmlspecialchars($value['office_name'] ?? $value['department_name'] ?? '') ?>
                        </div>
                        <div class="mb-0">
                            <?php if ($value['active'] === '1'): ?>
                                <span class="badge bg-success-lt"><?= "Active" ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary-lt"><?= "Inactive" ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex">
                        <a href="<?= '/elms/edit_user_detail?user_id=' . urlencode($value['id']) ?>" class="card-btn"
                            data-bs-toggle="tooltip" title="កែប្រែ">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            កែប្រែ
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No users found</p>
    <?php endif; ?>
</div>

<!-- pagination  -->
<div class="d-flex mt-4">
    <ul class="pagination ms-auto">
        <!-- Previous Button -->
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page - 1 ?>" tabindex="-1" aria-disabled="true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                        <polyline points="15 6 9 12 15 18" />
                    </svg>
                    prev
                </a>
            </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?= $page + 1 ?>">
                    next
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                        <polyline points="9 6 15 12 9 18" />
                    </svg>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<?php include('src/common/footer.php'); ?>

<style>
    .avatar-upload {
        position: relative;
        display: inline-block;
        cursor: pointer;
        width: 120px;
        height: 120px;
    }

    .avatar-upload img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .avatar-upload-button {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        opacity: 0;
        transition: opacity 0.3s;
        border-radius: 10px;
    }

    .avatar-upload:hover .avatar-upload-button {
        opacity: 1;
        cursor: pointer;
    }
</style>