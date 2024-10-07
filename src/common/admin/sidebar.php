<?php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/dashboard">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <polyline points="5 12 3 12 12 3 21 12 19 12" />
                                    <path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" />
                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                ទំព័រដើម
                            </span>
                        </a>
                    </li>
                    <li
                        class="nav-item <?= ($current_page == 'adminLeave' || $current_page == 'view-leave-detail') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/adminLeave">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M19 22v.01" />
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ
                            </span>
                        </a>
                    </li>
                    <!-- attendence  -->
                    <li class="nav-item <?= ($current_page == 'admin-attendances') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/admin-attendances">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M19 22v.01" />
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                បញ្ជីវត្តមាន
                            </span>
                        </a>
                    </li>
                    <!-- late & mission  -->
                    <li
                        class="nav-item dropdown <?= ($current_page == 'overtimein' || $current_page == 'overtimeout' || $current_page == 'leaveearly' || $current_page == 'mission') ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-category">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4 4h6v6h-6z" />
                                    <path d="M14 4h6v6h-6z" />
                                    <path d="M4 14h6v6h-6z" />
                                    <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                ការចេញចូលយឺត
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'overtimein') ? 'active' : '' ?>"
                                        href="/elms/overtimein">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436" />
                                                <path d="M19 22v-6" />
                                                <path d="M22 19l-3 -3l-3 3" />
                                                <path d="M12 7v5l2.5 2.5" />
                                            </svg>
                                        </span>
                                        ចូលយឺត
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'overtimeout') ? 'active' : '' ?>"
                                        href="/elms/overtimeout">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                        </span>
                                        ចេញយឺត
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'leaveearly') ? 'active' : '' ?>"
                                        href="/elms/leaveearly">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984" />
                                                <path d="M16 22l5 -5" />
                                                <path d="M21 21.5v-4.5h-4.5" />
                                                <path d="M12 7v5l2 2" />
                                            </svg>
                                        </span>
                                        ចេញមុន
                                    </a>
                                    <div class="dropdown-divider m-0"></div>
                                    <a class="dropdown-item <?= ($current_page == 'mission') ? 'active' : '' ?>"
                                        href="/elms/mission">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M19 22v.01" />
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        បេសកកម្ម
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- late&mission request  -->
                    <li
                        class="nav-item dropdown <?= in_array($current_page, ['adminall', 'adminpending', 'adminapproved', 'adminrejected', 'admintodaylate', 'adminmissions', 'admintodaymissions', 'adminleaves', 'adminleavetoday']) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-bell-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M13.5 17h-9.5a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6a2 2 0 1 1 4 0a7 7 0 0 1 4 6" />
                                    <path d="M9 17v1a3 3 0 0 0 5.914 .716" />
                                    <path d="M19 22v.01" />
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                គ្រប់គ្រងសំណើ
                                <?php if (!empty($getPendingCount)): ?>
                                    <span class="badge bg-red text-red-fg ms-2"><?= $getPendingCount; ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <!-- all requests  -->
                                    <a class="dropdown-item <?= in_array($current_page, ['adminpending', 'adminapproved', 'adminrejected', 'admintodaylate']) ? 'active' : '' ?>"
                                        href="/elms/adminpending">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M19 22v.01" />
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        <span class="me-2">សំណើយឺតទាំងអស់</span>
                                        <?php if ($getPendingCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $getPendingCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <div class="dropdown-divider m-0"></div>
                                    <!-- missions  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['adminmissions', 'admintodaymissions']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-share">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v6" />
                                                    <path d="M16 3v4" />
                                                    <path d="M8 3v4" />
                                                    <path d="M4 11h16" />
                                                    <path d="M16 22l5 -5" />
                                                    <path d="M21 21.5v-4.5h-4.5" />
                                                </svg>
                                            </span>
                                            <span>បេសកកម្ម</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/adminmissions"
                                                class="dropdown-item <?= in_array($current_page, ['adminmissions']) ? 'active' : '' ?>">
                                                បេសកកម្មទាំងអស់
                                            </a>
                                            <a href="/elms/admintodaymissions"
                                                class="dropdown-item <?= in_array($current_page, ['admintodaymissions']) ? 'active' : '' ?>">
                                                បេសកកម្មថ្ងៃនេះ
                                                <?php if (!empty($getAllMissionCount)): ?>
                                                    <span
                                                        class="badge bg-red text-red-fg ms-auto"><?= $getAllMissionCount ?></span>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </div>
                                    <!-- leave  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['adminleaves', 'adminleavetoday']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                    <path d="M16 3l0 4" />
                                                    <path d="M8 3l0 4" />
                                                    <path d="M4 11l16 0" />
                                                    <path d="M8 15h2v2h-2z" />
                                                </svg>
                                            </span>
                                            <span>ច្បាប់ឈប់សម្រាក</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/adminleaves"
                                                class="dropdown-item <?= in_array($current_page, ['adminleaves']) ? 'active' : '' ?>">
                                                ច្បាប់ឈប់សម្រាកទាំងអស់
                                            </a>
                                            <a href="/elms/adminleavetoday"
                                                class="dropdown-item <?= in_array($current_page, ['adminleavetoday']) ? 'active' : '' ?>">
                                                <span class="me-2">ច្បាប់ឈប់សម្រាកថ្ងៃនេះ</span>
                                                <?php if (!empty($getLeaveTodayCount)): ?>
                                                    <span
                                                        class="badge bg-red text-red-fg ms-auto"><?= $getLeaveTodayCount ?></span>
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- leave request -->
                    <li
                        class="nav-item dropdown <?= ($current_page == 'pending' || $current_page == 'approved' || $current_page == 'rejected') ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-mail-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 19h-10a2 2 0 0 1 -2 -2v-10a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v4.5" />
                                    <path d="M19 22v.01" />
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                    <path d="M3 7l9 6l9 -6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                សំណើច្បាប់ឈប់សម្រាក
                                <?php if (!empty($requestscount)): ?>
                                    <span class="badge bg-red text-red-fg ms-2"><?= $requestscount; ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column" style="width: 200px;">
                                    <a class="dropdown-item <?= ($current_page == 'pending') ? 'active' : '' ?>"
                                        href="/elms/pending">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M19 22v.01" />
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        <span>កំពុងរង់ចាំ</span>
                                        <?php if ($requestscount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $requestscount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'approved') ? 'active' : '' ?>"
                                        href="/elms/approved">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M19 22v.01" />
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        <span>បានអនុម័ត</span>
                                        <?php if ($approvedCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $approvedCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'rejected') ? 'active' : '' ?>"
                                        href="/elms/rejected">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                                <path d="M12 7v5l2 2" />
                                                <path d="M19 22v.01" />
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        <span>មិនអនុម័ត</span>
                                        <?php if ($rejectedCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $rejectedCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <!-- request  -->
                    <li
                        class="nav-item dropdown <?= in_array($current_page, ['user_index', 'department_index', 'office', 'leavetype', 'roles', 'positions', 'documents', 'holidays']) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-horizontal">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M14 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M4 6l8 0" />
                                    <path d="M16 6l4 0" />
                                    <path d="M8 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M4 12l2 0" />
                                    <path d="M10 12l10 0" />
                                    <path d="M17 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M4 18l11 0" />
                                    <path d="M19 18l1 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                គ្រប់គ្រង
                            </span>
                        </a>
                        <!-- manage  -->
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'user_index') ? 'active' : '' ?>"
                                        href="user_index">
                                        គណនីមន្ត្រី
                                    </a>
                                    <!-- <a class="dropdown-item <?= ($current_page == 'department_index') ? 'active' : '' ?>"
                                        href="/elms/department">
                                        នាយកដ្ឋាន
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'office') ? 'active' : '' ?>"
                                        href="/elms/office">
                                        ការិយាល័យ
                                    </a> -->
                                    <a class="dropdown-item <?= ($current_page == 'leavetype') ? 'active' : '' ?>"
                                        href="/elms/leavetype">
                                        ប្រភេទច្បាប់
                                    </a>
                                </div>
                                <div class="dropdown-menu-column">
                                    <!-- <a class="dropdown-item <?= ($current_page == 'roles') ? 'active' : '' ?>"
                                        href="/elms/roles">
                                        Role
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'positions') ? 'active' : '' ?>"
                                        href="/elms/positions">
                                        តួនាទី
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'documents') ? 'active' : '' ?>"
                                        href="/elms/documents">
                                        លិខិត
                                    </a> -->
                                    <a class="dropdown-item <?= ($current_page == 'holidays') ? 'active' : '' ?>"
                                        href="/elms/holidays">
                                        ថ្ងៃឈប់សម្រាក
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>