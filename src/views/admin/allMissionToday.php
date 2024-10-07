<?php
$title = "បេសកកម្មទាំងអស់";
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
                        <div>គ្រប់គ្រងសំណើ</div>
                    </div>
                    <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <!-- Attendance List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="/elms/adminmissions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path d="M16 3l0 4" />
                                <path d="M8 3l0 4" />
                                <path d="M4 11l16 0" />
                                <path d="M8 15h2v2h-2z" />
                            </svg>
                            បេសកកម្មទាំងអស់
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-primary active" href="/elms/admintodaymissions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                            </svg>
                            បេសកកម្មថ្ងៃនេះ
                            <?php if (!empty($getAllMissionCount)): ?>
                                <span class="badge bg-red text-red-fg ms-2"><?= $getAllMissionCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- filter  -->
            <div class="card-header">
                <form class="w-100 mb-0" action="/elms/admintodaymissions" method="POST">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- SVG icon for calendar -->
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
                                    name="start_date" id="start_date" autocomplete="off"
                                    value="<?= htmlspecialchars($_POST['start_date'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- SVG icon for calendar -->
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
                                <input class="form-control date-picker" placeholder="ដល់កាលបរិច្ឆេទ" type="text"
                                    name="end_date" id="leave_end_date" autocomplete="off"
                                    value="<?= htmlspecialchars($_POST['end_date'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-red w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                                <span>ស្វែងរក</span>
                            </button>
                        </div>
                        <div class="col-lg-2">
                            <a href="/elms/admintodaymissions" class="btn btn-outline w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
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
            <!-- end filter  -->
            <?php if (empty($getAllMissions)): ?>
                <div class="text-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <h3 class="text-primary mb-3">មិនមានបេសកកម្មទេថ្ងៃនេះ។</h3>
                </div>
            <?php else: ?>
                <!-- Second table: All Approved Attendances (excluding today) -->
                <?php if (!empty($getAllMissions)): ?>
                    <!-- table  -->
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered-less table-striped mb-0 sortable-table">
                            <thead>
                                <tr>
                                    <th>ល.រ</th>
                                    <th><button class="table-sort" data-sort="sort-name">ឈ្មោះ</button></th>
                                    <th><button class="table-sort" data-sort="sort-date">ឈ្មោះបេសកកម្ម</button></th>
                                    <th><button class="table-sort" data-sort="sort-type">កាលបរិច្ឆេទចាប់ពី</button></th>
                                    <th><button class="table-sort" data-sort="sort-type">ដល់កាលបរិច្ឆេទ</button></th>
                                    <th>ឯកសារភ្ជាប់</th>
                                    <th><button class="table-sort" data-sort="sort-approved-date">ស្នើនៅ</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getAllMissions as $key => $mission): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <div class="d-flex mb-0">
                                                <img src="<?= htmlspecialchars($mission['profile_picture']) ?>" class="avatar me-3"
                                                    style="object-fit: cover;" alt="Profile">
                                                <div class="d-flex flex-column">
                                                    <h3 class="text-primary mb-0">
                                                        <?= htmlspecialchars($mission['khmer_name']) ?>
                                                    </h3>
                                                    <div class="text-muted"><?= htmlspecialchars($mission['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($mission['missionName']) ?></td>
                                        <td><?= htmlspecialchars($mission['start_date']) ?></td>
                                        <td><?= htmlspecialchars($mission['end_date']) ?></td>
                                        <td>
                                            <a class="text-truncate"
                                                href="public/uploads/missions_attachments/<?= $mission['attachment'] ?>"
                                                target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                                </svg>
                                                ឯកសារភ្ជាប់
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($mission['created_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Logic -->
                    <div class="card-footer">
                        <?php
                        // Fetch total approved records for pagination calculation
                        $totalRecords = $adminModel->getTotalMission(); // Use the method to get total records
                        $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages
                        ?>

                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                                    <!-- Chevron left icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M15 6l-6 6l6 6"></path>
                                    </svg>
                                </a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= $i == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?= $currentPage == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage + 1 ?>">
                                    <!-- Chevron right icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M9 6l6 6l-6 6"></path>
                                    </svg>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>