<?php $current_page = basename($_SERVER['REQUEST_URI']); ?>
<div class="navbar-expand-md">
    <div class="collapse navbar-collapse" id="navbar-menu">
        <div class="navbar navbar-light">
            <div class="container-xl">
                <ul class="navbar-nav">
                    <li class="nav-item <?= ($current_page == 'dashboard') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/dashboard">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
                    <li class="nav-item dropdown <?= ($current_page == 'apply-leave' || $current_page == 'approved' || $current_page == 'rejected') ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-text-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M19 10h-14" />
                                    <path d="M5 6h14" />
                                    <path d="M14 14h-9" />
                                    <path d="M5 18h6" />
                                    <path d="M18 15v6" />
                                    <path d="M15 18h6" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                បង្កើតសំណើ
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'apply-leave') ? 'active' : '' ?>" href="/elms/apply-leave">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                                                <path d="M16 3v4" />
                                                <path d="M8 3v4" />
                                                <path d="M4 11h16" />
                                                <path d="M19 22v.01" />
                                                <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        ច្បាប់ឈប់សម្រាក
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'approved') ? 'active' : '' ?>" href="/elms/approved">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                                <path d="M12 7v5l2 2" />
                                                <path d="M19 22v.01" />
                                                <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                            </svg>
                                        </span>
                                        លិខិតចេញ​ ចូលយឺត
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'rejected') ? 'active' : '' ?>" href="/elms/rejected">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                                        បេសកកម្ម
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item <?= ($current_page == 'leave-requests' || $current_page == 'view-leave-detail') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/leave-requests">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
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
                    <li class="nav-item <?= ($current_page == 'overtimes') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/overtimes">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                    <path d="M12 7v5l2 2" />
                                    <path d="M19 22v.01" />
                                    <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                លិខិតចេញ​​ និងចូលយឺត
                            </span>
                        </a>
                    </li>
                    <li class="nav-item <?= ($current_page == 'missions') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/missions">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
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
                            <span class="nav-link-title">
                                បេសកកម្ម
                            </span>
                        </a>
                    </li>
                    <li class="nav-item <?= ($current_page == 'leave-calendar') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/leave-calendar">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
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
                            </span>
                            <span class="nav-link-title">
                                ប្រតិទិន
                            </span>
                        </a>
                    </li>
                </ul>
                <div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                    <form action="./" method="get" autocomplete="off" novalidate>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <circle cx="10" cy="10" r="7" />
                                    <line x1="21" y1="21" x2="15" y2="15" />
                                </svg>
                            </span>
                            <input type="text" value="" class="form-control" placeholder="Search…" aria-label="Search in website">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>