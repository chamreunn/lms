<?php
$title = "ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ";
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
// Assuming $requests is an array of all leave requests
$requestsPerPage = 10; // Number of requests to display per page
$totalRequests = count($requests);
$totalPages = ceil($totalRequests / $requestsPerPage);

// Get the current page from the URL, default to page 1 if not set
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); // Ensure the current page is within bounds

// Calculate the starting index of the requests to display
$startIndex = ($currentPage - 1) * $requestsPerPage;
$paginatedRequests = array_slice($requests, $startIndex, $requestsPerPage);

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
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="header-title mb-0 text-muted"><?= $title ?></h4>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th>ឈ្មោះមន្ត្រី</th>
                    <th>ប្រភេទច្បាប់</th>
                    <th>ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th>ដល់កាលបរិច្ឆេទ</th>
                    <th>រយៈពេល</th>
                    <th>ស្ថានភាព</th>
                    <th class="text-center">សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paginatedRequests)) : ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
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
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($paginatedRequests as $request) : ?>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <img src="https://hrms.iauoffsa.us/images/<?= $request['profile'] ?>" class="avatar" style="object-fit: cover;" alt="">
                                    <div class="d-flex flex-column mx-2">
                                        <h4 class="mx-0 mb-1 text-primary">
                                            <?= $request['user_name'] ?>
                                        </h4>
                                        <span class="text-muted"> <?= $request['email'] ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="badge <?= $request['color'] ?>"><?= $request['leave_type'] ?></div>
                            </td>
                            <td><?= translateDateToKhmer($request['start_date'], 'D j F, Y') ?></td>
                            <td><?= translateDateToKhmer($request['end_date'], 'D j F, Y') ?></td>
                            <td><?= $request['num_date'] ?>ថ្ងៃ</td>
                            <td>
                                <div class="badge 
                                        <?= $request['status'] == 'Pending' ? 'badge bg-warning' : '' ?>
                                        <?= $request['status'] == 'Approved' ? 'badge bg-success' : '' ?>
                                        <?= $request['status'] == 'Rejected' ? 'badge bg-danger' : '' ?>
                                        <?= $request['status'] == 'Cancelled' ? 'badge bg-secondary' : '' ?>
                                    ">
                                    <?= $request['status'] ?>
                                </div>
                            </td>
                            <td class="p-0 text-center">
                                <a href="/elms/view-leave?leave_id=<?= htmlspecialchars($request['lrId']) ?>" title="ពិនិត្យមើល" data-bs-placement="auto" data-bs-toggle="tooltip" class="icon me-0 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer d-flex justify-content-end rounded-3">
        <ul class="pagination mb-0">
            <!-- Previous Page Link -->
            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                <a class="page-link mx-1" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                    </svg>
                </a>
            </li>
            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <!-- Next Page Link -->
            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</div>
<?php include('src/common/footer.php'); ?>

<!-- Modal Apply Leave -->
<div class="modal modal-blur fade" id="apply-leave" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/dof-apply-leave" enctype="multipart/form-data">
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
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker" name="start_date" required>
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
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ដល់កាលបរិច្ឆេទ" class="form-control date-picker" name="end_date" required>
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

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-filter'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));

        new Litepicker({
            element: document.getElementById("start_date"),
            singleMode: true,
            format: "YYYY-MM-DD",
            lang: 'kh', // Set language to Khmer
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        });

        new Litepicker({
            element: document.getElementById("end_date"),
            singleMode: true,
            format: "YYYY-MM-DD",
            lang: 'kh', // Set language to Khmer
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        });

        // Handle the clear button click
        document.querySelector('.btn-outline-secondary').addEventListener('click', function() {
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
            document.getElementById('select-filter').tomselect.clear();
        });

        // Handle the filter button click
        document.getElementById('filter').addEventListener('click', function() {
            var startDate = document.getElementById('start_date').value;
            var endDate = document.getElementById('end_date').value;
            var status = document.getElementById('select-filter').value;

            var params = new URLSearchParams();
            if (startDate) {
                params.append('start_date', startDate);
            }
            if (endDate) {
                params.append('end_date', endDate);
            }
            if (status) {
                params.append('status', status);
            }

            window.location.href = '?' + params.toString();
        });
    });
</script>