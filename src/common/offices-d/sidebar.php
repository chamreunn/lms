<?php
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
?>
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <!-- home  -->
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

                    <!-- my leave  -->
                    <li
                        class="nav-item <?= ($current_page == 'dofficeLeave' || $current_page == 'view-leave-detail') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/dofficeLeave">
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
                                ច្បាប់ឈប់សម្រាក
                            </span>
                        </a>
                    </li>

                    <!-- attendence  -->
                    <li class="nav-item <?= ($current_page == 'my-attendances') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/my-attendances">
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
                                ព័ត៌មានអំពីវត្តមាន
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

                    <!-- change location  -->
                    <!-- <li hidden -->
                    <li
                        class="nav-item dropdown <?= in_array($current_page, ['transferout', 'hold', 'resign', 'backwork', 'view&edit-hold', 'view&edit-transferout']) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-template">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M4 4m0 1a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z" />
                                    <path
                                        d="M4 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                    <path d="M14 12l6 0" />
                                    <path d="M14 16l6 0" />
                                    <path d="M14 20l6 0" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                លិខិតរដ្ឋបាល
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= in_array($current_page, ['hold', 'view&edit-hold']) ? 'active' : '' ?>"
                                        href="/elms/hold">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-pause">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                <path d="M17 17v5" />
                                                <path d="M21 17v5" />
                                            </svg>
                                        </span>
                                        លិខិតព្យួរ
                                    </a>
                                    <a class="dropdown-item <?= in_array($current_page, ['transferout', 'view&edit-transferout']) ? 'active' : '' ?>"
                                        href="/elms/transferout">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-share">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                                                <path d="M16 22l5 -5" />
                                                <path d="M21 21.5v-4.5h-4.5" />
                                            </svg>
                                        </span>
                                        លិខិតផ្ទេរចេញ
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'resign') ? 'active' : '' ?>"
                                        href="/elms/resign">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-user-cancel">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                <path d="M17 21l4 -4" />
                                            </svg>
                                        </span>
                                        លិខិតលាឈប់
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'backwork') ? 'active' : '' ?>"
                                        href="/elms/backwork">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                <path d="M16 19h6" />
                                                <path d="M19 16v6" />
                                            </svg>
                                        </span>
                                        លិខិតបន្តការងារ
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- All Request  -->
                    <li
                        class="nav-item dropdown <?= in_array($current_page, ['pending', 'approved', 'rejected', 'holdApproved', 'holdRejected']) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown"
                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-list-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M15 15m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                                    <path d="M18.5 18.5l2.5 2.5" />
                                    <path d="M4 6h16" />
                                    <path d="M4 12h4" />
                                    <path d="M4 18h4" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                គ្រប់គ្រងសំណើ
                                <?php if (!empty($totalPendingCount)): ?>
                                    <span class="badge bg-red text-red-fg ms-2"><?= $totalPendingCount; ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'pending') ? 'active' : '' ?>"
                                        href="/elms/pending">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-list-details">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M13 5h8" />
                                                <path d="M13 9h5" />
                                                <path d="M13 15h8" />
                                                <path d="M13 19h5" />
                                                <path
                                                    d="M3 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path
                                                    d="M3 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                            </svg>
                                        </span>
                                        <span>សំណើទាំងអស់</span>
                                        <?php if ($totalPendingCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $totalPendingCount; ?></span>
                                        <?php endif; ?>
                                    </a>

                                    <div class="dropdown-divider m-0"></div>

                                    <!-- leave   -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['approved', 'rejected']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
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
                                            <span>ច្បាប់ឈប់សម្រាក</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/approved"
                                                class="dropdown-item <?= in_array($current_page, ['approved']) ? 'active' : '' ?>">
                                                <span>បានអនុម័ត</span>
                                            </a>
                                            <a href="/elms/rejected"
                                                class="dropdown-item <?= in_array($current_page, ['rejected']) ? 'active' : '' ?>">
                                                <span>មិនអនុម័ត</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- hold  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['holdApproved', 'holdRejected']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-pause">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                    <path d="M17 17v5" />
                                                    <path d="M21 17v5" />
                                                </svg>
                                            </span>
                                            <span>លិខិតព្យួរ</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/holdApproved"
                                                class="dropdown-item <?= in_array($current_page, ['holdApproved']) ? 'active' : '' ?>">
                                                <span>បានអនុម័ត</span>
                                            </a>
                                            <a href="/elms/holdRejected"
                                                class="dropdown-item <?= in_array($current_page, ['holdRejected']) ? 'active' : '' ?>">
                                                <span>មិនអនុម័ត</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- transferout  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['transferApproved', 'transferRejected']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-share">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3" />
                                                    <path d="M16 22l5 -5" />
                                                    <path d="M21 21.5v-4.5h-4.5" />
                                                </svg>
                                            </span>
                                            <span>លិខិតផ្ទេរចេញ</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/transferApproved"
                                                class="dropdown-item <?= in_array($current_page, ['transferApproved']) ? 'active' : '' ?>">
                                                <span>បានអនុម័ត</span>
                                            </a>
                                            <a href="/elms/transferRejected"
                                                class="dropdown-item <?= in_array($current_page, ['transferRejected']) ? 'active' : '' ?>">
                                                <span>មិនអនុម័ត</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- resigned  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['resignApproved', 'resignRejected']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-cancel">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h3.5" />
                                                    <path d="M19 19m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                    <path d="M17 21l4 -4" />
                                                </svg>
                                            </span>
                                            <span>លិខិតលាឈប់</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/resignApproved"
                                                class="dropdown-item <?= in_array($current_page, ['resignApproved']) ? 'active' : '' ?>">
                                                <span>បានអនុម័ត</span>
                                            </a>
                                            <a href="/elms/resignRejected"
                                                class="dropdown-item <?= in_array($current_page, ['resignRejected']) ? 'active' : '' ?>">
                                                <span>មិនអនុម័ត</span>
                                            </a>
                                        </div>
                                    </div>

                                    <!-- backwork  -->
                                    <div class="dropend">
                                        <a class="dropdown-item dropdown-toggle <?= in_array($current_page, ['backApproved', 'backRejected']) ? 'active' : '' ?>"
                                            href="#sidebar-authentication" data-bs-toggle="dropdown"
                                            data-bs-auto-close="outside" role="button" aria-expanded="false">
                                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-users-plus">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M5 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                                    <path d="M3 21v-2a4 4 0 0 1 4 -4h4c.96 0 1.84 .338 2.53 .901" />
                                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                                    <path d="M16 19h6" />
                                                    <path d="M19 16v6" />
                                                </svg>
                                            </span>
                                            <span>លិខិតចូលបម្រើការងារវិញ</span>
                                        </a>
                                        <div class="dropdown-menu">
                                            <a href="/elms/backApproved"
                                                class="dropdown-item <?= in_array($current_page, ['backApproved']) ? 'active' : '' ?>">
                                                <span>បានអនុម័ត</span>
                                            </a>
                                            <a href="/elms/backRejected"
                                                class="dropdown-item <?= in_array($current_page, ['backRejected']) ? 'active' : '' ?>">
                                                <span>មិនអនុម័ត</span>
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </li>

                    <!-- calendar  -->
                    <li class="nav-item <?= ($current_page == 'leave-calendar') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/leave-calendar">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
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
                            </span>
                            <span class="nav-link-title">
                                ប្រតិទិន
                            </span>
                        </a>
                    </li>

                    <!-- optional  -->
                    <!-- late&mission request  -->
                    <?php if (isset($_SESSION['manageUserRequest']) && $_SESSION['manageUserRequest'] == '1'): ?>
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
                    <?php endif; ?>

                    <!-- manage system  -->
                    <?php if (isset($_SESSION['generalManage']) && $_SESSION['generalManage'] == '1'): ?>
                        <li
                            class="nav-item dropdown <?= in_array($current_page, ['user_index', 'department_index', 'office', 'leavetype', 'roles', 'positions', 'documents', 'holidays', 'ipaddress', 'allqr']) ? 'active' : '' ?>">
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
                                        <!-- leavetype  -->
                                        <a class="dropdown-item <?= ($current_page == 'leavetype') ? 'active' : '' ?>"
                                            href="/elms/leavetype">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
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
                                            <span class="mx-2">ប្រភេទច្បាប់</span>
                                        </a>
                                        <!-- holiday  -->
                                        <a class="dropdown-item <?= ($current_page == 'holidays') ? 'active' : '' ?>"
                                            href="/elms/holidays">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                                <path d="M16 3l0 4" />
                                                <path d="M8 3l0 4" />
                                                <path d="M4 11l16 0" />
                                                <path d="M8 15h2v2h-2z" />
                                            </svg>
                                            <span class="mx-2">ថ្ងៃឈប់សម្រាក</span>
                                        </a>
                                        <!-- ipaddress  -->
                                        <a class="dropdown-item <?= ($current_page == 'ipaddress') ? 'active' : '' ?>"
                                            href="/elms/ipaddress">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-currency-ripple">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M7 12m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                <path d="M17 7m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                <path d="M17 17m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                                <path d="M10 12h3l2 -2.5" />
                                                <path d="M15 14.5l-2 -2.5" />
                                            </svg>
                                            <span class="mx-2">IP Address</span>
                                        </a>
                                        <!-- qrcode  -->
                                        <a class="dropdown-item <?= ($current_page == 'allqr') ? 'active' : '' ?>"
                                            href="/elms/allqr">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-qrcode">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M7 17l0 .01" />
                                                <path
                                                    d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M7 7l0 .01" />
                                                <path
                                                    d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
                                                <path d="M17 7l0 .01" />
                                                <path d="M14 14l3 0" />
                                                <path d="M20 14l0 .01" />
                                                <path d="M14 14l0 3" />
                                                <path d="M14 20l3 0" />
                                                <path d="M17 17l3 0" />
                                                <path d="M20 17l0 3" />
                                            </svg>
                                            <span class="mx-2">QR Code</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>