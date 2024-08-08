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
                    <a href="./settings.html" class="list-group-item list-group-item-action d-flex align-items-center active">គណនីរបស់ខ្ញុំ</a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">My Notifications</a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">Connected Apps</a>
                    <a href="./settings-plan.html" class="list-group-item list-group-item-action d-flex align-items-center">Plans</a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">Billing & Invoices</a>
                </div>
                <h4 class="subheader mt-4">Experience</h4>
                <div class="list-group list-group-transparent">
                    <a href="#" class="list-group-item list-group-item-action">Give Feedback</a>
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
                                        <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបលុបរូបភាពនេះ?</p>
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

                <h3 class="card-title mt-4">Business Profile</h3>
                <div class="row g-3">
                    <div class="col-md">
                        <div class="form-label">Business Name</div>
                        <input type="text" class="form-control" value="Tabler">
                    </div>
                    <div class="col-md">
                        <div class="form-label">Business ID</div>
                        <input type="text" class="form-control" value="560afc32">
                    </div>
                    <div class="col-md">
                        <div class="form-label">Location</div>
                        <input type="text" class="form-control" value="Peimei, China">
                    </div>
                </div>
                <h3 class="card-title mt-4">Email</h3>
                <p class="card-subtitle">This contact will be shown to others publicly, so choose it carefully.</p>
                <div>
                    <div class="row g-2">
                        <div class="col-auto">
                            <input type="text" class="form-control w-auto" value="paweluna@howstuffworks.com">
                        </div>
                        <div class="col-auto"><a href="#" class="btn">
                                Change
                            </a></div>
                    </div>
                </div>
                <h3 class="card-title mt-4">Password</h3>
                <p class="card-subtitle">You can set a permanent password if you don't want to use temporary login codes.</p>
                <div>
                    <a href="#" class="btn">
                        Set new password
                    </a>
                </div>
                <h3 class="card-title mt-4">Public profile</h3>
                <p class="card-subtitle">Making your profile public means that anyone on the Dashkit network will be able to find
                    you.</p>
                <div>
                    <label class="form-check form-switch form-switch-lg">
                        <input class="form-check-input" type="checkbox">
                        <span class="form-check-label form-check-label-on">You're currently visible</span>
                        <span class="form-check-label form-check-label-off">You're
                            currently invisible</span>
                    </label>
                </div>
            </div>
            <div class="card-footer bg-transparent mt-auto">
                <div class="btn-list justify-content-end">
                    <a href="#" class="btn">
                        Cancel
                    </a>
                    <a href="#" class="btn btn-primary">
                        Submit
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<?php include('src/common/footer.php'); ?>