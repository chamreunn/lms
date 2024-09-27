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
<div class="container mt-4">
    <!-- Page header -->
    <div class="row justify-content-between align-items-center mb-4">
        <div class="col-auto">
            <h2 class="page-title text-primary">
                គណនីរបស់ខ្ញុំ
            </h2>
        </div>
        <div class="col-auto">
            <a href="/elms/activity" class="btn btn-outline-primary">សកម្មភាព</a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Sidebar Links for Account Management -->
        <div class="col-md-3 d-none d-md-block">
            <div class="list-group">
                <a href="/elms/my-account" class="list-group-item active">គណនីរបស់ខ្ញុំ</a>
                <a href="/elms/activity" class="list-group-item">សកម្មភាព</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <!-- Account Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="mb-3">ពត៌មានគណនី</h4>
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="avatar avatar-xl"
                                style="background-image: url('<?= $_SESSION['user_profile'] ?>');"></span>
                        </div>
                        <div class="col">
                            <!-- Profile Picture Upload -->
                            <form action="/elms/change-profile-picture" method="POST" enctype="multipart/form-data"
                                class="d-inline-block">
                                <label class="btn btn-outline-primary">
                                    <input type="file" name="profile_picture" accept="image/*"
                                        onchange="this.form.submit()" style="display:none;">
                                    ផ្លាស់ប្តូររូបភាព
                                </label>
                            </form>
                        </div>
                        <div class="col-auto">
                            <!-- Profile Picture Reset Button -->
                            <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                លុបរូបភាព
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Info -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>ពត៌មានមន្ត្រី</h5>
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ឈ្មោះមន្ត្រី</label>
                            <input type="text" class="form-control" value="<?= $_SESSION['user_khmer_name'] ?>"
                                disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">USERNAME</label>
                            <input type="text" class="form-control" value="<?= $_SESSION['user_eng_name'] ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ភេទ</label>
                            <input type="text" class="form-control"
                                value="<?= htmlspecialchars($myaccounts['gender']) ?>" disabled>
                        </div>
                    </div>
                    <div class="row g-3 mt-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">តួនាទី</label>
                            <input type="text" class="form-control" value="<?= $_SESSION['position'] ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">នាយកដ្ឋាន</label>
                            <input type="text" class="form-control" value="<?= $_SESSION['departmentName'] ?>" disabled>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">ការិយាល័យ</label>
                            <input type="text" class="form-control" value="<?= $_SESSION['officeName'] ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email and Password -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5>អាសយដ្ឋានអ៊ីម៉ែល</h5>
                    <div class="row g-2">
                        <div class="col-10">
                            <input type="text" class="form-control" value="<?= $_SESSION['email'] ?>" disabled>
                        </div>
                        <div class="col-2">
                            <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal"
                                data-bs-target="#modalEmailChange">ផ្លាស់ប្តូរ</button>
                        </div>
                    </div>

                    <h5 class="mt-4">ពាក្យសម្ងាត់</h5>
                    <a href="#" class="btn btn-outline-danger">ផ្លាស់ប្តូរពាក្យសម្ងាត់</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Profile Picture Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">លុបរូបភាព</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/reset-profile-picture" method="POST">
                <div class="modal-body text-center">
                    <p>តើអ្នកប្រាកដទេថានិងលុបរូបភាពនេះ?</p>
                    <span class="avatar avatar-xl mb-3"
                        style="background-image: url('<?= htmlspecialchars($myaccounts['profile_picture']) ?>');"></span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-danger">បាទ / ចា៎</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Email Modal -->
<div class="modal fade" id="modalEmailChange" tabindex="-1" aria-labelledby="modalEmailChangeLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ផ្លាស់ប្តូរអាសយដ្ឋានអ៊ីម៉ែល</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="newEmail" class="form-label">អាសយដ្ឋានអ៊ីម៉ែលថ្មី</label>
                <input type="email" class="form-control" id="newEmail" name="new_email" placeholder="name@example.com">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">បោះបង់</button>
                <button type="button" class="btn btn-primary">ផ្លាស់ប្តូរ</button>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>