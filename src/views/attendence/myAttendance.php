<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}
$pretitle = "ទំព័រដើម";
$title = "បញ្ជីវត្តមាន";
include('src/common/header.php');
?>
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

        <?php if (!empty($todayAttendance)): ?>
            <div class="col-12">
                <div class="row-cards">
                    <?php
                    // Assuming $todayAttendance[0] is the record you're dealing with
                    $attendance = $todayAttendance[0]; // Access the first attendance record
                    if ($attendance['leave'] !== '1' && $attendance['mission'] !== '1'):
                        ?>
                        <div class="row g-3">
                            <!-- late in  -->
                            <div class="col-lg-4 col-sm-12 col-md-4">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary-lt text-white avatar">
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
                                                    if (isset($attendance['checkIn']) && $attendance['checkIn'] > '09:00:00') {
                                                        echo 'text-danger'; // Red for late check-in
                                                    }
                                                    ?>">
                                                    <?= $attendance['checkIn'] ?? '--:--:--' ?>
                                                </div>
                                                <div class="text-secondary">
                                                    ម៉ោងចូល
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- late out  -->
                            <div class="col-lg-4 col-sm-12 col-md-4">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-red-lt text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M9 12h12l-3 -3" />
                                                        <path d="M18 15l3 -3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="fw-bolder font-weight-medium 
                                        <?php
                                        if (isset($attendance['checkOut'])) {
                                            if ($attendance['checkOut'] > '17:30:00') {
                                                echo 'text-danger'; // Red for late check-out
                                            } elseif ($attendance['checkOut'] < '16:00:00') {
                                                echo 'text-info'; // Blue for early check-out
                                            }
                                        }
                                        ?>">
                                                    <?= $attendance['checkOut'] ?? '--:--:--' ?>
                                                </div>
                                                <div class="text-secondary">
                                                    ម៉ោងចេញ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- total  -->
                            <div class="col-lg-4 col-sm-12 col-md-4">
                                <div class="card card-sm bg-light">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-red-lt text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 -2v-2" />
                                                        <path d="M9 12h12l-3 -3" />
                                                        <path d="M18 15l3 -3" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="fw-bolder font-weight-medium 
                                        <?php
                                        if (isset($attendance['total'])) {
                                            // Convert 'total' to DateTime for comparison
                                            $totalTime = new DateTime($attendance['total']);
                                            $minimumTime = new DateTime('08:00:00'); // 8 hours
                                
                                            if ($totalTime < $minimumTime) {
                                                echo 'text-danger'; // Red for less than 8 hours
                                            } elseif ($totalTime < new DateTime('16:00:00')) {
                                                echo 'text-info'; // Blue for early check-out
                                            } else {
                                                echo ''; // No class for valid times
                                            }
                                        }
                                        ?>">
                                                    <?= $attendance['total'] ?? '--:--:--' ?>
                                                </div>
                                                <div class="text-secondary">
                                                    ម៉ោងសរុបថ្ងៃនេះ
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($attendance['leave'] == '1'): ?>
                        <div class="col-sm-12 col-lg-12">
                            <div class="card card-sm bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <h1 class="text-danger fw-bolder">
                                                ច្បាប់ឈប់សម្រាក
                                            </h1>
                                            <p class="text-muted mb-0">អ្នកបានដាក់ច្បាប់ឈប់សម្រាកសម្រាប់ថ្ងៃនេះ។</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($attendance['mission'] == '1'): ?>
                        <div class="col-sm-12 col-lg-12">
                            <div class="card card-sm bg-light">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col text-center">
                                            <h1 class="text-danger fw-bolder">
                                                បេសកកម្ម
                                            </h1>
                                            <p class="text-muted mb-0">អ្នកមានបេសកកម្មថ្ងៃនេះ។</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="mb-0">វត្តមានទាំងអស់</h3>
    </div>
    <div class="card-body">
        <div class="col-12">
            <div class="row row-cards">
                <?php if (empty($pagedData) || !is_array($pagedData)): ?>
                    <div class="text-center">
                        <img src="public/img/icons/svgs/empty.svg" alt="">
                        <p>មិនមានទិន្នន័យ</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($pagedData as $attendance): ?>
                        <div class="col-sm-3 col-lg-3">
                            <div class="card card-sm bg-light">
                                <div class="card-body">
                                    <!-- Date Display -->
                                    <div class="row justify-content-between align-items-center mb-3">
                                        <div class="col-auto">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                <path d="M12 7v5l3 3" />
                                            </svg>
                                            <span><?= $attendance['date'] ?? '--:--:--' ?></span>
                                        </div>
                                        <div class="col-auto">
                                            <!-- Badge for Attendance Status -->
                                            <div class="badge 
                                                        <?php
                                                        if (!empty($attendance['lateIn'])) {
                                                            echo 'bg-yellow'; // Yellow for Late Check-In
                                                        } elseif (!empty($attendance['lateOut'])) {
                                                            echo 'bg-yellow'; // Yellow for Late Check-Out
                                                        } elseif (!empty($attendance['leaveEarly'])) {
                                                            echo 'bg-orange'; // Orange for Leave Early
                                                        } elseif (!empty($attendance['status']) && $attendance['status'] === 'ច្បាប់') {
                                                            echo 'bg-danger'; // Red for Leave
                                                        } elseif (!empty($attendance['status']) && $attendance['status'] === 'បេសកកម្ម') {
                                                            echo 'bg-success'; // Green for Mission
                                                        } else {
                                                            echo 'bg-primary'; // Default
                                                        }
                                                        ?>">
                                                <?=
                                                    $attendance['lateIn'] ??
                                                    $attendance['lateOut'] ??
                                                    $attendance['leaveEarly'] ??
                                                    $attendance['status'] ??
                                                    'ទាន់ពេល';
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Check-In and Check-Out Time -->
                                    <div class="row">
                                        <div class="col text-center">
                                            <p class="text-muted">ម៉ោងចូល</p>
                                            <strong class="<?php
                                            if (isset($attendance['checkIn']) && strtotime($attendance['checkIn']) > strtotime('09:00:00')) {
                                                echo 'text-danger'; // Red for Late Check-In
                                            }
                                            ?>">
                                                <?= $attendance['checkIn'] ?? '--:--:--' ?>
                                            </strong>
                                        </div>
                                        <div class="col text-center">
                                            <p class="text-muted">ម៉ោងចេញ</p>
                                            <strong class="<?php
                                            if (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) > strtotime('17:30:00')) {
                                                echo 'text-danger'; // Red for Late Check-Out
                                            } elseif (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) < strtotime('16:00:00')) {
                                                echo 'text-info'; // Blue for Leave Early
                                            }
                                            ?>">
                                                <?= $attendance['checkOut'] ?? '--:--:--' ?>
                                            </strong>
                                        </div>
                                        <div class="col text-center">
                                            <p class="text-muted">ម៉ោងសរុប</p>
                                            <strong class="<?php
                                            if (isset($attendance['total'])) {
                                                $totalTime = new DateTime($attendance['total']);
                                                $minimumTime = new DateTime('08:00:00'); // 8 hours
                                    
                                                if ($totalTime < $minimumTime) {
                                                    echo 'text-danger'; // Red for less than 8 hours
                                                } elseif ($totalTime < new DateTime('16:00:00')) {
                                                    echo 'text-info'; // Blue for early check-out
                                                }
                                            }
                                            ?>">
                                                <?= $attendance['total'] ?? '--:--:--' ?>
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

        <!-- Pagination -->
        <?php if (!empty($pagedData) || !is_array($pagedData)): ?>
            <div class="pagination mt-3 justify-content-center">
                <!-- Previous Button -->
                <a href="?page=<?= max(1, $page - 1) ?>"
                    class="btn btn-sm mx-1 <?= $page > 1 ? 'btn-secondary' : 'btn-light disabled' ?>">
                    Previous
                </a>

                <!-- Show limited range of pages -->
                <?php
                $range = 2; // Number of pages to show before and after the current page
                $start = max(1, $page - $range); // Starting page
                $end = min($totalPages, $page + $range); // Ending page
                ?>

                <?php if ($start > 1): ?>
                    <a href="?page=1" class="btn btn-sm mx-1 btn-secondary">1</a>
                    <?php if ($start > 2): ?>
                        <span class="btn btn-sm mx-1 btn-light">...</span>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start; $i <= $end; $i++): ?>
                    <a href="?page=<?= $i ?>" class="btn btn-sm mx-1 <?= $i == $page ? 'btn-primary' : 'btn-secondary' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($end < $totalPages): ?>
                    <?php if ($end < $totalPages - 1): ?>
                        <span class="btn btn-sm mx-1 btn-light">...</span>
                    <?php endif; ?>
                    <a href="?page=<?= $totalPages ?>" class="btn btn-sm mx-1 btn-secondary"><?= $totalPages ?></a>
                <?php endif; ?>

                <!-- Next Button -->
                <a href="?page=<?= min($totalPages, $page + 1) ?>"
                    class="btn btn-sm mx-1 <?= $page < $totalPages ? 'btn-secondary' : 'btn-light disabled' ?>">
                    Next
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php include('src/common/footer.php'); ?>