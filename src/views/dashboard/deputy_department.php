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

$title = "ទំព័រដើម";
include('src/common/header.php');
// Ensure the necessary data is available
$count = isset($data['count']) ? $data['count'] : 0;
$leaves = isset($data['leaves']) ? $data['leaves'] : [];
$page = isset($data['page']) ? $data['page'] : 1;
$totalPages = isset($data['totalPages']) ? $data['totalPages'] : 1;
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
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div id="real-time-clock"></div>
                </div>
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? ""); ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M16 19h6" />
                                    <path d="M19 16v6" />
                                </svg>
                                <span>បង្កើតសំណើ</span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/elms/apply-leave">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h16"></path>
                                            <path d="M19 22v.01"></path>
                                            <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                        </svg>
                                        <span class="mx-1">ច្បាប់ឈប់សម្រាក</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06"></path>
                                            <path d="M12 7v5l2 2"></path>
                                            <path d="M19 22v.01"></path>
                                            <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                        </svg>
                                        <span class="mx-1">លិខិតយឺត</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h12"></path>
                                            <path d="M20 14l2 2h-3"></path>
                                            <path d="M20 18l2 -2"></path>
                                            <path d="M19 16a3 3 0 1 0 2 5.236"></path>
                                        </svg>
                                        <span class="mx-1">បេសកម្ម</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row row-card mb-3">
    <div class="col-12 mb-3">
        <div class="row row-cards">
            <!-- All Leave Requests Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    ច្បាប់ឈប់សម្រាកទាំងអស់
                                </div>
                                <div class="text-secondary">
                                    <?= $count ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Letters Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                        <path d="M12 7v5l2 2" />
                                        <path d="M19 22v.01" />
                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    លិខិតចេញ និងចូលយឺត
                                </div>
                                <div class="text-secondary">
                                    32 shipped
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h12" />
                                        <path d="M20 14l2 2h-3" />
                                        <path d="M20 18l2 -2" />
                                        <path d="M19 16a3 3 0 1 0 2 5.236" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    បេសកម្ម
                                </div>
                                <div class="text-secondary">
                                    16 today
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Likes Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-brand-facebook">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    132 Likes
                                </div>
                                <div class="text-secondary">
                                    21 today
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-8 mb-3">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title">ច្បាប់ឈប់សម្រាកទាំងអស់</h3>
                <div class="dropdown">
                    <a class="btn btn-primary dropdown-toggle" href="/elms/apply-leave" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M16 19h6" />
                            <path d="M19 16v6" />
                        </svg>
                        <span>បង្កើតសំណើ</span>
                    </a>

                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="/elms/apply-leave">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                                    <path d="M16 3v4"></path>
                                    <path d="M8 3v4"></path>
                                    <path d="M4 11h16"></path>
                                    <path d="M19 22v.01"></path>
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                </svg>
                                <span class="mx-1">ច្បាប់ឈប់សម្រាក</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06"></path>
                                    <path d="M12 7v5l2 2"></path>
                                    <path d="M19 22v.01"></path>
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                </svg>
                                <span class="mx-1">លិខិតយឺត</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3"></path>
                                    <path d="M16 3v4"></path>
                                    <path d="M8 3v4"></path>
                                    <path d="M4 11h12"></path>
                                    <path d="M20 14l2 2h-3"></path>
                                    <path d="M20 18l2 -2"></path>
                                    <path d="M19 16a3 3 0 1 0 2 5.236"></path>
                                </svg>
                                <span class="mx-1">បេសកម្ម</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <?php if (empty($leaves)) : ?>
                <div class="d-flex flex-column align-items-center justify-content-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                    <div class="dropdown mb-4">
                        <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M16 19h6" />
                                <path d="M19 16v6" />
                            </svg>
                            <span>បង្កើតសំណើ</span>
                        </a>

                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="/elms/apply-leave">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                                        <path d="M16 3v4"></path>
                                        <path d="M8 3v4"></path>
                                        <path d="M4 11h16"></path>
                                        <path d="M19 22v.01"></path>
                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                    </svg>
                                    <span class="mx-1">ច្បាប់ឈប់សម្រាក</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06"></path>
                                        <path d="M12 7v5l2 2"></path>
                                        <path d="M19 22v.01"></path>
                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                    </svg>
                                    <span class="mx-1">លិខិតយឺត</span>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3"></path>
                                        <path d="M16 3v4"></path>
                                        <path d="M8 3v4"></path>
                                        <path d="M4 11h12"></path>
                                        <path d="M20 14l2 2h-3"></path>
                                        <path d="M20 18l2 -2"></path>
                                        <path d="M19 16a3 3 0 1 0 2 5.236"></path>
                                    </svg>
                                    <span class="mx-1">បេសកម្ម</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="leaveTable">
                        <thead>
                            <tr>
                                <th scope="col" class="sortable">Leave Type</th>
                                <th scope="col" class="d-none d-sm-table-cell sortable">Remarks</th>
                                <th scope="col" class="sortable">Status</th>
                                <th scope="col" class="sortable">Start Date</th>
                                <th scope="col" class="d-none d-md-table-cell sortable">End Date</th>
                                <th scope="col" class="d-none d-lg-table-cell sortable">Number of Days</th>
                                <th scope="col" class="d-none d-lg-table-cell sortable">Date Created</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leaves as $leave) : ?>
                                <tr>
                                    <td>
                                        <span class="badge <?= htmlspecialchars($leave['color']) ?>"><?= htmlspecialchars($leave['leave_type']) ?></span>
                                    </td>
                                    <td class="d-none d-sm-table-cell"><?= htmlspecialchars($leave['remarks']) ?></td>
                                    <td>
                                        <span class="badge <?= $leave['status'] == 'Pending' ? 'bg-warning-lt' : '' ?>
                                    <?= $leave['status'] == 'Approved' ? 'badge-outline text-success' : '' ?>
                                    <?= $leave['status'] == 'Rejected' ? 'badge-outline text-danger' : '' ?>
                                    <?= $leave['status'] == 'Cancelled' ? 'badge-outline text-secondary' : '' ?>">
                                            <?= htmlspecialchars($leave['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($leave['start_date']) ?></td>
                                    <td class="d-none d-md-table-cell"><?= htmlspecialchars($leave['end_date']) ?></td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($leave['num_date']) ?></td>
                                    <td class="d-none d-lg-table-cell"><?= htmlspecialchars($leave['created_at']) ?></td>
                                    <td class="p-0">
                                        <a href="/elms/view-leave-detail?leave_id=<?= htmlspecialchars($leave['id']) ?>" title="ពិនិត្យមើល" data-bs-placement="auto" data-bs-toggle="tooltip" class="icon me-2 p-0 edit-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                            </svg>
                                        </a>
                                        <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger mt-0 p-0" data-bs-toggle="modal" data-bs-target="#deleteModal<?= htmlspecialchars($leave['id']) ?>">
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

                                <div class="modal modal-blur fade" id="deleteModal<?= htmlspecialchars($leave['id']) ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-status bg-danger"></div>
                                            <form action="/elms/leave-delete" method="POST">
                                                <div class="modal-body text-center py-4 mb-0">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($leave['id']) ?>">
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
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="card-footer d-flex align-items-center justify-content-end">
                    <ul class="pagination mb-0">
                        <?php if ($page > 1) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M15 6l-6 6l6 6"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages) : ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24h24H0z" fill="none"></path>
                                        <path d="M9 6l6 6l-6 6"></path>
                                    </svg>
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const table = document.getElementById('leaveTable');
                    const headers = table.querySelectorAll('.sortable');

                    headers.forEach(header => {
                        header.addEventListener('click', function() {
                            const tableBody = table.querySelector('tbody');
                            const rows = Array.from(tableBody.querySelectorAll('tr'));
                            const index = Array.from(header.parentNode.children).indexOf(header);
                            const order = header.dataset.order === 'asc' ? 'desc' : 'asc';
                            header.dataset.order = order;

                            rows.sort((a, b) => {
                                const cellA = a.children[index].innerText.toLowerCase();
                                const cellB = b.children[index].innerText.toLowerCase();

                                if (cellA < cellB) {
                                    return order === 'asc' ? -1 : 1;
                                }
                                if (cellA > cellB) {
                                    return order === 'asc' ? 1 : -1;
                                }
                                return 0;
                            });

                            tableBody.append(...rows);
                        });
                    });
                });
            </script>
        </div>
    </div>

    <div class="col-sm-12 col-lg-4 mb-3">
        <div class="card h-100">
            <h3 class="card-header">ច្បាប់របស់សមាជិក</h3>
            <div class="card-body">
                <div class="list-group">
                    <?php if (empty($getuserapproves)) : ?>
                        <div class="d-flex flex-column align-items-center justify-content-center text-center empty-state">
                            <img src="public/img/icons/svgs/empty.svg" alt="No data" class="empty-icon">
                            <p class="empty-text">មិនមានការឈប់សម្រាក</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($getuserapproves as $getuserapprove) : ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $getuserapprove['id'] ?>">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $getuserapprove['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $getuserapprove['id'] ?>">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $getuserapprove['profile'] ?>" class="avatar me-3" alt="Profile picture">
                                            <div class="flex-grow-1">
                                                <h5 class="name mb-1"><?= htmlspecialchars($getuserapprove['khmer_name']) ?></h5>
                                                <p class="date mb-1"><?= translateDateToKhmer($getuserapprove['created_at'], 'D j F Y') ?></p>
                                            </div>
                                            <span class="badge <?= $getuserapprove['color'] ?>"><?= htmlspecialchars($getuserapprove['status']) ?></span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?= $getuserapprove['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $getuserapprove['id'] ?>" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <!-- Add additional details here -->
                                        <p><strong>Leave Reason:</strong> <?= htmlspecialchars($getuserapprove['remarks']) ?></p>
                                        <p><strong>Additional Notes:</strong> <?= htmlspecialchars($getuserapprove['start_date']) ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('src/common/footer.php'); ?>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Check if it's the user's first login
        if (!sessionStorage.getItem('hasVisitedBefore')) {
            // Show the modal with static backdrop
            var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();

            // Set flag in sessionStorage so the modal doesn't show again
            sessionStorage.setItem('hasVisitedBefore', 'true');
        }

        // Handle form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Add your form submission logic here (e.g., AJAX call to server)
            // For now, just close the modal
            var myModalEl = document.getElementById('changePasswordModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        });
    });
</script> -->
<!-- Modal for changing password -->
<div class="modal modal-blur fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Your Password</h5>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>