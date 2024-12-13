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

$pretitle = "ទំព័ដើម";
$title = "ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ";

$customButton = '
    <div class="d-flex">
        <a href="#" data-bs-toggle="modal" data-bs-target="#head-office-apply-leave"
            class="btn btn-primary d-none d-sm-inline-block">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <line x1="12" y1="5" x2="12" y2="19" />
                <line x1="5" y1="12" x2="19" y2="12" />
            </svg>
            បង្កើតសំណើច្បាប់
        </a>
        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
            data-bs-target="#head-office-apply-leave" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>
';
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    // Define Khmer translations for days and months
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

    // Define Khmer numerals
    $numerals = [
        '0' => '០',
        '1' => '១',
        '2' => '២',
        '3' => '៣',
        '4' => '៤',
        '5' => '៥',
        '6' => '៦',
        '7' => '៧',
        '8' => '៨',
        '9' => '៩'
    ];

    // Get the English day and month names
    $englishDay = date('D', strtotime($date));
    $englishMonth = date('F', strtotime($date));

    // Translate English day and month names to Khmer
    $translatedDay = $days[$englishDay] ?? $englishDay;
    $translatedMonth = $months[$englishMonth] ?? $englishMonth;

    // Format the date in English
    $formattedDate = date($format, strtotime($date));

    // Replace day and month with Khmer
    $translatedDate = str_replace(
        [$englishDay, $englishMonth],
        [$translatedDay, $translatedMonth],
        $formattedDate
    );

    // Replace Arabic numerals with Khmer numerals
    $translatedDate = strtr($translatedDate, $numerals);

    return $translatedDate;
}
include('src/common/header.php');
// Assuming $requests is an array of all leave requests
$requestsPerPage = 10; // Number of requests to display per page
$totalRequests = count($requests);
$totalPages = ceil($totalRequests / $requestsPerPage);

// Get the current page from the URL, default to page 1 if not set
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$currentPage = max(1, min($currentPage, $totalPages)); // Ensure the current page is within bounds

// Calculate the starting index of the requests to display
$startIndex = ($currentPage - 1) * $requestsPerPage;
$paginatedRequests = array_slice($requests, $startIndex, $requestsPerPage);

?>

