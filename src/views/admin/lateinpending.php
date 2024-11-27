<?php
$pretitle = "ទំព័រដើម";
$title = "សំណើយឺតទាំងអស់";
include('src/common/header.php');
$action = $_GET['action'] ?? 'latein';
?>

<!-- Attendance List -->
<div class="col-12">
    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link text-info active" href="/elms/adminpending">
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
                        សំណើទាំងអស់
                        <?php if (!empty($getPendingCount)): ?>
                            <span class="badge bg-red text-red-fg mx-2"><?= $getPendingCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-primary" href="/elms/admintodaylate">
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
                        យឺតថ្ងៃនេះ
                        <?php if (!empty($gettodaylatecount)): ?>
                            <span class="badge bg-red text-red-fg mx-2"><?= $gettodaylatecount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/elms/adminapproved">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
        <?php if (empty($getAll)): ?>
            <div class="text-center">
                <img src="public/img/icons/svgs/empty.svg" alt="">
                <h3 class="text-primary mb-3">មិនទាន់មានសំណើនៅឡើយ។</h3>
            </div>
        <?php else: ?>
            <div class="list-group list-group-flush list-group-hoverable mb-0">
                <?php foreach ($getAll as $key => $attendance): ?>
                    <a href="#" class="list-group-item text-decoration-none d-flex justify-content-between align-items-center"
                        data-bs-toggle="modal" data-bs-target="#attendanceModal<?= $key ?>">
                        <div class="d-flex align-items-center">
                            <img src="<?= $attendance['profile_picture'] ?>" class="avatar me-3" style="object-fit: cover;"
                                alt="Profile">
                            <div>
                                <h3 class="text-primary mb-0"><?= htmlspecialchars($attendance['khmer_name']) ?></h3>
                                <span>ស្នើនៅ : </span><small
                                    class="text-muted"><?= htmlspecialchars($attendance['date']) ?></small>
                            </div>
                        </div>
                        <span
                            class="badge <?= $attendance['type'] == 'latein' ? 'bg-danger-lt' : ($attendance['type'] == 'lateout' ? 'bg-purple-lt' : ($attendance['type'] == 'leaveearly' ? 'bg-lime-lt' : '')) ?>">
                            <?= $attendance['type'] == 'latein' ? 'ចូលយឺត' : ($attendance['type'] == 'lateout' ? 'ចេញយឺត' : 'ចេញមុន') ?>
                        </span>
                    </a>

                    <!-- Modal for Attendance Details -->
                    <div class="modal modal-blur fade" id="attendanceModal<?= $key ?>" tabindex="-1"
                        aria-labelledby="attendanceModalLabel<?= $key ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <div class="d-flex">
                                        <h5 class="modal-title text-primary mb-0" id="attendanceModalLabel<?= $key ?>">
                                            <?= htmlspecialchars($attendance['khmer_name']) ?>
                                        </h5>
                                        <span
                                            class="badge mx-2 <?= $attendance['type'] == 'latein' ? 'bg-danger-lt' : ($attendance['type'] == 'lateout' ? 'bg-purple-lt' : ($attendance['type'] == 'leaveearly' ? 'bg-lime-lt' : '')) ?>">
                                            <?= $attendance['type'] == 'latein' ? 'ចូលយឺត' : ($attendance['type'] == 'lateout' ? 'ចេញយឺត' : 'ចេញមុន') ?>
                                        </span>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-primary fw-bold mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-bell-ringing">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M10 5a2 2 0 0 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
                                                <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
                                                <path d="M21 6.727a11.05 11.05 0 0 0 -2.794 -3.727" />
                                                <path d="M3 6.727a11.05 11.05 0 0 1 2.792 -3.727" />
                                            </svg>
                                            ស្ថានភាព
                                        </span>
                                        <span
                                            class="badge <?= $attendance['status'] == 'Pending' ? 'bg-warning' : ($attendance['status'] == 'Approved' ? 'bg-success' : 'bg-danger') ?>">
                                            <?= htmlspecialchars($attendance['status']) ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-primary fw-bold mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
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
                                            កាលបរិច្ឆេទ
                                        </span>
                                        <span><?= htmlspecialchars($attendance['date']) ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-primary fw-bold mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            ម៉ោង
                                        </span>
                                        <span
                                            class="<?= $attendance['late_in'] ? 'text-danger' : ($attendance['late_out'] ? 'text-purple' : ($attendance['leave_early'] ? 'text-lime' : '')) ?>">
                                            <?= $attendance['late_in'] ?? $attendance['late_out'] ?? $attendance['leave_early'] ?? '—' ?>
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-primary fw-bold mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                <path d="M12 7v5l.5 .5" />
                                                <path
                                                    d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                <path d="M15 21v-6" />
                                            </svg>
                                            រយៈពេល
                                        </span>
                                        <span><?= htmlspecialchars($attendance['late']) . " នាទី" ?></span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-primary fw-bold mb-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path
                                                    d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                            មូលហេតុ
                                        </span>
                                        <span><?= htmlspecialchars($attendance['reasons']) ?: '—' ?></span>
                                    </li>
                                </ul>
                                <div class="modal-footer bg-light">
                                    <div class="w-100">
                                        <form action="/elms/actionLate" method="POST">
                                            <!-- Email -->
                                            <input type="hidden" name="uEmail"
                                                value="<?= htmlspecialchars($attendance['email']) ?>">

                                            <!-- Reason  -->
                                            <input type="hidden" name="reasons"
                                                value="<?= htmlspecialchars($attendance['reasons']) ?>">

                                            <!-- User ID -->
                                            <input type="hidden" name="uId" value="<?= htmlspecialchars($attendance['uId']) ?>">

                                            <!-- Late ID -->
                                            <input type="hidden" name="lateId"
                                                value="<?= htmlspecialchars($attendance['id']) ?>">

                                            <!-- Lateness Type -->
                                            <input type="hidden" name="latenessType"
                                                value="<?= htmlspecialchars($attendance['type']) ?>">

                                            <!-- Check-in time (if applicable) -->
                                            <input type="hidden" name="date"
                                                value="<?= htmlspecialchars($attendance['date'] ?? '') ?>">

                                            <!-- Late-in time -->
                                            <input type="hidden" name="lateIn"
                                                value="<?= htmlspecialchars($attendance['late_in'] ?? '') ?>">

                                            <!-- Late-out time -->
                                            <input type="hidden" name="lateOut"
                                                value="<?= htmlspecialchars($attendance['late_out'] ?? '') ?>">

                                            <!-- Leave-early time -->
                                            <input type="hidden" name="leaveEarly"
                                                value="<?= htmlspecialchars($attendance['leave_early'] ?? '') ?>">

                                            <!-- Updated At -->
                                            <input type="hidden" name="updatedAt"
                                                value="<?= htmlspecialchars($attendance['updated_at']) ?>">

                                            <div class="row">
                                                <!-- Approve Button -->
                                                <div class="col">
                                                    <button class="btn btn-outline-success w-100" type="submit" name="status"
                                                        value="Approved">
                                                        អនុម័ត
                                                    </button>
                                                </div>

                                                <!-- Reject Button -->
                                                <div class="col">
                                                    <button class="btn btn-outline-danger w-100" type="submit" name="status"
                                                        value="Rejected">
                                                        មិនអនុម័ត
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include('src/common/footer.php'); ?>