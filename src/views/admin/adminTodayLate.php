<?php
$title = "យឺតថ្ងៃនេះ";
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
                        <a class="nav-link" href="/elms/adminpending">
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
                            សំណើទាំងអស់
                            <?php if (!empty($getPendingCount)): ?>
                                <span class="badge bg-red text-red-fg mx-2"><?= $getPendingCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-primary active" href="/elms/admintodaylate">
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
                            យឺតថ្ងៃនេះ
                            <?php if (!empty($gettodaylatecount)): ?>
                                <span class="badge bg-red text-red-fg mx-2"><?= $gettodaylatecount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/elms/adminapproved">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-check">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11.5 21h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M15 19l2 2l4 -4" />
                            </svg>
                            បានអនុម័ត
                            <?php if (!empty($getApprovedCount)): ?>
                                <span class="badge bg-red text-red-fg mx-2"><?= $getApprovedCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/elms/adminrejected">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-x">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M13 21h-7a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6.5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M22 22l-5 -5" />
                                <path d="M17 22l5 -5" />
                            </svg>
                            មិនអនុម័ត
                            <?php if (!empty($getRejectedCount)): ?>
                                <span class="badge bg-red text-red-fg mx-2"><?= $getRejectedCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <?php if (empty($gettodaylates)): ?>
                <div class="text-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <h3 class="text-primary mb-4">មិនមានអ្នកយឺតទេថ្ងៃនេះ</h3>
                </div>
            <?php else: ?>
                <!-- Second table: All Approved Attendances (excluding today) -->
                <?php if (!empty($gettodaylates)): ?>
                    <!-- table  -->
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered-less table-striped mb-0 sortable-table">
                            <thead>
                                <tr>
                                    <th>ល.រ</th>
                                    <th><button class="table-sort" data-sort="sort-name">ឈ្មោះ</button></th>
                                    <th><button class="table-sort" data-sort="sort-date">កាលបរិច្ឆេទ</button></th>
                                    <th><button class="table-sort" data-sort="sort-type">ប្រភេទ</button></th>
                                    <th><button class="table-sort" data-sort="sort-type">រយៈពេល</button></th>
                                    <th>ស្ថានភាព</th>
                                    <th><button class="table-sort" data-sort="sort-approved-date">បានអនុម័តនៅ</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($gettodaylates as $key => $attendance): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <div class="d-flex mb-0">
                                                <img src="<?= htmlspecialchars($attendance['profile_picture']) ?>"
                                                    class="avatar me-3" style="object-fit: cover;" alt="Profile">
                                                <div class="d-flex flex-column">
                                                    <h3 class="text-primary mb-0">
                                                        <?= htmlspecialchars($attendance['khmer_name']) ?>
                                                    </h3>
                                                    <div class="text-muted"><?= htmlspecialchars($attendance['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($attendance['date']) ?></td>
                                        <td>
                                            <span
                                                class="badge <?= $attendance['type'] == 'latein' ? 'bg-warning-lt' : ($attendance['type'] == 'lateout' ? 'bg-info-lt' : 'bg-danger-lt') ?>">
                                                <?= $attendance['type'] == 'latein' ? 'ចូលយឺត' : ($attendance['type'] == 'lateout' ? 'ចេញយឺត' : 'ចេញមុន') ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($attendance['late']) ?></td>
                                        <td><span class="badge bg-success">បានអនុម័ត</span></td>
                                        <td><?= htmlspecialchars($attendance['updated_at']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Logic -->
                    <div class="card-footer">
                        <?php
                        // Fetch total approved records for pagination calculation
                        $totalRecords = $adminModel->getTotalTodayLate('Approved'); // Use the method to get total records
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