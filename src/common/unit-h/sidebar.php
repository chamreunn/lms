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
                        class="nav-item <?= ($current_page == 'hunitLeave' || $current_page == 'view-leave-detail') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/hunitLeave">
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
                    <li
                        class="nav-item dropdown <?= ($current_page == 'hunitpending' || $current_page == 'hunitapproved' || $current_page == 'hunitrejected') ? 'active' : '' ?>">
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
                                គ្រប់គ្រងសំណើ
                                <?php if (!empty($requestscount)): ?>
                                    <span class="badge bg-red text-red-fg ms-auto"><?= $requestscount; ?></span>
                                <?php endif; ?>
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'hunitpending') ? 'active' : '' ?>"
                                        href="/elms/hunitpending">
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
                                        កំពុងរង់ចាំ
                                        <?php if (!empty($requestscount)): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $requestscount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'hunitapproved') ? 'active' : '' ?>"
                                        href="/elms/hunitapproved">
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
                                        បានអនុម័ត
                                        <?php if ($approvedCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $approvedCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'hunitrejected') ? 'active' : '' ?>"
                                        href="/elms/hunitrejected">
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
                                        មិនអនុម័ត
                                        <?php if ($rejectedCount > 0): ?>
                                            <span class="badge bg-red text-red-fg ms-auto"><?= $rejectedCount; ?></span>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
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
                </ul>
            </div>
        </div>
    </div>
</div>