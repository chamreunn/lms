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
    <div class="row row-card">
        <div class="col-12 mb-3">
            <div class="row row-cards">
                <!-- All Leave Requests Card -->
                <div class="col-md-6 col-lg-3">
                    <a href="/elms/leaves" class="card">
                        <div class="card-status-top bg-green"></div>
                        <div class="card-body row">
                            <div class="col">
                                <h3 class="text-green font-weight-medium">
                                    ច្បាប់ឈប់សម្រាក
                                </h3>
                                <div class="text-muted font-weight-medium mb-0">
                                    <?= date('F-Y') ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <?= $getLeavesApproved ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- late in  -->
                <div class="col-md-6 col-lg-3">
                    <a href="/elms/latein" class="card">
                        <div class="card-status-top bg-primary"></div>
                        <div class="card-body row">
                            <div class="col">
                                <h3 class="text-primary font-weight-medium">
                                    សំណើចូលយឺត
                                </h3>
                                <span class="text-muted font-weight-medium mb-0">
                                    <?= date('F-Y') ?>
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <?= $getLatesInCount ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- late out  -->
                <div class="col-md-6 col-lg-3">
                    <a href="/elms/lateout" class="card card-sm">
                        <div class="card-status-top bg-warning"></div>
                        <div class="card-body row">
                            <div class="col">
                                <h3 class="text-warning font-weight-medium">
                                    សំណើចេញយឺត
                                </h3>
                                <span class="text-muted font-weight-medium mb-0">
                                    <?= date('F-Y') ?>
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <?= $getLatesOutCount ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- leave early  -->
                <div class="col-md-6 col-lg-3">
                    <a href="/elms/leaveearly" class="card card-sm">
                        <div class="card-status-top bg-red"></div>
                        <div class="card-body row">
                            <div class="col">
                                <h3 class="text-red font-weight-medium">
                                    សំណើចេញមុន
                                </h3>
                                <span class="text-muted font-weight-medium mb-0">
                                    <?= date('F-Y') ?>
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="bg-red text-white avatar">
                                    <?= $getLeavesEarlyCount ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- mission  -->
                <div class="col-md-6 col-lg-3">
                    <a href="/elms/missions" class="card card-sm">
                        <div class="card-status-top bg-purple"></div>
                        <div class="card-body row">
                            <div class="col">
                                <h3 class="text-purple font-weight-medium">
                                    បេសកកម្ម
                                </h3>
                                <span class="text-muted font-weight-medium mb-0">
                                    <?= date('F-Y') ?>
                                </span>
                            </div>
                            <div class="col-auto">
                                <span class="bg-purple text-white avatar">
                                    <?= $getMissions ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" href="/elms/admin-attendances">
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
                        បញ្ជីវត្តមានទាំងអស់
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        ថ្នាក់ដឹកនាំអង្គភាព
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        នាយកដ្ឋានកិច្ចការទូទៅ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        នាយកដ្ឋានសវនកម្មទី១
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        នាយកដ្ឋានសវនកម្មទី២
                    </a>
                </li>
            </ul>
        </div>
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
        <div class="table-responsive">
            <table class="table table-vcenter card-table table-striped">
                <thead>
                    <tr>
                        <th class="text-center">ល.រ</th>
                        <th class="text-center">ឈ្មោះគណនី</th>
                        <th class="text-center d-none d-md-table-cell">កាលបរិច្ឆេទ</th>
                        <th class="text-center d-none d-md-table-cell">ច្បាប់ឈប់សម្រាក</th>
                        <th class="text-center d-none d-lg-table-cell">បេសកម្ម</th> <!-- Show only on larger screens -->
                        <th class="text-center d-none d-md-table-cell">ម៉ោងចូល</th>
                        <th class="text-center d-none d-md-table-cell">ចូលយឺត</th> <!-- Hidden on small screens -->
                        <th class="text-center d-none d-md-table-cell">ម៉ោងចេញ</th>
                        <th class="text-center d-none d-md-table-cell">ចេញមុន</th> <!-- Hidden on small screens -->
                        <th class="text-center d-none d-lg-table-cell">ចេញយឺត</th> <!-- Show only on larger screens -->
                        <th class="text-center d-none d-md-table-cell">សរុប</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($userAttendances['data'])): ?>
                        <?php foreach ($userAttendances['data']['data'] as $key => $attendance): ?>
                            <tr>
                                <td class="text-center d-none d-md-table-cell"><?= $key + 1 ?></td>
                                <td class="text-center d-sm-block d-lg-none d-md-table-cell">
                                    <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseExample<?= $attendance['id'] ?>" aria-expanded="false"
                                        aria-controls="collapseExample">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-plus m-0">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                    </button>
                                </td>
                                <td class="mx-0">
                                    <div class="d-flex justify-content-start align-items-center mb-0">
                                        <img src="<?= 'https://hrms.iauoffsa.us/images/' . $attendance['image'] ?>"
                                            class="avatar" style="object-fit: cover;" alt="">
                                        <div class="d-flex flex-column mx-2 mb-0">
                                            <h3 class="mb-0 text-primary">
                                                <?= $attendance['lastNameKh'] . " " . $attendance['firstNameKh'] ?>
                                            </h3>
                                            <p class="text-muted mb-0"><?= $attendance['email'] ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center d-none d-md-table-cell"><?= $attendance['date'] ?></td>
                                <td class="text-center d-none d-md-table-cell"><?= $attendance['leave'] ?></td>
                                <td class="text-center d-none d-lg-table-cell"><?= $attendance['mission'] ?></td>
                                <!-- Hidden on small screens -->
                                <td
                                    class="text-center d-none d-md-table-cell <?= ($attendance['checkIn'] > '09:00:00') ? 'text-danger' : '' ?>">
                                    <?= $attendance['checkIn'] ?>
                                </td>
                                <td class="text-center d-none d-md-table-cell text-red"><?= $attendance['lateIn'] ?></td>
                                <!-- Hidden on small screens -->
                                <td
                                    class="text-center d-none d-md-table-cell <?= ($attendance['checkOut'] < '16:00:00' || $attendance['checkOut'] > '17:30:00') ? 'text-danger' : '' ?>">
                                    <?= $attendance['checkOut'] ?>
                                </td>
                                <td class="text-center d-none d-md-table-cell text-red"><?= $attendance['exitFirst'] ?></td>
                                <!-- Hidden on small screens -->
                                <td class="text-center d-none d-lg-table-cell text-red"><?= $attendance['lateOut'] ?></td>
                                <!-- Hidden on small screens -->
                                <td class="text-center d-none d-md-table-cell"><?= $attendance['total'] ?></td>
                            </tr>
                            <tr class="d-sm-none">
                                <td colspan="7" class="p-0">
                                    <div class="collapse" id="collapseExample<?= $attendance['id'] ?>" style="">
                                        <table class="table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">កាលបរិច្ឆេទ : </strong>
                                                        <div><?= $attendance['date'] ?? "_" ?></div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ច្បាប់ឈប់សម្រាក : </strong>
                                                        <span> <?= $attendance['leave'] ?? "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">បេសកម្ម : </strong>
                                                        <span><?= $attendance['mission'] ?? "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ម៉ោងចូល : </strong>
                                                        <span
                                                            class="text-center <?= ($attendance['checkIn'] > '09:00:00') ? 'text-danger' : '' ?>"><?= isset($attendance['checkIn']) ? $attendance['checkIn'] . " នាទី" : "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ចូលយឺត : </strong>
                                                        <span
                                                            class="text-red"><?= isset($attendance['lateIn']) ? $attendance['lateIn'] . " នាទី" : "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ម៉ោងចេញ : </strong>
                                                        <span
                                                            class="text-center <?= ($attendance['checkOut'] < '16:00:00' || $attendance['checkOut'] > '17:30:00') ? 'text-danger' : '' ?>"><?= isset($attendance['checkOut']) ? $attendance['checkOut'] . " នាទី" : "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ចេញយឺត : </strong>
                                                        <span
                                                            class="text-red"><?= isset($attendance['lateOut']) ? $attendance['lateOut'] . " នាទី" : "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">ចេញមុន : </strong>
                                                        <span
                                                            class="text-red"><?= isset($attendance['exitFirst']) ? $attendance['exitFirst'] . " នាទី" : "_" ?></span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="d-flex justify-content-between">
                                                        <strong class="text-primary">សរុប : </strong>
                                                        <span><?= $attendance['total'] ?? "_" ?></span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
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
</div>
<?php include('src/common/footer.php'); ?>
<!-- khmer number  -->
<script>
    function convertToKhmerNumerals(num) {
        const khmerNumerals = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
        return num.toString().split('').map(digit => khmerNumerals[digit]).join('');
    }

    function updateDateTime() {
        const clockElement = document.getElementById('real-time-clock');
        const currentTime = new Date();

        // Define Khmer arrays for days of the week and months.
        const daysOfWeek = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
        const dayOfWeek = daysOfWeek[currentTime.getDay()];

        const months = ['មករា', 'កុម្ភៈ', 'មិនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        const month = months[currentTime.getMonth()];

        const day = convertToKhmerNumerals(currentTime.getDate());
        const year = convertToKhmerNumerals(currentTime.getFullYear());

        // Calculate and format hours, minutes, seconds, and time of day in Khmer.
        let hours = currentTime.getHours();
        let period;

        if (hours >= 5 && hours < 12) {
            period = 'ព្រឹក'; // Khmer for AM (morning)
        } else if (hours >= 12 && hours < 17) {
            period = 'រសៀល'; // Khmer for afternoon
        } else if (hours >= 17 && hours < 20) {
            period = 'ល្ងាច'; // Khmer for evening
        } else {
            period = 'យប់'; // Khmer for night
        }

        hours = hours % 12 || 12;
        const khmerHours = convertToKhmerNumerals(hours);
        const khmerMinutes = convertToKhmerNumerals(currentTime.getMinutes().toString().padStart(2, '0'));
        const khmerSeconds = convertToKhmerNumerals(currentTime.getSeconds().toString().padStart(2, '0'));

        // Construct the date and time string in the desired Khmer format.
        const dateTimeString = `${dayOfWeek}, ${day} ${month} ${year} ${khmerHours}:${khmerMinutes}:${khmerSeconds} ${period}`;
        clockElement.textContent = dateTimeString;
    }

    // Update the date and time every second (1000 milliseconds).
    setInterval(updateDateTime, 1000);

    // Initial update.
    updateDateTime();
</script>