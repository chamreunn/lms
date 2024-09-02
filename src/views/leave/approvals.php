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
                    <a class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal" data-bs-target="#apply-leave">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M16 19h6" />
                            <path d="M19 16v6" />
                        </svg>
                        <span>បង្កើតសំណើច្បាប់</span>
                    </a>
                    <a href="/elms/apply-leave" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal" data-bs-target="#apply-leave">
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
<?php if (empty($requests)) : ?>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-center">
                <div class="text-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <h4>មិនមានសំណើច្បាប់ឈប់សម្រាក!</h4>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <?php foreach ($requests as $request) : ?>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100 p-0">
                    <div class="card-body p-3">
                        <div class="ribbon bg-red" >កំពុងរង់ចាំអនុម័ត...</div>
                        <div class="d-flex w-100 justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img class="avatar rounded-circle" style="object-fit: cover;" src="<?= 'https://hrms.iauoffsa.us/images/' . $request['profile'] ?>" alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <h4 class="mb-1 text-primary"><?= $request['user_name'] ?></h4>
                                        <small class="fw-bolder"><?= translateDateToKhmer($request['created_at'], 'j F Y h:i A') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <div class="text-primary mb-2">
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
                                <strong>រយៈពេល : </strong><?= $request['num_date'] ?>ថ្ងៃ
                            </div>
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9z" />
                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                                <strong>ប្រភេទច្បាប់ : </strong><?= $request['leave_type'] ?>
                            </div>
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M16 3l0 4" />
                                    <path d="M8 3l0 4" />
                                    <path d="M4 11l16 0" />
                                    <path d="M8 15h2v2h-2z" />
                                </svg>
                                <strong>កាលបរិច្ឆេទចាប់ពី : </strong><?= translateDateToKhmer($request['start_date'], 'j F, Y') ?>
                            </div>
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M16 3l0 4" />
                                    <path d="M8 3l0 4" />
                                    <path d="M4 11l16 0" />
                                    <path d="M8 15h2v2h-2z" />
                                </svg>
                                <strong>ដល់កាលបរិច្ឆេទ : </strong><?= translateDateToKhmer($request['end_date'], 'j F, Y') ?>
                            </div>
                            <?php if ($request['attachment'] > 0) : ?>
                                <div class="text-primary mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                    </svg>
                                    <strong>ឯកសារភ្ជាប់ : </strong><a target="_blank" href="public/uploads/leave_attachments/<?= $request['attachment'] ?>">ចុចទីនេះ</a>
                                </div>
                            <?php endif; ?>
                            <div class="text-primary">
                                <label for="" class="form-label"><strong>មូលហេតុ</strong></label>
                                <textarea style="resize: none;" name="" class="form-control" id="" disabled><?= $request['remarks'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#approved<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                                    <i class="fas fa-check"></i> អនុម័ត
                                </a>
                            </div>
                            <div class="col">
                                <a href="#" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#rejected<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="Reject">
                                    <i class="fas fa-times"></i> មិនអនុម័ត
                                </a>
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
                                <input type="hidden" name="uname" value="<?= $request['user_name'] ?>">
                                <input type="hidden" name="leaveType" value="<?= $request['leave_type'] ?>">
                                <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                <input type="hidden" name="start_date" value="<?= $request['start_date'] ?>">
                                <input type="hidden" name="end_date" value="<?= $request['end_date'] ?>">
                                <input type="hidden" name="duration" value="<?= $request['num_date'] ?>">
                                <input type="hidden" name="uremarks" value="<?= $request['remarks'] ?>">
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
                        <form action="/elms/pending" method="POST" enctype="multipart/form-data">
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
                                <div class="mt-3" hidden>
                                    <label id="file-name<?= $request['id'] ?>" for="upload-signature<?= $request['id'] ?>" class="btn w-100 text-start">ហត្ថលេខា<span class="text-red fw-bold mx-1">*</span></label>
                                    <input type="file" name="manager_signature" id="upload-signature<?= $request['id'] ?>" accept="image/png" hidden onchange="displayFileName(<?= $request['id'] ?>)" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <input type="hidden" name="uname" value="<?= $request['user_name'] ?>">
                                <input type="hidden" name="leaveType" value="<?= $request['leave_type'] ?>">
                                <input type="hidden" name="user_id" value="<?= $request['user_id'] ?>">
                                <input type="hidden" name="start_date" value="<?= $request['start_date'] ?>">
                                <input type="hidden" name="end_date" value="<?= $request['end_date'] ?>">
                                <input type="hidden" name="duration" value="<?= $request['num_date'] ?>">
                                <input type="hidden" name="uremarks" value="<?= $request['remarks'] ?>">
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
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php include('src/common/footer.php'); ?>
<!-- tom select  -->
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

<!-- Modal Apply Leave -->
<div class="modal modal-blur fade" id="apply-leave" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype) : ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>" data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>' <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?> (<?= $leavetype['duration'] ?>ថ្ងៃ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control" id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ដល់កាលបរិច្ឆេទ" class="form-control" id="lend_date" name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <!-- <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                        </span> -->
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5" class="form-control" id="remarks" name="remarks" required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 mb-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="signature" onchange="toggleFileInput(this, 'signatureFile')" checked>
                                    <label class="form-check-label cursor-pointer" for="signature">
                                        ហត្ថលេខា<span class="text-red fw-bold mx-1">*</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mb-3" id="signatureFile">
                                <label id="displayName" for="signature_file" class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                                    <span class="p-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                        </svg>
                                    </span>
                                    <span>ហត្ថលេខា</span>
                                </label>
                                <input type="file" name="signature" id="signature_file" accept="image/png" required hidden onchange="displayFileName('signature_file', 'displayName')" />
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="attachment" onchange="toggleFileInput(this, 'attachmentFile')">
                                    <label class="form-check-label cursor-pointer" for="attachment">
                                        ឯកសារភ្ជាប់
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 mb-3" id="attachmentFile" style="display: none;">
                                <label id="attachmentDisplayName" for="attachment_file" class="btn w-100 text-start p-3 bg-light">
                                    ឯកសារភ្ជាប់
                                </label>
                                <input type="file" name="attachment" id="attachment_file" class="form-control" hidden onchange="displayFileName('attachment_file', 'attachmentDisplayName')" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">
                        <span>បង្កើតសំណើ</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send mx-1">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 14l11 -11" />
                            <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- id  -->
<script>
    function displayFileName(requestId) {
        const input = document.getElementById(`upload-signature${requestId}`);
        const fileNameDiv = document.getElementById(`file-name${requestId}`);
        const fileName = input.files[0] ? input.files[0].name : '';
        fileNameDiv.textContent = fileName;
    }
</script>