<div class="card rounded-3">
    <div class="card-header mb-3">
        <div class="d-flex align-items-center justify-content-between">
            <h4 class="header-title mb-0 text-muted"><?= $title ?></h4>
        </div>
    </div>

    <div class="card-body border-bottom">
        <form class="mb-0" action="/elms/hofficeLeave" method="POST">
            <div class="row align-items-center">
                <div class="col-lg-3 mb-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path
                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                                </path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <path d="M4 11h16"></path>
                                <path d="M11 15h1"></path>
                                <path d="M12 15v3"></path>
                            </svg>
                        </span>
                        <input class="form-control date-picker" placeholder="កាលបរិច្ឆេទចាប់ពី" type="text"
                            name="start_date" id="start_date" autocomplete="off" />
                    </div>
                </div>
                <div class="col-lg-3 mb-3">
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path
                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z">
                                </path>
                                <path d="M16 3v4"></path>
                                <path d="M8 3v4"></path>
                                <path d="M4 11h16"></path>
                                <path d="M11 15h1"></path>
                                <path d="M12 15v3"></path>
                            </svg>
                        </span>
                        <input class="form-control date-picker" placeholder="ដល់កាលបរិច្ឆេទ" type="text" name="end_date"
                            id="leave_end_date" autocomplete="off" />
                    </div>
                </div>
                <div class="col-lg-3 mb-3">
                    <select type="text" class="form-select" id="select-status" name="status" tabindex="-1">
                        <option class="text-muted" selected disabled>ស្ថានភាព</option>
                        <option value="Pending" data-custom-properties="&lt;span class=&quot;badge bg-warning&quot;">
                            Pending</option>
                        <option value="Approved" data-custom-properties="&lt;span class=&quot;badge bg-success&quot;">
                            Approved</option>
                        <option value="Rejected" data-custom-properties="&lt;span class=&quot;badge bg-danger&quot;">
                            Rejected</option>
                        <option value="Canceled" data-custom-properties="&lt;span class=&quot;badge bg-secondary&quot;">
                            Canceled</option>
                    </select>
                </div>
                <div class="col mb-3">
                    <button type="submit" class="btn w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                            <path d="M21 21l-6 -6" />
                        </svg>
                        <span>ស្វែងរក</span>
                    </button>
                </div>
                <div class="col mb-3">
                    <a href="/elms/hofficeLeave" type="reset" class="btn w-100 btn-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-rotate-clockwise">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                        </svg>
                        <span>សម្អាត</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table card-table table-vcenter text-nowrap datatable">
            <thead>
                <tr>
                    <th class="d-none d-sm-table-cell">ប្រភេទច្បាប់</th>
                    <th class="d-none d-sm-table-cell">ចាប់ពីកាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">ដល់កាលបរិច្ឆេទ</th>
                    <th class="d-none d-sm-table-cell">រយៈពេល</th>
                    <th class="d-none d-sm-table-cell">ស្ថានភាព</th>
                    <th class="d-none d-sm-table-cell">មូលហេតុ</th>
                    <th>សកម្មភាព</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($paginatedRequests)): ?>
                    <tr>
                        <td colspan="8" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                            <p>មិនទាន់មានសំណើនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោម ឬស្តាំដៃខាងលើ</p>
                            <a class="btn btn-primary d-none d-sm-inline-block mb-3" data-bs-toggle="modal"
                                data-bs-target="#head-office-apply-leave">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
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
                            <a href="#" class="btn btn-primary d-sm-none btn-icon mb-3"
                                data-bs-toggle="modal" data-bs-target="#head-office-apply-leave">
                                <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td class="d-none d-sm-table-cell">
                                <div class="badge <?= htmlspecialchars($request['color']) ?>">
                                    <?= htmlspecialchars($request['leave_type_name']) ?>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($request['start_date'], 'D,j F Y') ?>
                            </td>
                            <td class="d-none d-sm-table-cell"><?= translateDateToKhmer($request['end_date'], 'D,j F Y') ?></td>
                            <td class="d-none d-sm-table-cell"><?= $request['num_date'] ?>ថ្ងៃ</td>
                            <td class="d-none d-sm-table-cell">
                                <div class="badge 
                                    <?php
                                    if ($request['status'] === 'Pending') {
                                        echo 'bg-warning';
                                    } elseif ($request['status'] === 'Rejected') {
                                        echo 'bg-danger';
                                    } elseif ($request['status'] === 'Approved') {
                                        echo 'bg-success';
                                    } else {
                                        echo htmlspecialchars($request['color']); // Fallback to original color if not one of the specified statuses
                                    }
                                    ?>">
                                    <?= htmlspecialchars($request['status']) ?>
                                </div>
                            </td>
                            <td class="d-none d-sm-table-cell">
                                <span class="text-truncate" data-bs-placement="top" data-bs-toggle="tooltip"
                                    title="<?= htmlspecialchars($request['remarks']) ?>"><?= htmlspecialchars($request['remarks']) ?></span>
                            </td>
                            <td class="text-center p-0">
                                <a href="/elms/view-leave-detail-h?leave_id=<?= htmlspecialchars($request['id']) ?>"
                                    title="ពិនិត្យមើល" data-bs-placement="auto" data-bs-toggle="tooltip"
                                    class="icon me-0 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                                <?php if ($request['status'] !== 'Approved'): ?>
                                    <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?= htmlspecialchars($request['id']) ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                        </svg>
                                    </a>
                                <?php endif; ?>
                                <a href="#" class="d-sm-none" title="បង្ហាញបន្ថែម" data-bs-toggle="collapse"
                                    data-bs-target="#collapseRequest<?= $request['id'] ?>" aria-expanded="false"
                                    aria-controls="collapseRequest<?= $request['id'] ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-dots">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                    </svg>
                                </a>
                            </td>
                        </tr>

                        <tr class="d-sm-none">
                            <td colspan="7" class="p-0">
                                <div class="collapse" id="collapseRequest<?= $request['id'] ?>">
                                    <table class="table mb-0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <strong>ប្រភេទច្បាប់ : </strong>
                                                    <div class="badge <?= htmlspecialchars($request['color']) ?>">
                                                        <?= htmlspecialchars($request['leave_type']) ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>ចាប់ពីកាលបរិច្ឆេទ : </strong>
                                                    <span> <?= translateDateToKhmer($request['start_date'], 'D,j F Y') ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>ដល់កាលបរិច្ឆេទ : </strong>
                                                    <span><?= translateDateToKhmer($request['end_date'], 'D,j F Y') ?></span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>រយៈពេល : </strong>
                                                    <span><?= $request['num_date'] ?>ថ្ងៃ</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <strong>មូលហេតុ : </strong>
                                                    <span class="text-truncate" data-bs-placement="top" data-bs-toggle="tooltip"
                                                        title="<?= htmlspecialchars($request['remarks']) ?>"><?= htmlspecialchars($request['remarks']) ?></span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>

                        <!-- delete  -->
                        <div class="modal modal-blur fade" id="deleteModal<?= htmlspecialchars($request['id']) ?>" tabindex="-1"
                            role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-status bg-danger"></div>
                                    <form action="/elms/hoffice-delete" method="POST">
                                        <div class="modal-body text-center py-4 mb-0">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($request['id']) ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M12 9v4"></path>
                                                <path
                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                </path>
                                                <path d="M12 16h.01"></path>
                                            </svg>
                                            <h5 class="modal-title fw-bold text-danger">លុបសំណើច្បាប់</h5>
                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបសំណើច្បាប់នេះ?</p>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <div class="w-100">
                                                <div class="row">
                                                    <div class="col">
                                                        <button type="button" class="btn w-100"
                                                            data-bs-dismiss="modal">បោះបង់</button>
                                                    </div>
                                                    <div class="col">
                                                        <button type="submit" class="btn btn-danger ms-auto w-100">បាទ /
                                                            ចា៎</button>
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M15 6l-6 6l6 6"></path>
                    </svg>
                </a>
            </li>
            <!-- Page Number Links -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>"><a class="page-link"
                        href="?page=<?= $i ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <!-- Next Page Link -->
            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M9 6l6 6l-6 6"></path>
                    </svg>
                </a>
            </li>
        </ul>
    </div>
</div>

<?php include('src/common/footer.php'); ?>