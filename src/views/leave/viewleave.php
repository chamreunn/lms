<!-- src/views/leave/viewleave.php -->
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

$title = "ពិនិត្យមើលច្បាប់ឈប់សម្រាក";
ob_start();
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
                    <a href="/elms/apply-leave" class="btn btn-primary d-none d-sm-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        បង្កើតសំណើច្បាប់
                    </a>
                    <a href="/elms/apply-leave" class="btn btn-primary d-sm-none btn-icon">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    $days = [
        'Mon' => 'ច័ន្ទ',
        'Tue' => 'អង្គារ',
        'Wed' => 'ពុធ',
        'Thu' => 'ព្រហស្បតិ៍',
        'Fri' => 'សុក្រ',
        'Sat' => 'សៅរ៍',
        'Sun' => 'អាទិត្យ'
    ];
    $months = [
        'January' => 'មករា',
        'February' => 'កុម្ភៈ',
        'March' => 'មីនា',
        'April' => 'មេសា',
        'May' => 'ឧសភា',
        'June' => 'មិថុនា',
        'July' => 'កក្កដា',
        'August' => 'សីហា',
        'September' => 'កញ្ញា',
        'October' => 'តុលា',
        'November' => 'វិច្ឆិកា',
        'December' => 'ធ្នូ'
    ];

    $translatedDay = $days[date('D', strtotime($date))];
    $translatedMonth = $months[date('F', strtotime($date))];
    $translatedDate = str_replace(
        [date('D', strtotime($date)), date('F', strtotime($date))],
        [$translatedDay, $translatedMonth],
        date($format, strtotime($date))
    );

    return $translatedDate;
}
?>
<div class="card rounded-3 shadow-sm">
    <div class="card-header justify-content-between">
        <h3 class="mb-0"><?= $title ?></h3>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs nav-fill" data-bs-toggle="tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-home-7" class="nav-link active" data-bs-toggle="tab" aria-selected="true" role="tab"><!-- Download SVG icon from http://tabler-icons.io/i/home -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-description">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 17h6" />
                                <path d="M9 13h6" />
                            </svg>
                            ព័ត៌មានអំពីច្បាប់ឈប់សម្រាក
                            <div class="badge mx-2 <?= $request['status'] == 'Pending' ? 'badge bg-warning-lt' : '' ?>
                                        <?= $request['status'] == 'Approved' ? 'badge bg-success-lt' : '' ?>
                                        <?= $request['status'] == 'Rejected' ? 'badge bg-danger-lt' : '' ?>
                                        <?= $request['status'] == 'Cancelled' ? 'badge bg-secondary-lt' : '' ?>">
                                <?= $request['status'] ?>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-profile-7" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1"><!-- Download SVG icon from http://tabler-icons.io/i/user -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                            </svg>
                            ឯកសារភ្ជាប់
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#tabs-activity-7" class="nav-link" data-bs-toggle="tab" aria-selected="false" role="tab" tabindex="-1"><!-- Download SVG icon from http://tabler-icons.io/i/activity -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 9h8" />
                                <path d="M8 13h6" />
                                <path d="M12.01 18.594l-4.01 2.406v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v5.5" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            ការអនុម័ត និងមតិយោបល់
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active show" id="tabs-home-7" role="tabpanel">
                        <form>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ប្រភេទច្បាប់ :</label>
                                <div class="col-sm-8">
                                    <div class="badge <?= $request['color'] ?>"><?= $request['leave_type_name'] ?></div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ចាប់ពីកាលបរិច្ឆេទ :</label>
                                <div class="col-sm-8">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M7 14h.013" />
                                                <path d="M10.01 14h.005" />
                                                <path d="M13.01 14h.005" />
                                                <path d="M16.015 14h.005" />
                                                <path d="M13.015 17h.005" />
                                                <path d="M7.01 17h.005" />
                                                <path d="M10.01 17h.005" />
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" value="<?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ដល់កាលបរិច្ឆេទ :</label>
                                <div class="col-sm-8">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M7 14h.013" />
                                                <path d="M10.01 14h.005" />
                                                <path d="M13.01 14h.005" />
                                                <path d="M16.015 14h.005" />
                                                <path d="M13.015 17h.005" />
                                                <path d="M7.01 17h.005" />
                                                <path d="M10.01 17h.005" />
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" value="<?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?>" disabled>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">រយៈពេល​ :</label>
                                <div class="col-sm-8">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-stats">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M18 14v4h4" />
                                                <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                                <path d="M15 3v4" />
                                                <path d="M7 3v4" />
                                                <path d="M3 11h16" />
                                            </svg>
                                        </span>
                                        <input type="text" class="form-control" value="<?= $request['num_date'] ?>ថ្ងៃ" disabled>
                                    </div>
                                </div>
                            </div>
                             <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">ទំនាក់ទំនង :</label>
                                <div class="col-sm-8">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                            </svg>
                                        </span>
                                        <input type="text" value="<?= $request['phone_number'] ?>" class="form-control overflow-hidden" style="resize: none;" disabled />
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label class="col-sm-4 col-form-label">មូលហេតុ :</label>
                                <div class="col-sm-8">
                                    <div class="input-icon">
                                        <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                        </span>
                                        <textarea type="text" class="form-control overflow-hidden" style="resize: none;" disabled><?= $request['remarks'] ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane" id="tabs-profile-7" role="tabpanel">
                        <?php if (empty($request['attachment'])) : ?>
                            <div class="empty">
                                <div class="empty-img">
                                    <img src="public/img/illustrations/no-data-found.svg" alt="">
                                </div>
                                <h3 class="empty-title mb-0 d-flex align-items-center">
                                    <span class="icon mb-3 me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mood-puzzled">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14.986 3.51a9 9 0 1 0 1.514 16.284c2.489 -1.437 4.181 -3.978 4.5 -6.794" />
                                            <path d="M10 10h.01" />
                                            <path d="M14 8h.01" />
                                            <path d="M12 15c1 -1.333 2 -2 3 -2" />
                                            <path d="M20 9v.01" />
                                            <path d="M20 6a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                        </svg>
                                    </span>
                                    មិនមានឯកសារភ្ជាប់!
                                </h3>
                            </div>
                        <?php else : ?>
                            <div class="card">
                                <div class="card-body d-flex align-items-center">
                                    <div class="me-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-file">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                            <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-1">Attached Document</h5>
                                        <p class="card-text mb-2"><?= htmlspecialchars($request['attachment']) ?></p>
                                        <a href="public/uploads/leave_attachments/<?= htmlspecialchars($request['attachment']) ?>" class="btn btn-primary" target="_blank">View Document</a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="tab-pane" id="tabs-activity-7" role="tabpanel">
                        <?php if (empty($request['approvals'])) : ?>
                            <div class="empty text-center">
                                <div class="empty-img">
                                    <img src="public/img/illustrations/no-data-found.svg" alt="No Data Found">
                                </div>
                                <p class="empty-title">មិនមានការអនុម័តទេ!</p>
                            </div>
                        <?php else : ?>
                            <div class="list-group bg-light rounded-3 p-2 pb-0 border">
                                <?php foreach ($request['approvals'] as $approval) : ?>
                                    <a type="button" class="list-group-item list-group-item-action flex-column align-items-start mb-2 rounded-3" data-bs-toggle="modal" data-bs-target="#approvalModal<?= $approval['id'] ?>" data-approval='<?= json_encode($approval) ?>'>
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $approval['profile'] ?>" class="avatar rounded-circle me-3" alt="">
                                                <div class="">
                                                    <h5 class="mb-0"><?= $approval['approver_name'] ?></h5>
                                                    <small class="badge mb-2 <?= $approval['position_color'] ?>"><?= $approval['position_name'] ?></small><br>
                                                    <small class="text-muted">អនុម័តនៅ : <?= translateDateToKhmer($approval['updated_at'], 'D F j, Y h:i A') ?></small>
                                                </div>
                                            </div>
                                            <div>
                                                <span class="badge 
                        <?= $approval['status'] == 'Pending' ? 'bg-warning' : '' ?>
                        <?= $approval['status'] == 'Approved' ? 'bg-success' : '' ?>
                        <?= $approval['status'] == 'Rejected' ? 'bg-danger' : '' ?>
                        <?= $approval['status'] == 'Cancelled' ? 'bg-secondary' : '' ?>">
                                                    <i class="status-icon <?= $approval['status'] == 'Pending' ? 'bi bi-clock' : '' ?>
                            <?= $approval['status'] == 'Approved' ? 'bi bi-check-circle' : '' ?>
                            <?= $approval['status'] == 'Rejected' ? 'bi bi-x-circle' : '' ?>
                            <?= $approval['status'] == 'Cancelled' ? 'bi bi-slash-circle' : '' ?>"></i>
                                                    <?= $approval['status'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- Modal Template -->
                                    <div class="modal modal-blur fade" id="approvalModal<?= $approval['id'] ?>" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approvalModalLabel">Approval Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <img src="<?= $approval['profile'] ?>" id="modalProfileImage" class="avatar rounded-circle me-3" alt="">
                                                        <div class="w-100">
                                                            <h5 class="mb-0"><?= $approval['approver_name'] ?></h5>
                                                            <small class="text-muted">អនុម័តនៅ : <?= translateDateToKhmer($approval['updated_at'], 'D, j F Y h:i A') ?></small><br>
                                                            <textarea name="remarks" style="resize: none;" class="form-control mt-2" disabled><?= $approval['remarks'] ?? "មិនមានមតិយោបល់" ?></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                                                </div>
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

    <div class="card-footer rounded-bottom">
        <div class="row justify-content-end">
            <div class="col-lg-2">
                <?php if ($request['status'] !== 'Cancelled') { ?>
                    <a href="/elms/leave-requests" data-bs-toggle="modal" data-bs-target="#cancelModel" class="btn btn-danger w-100">បោះបង់</a>
                    <!-- Modal -->
                    <div class="modal modal-blur fade" id="cancelModel" tabindex="-1" position="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered" position="document">
                            <div class="modal-content">
                                <div class="modal-status bg-danger"></div>
                                <form action="/elms/leave-cancel" method="POST">
                                    <div class="modal-body text-center py-4 mb-0">
                                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                                        <input type="hidden" name="status" value="Cancelled">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12 9v4"></path>
                                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                        <h5 class="modal-title fw-bold text-danger">បោះបង់ច្បាប់ឈប់សម្រាក</h5>
                                        <p class="mb-0">អ្នកមិនអាចយកច្បាប់នេះត្រឡប់មកវិញបានទេប្រសិនបើអ្នក <span class="text-danger fw-bold">យល់ព្រម</span></p>
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <div class="w-100 mt-3">
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
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include('src/common/footer.php'); ?>