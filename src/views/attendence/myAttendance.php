<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

$title = "បញ្ជីវត្តមាន";
include('src/common/header.php');
?>

<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    ទំព័រដើម
                </div>
                <h2 class="page-title"><?= $title ?> </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <!-- Example single danger button -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle d-none d-sm-inline-block"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                បង្កើតសំណើ
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-late-in"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436"></path>
                                                <path d="M19 22v-6"></path>
                                                <path d="M22 19l-3 -3l-3 3"></path>
                                                <path d="M12 7v5l2.5 2.5"></path>
                                            </svg>
                                        </span>
                                        សំណើចូលយឺត
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-late-out"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45"></path>
                                                <path d="M16 19h6"></path>
                                                <path d="M19 16v6"></path>
                                                <path d="M12 7v5l3 3"></path>
                                            </svg>
                                        </span>
                                        សំណើចេញយឺត
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-leaveearly"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984"></path>
                                                <path d="M16 22l5 -5"></path>
                                                <path d="M21 21.5v-4.5h-4.5"></path>
                                                <path d="M12 7v5l2 2"></path>
                                            </svg>
                                        </span>
                                        សំណើចេញមុន
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#mission" data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4">
                                                </path>
                                                <path d="M16 3v4"></path>
                                                <path d="M8 3v4"></path>
                                                <path d="M4 11h16"></path>
                                                <path d="M19 22v.01"></path>
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483">
                                                </path>
                                            </svg>
                                        </span>
                                        សំណើបេសកកម្ម
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary d-sm-none btn-icon me-0"
                                data-bs-toggle="dropdown" aria-expanded="true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-late-in"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-up">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.983 12.548a9 9 0 1 0 -8.45 8.436"></path>
                                                <path d="M19 22v-6"></path>
                                                <path d="M22 19l-3 -3l-3 3"></path>
                                                <path d="M12 7v5l2.5 2.5"></path>
                                            </svg>
                                        </span>
                                        សំណើចូលយឺត
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-late-out"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-plus">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.984 12.535a9 9 0 1 0 -8.468 8.45"></path>
                                                <path d="M16 19h6"></path>
                                                <path d="M19 16v6"></path>
                                                <path d="M12 7v5l3 3"></path>
                                            </svg>
                                        </span>
                                        សំណើចេញយឺត
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#apply-leaveearly"
                                        data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-share">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M20.943 13.016a9 9 0 1 0 -8.915 7.984"></path>
                                                <path d="M16 22l5 -5"></path>
                                                <path d="M21 21.5v-4.5h-4.5"></path>
                                                <path d="M12 7v5l2 2"></path>
                                            </svg>
                                        </span>
                                        សំណើចេញមុន
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" data-bs-target="#mission" data-bs-toggle="modal">
                                        <span class="nav-link-icon d-md-none d-lg-inline-block">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path
                                                    d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4">
                                                </path>
                                                <path d="M16 3v4"></path>
                                                <path d="M8 3v4"></path>
                                                <path d="M4 11h16"></path>
                                                <path d="M19 22v.01"></path>
                                                <path
                                                    d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483">
                                                </path>
                                            </svg>
                                        </span>
                                        សំណើបេសកកម្ម
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="card">
        <!-- Form for filtering attendance -->
        <div class="card-header">
            <form action="/elms/my-attendances" class="row w-100" method="GET">
                <!-- Start Date -->
                <div class="col-lg-5">
                    <div class="input-icon">
                        <span class="input-icon-addon">
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
                        <input class="form-control date-picker" name="fromDate" placeholder="កាលបរិច្ឆេទ"
                            autocomplete="off">
                    </div>
                </div>

                <!-- End Date -->
                <div class="col-lg-5">
                    <div class="input-icon">
                        <span class="input-icon-addon">
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
                        <input class="form-control date-picker" name="toDate" placeholder="កាលបរិច្ឆេទ"
                            autocomplete="off">
                    </div>
                </div>

                <!-- Search Button -->
                <div class="col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                            <path d="M21 21l-6 -6"></path>
                        </svg>
                        ស្វែងរក
                    </button>
                </div>
            </form>
        </div>

        <!-- Attendance Table -->
        <table class="table table-responsive table-hover mb-0">
            <thead>
                <tr>
                    <th>ល.រ</th>
                    <th>កាលបរិច្ឆេទ</th>
                    <th>ច្បាប់ឈប់សម្រាក</th>
                    <th class="d-none d-md-table-cell">បេសកម្ម</th>
                    <th>ម៉ោងចូល</th>
                    <th class="d-none d-md-table-cell">ចូលយឺត</th>
                    <th>ម៉ោងចេញ</th>
                    <th class="d-none d-md-table-cell">ចេញមុន</th>
                    <th class="d-none d-md-table-cell">ចេញយឺត</th>
                    <th>សរុប</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($userAttendances['data'])): ?>
                    <?php foreach ($userAttendances['data'] as $key => $attendance): ?>
                        <tr>
                            <td><?= $key + 1 ?></td>
                            <td><?= $attendance['date'] ?></td>
                            <td><?= $attendance['leave'] ?></td>
                            <td class="d-none d-md-table-cell"><?= $attendance['mission'] ?></td>
                            <td class="<?= ($attendance['checkIn'] > '09:00:00') ? 'text-danger' : '' ?>">
                                <?= $attendance['checkIn'] ?>
                            </td>
                            <td class="d-none d-md-table-cell text-red"><?= $attendance['lateIn'] ?></td>
                            <td
                                class="<?= ($attendance['checkOut'] < '16:00:00' || $attendance['checkOut'] > '17:30:00') ? 'text-danger' : '' ?>">
                                <?= $attendance['checkOut'] ?>
                            </td>
                            <td class="d-none d-md-table-cell text-red"><?= $attendance['exitFirst'] ?></td>
                            <td class="d-none d-md-table-cell"><?= $attendance['lateOut'] ?></td>
                            <td><?= $attendance['total'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">មិនមានទិន្នន័យ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include('src/common/footer.php'); ?>