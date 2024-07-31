<?php
$title = "កំពុងរង់ចាំការអនុម័ត";
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
<div class="row">
    <?php if (empty($requests)) : ?>
        <div class="d-flex align-items-center justify-content-center">
            <div class="text-center">
                <img src="public/img/icons/svgs/empty.svg" alt="">
                <h4>មិនមានសំណើច្បាប់ឈប់សម្រាក!</h4>
            </div>
        </div>
    <?php else : ?>
        <?php foreach ($requests as $request) : ?>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100 border-<?= $request['status'] == 'Pending' ? 'warning' : '' ?> p-0">
                    <div class="card-body">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img class="avatar rounded-circle" style="object-fit: cover;" src="<?= $request['profile'] ?>" alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <h5 class="mb-1"><?= $request['khmer_name'] ?></h5>
                                        <div class="badge ms-auto <?= $request['status'] == 'Pending' ? 'bg-warning-lt' : '' ?>
                                                    <?= $request['status'] == 'Approved' ? 'bg-success-lt' : '' ?>
                                                    <?= $request['status'] == 'Rejected' ? 'bg-danger-lt' : '' ?>
                                                    <?= $request['status'] == 'Cancelled' ? 'bg-secondary-lt' : '' ?>">
                                            <?= $request['status'] ?>
                                        </div>
                                    </div>
                                    <small class="mb-1"><?= translateDateToKhmer($request['created_at'], 'j F Y h:i A') ?></small>
                                </div>
                            </div>
                        </div>
                        <div class="text-primary mb-2"><?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?> - <?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?></div>
                        <textarea style="resize: none;" name="" class="form-control" id="" disabled><?= $request['remarks'] ?></textarea>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#approved<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>">អនុម័ត</a>
                            </div>
                            <div class="col">
                                <a href="#" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejected<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>">មិនអនុម័ត</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- approved modal -->
            <div class="modal modal-blur fade" id="approved<?= $request['id'] ?>" tabindex="-1" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-success"></div>
                        <form action="/elms/pending" method="POST" enctype="multipart/form-data">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-green icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M9 12l2 2l4 -4"></path>
                                </svg>
                                <h3 class="text-success fw-bolder">អនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-success fw-bolder">បន្ត</span> ដើម្បីអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-green w-100" data-bs-toggle="collapse" href="#approved" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="approved">
                                    <input name="remarks" class="form-control" list="datalistOptions" placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="អាចឈប់សម្រាកបាន"></option>
                                        <option value="United Arab Emirates"></option>
                                        <option value="Afghanistan"></option>
                                        <option value="Antigua"></option>
                                        <option value="Anguilla"></option>
                                        <option value="Armenia"></option>
                                        <option value="Angolan"></option>
                                        <option value="Antarctica"></option>
                                        <option value="Argentina"></option>
                                        <option value="American Samoa"></option>
                                    </datalist>
                                </div>
                                <div class="mt-3">
                                    <label id="file-name<?= $request['id'] ?>" for="upload-signature<?= $request['id'] ?>" class="btn w-100 text-start">ហត្ថលេខា<span class="text-red fw-bold mx-1">*</span></label>
                                    <input type="file" name="manager_signature" id="upload-signature<?= $request['id'] ?>" accept="image/png" hidden onchange="displayFileName(<?= $request['id'] ?>)" required />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="status" value="Approved">
                                <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                                បោះបង់
                                            </a>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-success w-100">
                                                បន្ត
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- rejected modal  -->
            <div class="modal modal-blur fade" id="rejected<?= $request['id'] ?>" tabindex="-1" aria-modal="true" role="dialog">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-danger"></div>
                        <form action="/elms/pending" method="POST">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 9v4"></path>
                                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                    <path d="M12 16h.01"></path>
                                </svg>
                                <h3 class="text-danger fw-bolder">មិនអនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-danger fw-bolder">បន្ត</span> ដើម្បីមិនអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-red w-100" data-bs-toggle="collapse" href="#rejected" role="button" aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="rejected">
                                    <input name="remarks" class="form-control" list="datalistOptions" placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="Andorra">
                                        </option>
                                        <option value="United Arab Emirates">
                                        </option>
                                        <option value="Afghanistan">
                                        </option>
                                        <option value="Antigua">
                                        </option>
                                        <option value="Anguilla">
                                        </option>
                                        <option value="Armenia">
                                        </option>
                                        <option value="Angolan">
                                        </option>
                                        <option value="Antarctica">
                                        </option>
                                        <option value="Argentina">
                                        </option>
                                        <option value="American Samoa">
                                        </option>
                                    </datalist>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                                បោះបង់
                                            </a>
                                        </div>
                                        <div class="col">
                                            <a href="#" class="btn btn-danger w-100" data-bs-dismiss="modal">
                                                បន្ត
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal for Leave Request Details -->
            <div class="modal modal-blur fade" id="modalview<?= $request['id'] ?>" tabindex="-1" aria-labelledby="requestDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <form action="/elms/pending" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="requestDetailModalLabel">
                                    <?= $title ?>
                                    <span class="badge mx-2 <?= $request['status'] == 'Pending' ? 'bg-warning-lt' : '' ?>
                                                <?= $request['status'] == 'Approved' ? 'bg-success-lt' : '' ?>
                                                <?= $request['status'] == 'Rejected' ? 'bg-danger-lt' : '' ?>
                                                <?= $request['status'] == 'Cancelled' ? 'bg-secondary-lt' : '' ?>">
                                        <?= $request['status'] ?>
                                    </span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body mb-0">
                                <div class="row">
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">ឈ្មោះមន្ត្រី :</label>
                                        <div class="col-sm-8">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-edit">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                        <path d="M18.42 15.61a2.1 2.1 0 0 1 2.97 2.97l-3.39 3.42h-3v-3l3.42 -3.39z" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" value="<?= $request['khmer_name'] ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 form-label">ប្រភេទច្បាប់ :</label>
                                        <div class="col-sm-8">
                                            <div class="badge <?= $request['color'] ?>"><?= $request['leave_type'] ?></div>
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
                                                        <path d="M10.015 17h.005" />
                                                        <path d="M16.015 17h.005" />
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
                                                        <path d="M10.015 17h.005" />
                                                        <path d="M16.015 17h.005" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" value="<?= translateDateToKhmer($request['end_date'], 'D, j F Y') ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">មូលហេតុ :</label>
                                        <div class="col-sm-8">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 5h10a2 2 0 0 1 2 2v5m-1.992 4.992a2 2 0 0 1 -2.008 2.008h-10a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2" />
                                                        <path d="M12 8h.01" />
                                                        <path d="M17 12h.01" />
                                                        <path d="M8 16h.01" />
                                                        <path d="M4 21l4 -4" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" value="<?= $request['remarks'] ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">មន្ត្រីផ្ទេរសំណើ :</label>
                                        <div class="col-sm-8">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M8 9a3 3 0 1 0 3 3" />
                                                        <path d="M9.5 14.5c.5 .5 1.5 .5 2.5 .5" />
                                                        <path d="M21 8a3.5 3.5 0 0 1 0 8" />
                                                        <path d="M16.5 20h-8.5a2 2 0 0 1 -2 -2" />
                                                        <path d="M4 12a3 3 0 0 1 3 -3" />
                                                        <path d="M9 3.5c1 .5 2 .5 3 0" />
                                                        <path d="M20 5.5c1 0 2 -1 2 -2" />
                                                        <path d="M15.5 8.5a1 1 0 0 0 1.5 -1.5" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" value="<?= $request['created_by'] ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3 row">
                                        <label class="col-sm-4 col-form-label">សម្គាល់បន្ថែម :</label>
                                        <div class="col-sm-8">
                                            <div class="input-icon">
                                                <span class="input-icon-addon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-book">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 19h16" />
                                                        <path d="M4 5h16" />
                                                        <path d="M7 19v-14" />
                                                        <path d="M17 19v-14" />
                                                    </svg>
                                                </span>
                                                <input type="text" class="form-control" value="<?= $request['additional_notes'] ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-link link-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?php include('src/common/footer.php'); ?>
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select-status")), {
                copyClassesToDropdown: false,
                dropdownClass: "dropdown-menu ts-dropdown",
                optionClass: "dropdown-item",
                controlInput: "<input>",
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                        }
                        return "<div>" + escape(data.text) + "</div>";
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                        }
                        return "<div>" + escape(data.text) + "</div>";
                    },
                },
            });
    });
    // @formatter:on
</script>

<script>
    function displayFileName(requestId) {
        const input = document.getElementById(`upload-signature${requestId}`);
        const fileNameDiv = document.getElementById(`file-name${requestId}`);
        const fileName = input.files[0] ? input.files[0].name : '';
        fileNameDiv.textContent = fileName;
    }
</script>