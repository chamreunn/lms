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
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link" href="/elms/all-attendances">
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
                    <a class="nav-link text-primary fw-bold active" href="#">
                        យឺតថ្ងៃនេះ
                        <?php if (!empty($gettodaylatecount)): ?>
                            <div class="badge bg-red text-red-fg mx-2"><?= $gettodaylatecount ?></div>
                        <?php endif; ?>
                    </a>
                </li>
                <!-- <li class="nav-item">
                    <a class="nav-link" href="#">
                        ច្បាប់ថ្ងៃនេះ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        បេសកកម្មថ្ងៃនេះ
                    </a>
                </li> -->
            </ul>
        </div>
        <!-- Form for filtering attendance -->
        <div class="card-header">
            <form action="/elms/all-attendances" class="row w-100" method="GET">
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
                                <th><button class="table-sort" data-sort="sort-type">ម៉ោង</button></th>
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
                                            <img src="<?= htmlspecialchars($attendance['profile_picture']) ?>" class="avatar me-3"
                                                style="object-fit: cover;" alt="Profile">
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
                                    <td>
                                        <?php
                                        if ($attendance['type'] == 'latein') {
                                            echo htmlspecialchars($attendance['late_in']);
                                        } elseif ($attendance['type'] == 'lateout') {
                                            echo htmlspecialchars($attendance['late_out']);
                                        } else {
                                            echo htmlspecialchars($attendance['leave_early']);
                                        }
                                        ?>
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon">
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon">
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