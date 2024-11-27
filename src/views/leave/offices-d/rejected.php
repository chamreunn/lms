<?php
$pretitle = "ទំព័រដើម";
$title = "មិនអនុម័ត";
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
    
    <!-- table display  -->
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
                <?php if (empty($paginatedRequests)): ?>
                    <tr>
                        <td colspan="7" class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនទាន់មានសំណើច្បាប់ថ្មីនៅឡើយ។</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paginatedRequests as $request): ?>
                        <tr>
                            <td>
                                <div class="d-flex">
                                    <img src="https://hrms.iauoffsa.us/images/<?= $request['profile'] ?>" class="avatar"
                                        style="object-fit: cover;" alt="">
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
                                <a href="/elms/view-leave?leave_id=<?= htmlspecialchars($request['lrId']) ?>" title="ពិនិត្យមើល"
                                    data-bs-placement="auto" data-bs-toggle="tooltip" class="icon me-0 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                        <path
                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- pagination  -->
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