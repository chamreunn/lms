<?php
$title = "ច្បាប់ឈប់សម្រាក";
include('src/common/header.php');
$action = $_GET['action'] ?? 'latein';
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="col-12">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle mb-1">
                        <div>សំណើចេញ ចូលយឺត</div>
                    </div>
                    <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="row mt-3 mb-3">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-3 mb-3">

            <div class="card list-group mb-3">
                <a href="/elms/adminpending?action=allLate" class="list-group-item list-group-item-action">
                    <!-- SVG and Badge for Late In -->
                    <div class="d-flex">
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
                        <div class="mx-2">សំណើដែលបានអនុម័ត</div>
                        <span class="badge bg-danger text-white ms-auto"><?= $getApproved ?></span>
                    </div>
                </a>
            </div>

            <div class="card list-group mb-3">
                <a href="/elms/adminpending?action=latein" class="list-group-item list-group-item-action">
                    <!-- SVG and Badge for Late In -->
                    <div class="d-flex">
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
                        <div class="mx-2">ចូលយឺត</div>
                        <span class="badge bg-danger text-white ms-auto"><?= $getLateInCount ?></span>
                    </div>
                </a>
                <a href="/elms/adminpending?action=lateout" class="list-group-item list-group-item-action">
                    <!-- SVG and Badge for Late Out -->
                    <div class="d-flex">
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
                        <div class="mx-2">ចេញយឺត</div>
                        <span class="badge bg-danger text-white ms-auto"><?= $getLateOutCount ?></span>
                    </div>
                </a>
                <a href="/elms/adminpending?action=leaveearly" class="list-group-item list-group-item-action>">
                    <!-- SVG and Badge for Leave Early -->
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M11.5 21h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M20.2 20.2l1.8 1.8" />
                        </svg>
                        <div class="mx-2">ចេញមុន</div>
                        <span class="badge bg-danger text-white ms-auto"><?= $getLeaveEarlyCount ?></span>
                    </div>
                </a>
                <!-- leave  -->
                <a href="/elms/adminApprovedLeave" class="list-group-item list-group-item-action active">
                    <!-- SVG and Badge for Leave Early -->
                    <div class="d-flex">
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
                        <div class="mx-2">ច្បាប់ឈប់សម្រាក</div>
                        <?php if (!empty($getLeaveCount)): ?>
                            <span class="badge bg-danger text-white ms-auto">
                                <?= $getLeaveCount ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-9">
            <div class="card">
                <?php if (empty($getAllLeave)): ?>
                    <div class="card-header">
                        <h3 class="card-title text-primary mb-0">
                            <span><?= $title ?></span>
                        </h3>
                    </div>
                    <div class="text-center mb-3">
                        <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                        <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                    </div>
                <?php else: ?>
                    <div class="card-header">
                        <h3 class="card-title text-primary mb-0">
                            <span>ថ្ងៃនេះ <?= date('Y-m-d') ?></span>
                        </h3>
                        <a href="/elms/AllLeavesAdmin" class="btn btn-sm btn-primary ms-auto">
                            មើលទាំងអស់
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right mx-2 me-0">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M9 6l6 6l-6 6" />
                            </svg>
                        </a>
                    </div>
                    <table class="table table-responsive mb-0">
                        <thead>
                            <tr>
                                <th>ឈ្មោះថ្នាក់ដឹកនាំ និងមន្ត្រិ</th>
                                <th>ប្រភេទច្បាប់</th>
                                <th>កាលបរិច្ឆេទចាប់ពី</th>
                                <th>ដល់កាលបរិច្ឆេទ</th>
                                <th>មូលហេតុ</th>
                                <th>ស្ថានភាព</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($getAllLeave as $leave): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex">

                                            <img src="<?= $leave['profile'] ?>" class="avatar" style="object-fit: cover;"
                                                alt="">
                                            <div class="d-flex flex-column mx-2">
                                                <h4 class="mx-0 mb-1 text-primary">
                                                    <?= $leave['khmer_name'] ?>
                                                </h4>
                                                <span class="text-muted"><?= $leave['email'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge <?= $leave['ltColor'] ?>"><?= $leave['leave_type'] ?></span></td>
                                    <td><?= $leave['start_date'] ?></td>
                                    <td><?= $leave['end_date'] ?></td>
                                    <td><?= $leave['remarks'] ?></td>
                                    <td><span class="badge bg-success"><?= $leave['status'] ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>