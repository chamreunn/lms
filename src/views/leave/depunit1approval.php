
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
<div class="card rounded-3">
    <div class="card-header border-bottom">
        <h2 class="mb-0">
            <span class="icon mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-clock">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10.5 21h-4.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                    <path d="M16 3v4" />
                    <path d="M8 3v4" />
                    <path d="M4 11h10" />
                    <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                    <path d="M18 16.5v1.5l.5 .5" />
                </svg>
            </span>
            <span class="mb-0">
                <?= $title ?>
            </span>
        </h2>
    </div>
    <div class="card-body">
        <div class="list-group">
            <?php if (empty($requests)) : ?>
                <div class="d-flex align-items-center justify-content-center">
                    <div class="text-center">
                        <img src="public/img/icons/svgs/empty.svg" alt="">
                        <h4>មិនមានសំណើច្បាប់ឈប់សម្រាក!</h4>
                    </div>
                </div>
            <?php else : ?>
                <?php foreach ($requests as $request) : ?>
                    <p class="mb-2 text-muted fw-bold"><?= translateDateToKhmer($request['created_at'], 'D, j F Y h:i A') ?></p>
                    <a href="#" class="list-group-item list-group-item-action rounded-4 mb-2" data-bs-toggle="modal" data-bs-target="#modalview<?= $request['id'] ?>" data-request-id="<?= $request['id'] ?>">
                        <div class="d-flex w-100 justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <img class="avatar rounded-circle" style="object-fit: cover;" src="<?= $request['profile'] ?>" alt="">
                                </div>
                                <div class="d-flex flex-column">
                                    <h3 class="mb-1"><?= $request['khmer_name'] ?><span class="badge mx-3 <?= $request['status'] == 'Pending' ? 'badge bg-warning-lt' : '' ?>
                                        <?= $request['status'] == 'Approved' ? 'badge bg-success-lt' : '' ?>
                                        <?= $request['status'] == 'Rejected' ? 'badge bg-danger-lt' : '' ?>
                                        <?= $request['status'] == 'Cancelled' ? 'badge bg-secondary-lt' : '' ?>">
                                            <?= $request['status'] ?>
                                        </span>
                                    </h3>
                                    <p class="mb-1">មូលហេតុ : <?= $request['remarks'] ?></p>
                                    <p class="mb-0">ប្រភេទច្បាប់ : <span class="badge <?= $request['color'] ?>"><?= $request['leave_type'] ?></span></p>
                                </div>
                            </div>
                            <small class="text-primary"><?= translateDateToKhmer($request['start_date'], 'D, j F Y') ?> - <?= translateDateToKhmer($request['end_date'],'D, j F Y') ?></small>
                        </div>
                    </a>

                    <!-- Modal for Leave Request Details -->
                    <div class="modal modal-blur fade" id="modalview<?= $request['id'] ?>" tabindex="-1" aria-labelledby="requestDetailModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content">
                                <form action="/elms/headdepartmentpending" method="POST">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="requestDetailModalLabel">
                                            <?= $title ?>
                                            <span class="badge mx-2 <?= $request['status'] == 'Pending' ? 'badge bg-warning-lt' : '' ?>
                                        <?= $request['status'] == 'Approved' ? 'badge bg-success-lt' : '' ?>
                                        <?= $request['status'] == 'Rejected' ? 'badge bg-danger-lt' : '' ?>
                                        <?= $request['status'] == 'Cancelled' ? 'badge bg-secondary-lt' : '' ?>">
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
                                                                <path d="M10.01 17h.005" />
                                                            </svg>
                                                        </span>
                                                        <input type="text" class="form-control" value="<?= translateDateToKhmer($request['start_date'],'D, j F Y') ?>" disabled>
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
                                                        <input type="text" class="form-control" value="<?= translateDateToKhmer($request['end_date'],'D, j F Y') ?>" disabled>
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
                                            <div class="row">
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
                                        </div>
                                        <!-- Leave request details will be loaded here dynamically -->
                                    </div>
                                    <div class="modal-body mb-2">
                                        <div class="row">
                                            <?php if ($request['attachment']) : ?>
                                                <a href="public/uploads/leave_attachments/<?= $request['attachment'] ?>" target="_blank">View</a>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body rounded-2 bg-light border">
                                            <div class="row">
                                                <label class="col-sm-4 col-form-label">មតិយោបល់ :</label>
                                                <div class="col-sm-12">
                                                    <div class="input-icon">
                                                        <span class="input-icon-addon">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M8 9h8" />
                                                                <path d="M8 13h6" />
                                                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                                            </svg>
                                                        </span>
                                                        <textarea type="text" name="remarks" placeholder="មតិយោបល់" class="form-control overflow-hidden" style="resize: none;"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-4 col-form-label">ស្ថានភាព<span class="text-danger fw-bold mx-1">*</span></label>
                                                <div class="col-sm-12">
                                                    <select name="status" id="select-status" class="form-control form-select" required>
                                                        <option selected="" disabled data-custom-properties='&lt;span class="badge bg-warning-lt"'><?= $request['status'] ?></option>
                                                        <option value="Approved" data-custom-properties='&lt;span class="badge bg-success-lt"'>Approved</option>
                                                        <option value="Rejected" data-custom-properties='&lt;span class="badge bg-danger-lt"'>Rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-end">
                                        <button type="button" class="btn w-25 me-2" data-bs-dismiss="modal">បោះបង់</button>
                                        <button type="submit" class="btn btn-primary w-25" data-bs-dismiss="modal">បញ្ជូន</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
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