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
?>
<?php
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
    <div class="card-header">
        <div class="row align-items-center w-100">
            <div class="col-sm-12 col-lg col-md mb-3">
                <label for="" class="form-label">កាលបរិច្ឆេទចាប់ពី</label>
                <input type="text" placeholder="កាលបរិច្ឆេទ" id="start_date" autocomplete="off" class="form-control">
            </div>
            <div class="col-sm-12 col-lg col-md mb-3">
                <label for="" class="form-label">ដល់កាលបរិច្ឆេទ</label>
                <input type="text" id="end_date" autocomplete="off" placeholder="កាលបរិច្ឆេទ" class="form-control">
            </div>
            <div class="col-sm-12 col-lg col-md mb-3">
                <label for="" class="form-label">ស្ថានភាព</label>
                <select class="form-control form-select" name="" id="select-filter">
                    <option selected disabled>ជ្រើសរើសស្ថានភាព</option>
                    <option value="Pending" data-custom-properties='&lt;span class="badge bg-warning"&gt;'>Pending</option>
                    <option value="Approved" data-custom-properties='&lt;span class="badge bg-success"&gt;'>Approved</option>
                    <option value="Rejected" data-custom-properties='&lt;span class="badge bg-danger"&gt;'>Rejected</option>
                    <option value="Canceled" data-custom-properties='&lt;span class="badge bg-secondary"&gt;'>Canceled</option>
                </select>
            </div>
            <div class="col-sm-12 col-lg col-md mb-3">
                <button class="btn btn-outline-secondary w-100">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                    </svg>
                    សម្អាត
                </button>
            </div>
            <div class="col-sm-12 col-lg col-md mb-3">
                <button class="btn btn-primary w-100" id="filter">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                        <path d="M21 21l-6 -6" />
                    </svg>
                    ស្វែងរក
                </button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th>ប្រភេទច្បាប់</th>
                    <th>ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th>ដល់កាលបរិច្ឆេទ</th>
                    <th>រយៈពេល</th>
                    <th>ស្ថានភាព</th>
                    <th>សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paginatedRequests)) : ?>
                    <tr>
                        <td colspan="6" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                            <a href="/elms/apply-leave" class="btn btn-primary">
                                <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <line x1="12" y1="5" x2="12" y2="19" />
                                    <line x1="5" y1="12" x2="19" y2="12" />
                                </svg>
                                បង្កើតប្រភេទច្បាប់ថ្មី
                            </a>
                        </td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($paginatedRequests as $request) : ?>
                        <tr>
                            <td>
                                <div class="badge <?= htmlspecialchars($request['color']) ?>"><?= htmlspecialchars($request['leave_type']) ?></div>
                            </td>
                            <td><?= translateDateToKhmer(htmlspecialchars($request['start_date'])) ?></td>
                            <td><?= translateDateToKhmer(htmlspecialchars($request['end_date'])) ?></td>
                            <td><?= htmlspecialchars($request['num_date']) ?>ថ្ងៃ</td>
                            <td>
                                <span class="status-dot status-dot-animated
                <?= $request['status'] == 'Pending' ? 'bg-warning' : '' ?>
                <?= $request['status'] == 'Approved' ? 'badge-outline text-success' : '' ?>
                <?= $request['status'] == 'Rejected' ? 'badge-outline text-danger' : '' ?>
                <?= $request['status'] == 'Cancelled' ? 'badge-outline text-secondary' : '' ?> me-1">
                                </span>
                                <?= htmlspecialchars($request['status']) ?>
                            </td>
                            <td class="p-0">
                                <a href="/elms/view-leave-detail?leave_id=<?= htmlspecialchars($request['id']) ?>" title="ពិនិត្យមើល" data-bs-placement="auto" data-bs-toggle="tooltip" class="icon me-2 p-0 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                                <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger mt-0 p-0" data-bs-toggle="modal" data-bs-target="#deleteModal<?= htmlspecialchars($request['id']) ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M4 7l16 0" />
                                        <path d="M10 11l0 6" />
                                        <path d="M14 11l0 6" />
                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                        <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        <div class="modal modal-blur fade" id="deleteModal<?= htmlspecialchars($request['id']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-status bg-danger"></div>
                                    <form action="/elms/leave-delete" method="POST">
                                        <div class="modal-body text-center py-4 mb-0">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 9v4"></path>
                                                <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                                                <path d="M12 16h.01"></path>
                                            </svg>
                                            <h5 class="modal-title fw-bold text-danger">លុបសំណើច្បាប់</h5>
                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបសំណើច្បាប់នេះ?</p>
                                        </div>
                                        <div class="modal-footer bg-light border-top">
                                            <div class="w-100 mt-3">
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