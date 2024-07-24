<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

$title = "ទំព័រដើម";
include('src/common/header.php');
// Ensure the necessary data is available
$count = isset($data['count']) ? $data['count'] : 0;
$leaves = isset($data['leaves']) ? $data['leaves'] : [];
$page = isset($data['page']) ? $data['page'] : 1;
$totalPages = isset($data['totalPages']) ? $data['totalPages'] : 1;
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    $days = [
        'Mon' => 'ច័ន្ទ',
        'Tue' => 'អង្គារ',
        'Wed' => 'ពុធ',
        'Thu' => 'ព្រហស្បតិ៍',
        'Fri' => 'សុក្រ',
        'Sat' => 'សៅរ៍',
        'Sun' => 'អាទិត្យ'
    ];
    $months = [
        'January' => 'មករា',
        'February' => 'កុម្ភៈ',
        'March' => 'មីនា',
        'April' => 'មេសា',
        'May' => 'ឧសភា',
        'June' => 'មិថុនា',
        'July' => 'កក្កដា',
        'August' => 'សីហា',
        'September' => 'កញ្ញា',
        'October' => 'តុលា',
        'November' => 'វិច្ឆិកា',
        'December' => 'ធ្នូ'
    ];

    $translatedDay = $days[date('D', strtotime($date))];
    $translatedMonth = $months[date('F', strtotime($date))];
    $translatedDate = str_replace(
        [date('D', strtotime($date)), date('F', strtotime($date))],
        [$translatedDay, $translatedMonth],
        date($format, strtotime($date))
    );

    return $translatedDate;
}
?>
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div id="real-time-clock"></div>
                </div>
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? ""); ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <div class="dropdown">
                            <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5" />
                                    <path d="M16 3v4" />
                                    <path d="M8 3v4" />
                                    <path d="M4 11h16" />
                                    <path d="M16 19h6" />
                                    <path d="M19 16v6" />
                                </svg>
                                <span>បង្កើតសំណើ</span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="/elms/apply-leave">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-question">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M15 21h-9a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h16"></path>
                                            <path d="M19 22v.01"></path>
                                            <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                        </svg>
                                        <span class="mx-1">ច្បាប់ឈប់សម្រាក</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-question">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06"></path>
                                            <path d="M12 7v5l2 2"></path>
                                            <path d="M19 22v.01"></path>
                                            <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483"></path>
                                        </svg>
                                        <span class="mx-1">លិខិតយឺត</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-repeat">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3"></path>
                                            <path d="M16 3v4"></path>
                                            <path d="M8 3v4"></path>
                                            <path d="M4 11h12"></path>
                                            <path d="M20 14l2 2h-3"></path>
                                            <path d="M20 18l2 -2"></path>
                                            <path d="M19 16a3 3 0 1 0 2 5.236"></path>
                                        </svg>
                                        <span class="mx-1">បេសកម្ម</span>
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

