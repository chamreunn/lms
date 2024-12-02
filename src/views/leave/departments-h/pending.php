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
                <div class="page-pretitle">
                    ទំព័រដើម
                </div>
                <h2 class="page-title text-primary">
                    <?php echo $title ?? "" ?>
                </h2>
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
<!-- leave request  -->
<?php if (empty($requests) && empty($hold) && empty($transferouts) && empty($backworks) && empty($resigns)): ?>
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
<?php else: ?>
    <div class="row g-3 mt-2">
        <?php foreach ($requests as $request): ?>
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card h-100 p-0 border">
                    <div class="card-body p-3">
                        <div class="ribbon bg-red">កំពុងរង់ចាំអនុម័ត...</div>
                        <div class="d-flex w-100 justify-content-between mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img class="avatar rounded-circle" style="object-fit: cover;"
                                        src="<?= 'https://hrms.iauoffsa.us/images/' . $request['profile'] ?>" alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <div class="mb-2">
                                        <h4 class="mb-1 text-primary"><?= $request['user_name'] ?></h4>
                                        <small
                                            class="fw-bolder"><?= translateDateToKhmer($request['created_at'], 'j F Y h:i A') ?></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="p-2">
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-briefcase-2">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 9a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v9a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-9z" />
                                    <path d="M8 7v-2a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v2" />
                                </svg>
                                <strong>ប្រភេទច្បាប់ : </strong><?= $request['leave_type'] ?>
                            </div>
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M16 3l0 4" />
                                    <path d="M8 3l0 4" />
                                    <path d="M4 11l16 0" />
                                    <path d="M8 15h2v2h-2z" />
                                </svg>
                                <strong>កាលបរិច្ឆេទចាប់ពី :
                                </strong><?= translateDateToKhmer($request['start_date'], 'j F, Y') ?>
                            </div>
                            <div class="text-primary mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                    <path d="M16 3l0 4" />
                                    <path d="M8 3l0 4" />
                                    <path d="M4 11l16 0" />
                                    <path d="M8 15h2v2h-2z" />
                                </svg>
                                <strong>ដល់កាលបរិច្ឆេទ : </strong><?= translateDateToKhmer($request['end_date'], 'j F, Y') ?>
                            </div>
                            <?php if ($request['attachment'] > 0): ?>
                                <div class="text-primary mb-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                    </svg>
                                    <strong>ឯកសារភ្ជាប់ : </strong><a target="_blank"
                                        href="public/uploads/leave_attachments/<?= $request['attachment'] ?>">ចុចទីនេះ</a>
                                </div>
                            <?php endif; ?>
                            <div class="text-primary">
                                <label for="" class="form-label"><strong>មូលហេតុ</strong></label>
                                <textarea style="resize: none;" name="" class="form-control" id=""
                                    disabled><?= $request['remarks'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn btn-outline-success w-100" data-bs-toggle="modal"
                                    data-bs-target="#approved<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Approve">
                                    <i class="fas fa-check"></i> អនុម័ត
                                </a>
                            </div>
                            <div class="col">
                                <a href="#" class="btn btn-outline-danger w-100" data-bs-toggle="modal"
                                    data-bs-target="#rejected<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>"
                                    data-bs-toggle="tooltip" data-bs-placement="top" title="Reject">
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
                        <form action="/elms/headdepartmentpending" method="POST" enctype="multipart/form-data">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon mb-2 text-green icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M9 12l2 2l4 -4"></path>
                                </svg>
                                <h3 class="text-success fw-bolder">អនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-success fw-bolder">បន្ត</span>
                                    ដើម្បីអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-green w-100" data-bs-toggle="collapse" href="#approved" role="button"
                                    aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="approved">
                                    <input name="remarks" class="form-control" list="datalistOptions"
                                        placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="អាចឈប់សម្រាកបាន"></option>
                                    </datalist>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="status" value="Approved">
                                <input type="hidden" name="uname" value="<?= $request['user_name'] ?>">
                                <input type="hidden" name="leaveType" value="<?= $request['leave_type'] ?>">
                                <input type="hidden" name="uemail" value="<?= $request['uemails'] ?>">
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
                        <form action="/elms/headdepartmentpending" method="POST" enctype="multipart/form-data">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
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
                                <h3 class="text-danger fw-bolder">មិនអនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-danger fw-bolder">បន្ត</span>
                                    ដើម្បីមិនអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-red w-100" data-bs-toggle="collapse" href="#rejected" role="button"
                                    aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="rejected">
                                    <input name="remarks" class="form-control" list="datalistOptions"
                                        placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="Andorra">
                                        </option>
                                    </datalist>
                                </div>
                                <div class="mt-3" hidden>
                                    <label id="file-name<?= $request['id'] ?>" for="upload-signature<?= $request['id'] ?>"
                                        class="btn w-100 text-start">ហត្ថលេខា<span
                                            class="text-red fw-bold mx-1">*</span></label>
                                    <input type="file" name="manager_signature" id="upload-signature<?= $request['id'] ?>"
                                        accept="image/png" hidden onchange="displayFileName(<?= $request['id'] ?>)" />
                                </div>
                            </div>
                            <div class="modal-footer">
                                <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <input type="hidden" name="uname" value="<?= $request['user_name'] ?>">
                                <input type="hidden" name="leaveType" value="<?= $request['leave_type'] ?>">
                                <input type="hidden" name="uemail" value="<?= $request['uemails'] ?>">
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

    <!-- hold request  -->
    <div class="row g-3">
        <!-- hold  -->
        <?php foreach ($hold as $index => $holds): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 hover-shadow-lg overflow-hidden">
                    <div class="ribbon <?= $holds['color'] ?>">
                        <?php if ($holds['type'] == 'transfer'): ?>
                            <span>លិខិតផ្ទេរចេញ</span>
                        <?php elseif ($holds['type'] == 'hold'): ?>
                            <span>លិខិតពួ្យរការងារ</span>
                        <?php elseif ($holds['type'] == 'resign'): ?>
                            <span>លិខិតលាឈប់</span>
                        <?php else: ?>
                            <span>លិខិតចូលបម្រើការងារវិញ</span>
                        <?php endif; ?>
                    </div>
                    <!-- Trigger modal on click by adding data attributes -->
                    <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal"
                        data-bs-target="#detailModal<?= $index ?>">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <img class="avatar" style="object-fit: cover;" src="<?= $holds['profile'] ?>"
                                    alt="User Profile">
                            </div>
                            <div class="info-container flex-grow-1">
                                <h5 class="mb-1 text-primary"><?= $holds['user_name'] ?></h5>
                                <small class="text-muted fw-bold">
                                    <?= translateDateToKhmer($holds['created_at'], 'j F Y h:i A') ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal modal-blur fade" id="detailModal<?= $index ?>" tabindex="-1"
                aria-labelledby="detailModalLabel<?= $index ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header <?= $holds['color'] ?> text-light">
                            <h5 class="modal-title" id="detailModalLabel<?= $index ?>">លិខិតពួ្យរការងារ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- detail section  -->
                        <form action="/elms/hdaction<?= $holds['type'] ?>" method="POST">

                            <input type="hidden" name="holdId" value="<?= $holds['id'] ?>">

                            <div class="col-12" hidden>
                                <label class="form-label fw-bold">អ្នកអនុម័ត
                                    <span class="text-danger mx-1 fw-bold">*</span>
                                </label>
                                <select class="form-select select-people" id="transfer_id_hof" name="approverId" required>
                                    <?php if (isset($approver['ids'][0])): ?>
                                        <option value="<?= htmlspecialchars($approver['ids'][0], ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][0], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][0], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][0], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach ($approver['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="modal-body">
                                <div class="row mb-3 g-3">
                                    <div class="col-12 text-center">
                                        <!-- User Profile Picture -->
                                        <img src="<?= $holds['profile'] ?>" alt="User Profile" class="avatar avatar-xl"
                                            style="object-fit: cover;">
                                    </div>
                                </div>

                                <!-- Details as a List -->
                                <div class="bg-light p-3 rounded-3">
                                    <dl class="row g-1 mb-0">
                                        <dt class="col-sm-12 col-md-4"><strong>ឈ្មោះ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $holds['user_name'] ?></dd>

                                        <dt class="col-sm-12 col-md-4"><strong>កាលបរិច្ឆេទព្យួរ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= translateDateToKhmer($holds['start_date'], 'j F Y') ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right text-primary">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12l14 0" />
                                                <path d="M15 16l4 -4" />
                                                <path d="M15 8l4 4" />
                                            </svg>
                                            <?= translateDateToKhmer($holds['end_date'], 'j F Y') ?>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>រយៈពេល:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $holds['duration'] ?></dd>

                                        <dt class="col-sm-12 col-md-4"><strong>កាលបរិច្ឆេទស្នើ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= translateDateToKhmer($holds['created_at'], 'j F Y h:i A') ?>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>មូលហេតុ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $holds['reason'] ?></dd>

                                        <!-- Status Section -->
                                        <dt class="col-sm-12 col-md-4"><strong>ស្ថានភាព:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?php if ($holds['status'] == 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($holds['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">មិនអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php endif; ?>
                                        </dd>

                                        <!-- Attachments Section -->
                                        <?php if (!empty($holds['attachments'])): ?>
                                            <dt class="col-sm-12 col-md-4"><strong>ឯកសារភ្ជាប់:</strong></dt>
                                            <dd class="col-sm-12 col-md-8">
                                                <ul class="list-unstyled">
                                                    <?php
                                                    // Convert attachments string to an array
                                                    $attachments = explode(',', $holds['attachments']);
                                                    foreach ($attachments as $attachment): ?>
                                                        <li>
                                                            <a href="public/uploads/hold-attachments/<?= htmlspecialchars($attachment) ?>"
                                                                target="_blank" class="text-primary">
                                                                <i class="bi bi-paperclip"></i> <?= htmlspecialchars($attachment) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <!-- approved or rejected section  -->
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="approved"
                                                    class="form-selectgroup-input" checked>
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-success fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>អនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="rejected"
                                                    class="form-selectgroup-input">
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-danger fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>មិនអនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="comment" class="form-label fw-bold">មតិយោបល់</label>
                                        <textarea name="comment" id="comment" placeholder="សូមបញ្ចូលមតិយោបល់..."
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- footer section  -->
                            <div class="modal-footer bg-light">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">បន្ត</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- transferout  -->
        <?php foreach ($transferouts as $index => $transferout): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 hover-shadow-lg overflow-hidden">
                    <div class="ribbon <?= $transferout['color'] ?>">
                        <?php if ($transferout['type'] == 'transferout'): ?>
                            <span>លិខិតផ្ទេរចេញ</span>
                        <?php elseif ($transferout['type'] == 'hold'): ?>
                            <span>លិខិតពួ្យរការងារ</span>
                        <?php elseif ($transferout['type'] == 'resign'): ?>
                            <span>លិខិតលាឈប់</span>
                        <?php else: ?>
                            <span>លិខិតចូលបម្រើការងារវិញ</span>
                        <?php endif; ?>
                    </div>
                    <!-- Trigger modal on click by adding data attributes -->
                    <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal"
                        data-bs-target="#detailModal<?= $index ?>">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <img class="avatar" style="object-fit: cover;" src="<?= $transferout['profile'] ?>"
                                    alt="User Profile">
                            </div>
                            <div class="info-container flex-grow-1">
                                <h5 class="mb-1 text-primary"><?= $transferout['user_name'] ?></h5>
                                <small class="text-muted fw-bold">
                                    <?= translateDateToKhmer($transferout['created_at'], 'j F Y h:i A') ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal modal-blur fade" id="detailModal<?= $index ?>" tabindex="-1"
                aria-labelledby="detailModalLabel<?= $index ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header <?= $transferout['color'] ?> text-light">
                            <h5 class="modal-title" id="detailModalLabel<?= $index ?>">លិខិតផ្ទេរការងារ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- detail section  -->
                        <form action="/elms/hdaction<?= $transferout['type'] ?>" method="POST">
                            <div class="col-12" hidden>
                                <input type="text" name="transferoutId" value="<?= $transferout['id'] ?>">
                                <label class="form-label fw-bold">អ្នកអនុម័ត
                                    <span class="text-danger mx-1 fw-bold">*</span>
                                </label>
                                <select class="form-select select-people" id="transfer_id_hof" name="approverId" required>
                                    <?php if (isset($approver['ids'][0])): ?>
                                        <option value="<?= htmlspecialchars($approver['ids'][0], ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][0], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][0], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][0], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach ($approver['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="modal-body">
                                <!-- Details as a List -->
                                <div class="bg-light p-3 rounded-3">
                                    <div class="col-12 text-center mb-3">
                                        <!-- User Profile Picture -->
                                        <img src="<?= $transferout['profile'] ?>" alt="User Profile" class="avatar avatar-xl"
                                            style="object-fit: cover;">
                                    </div>
                                    <dl class="row g-1 mb-0">
                                        <dt class="col-sm-12 col-md-4"><strong>ឈ្មោះ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $transferout['user_name'] ?></dd>

                                        <dt class="col-sm-12 col-md-4"><strong>កាលបរិច្ឆេទស្នើ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= translateDateToKhmer($transferout['created_at'], 'j F Y') ?>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>ផ្ទេរពី:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= $transferout['from_department_name'] ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right text-primary">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12l14 0" />
                                                <path d="M15 16l4 -4" />
                                                <path d="M15 8l4 4" />
                                            </svg>
                                            <strong><?= $transferout['to_department_name'] ?></strong>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>ផ្ទេរពី:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= $transferout['from_office_name'] ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-narrow-right text-primary">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 12l14 0" />
                                                <path d="M15 16l4 -4" />
                                                <path d="M15 8l4 4" />
                                            </svg>
                                            <strong><?= $transferout['to_office_name'] ?></strong>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>មូលហេតុ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $transferout['reason'] ?></dd>

                                        <!-- Status Section -->
                                        <dt class="col-sm-12 col-md-4"><strong>ស្ថានភាព:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?php if ($transferout['status'] == 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($transferout['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">មិនអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php endif; ?>
                                        </dd>

                                        <!-- Attachments Section -->
                                        <?php if (!empty($transferout['attachments'])): ?>
                                            <dt class="col-sm-12 col-md-4"><strong>ឯកសារភ្ជាប់:</strong></dt>
                                            <dd class="col-sm-12 col-md-8">
                                                <ul class="list-unstyled">
                                                    <?php
                                                    // Convert attachments string to an array
                                                    $attachments = explode(',', $transferout['attachments']);
                                                    foreach ($attachments as $attachment): ?>
                                                        <li>
                                                            <a href="public/uploads/hold-attachments/<?= htmlspecialchars($attachment) ?>"
                                                                target="_blank" class="text-primary">
                                                                <i class="bi bi-paperclip"></i> <?= htmlspecialchars($attachment) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <!-- approved or rejected section  -->
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="approved"
                                                    class="form-selectgroup-input" checked>
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-success fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>អនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="rejected"
                                                    class="form-selectgroup-input">
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-danger fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>មិនអនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="comment" class="form-label fw-bold">មតិយោបល់</label>
                                        <textarea name="comment" id="comment" placeholder="សូមបញ្ចូលមតិយោបល់..."
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- footer section  -->
                            <div class="modal-footer bg-light">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">បន្ត</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- backwork  -->
        <?php foreach ($backworks as $index => $backwork): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 hover-shadow-lg overflow-hidden">
                    <div class="ribbon <?= $backwork['color'] ?>">
                        <?php if ($backwork['type'] == 'transferout'): ?>
                            <span>លិខិតផ្ទេរចេញ</span>
                        <?php elseif ($backwork['type'] == 'hold'): ?>
                            <span>លិខិតពួ្យរការងារ</span>
                        <?php elseif ($backwork['type'] == 'resign'): ?>
                            <span>លិខិតលាឈប់</span>
                        <?php elseif ($backwork['type'] == 'back'): ?>
                            <span>លិខិតចូលបម្រើការងារវិញ</span>
                        <?php endif; ?>
                    </div>
                    <!-- Trigger modal on click by adding data attributes -->
                    <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal"
                        data-bs-target="#detailModal<?= $index ?>">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <img class="avatar" style="object-fit: cover;" src="<?= $backwork['profile'] ?>"
                                    alt="User Profile">
                            </div>
                            <div class="info-container flex-grow-1">
                                <h5 class="mb-1 text-primary"><?= $backwork['user_name'] ?></h5>
                                <small class="text-muted fw-bold">
                                    <?= translateDateToKhmer($backwork['created_at'], 'j F Y h:i A') ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal modal-blur fade" id="detailModal<?= $index ?>" tabindex="-1"
                aria-labelledby="detailModalLabel<?= $index ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header <?= $backwork['color'] ?> text-light">
                            <h5 class="modal-title" id="detailModalLabel<?= $index ?>">លិខិតផ្ទេរការងារ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- detail section  -->
                        <form action="/elms/hdaction<?= $backwork['type'] ?>" method="POST">
                            <div class="col-12" hidden>
                                <input type="text" name="backworkId" value="<?= $backwork['id'] ?>">
                                <label class="form-label fw-bold">អ្នកអនុម័ត
                                    <span class="text-danger mx-1 fw-bold">*</span>
                                </label>
                                <select class="form-select select-people" id="transfer_id_hof" name="approverId" required>
                                    <?php if (isset($approver['ids'][0])): ?>
                                        <option value="<?= htmlspecialchars($approver['ids'][0], ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][0], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][0], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][0], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach ($approver['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="modal-body">
                                <!-- Details as a List -->
                                <div class="bg-light p-3 rounded-3">
                                    <div class="col-12 text-center mb-3">
                                        <!-- User Profile Picture -->
                                        <img src="<?= $backwork['profile'] ?>" alt="User Profile" class="avatar avatar-xl"
                                            style="object-fit: cover;">
                                    </div>
                                    <dl class="row g-1 mb-0">
                                        <dt class="col-sm-12 col-md-4"><strong>ឈ្មោះ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $backwork['user_name'] ?></dd>

                                        <dt class="col-sm-12 col-md-4"><strong>កាលបរិច្ឆេទស្នើ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= translateDateToKhmer($backwork['created_at'], 'j F Y') ?>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>សុំចូលបម្រើការងារនៅ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?= translateDateToKhmer($backwork['date'], 'j F Y') ?>
                                        </dd>

                                        <dt class="col-sm-12 col-md-4"><strong>មូលហេតុ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $backwork['reason'] ?></dd>

                                        <!-- Status Section -->
                                        <dt class="col-sm-12 col-md-4"><strong>ស្ថានភាព:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?php if ($backwork['status'] == 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($backwork['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">មិនអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php endif; ?>
                                        </dd>

                                        <!-- Attachments Section -->
                                        <?php if (!empty($backwork['attachments'])): ?>
                                            <dt class="col-sm-12 col-md-4"><strong>ឯកសារភ្ជាប់:</strong></dt>
                                            <dd class="col-sm-12 col-md-8">
                                                <ul class="list-unstyled">
                                                    <?php
                                                    // Convert attachments string to an array
                                                    $attachments = explode(',', $backwork['attachments']);
                                                    foreach ($attachments as $attachment): ?>
                                                        <li>
                                                            <a href="public/uploads/backwork-attachments/<?= htmlspecialchars($attachment) ?>"
                                                                target="_blank" class="text-primary">
                                                                <i class="bi bi-paperclip"></i> <?= htmlspecialchars($attachment) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <!-- approved or rejected section  -->
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="approved"
                                                    class="form-selectgroup-input" checked>
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-success fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>អនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="rejected"
                                                    class="form-selectgroup-input">
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-danger fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>មិនអនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="comment" class="form-label fw-bold">មតិយោបល់</label>
                                        <textarea name="comment" id="comment" placeholder="សូមបញ្ចូលមតិយោបល់..."
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- footer section  -->
                            <div class="modal-footer bg-light">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">បន្ត</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- resigned  -->
        <?php foreach ($resigns as $index => $resign): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 hover-shadow-lg overflow-hidden">
                    <div class="ribbon <?= $resign['color'] ?>">
                        <?php if ($resign['type'] == 'transfer'): ?>
                            <span>លិខិតផ្ទេរចេញ</span>
                        <?php elseif ($resign['type'] == 'hold'): ?>
                            <span>លិខិតពួ្យរការងារ</span>
                        <?php elseif ($resign['type'] == 'resign'): ?>
                            <span>លិខិតលាឈប់</span>
                        <?php else: ?>
                            <span>លិខិតចូលបម្រើការងារវិញ</span>
                        <?php endif; ?>
                    </div>
                    <!-- Trigger modal on click by adding data attributes -->
                    <a href="#" class="text-decoration-none text-dark" data-bs-toggle="modal"
                        data-bs-target="#detailModalResign<?= $index ?>">
                        <div class="card-body p-3 d-flex align-items-center">
                            <div class="avatar-container me-3">
                                <img class="avatar" style="object-fit: cover;" src="<?= $resign['profile'] ?>"
                                    alt="User Profile">
                            </div>
                            <div class="info-container flex-grow-1">
                                <h5 class="mb-1 text-primary"><?= $resign['user_name'] ?></h5>
                                <small class="text-muted fw-bold">
                                    <?= translateDateToKhmer($resign['created_at'], 'j F Y h:i A') ?>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Modal Structure -->
            <div class="modal modal-blur fade" id="detailModalResign<?= $index ?>" tabindex="-1"
                aria-labelledby="detailModalLabel<?= $index ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md">
                    <div class="modal-content">
                        <div class="modal-header <?= $resign['color'] ?> text-light">
                            <h5 class="modal-title" id="detailModalLabel<?= $index ?>">លិខិតលាឈប់</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <!-- detail section  -->
                        <form action="/elms/hdaction<?= $resign['type'] ?>" method="POST">

                            <input type="hidden" name="resignId" value="<?= $resign['id'] ?>">

                            <div class="col-12" hidden>
                                <label class="form-label fw-bold">អ្នកអនុម័ត
                                    <span class="text-danger mx-1 fw-bold">*</span>
                                </label>
                                <select class="form-select select-people" id="transfer_id_hof" name="approverId" required>
                                    <?php if (isset($approver['ids'][0])): ?>
                                        <option value="<?= htmlspecialchars($approver['ids'][0], ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][0], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][0], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][0], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endif; ?>

                                    <?php foreach ($approver['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($approver['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>

                            <div class="modal-body">
                                <div class="row mb-3 g-3">
                                    <div class="col-12 text-center">
                                        <!-- User Profile Picture -->
                                        <img src="<?= $resign['profile'] ?>" alt="User Profile" class="avatar avatar-xl"
                                            style="object-fit: cover;">
                                    </div>
                                </div>

                                <!-- Details as a List -->
                                <div class="bg-light p-3 rounded-3">
                                    <dl class="row g-1 mb-0">
                                        <dt class="col-sm-12 col-md-4"><strong>ឈ្មោះ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $resign['user_name'] ?></dd>


                                        <dt class="col-sm-12 col-md-4"><strong>មូលហេតុ:</strong></dt>
                                        <dd class="col-sm-12 col-md-8"><?= $resign['reason'] ?></dd>

                                        <!-- Status Section -->
                                        <dt class="col-sm-12 col-md-4"><strong>ស្ថានភាព:</strong></dt>
                                        <dd class="col-sm-12 col-md-8">
                                            <?php if ($resign['status'] == 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($resign['status'] == 'rejected'): ?>
                                                <span class="badge bg-danger">មិនអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php endif; ?>
                                        </dd>

                                        <!-- Attachments Section -->
                                        <?php if (!empty($resign['attachments'])): ?>
                                            <dt class="col-sm-12 col-md-4"><strong>ឯកសារភ្ជាប់:</strong></dt>
                                            <dd class="col-sm-12 col-md-8">
                                                <ul class="list-unstyled">
                                                    <?php
                                                    // Convert attachments string to an array
                                                    $attachments = explode(',', $resign['attachments']);
                                                    foreach ($attachments as $attachment): ?>
                                                        <li>
                                                            <a href="public/uploads/resign-attachments/<?= htmlspecialchars($attachment) ?>"
                                                                target="_blank" class="text-primary">
                                                                <i class="bi bi-paperclip"></i> <?= htmlspecialchars($attachment) ?>
                                                            </a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </dd>
                                        <?php endif; ?>
                                    </dl>
                                </div>
                            </div>

                            <!-- approved or rejected section  -->
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="approved"
                                                    class="form-selectgroup-input" checked>
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-success fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>អនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-sm-12 col-md-6">
                                        <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                            <label class="form-selectgroup-item flex-fill">
                                                <input type="radio" name="status" value="rejected"
                                                    class="form-selectgroup-input">
                                                <div
                                                    class="form-selectgroup-label d-flex align-items-center p-3 text-danger fw-bold">
                                                    <div class="me-3">
                                                        <span class="form-selectgroup-check"></span>
                                                    </div>
                                                    <div>
                                                        <strong>មិនអនុម័ត</strong>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="comment" class="form-label fw-bold">មតិយោបល់</label>
                                        <textarea name="comment" id="comment" placeholder="សូមបញ្ចូលមតិយោបល់..."
                                            class="form-control"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- footer section  -->
                            <div class="modal-footer bg-light">
                                <div class="row w-100">
                                    <div class="col-6">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" class="btn btn-primary w-100">បន្ត</button>
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