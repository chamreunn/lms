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
                    <a href="/elms/my-account" class="list-group-item list-group-item-action d-flex align-items-center active">គណនីរបស់ខ្ញុំ</a>
                    <a href="/elms/activity" class="list-group-item list-group-item-action d-flex align-items-center">សកម្មភព</a>
                </div>
            </div>
        </div>
        <div class="col d-flex flex-column">
            <div class="card-body">
                <h2 class="mb-4">គណនីរបស់ខ្ញុំ</h2>
                <h3 class="card-title">ពត៌មានគណនី</h3>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-xl" style="background-image: url('<?= htmlspecialchars($myaccounts['profile_picture']) ?>')" ;></span>
                    </div>
                    <div class="col-auto">
                        <!-- Form to change the profile picture -->
                        <form action="/elms/change-profile-picture" method="POST" enctype="multipart/form-data">
                            <label class="btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                </svg>
                                ផ្លាស់ប្តូររូបភាព
                                <input type="file" name="profile_picture" accept="image/*" onchange="this.form.submit()" style="display:none;">
                            </label>
                        </form>
                    </div>
                    <div class="col-auto">
                        <!-- Form to reset the profile picture -->
                        <button type="submit" class="btn btn-outline-danger" data-bs-target="#deleteModal" data-bs-toggle="modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                            លុបរូបភាព
                        </button>
                    </div>

                    <div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-status bg-danger"></div>
                                <form action="/elms/reset-profile-picture" method="POST">
                                    <div class="modal-body text-center py-4 mb-0">
                                        <h5 class="modal-title fw-bold text-danger mb-3">លុបរូបភាព</h5>
                                        <div class="col-auto">
                                            <span class="avatar avatar-xl mb-3" style="background-image: url('<?= htmlspecialchars($myaccounts['profile_picture']) ?>')" ;></span>
                                        </div>
                                        <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបរូបភាពនេះ?</p>
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                </div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">ឈ្មោះមន្ត្រី</div>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($myaccounts['khmer_name']) ?>" disabled>
                    </div>
                    <div class="col-md">
                        <div class="form-label fw-bold">USERNAME</div>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($myaccounts['username']) ?>" disabled>
                    </div>
                    <div class="col-md">
                        <div class="form-label fw-bold">ភេទ</div>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($myaccounts['gender']) ?>" disabled>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">តួនាទី</div>
                        <input type="text" class="form-control" value="<?= $_SESSION['position'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <div class="form-label">នាយកដ្ឋាន</div>
                        <input type="text" class="form-control" value="<?= $myaccounts['department_name'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <div class="form-label">ការិយាល័យ</div>
                        <input type="text" class="form-control" value="<?= $myaccounts['office_name'] ?>" disabled>
                    </div>
                </div>
                <h3 class="card-title mt-4">អាសយដ្ឋានអ៊ីម៉ែល</h3>
                <p class="card-subtitle">សូមពិនិត្យអាសយដ្ឋានអ៊ីម៉ែលអោយបានត្រឹមត្រូវ។​ ការស្នើសុំច្បាប់ ការដាក់លិខិតផ្សេងៗនិងត្រូវបានផ្ញើទៅកាន់អាសយដ្ឋានអ៊ីម៉ែលរបស់អ្នក។</p>
                <div>
                    <div class="row g-2">
                        <div class="col-10">
                            <input type="text" class="form-control" value="<?= htmlspecialchars($myaccounts['email']) ?>" style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;" disabled>
                        </div>
                        <div class="col-2">
                            <a href="#" class="btn btn-red w-100">
                                <span>ផ្លាស់ប្តូរ</span>
                            </a>
                        </div>
                    </div>
                </div>
                <h3 class="card-title mt-4">ពាក្យសម្ងាត់</h3>
                <p class="card-subtitle">សូមប្រើប្រាស់ពាក្យសម្ងាត់ដែលមានសុវត្ថិភាពខ្ពស់។ ត្រូវមានអក្សរធំ អក្សរតូច លេខ និងសញ្ញាជាដើម។</p>
                <div>
                    <a href="#" class="btn btn-red">
                        ផ្លាស់ប្តូរពាក្យសម្ងាត់ថ្មី
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<?php include('src/common/footer.php'); ?>