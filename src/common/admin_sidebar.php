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
                    <li class="nav-item dropdown <?= ($current_page == 'pending' || $current_page == 'approved' || $current_page == 'rejected') ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-base" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <polyline points="12 3 20 7.5 20 16.5 12 21 4 16.5 4 7.5 12 3" />
                                    <line x1="12" y1="12" x2="20 7.5" />
                                    <line x1="12" y1="12" x2="12 21" />
                                    <line x1="12" y1="12" x2="4 7.5" />
                                    <line x1="16 5.25" x2="8 9.75" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                សំណើច្បាប់
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'pending') ? 'active' : '' ?>" href="/elms/pending">
                                        ច្បាប់រង់ចាំអនុម័ត
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'approved') ? 'active' : '' ?>" href="/elms/approved">
                                        ច្បាប់ដែលបានអនុម័ត
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'rejected') ? 'active' : '' ?>" href="/elms/rejected">
                                        ច្បាប់មិនអនុម័ត
                                    </a>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item <?= ($current_page == 'leave-requests') ? 'active' : '' ?>">
                        <a class="nav-link" href="/elms/leave-requests">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <polyline points="9 11 12 14 20 6" />
                                    <path d="M20 12v6a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h9" />
                                </svg>
                            </span>
                            <span class="nav-link-title">
                                ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ
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
                    <li class="nav-item dropdown <?= in_array($current_page, ['user_index', 'department', 'office', 'leavetype', 'roles', 'positions']) ? 'active' : '' ?>">
                        <a class="nav-link dropdown-toggle" href="#navbar-extra" data-bs-toggle="dropdown" data-bs-auto-close="outside" role="button" aria-expanded="false">
                            <span class="nav-link-icon d-md-none d-lg-inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-horizontal">
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
                        <div class="dropdown-menu">
                            <div class="dropdown-menu-columns">
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'user_index') ? 'active' : '' ?>" href="user_index">
                                        គណនីមន្ត្រី
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'department') ? 'active' : '' ?>" href="department">
                                        នាយកដ្ឋាន
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'office') ? 'active' : '' ?>" href="/elms/office">
                                        ការិយាល័យ
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'leavetype') ? 'active' : '' ?>" href="/elms/leavetype">
                                        ប្រភេទច្បាប់
                                    </a>
                                </div>
                                <div class="dropdown-menu-column">
                                    <a class="dropdown-item <?= ($current_page == 'roles') ? 'active' : '' ?>" href="/elms/roles">
                                        Role
                                    </a>
                                    <a class="dropdown-item <?= ($current_page == 'positions') ? 'active' : '' ?>" href="/elms/positions">
                                        តួនាទី
                                    </a>
                                </div>
                            </div>
                        </div>
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