<div class="row row-card mb-3">
    <div class="col-12 mb-3">
        <div class="row row-cards">
            <!-- All Leave Requests Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-primary text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                        <path d="M16 3v4" />
                                        <path d="M8 3v4" />
                                        <path d="M4 11h16" />
                                        <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                        <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    ច្បាប់ឈប់សម្រាកទាំងអស់
                                </div>
                                <div class="text-secondary">
                                    <?= $count ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Late Letters Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-green text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-clock-question">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20.975 11.33a9 9 0 1 0 -5.717 9.06" />
                                        <path d="M12 7v5l2 2" />
                                        <path d="M19 22v.01" />
                                        <path d="M19 19a2.003 2.003 0 0 0 .914 -3.782a1.98 1.98 0 0 0 -2.414 .483" />
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    លិខិតចេញ និងចូលយឺត
                                </div>
                                <div class="text-secondary">
                                    32 shipped
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Missions Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-warning text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-calendar-repeat">
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
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    បេសកម្ម
                                </div>
                                <div class="text-secondary">
                                    16 today
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Likes Card -->
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-success text-white avatar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-brand-facebook">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M7 10v4h3v7h4v-7h3l1 -4h-4v-2a1 1 0 0 1 1 -1h3v-4h-3a5 5 0 0 0 -5 5v2h-3"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    132 Likes
                                </div>
                                <div class="text-secondary">
                                    21 today
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-lg-4 mb-3">
        <div class="card h-100">
            <h3 class="card-header">ច្បាប់របស់សមាជិក</h3>
            <div class="card-body">
                <div class="list-group">
                    <?php if (empty($getuserapproves)) : ?>
                        <div class="d-flex flex-column align-items-center justify-content-center text-center empty-state">
                            <img src="public/img/icons/svgs/empty.svg" alt="No data" class="empty-icon">
                            <p class="empty-text">មិនមានការឈប់សម្រាក</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($getuserapproves as $getuserapprove) : ?>
                            <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $getuserapprove['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $getuserapprove['profile'] ?>" class="avatar me-3 rounded-circle" alt="Profile picture">
                                    <div class="flex-grow-1">
                                        <h5 class="name mb-1"><?= htmlspecialchars($getuserapprove['khmer_name']) ?></h5>
                                        <small class="date text-muted mb-1"><?= translateDateToKhmer($getuserapprove['start_date'], 'j') . " ដល់ " . translateDateToKhmer($getuserapprove['end_date'], 'j F Y') ?></small>
                                    </div>
                                    <span class="badge <?= $getuserapprove['status'] == 'Pending' ? 'bg-warning-lt' : '' ?>
                                    <?= $getuserapprove['status'] == 'Approved' ? 'badge-outline text-success' : '' ?>
                                    <?= $getuserapprove['status'] == 'Rejected' ? 'badge-outline text-danger' : '' ?>
                                    <?= $getuserapprove['status'] == 'Cancelled' ? 'badge-outline text-secondary' : '' ?>">
                                        <?= htmlspecialchars($getuserapprove['status']) ?>
                                    </span>
                                </div>
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="detailsModal<?= $getuserapprove['id'] ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?= $getuserapprove['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailsModalLabel<?= $getuserapprove['id'] ?>">
                                                <?= htmlspecialchars($getuserapprove['khmer_name']) ?>'s Leave Details
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-person-fill"></i> ឈ្មោះមន្ត្រី</th>
                                                        <td><?= htmlspecialchars($getuserapprove['khmer_name']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-person-fill"></i> ប្រភេទច្បាប់</th>
                                                        <td><span class="badge <?= htmlspecialchars($getuserapprove['leavetype_color']) ?>"><?= htmlspecialchars($getuserapprove['leave_type']) ?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-calendar-event-fill"></i> ចាប់ពី</th>
                                                        <td><?= translateDateToKhmer(htmlspecialchars($getuserapprove['start_date']), 'D, j F Y') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-calendar-x-fill"></i> ដល់</th>
                                                        <td><?= translateDateToKhmer(htmlspecialchars($getuserapprove['end_date']), 'D, j F Y') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-info-circle-fill"></i> មូលហេតុ</th>
                                                        <td><?= htmlspecialchars($getuserapprove['remarks']) ?></td>
                                                    </tr>
                                                    <!-- Add additional details here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
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

    <div class="col-sm-12 col-lg-4 mb-3">
        <div class="card h-100">
            <h3 class="card-header">ច្បាប់របស់សមាជិក</h3>
            <div class="card-body">
                <div class="list-group">
                    <?php if (empty($getuserapproves)) : ?>
                        <div class="d-flex flex-column align-items-center justify-content-center text-center empty-state">
                            <img src="public/img/icons/svgs/empty.svg" alt="No data" class="empty-icon">
                            <p class="empty-text">មិនមានការឈប់សម្រាក</p>
                        </div>
                    <?php else : ?>
                        <?php foreach ($getuserapproves as $getuserapprove) : ?>
                            <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#detailsModal<?= $getuserapprove['id'] ?>">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $getuserapprove['profile'] ?>" class="avatar me-3 rounded-circle" alt="Profile picture">
                                    <div class="flex-grow-1">
                                        <h5 class="name mb-1"><?= htmlspecialchars($getuserapprove['khmer_name']) ?></h5>
                                        <small class="date text-muted mb-1"><?= translateDateToKhmer($getuserapprove['start_date'], 'j') . " ដល់ " . translateDateToKhmer($getuserapprove['end_date'], 'j F Y') ?></small>
                                    </div>
                                    <span class="badge <?= $getuserapprove['status'] == 'Pending' ? 'bg-warning-lt' : '' ?>
                                    <?= $getuserapprove['status'] == 'Approved' ? 'badge-outline text-success' : '' ?>
                                    <?= $getuserapprove['status'] == 'Rejected' ? 'badge-outline text-danger' : '' ?>
                                    <?= $getuserapprove['status'] == 'Cancelled' ? 'badge-outline text-secondary' : '' ?>">
                                        <?= htmlspecialchars($getuserapprove['status']) ?>
                                    </span>
                                </div>
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="detailsModal<?= $getuserapprove['id'] ?>" tabindex="-1" aria-labelledby="detailsModalLabel<?= $getuserapprove['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="detailsModalLabel<?= $getuserapprove['id'] ?>">
                                                <?= htmlspecialchars($getuserapprove['khmer_name']) ?>'s Leave Details
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <table class="table">
                                                <tbody>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-person-fill"></i> ឈ្មោះមន្ត្រី</th>
                                                        <td><?= htmlspecialchars($getuserapprove['khmer_name']) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-person-fill"></i> ប្រភេទច្បាប់</th>
                                                        <td><span class="badge <?= htmlspecialchars($getuserapprove['leavetype_color']) ?>"><?= htmlspecialchars($getuserapprove['leave_type']) ?></span></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-calendar-event-fill"></i> ចាប់ពី</th>
                                                        <td><?= translateDateToKhmer(htmlspecialchars($getuserapprove['start_date']),'D, j F Y') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-calendar-x-fill"></i> ដល់</th>
                                                        <td><?= translateDateToKhmer(htmlspecialchars($getuserapprove['end_date']),'D, j F Y') ?></td>
                                                    </tr>
                                                    <tr>
                                                        <th scope="row"><i class="bi bi-info-circle-fill"></i> មូលហេតុ</th>
                                                        <td><?= htmlspecialchars($getuserapprove['remarks']) ?></td>
                                                    </tr>
                                                    <!-- Add additional details here -->
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
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
</div>
<?php include('src/common/footer.php'); ?>
<!-- <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Check if it's the user's first login
        if (!sessionStorage.getItem('hasVisitedBefore')) {
            // Show the modal with static backdrop
            var myModal = new bootstrap.Modal(document.getElementById('changePasswordModal'), {
                backdrop: 'static',
                keyboard: false
            });
            myModal.show();

            // Set flag in sessionStorage so the modal doesn't show again
            sessionStorage.setItem('hasVisitedBefore', 'true');
        }

        // Handle form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            // Add your form submission logic here (e.g., AJAX call to server)
            // For now, just close the modal
            var myModalEl = document.getElementById('changePasswordModal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        });
    });
</script> -->
<!-- Modal for changing password -->
<div class="modal modal-blur fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Your Password</h5>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>