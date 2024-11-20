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
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-center mb-3">
                <div class="col-auto">
                    <img src="<?= $_SESSION['user_profile'] ?? 'no image' ?>" alt="" style="object-fit: cover;"
                        class="avatar avatar-lg">
                </div>
                <div class="col-auto px-3">
                    <h2><?= $_SESSION['user_khmer_name'] ?? 'No Name' ?></h2>
                    <div class="text-muted">
                        <span class="badge <?= $_SESSION['position_color'] ?>"><?= $_SESSION['position'] ?></span>
                    </div>
                </div>
                <div class="col-auto hour ms-auto">
                    <h1 class="fw-bolder text-primary font-medium"><?= date('D,d-m-Y') ?>
                    </h1>
                </div>
            </div>

            <div class="col-12">
                <div class="row row-cards">
                    <div class="col-sm-6 col-lg-6">
                        <div class="card card-sm bg-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-primary-lt text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-login">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                                <path d="M21 12h-13l3 -3" />
                                                <path d="M11 15l-3 -3" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bolder font-weight-medium 
                                            <?php
                                            if (isset($attendances['attendance']['checkIn']) && $attendances['attendance']['checkIn'] > '09:00:00') {
                                                echo 'text-danger'; // Red for late check-in
                                            }
                                            ?>">
                                            <?= $attendances['attendance']['checkIn'] ?? '--:--:--' ?>
                                        </div>
                                        <div class="text-secondary">
                                            ម៉ោងចូល
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-6">
                        <div class="card card-sm bg-light">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-auto">
                                        <span
                                            class="bg-red-lt text-white avatar"><!-- Download SVG icon from http://tabler-icons.io/i/shopping-cart -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path
                                                    d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                                                <path d="M9 12h12l-3 -3" />
                                                <path d="M18 15l3 -3" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="fw-bolder font-weight-medium 
                                            <?php
                                            if (isset($attendances['attendance']['checkOut']) && $attendances['attendance']['checkOut'] > '17:30:00') {
                                                echo 'text-danger'; // Red for late check-in
                                            }
                                            ?>">
                                            <?= $attendances['attendance']['checkOut'] ?? '--:--:--' ?>
                                        </div>
                                        <div class="text-secondary">
                                            ម៉ោងចេញ
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">វត្តមានទាំងអស់</h3>
        </div>
        <div class="card-body">
            <div class="col-12">
                <div class="row row-cards">
                    <?php if (empty($fullAttendances['data'])): ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p>មិនមានទិន្នន័យ</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($fullAttendances['data'] as $attendance): ?>
                            <div class="col-sm-3 col-lg-3">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row justify-content-between align-items-center mb-3">
                                            <div class="col-auto">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                    <path d="M12 7v5l3 3" />
                                                </svg>
                                                <span><?= $attendance['date'] ?? '--:--:--' ?></span>
                                            </div>
                                            <div class="col-auto">
                                                <div class="badge 
                                                    <?php
                                                    if (!empty($attendance['lateIn'])) {
                                                        echo 'bg-danger'; // Red for Late Check-In
                                                    } elseif (!empty($attendance['lateOut'])) {
                                                        echo 'bg-warning'; // Yellow for Late Check-Out
                                                    } else {
                                                        echo 'bg-primary'; // Blue for On Time
                                                    }
                                                    ?>">
                                                    <?php
                                                    if (!empty($attendance['lateIn'])) {
                                                        echo $attendance['lateIn']; // Display Late Check-In
                                                    } elseif (!empty($attendance['lateOut'])) {
                                                        echo $attendance['lateOut']; // Display Late Check-Out
                                                    } else {
                                                        echo 'On Time'; // Default Text
                                                    }
                                                    ?>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col text-center">
                                                <p class="text-muted">ម៉ោងចូល</p>
                                                <strong class="<?php
                                                if (isset($attendance['checkIn']) && $attendance['checkIn'] > '09:00:00') {
                                                    echo 'text-danger'; // Red for late check-out
                                                } ?>">
                                                    <?= $attendance['checkIn'] ?? '--:--:--' ?>
                                                </strong>
                                            </div>
                                            <div class="col text-center">
                                                <p class="text-muted">ម៉ោងចេញ</p>
                                                <strong class="<?php
                                                if (isset($attendance['checkOut']) && $attendance['checkOut'] > '17:30:00') {
                                                    echo 'text-danger'; // Red for late check-out
                                                } ?>">
                                                    <?= $attendance['checkOut'] ?? '--:--:--' ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <!-- old one  -->
</div>
<?php include('src/common/footer.php'); ?>