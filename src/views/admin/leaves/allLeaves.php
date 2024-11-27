<?php
$pretitle = "ទំព័រដើម";
$title = "ច្បាប់ឈប់សម្រាកទាំងអស់";
include('src/common/header.php');
$action = $_GET['action'] ?? 'latein';
?>

<!-- Attendance List -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link fw-bold text-primary active" href="/elms/adminleaves">
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
                        <?= $title ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/elms/adminleavetoday">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                            <path d="M16 3v4" />
                            <path d="M8 3v4" />
                            <path d="M4 11h16" />
                            <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                        </svg>
                        ច្បាប់ឈប់សម្រាកថ្ងៃនេះ
                        <?php if (!empty($getLeaveTodayCount)): ?>
                            <span class="badge bg-red text-red-fg ms-2"><?= $getLeaveTodayCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
        <!-- filter  -->
        <div class="card-header">
            <form class="w-100 mb-0" action="/elms/adminleaves" method="POST">
                <div class="row g-3">
                    <div class="col-lg-3">
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
                    <div class="col-lg-3">
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
                    <div class="col-lg-3">
                        <select type="text" class="form-select" id="select-status" name="status" tabindex="-1">
                            <option class="text-muted" selected disabled>ស្ថានភាព</option>
                            <option data-custom-properties="&lt;span class=&quot;badge bg-warning&quot;" value="Pending"
                                <?php echo (isset($_POST['status']) && $_POST['status'] == 'Pending') ? 'selected' : ''; ?>>
                                Pending
                            </option>
                            <option data-custom-properties="&lt;span class=&quot;badge bg-green&quot;" value="Approved"
                                <?php echo (isset($_POST['status']) && $_POST['status'] == 'Approved') ? 'selected' : ''; ?>>
                                Approved
                            </option>
                            <option data-custom-properties="&lt;span class=&quot;badge bg-danger&quot;" value="Rejected"
                                <?php echo (isset($_POST['status']) && $_POST['status'] == 'Rejected') ? 'selected' : ''; ?>>
                                Rejected
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-2 ms-auto">
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
                    <div class="col-lg-1">
                        <a href="/elms/adminleaves" class="btn btn-outline w-100">
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
        <?php if (empty($getAllLeaves)): ?>
            <div class="text-center">
                <img src="public/img/icons/svgs/empty.svg" alt="">
                <h3 class="text-primary mb-3">មិនទាន់មានសំណើនៅឡើយ។</h3>
            </div>
        <?php else: ?>
            <!-- Second table: All Approved Attendances (excluding today) -->
            <?php if (!empty($getAllLeaves)): ?>
                <!-- table  -->
                <div class="table-responsive">
                    <table class="table table-vcenter table-bordered-less table-striped mb-0 sortable-table">
                        <thead>
                            <tr>
                                <th><button class="table-sort" data-sort="sort-name">ឈ្មោះ</button></th>
                                <th><button class="table-sort" data-sort="sort-leave-type">ប្រភេទច្បាប់</button></th>
                                <th><button class="table-sort" data-sort="sort-start-date">កាលបរិច្ឆេទចាប់ពី</button></th>
                                <th><button class="table-sort" data-sort="sort-end-date">ដល់កាលបរិច្ឆេទ</button></th>
                                <th><button class="table-sort" data-sort="sort-reason">មូលហេតុ</button></th>
                                <th><button class="table-sort" data-sort="sort-created-at">ស្នើនៅ</button></th>
                                <th><button class="table-sort" data-sort="sort-status">ស្ថានភាព</button></th>
                                <th><button class="table-sort" data-sort="sort-action">សកម្មភាព</button></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($getAllLeaves as $leave): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex mb-0">
                                            <img src="<?= htmlspecialchars($leave['profile_picture']) ?>" class="avatar me-3"
                                                style="object-fit: cover;" alt="Profile">
                                            <div class="d-flex flex-column">
                                                <h3 class="text-primary mb-0">
                                                    <?= htmlspecialchars($leave['khmer_name']) ?>
                                                </h3>
                                                <div class="text-muted"><?= htmlspecialchars($leave['email']) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge <?= $leave['leaveTypeColor'] ?>"><?= htmlspecialchars($leave['leave_type_name']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($leave['start_date']) ?></td>
                                    <td><?= htmlspecialchars($leave['end_date']) ?></td>
                                    <td class="text-truncate"><?= htmlspecialchars($leave['remarks']) ?></td>
                                    <td><?= htmlspecialchars($leave['created_at']) ?></td>
                                    <td>
                                        <span
                                            class="badge <?= $leave['status'] == 'Pending' ? 'bg-warning' : ($leave['status'] == 'Approved' ? 'bg-success' : 'bg-danger') ?>">
                                            <?= htmlspecialchars($leave['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="/elms/admin-view-leave?leave_id=<?= $leave['id'] ?>">ពិនិត្យមើល</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Logic -->
                <div class="card-footer">
                    <?php
                    // Fetch total approved records for pagination calculation
                    $totalRecords = $adminModel->getTotalLeaveCount(); // Use the method to get total records
                    $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages
                    ?>

                    <ul class="pagination justify-content-end mb-0">
                        <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                                <!-- Chevron left icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon">
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
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include('src/common/footer.php'); ?>