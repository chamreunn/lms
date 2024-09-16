<?php
$title = "កំពុងរង់ចាំអនុម័ត";
include('src/common/header.php');
$action = $_GET['action'] ?? 'latein';
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div>ថ្ងៃនេះ</div>
                </div>
                <h2 class="page-title text-primary mb-0" id="real-time-clock"></h2>
            </div>
        </div>
    </div>
</div>

<div id="cardView" class="row row-cards">
    <div class="container-fluid">
        <div class="row mt-3 mb-3">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-3 mb-3">
                <div class="card list-group mb-3">
                    <a href="/elms/adminpending?action=latein"
                        class="list-group-item list-group-item-action <?php echo $action == 'latein' ? 'active' : ''; ?>">
                        <!-- SVG and Badge for Late In -->
                        <div class="d-flex">
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
                            <div class="mx-2">ចូលយឺត</div>
                            <span class="badge bg-danger text-white ms-auto"><?= $getLateInCount ?></span>
                        </div>
                    </a>
                    <a href="/elms/adminpending?action=lateout"
                        class="list-group-item list-group-item-action <?php echo $action == 'lateout' ? 'active' : ''; ?>">
                        <!-- SVG and Badge for Late Out -->
                        <div class="d-flex">
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
                            <div class="mx-2">ចេញយឺត</div>
                            <span class="badge bg-danger text-white ms-auto"><?= $getLateOutCount ?></span>
                        </div>
                    </a>
                    <a href="/elms/adminpending?action=leaveearly"
                        class="list-group-item list-group-item-action <?php echo $action == 'leaveearly' ? 'active' : ''; ?>">
                        <!-- SVG and Badge for Leave Early -->
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-search">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M11.5 21h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M18 18m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                <path d="M20.2 20.2l1.8 1.8" />
                            </svg>
                            <div class="mx-2">ចេញមុន</div>
                            <span class="badge bg-danger text-white ms-auto"><?= $getLeaveEarlyCount ?></span>
                        </div>
                    </a>
                </div>

                <div class="card list-group">
                    <a href="/elms/adminpending?action=allLate"
                        class="list-group-item list-group-item-action <?php echo $action == 'allLate' ? 'active' : ''; ?>">
                        <!-- SVG and Badge for Late In -->
                        <div class="d-flex">
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
                            <div class="mx-2">សំណើទាំងអស់</div>
                            <span class="badge bg-danger text-white ms-auto"><?= $getAllLate ?></span>
                        </div>
                    </a>
                </div>
            </div>


            <!-- Main Content Area -->
            <div class="col-md-9 col-lg-9">
                <div class="card">
                    <?php if ($action == 'latein'): ?>
                        <!-- Late In List -->
                        <ul class="list-group">
                            <?php if (empty($getAll)): ?>
                                <div class="card-header">
                                    <h3 class="card-title text-primary mb-0">
                                        <span>ចូលយឺត</span>
                                    </h3>
                                </div>
                                <div class="text-center mb-3">
                                    <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                                    <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($getAll as $request): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title text-primary mb-0">
                                                <span>ចូលយឺត</span>
                                            </h3>
                                        </div>
                                        <div class="list-group list-group-flush list-group-hoverable">
                                            <a href="viewLateDetail?id=<?= $request['id'] ?>"
                                                class="list-group-item list-group-item-action" aria-current="true">
                                                <!-- Profile Picture -->
                                                <div class="d-flex">
                                                    <div class="d-flex">
                                                        <img src="<?= $request['profile_picture'] ?>" class="avatar me-3"
                                                            style="object-fit: cover;" alt="Profile Picture">
                                                        <div class="justify-content-between">
                                                            <h4 class="mb-1 text-primary"><?= $request['khmer_name'] ?></h4>
                                                            <div class="text-muted"><?= $request['date'] ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="badge bg-warning"><?= $request['status'] ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                    <?php elseif ($action == 'lateout'): ?>
                        <!-- Late Out List -->
                        <ul class="list-group">
                            <?php if (empty($getAll)): ?>
                                <div class="card-header">
                                    <h3 class="card-title text-primary mb-0">
                                        <span>ចេញយឺត</span>
                                    </h3>
                                </div>
                                <div class="text-center mb-3">
                                    <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                                    <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($getAll as $request): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title text-primary mb-0">
                                                <span>ចេញយឺត</span>
                                            </h3>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            <a href="viewLateDetailLateOut?id=<?= $request['id'] ?>"
                                                class="list-group-item list-group-item-action" aria-current="true">
                                                <!-- Profile Picture -->
                                                <div class="d-flex">
                                                    <div class="d-flex">
                                                        <img src="<?= $request['profile_picture'] ?>" class="avatar me-3"
                                                            style="object-fit: cover;" alt="Profile Picture">
                                                        <div class="justify-content-between">
                                                            <h4 class="mb-1 text-primary"><?= $request['khmer_name'] ?></h4>
                                                            <div class="text-muted"><?= $request['date'] ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="badge bg-warning"><?= $request['status'] ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                    <?php elseif ($action == 'leaveearly'): ?>
                        <!-- Leave Early List -->
                        <ul class="list-group">
                            <?php if (empty($getAll)): ?>
                                <div class="card-header">
                                    <h3 class="card-title text-primary mb-0">
                                        <span>ចេញមុន</span>
                                    </h3>
                                </div>
                                <div class="text-center mb-3">
                                    <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                                    <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($getAll as $request): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title text-primary mb-0">
                                                <span>ចេញមុន</span>
                                            </h3>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            <a href="viewLateDetail?<?= $request['id'] ?>"
                                                class="list-group-item list-group-item-action" aria-current="true">
                                                <!-- Profile Picture -->
                                                <div class="d-flex">
                                                    <div class="d-flex">
                                                        <img src="<?= $request['profile_picture'] ?>" class="avatar me-3"
                                                            style="object-fit: cover;" alt="Profile Picture">
                                                        <div class="justify-content-between">
                                                            <h4 class="mb-1 text-primary"><?= $request['khmer_name'] ?></h4>
                                                            <div class="text-muted"><?= $request['date'] ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="badge bg-warning"><?= $request['status'] ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                    <?php elseif ($action == 'allLate'): ?>
                        <!-- Leave Early List -->
                        <ul class="list-group">
                            <?php if (empty($getAlls)): ?>
                                <div class="card-header">
                                    <h3 class="card-title text-primary mb-0">
                                        <span>សំណើទាំងអស់</span>
                                    </h3>
                                </div>
                                <div class="text-center mb-3">
                                    <img src="public/img/icons/svgs/empty.svg" alt="No Image">
                                    <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($getAlls as $request): ?>
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title text-primary mb-0">
                                                <span>ចេញមុន</span>
                                            </h3>
                                        </div>
                                        <div class="list-group list-group-flush">
                                            <a href="viewLateDetail?<?= $request['id'] ?>"
                                                class="list-group-item list-group-item-action" aria-current="true">
                                                <!-- Profile Picture -->
                                                <div class="d-flex">
                                                    <div class="d-flex">
                                                        <img src="<?= $request['profile_picture'] ?>" class="avatar me-3"
                                                            style="object-fit: cover;" alt="Profile Picture">
                                                        <div class="justify-content-between">
                                                            <h4 class="mb-1 text-primary"><?= $request['khmer_name'] ?></h4>
                                                            <div class="text-muted"><?= $request['date'] ?></div>
                                                        </div>
                                                    </div>
                                                    <div class="ms-auto">
                                                        <div class="badge bg-warning"><?= $request['status'] ?></div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>

                    <?php else: ?>
                        <!-- Default Content -->
                        <p class="text-secondary">Please select an option from the sidebar to view the requests.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="listView" class="row d-none">
    <?php foreach ($getAll as $getLate): ?>
        <div class="col-12">
            <div class="list-item border rounded p-3 mb-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <!-- Profile Picture -->
                    <img src="<?= $getLate['profile_picture'] ?>" class="avatar avatar-lg me-3 rounded-circle"
                        style="object-fit: cover; width: 70px; height: 70px;" alt="Profile Picture">

                    <!-- User Information -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-1 fw-bold"><?= $getLate['khmer_name'] ?></h5>
                            <div class="badge bg-primary"><?= $getLate['late_status'] ?></div>
                        </div>
                        <p class="text-muted mb-1"><?= $getLate['email'] ?></p>
                        <p class="text-muted"><strong>កាលបរិច្ឆេទ:</strong> <?= $getLate['date'] ?></p>

                        <!-- Display late in, out or leave early -->
                        <?php if (!empty($getLate['late_in'])): ?>
                            <p class="text-muted"><strong>ម៉ោងចូល:</strong> <?= $getLate['late_in'] ?></p>
                            <p class="text-muted"><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php elseif (!empty($getLate['late_out'])): ?>
                            <p class="text-muted"><strong>ម៉ោងចេញ:</strong> <?= $getLate['late_out'] ?></p>
                            <p class="text-muted"><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php elseif (!empty($getLate['leave_early'])): ?>
                            <p class="text-muted"><strong>ម៉ោងចេញមុន:</strong> <?= $getLate['leave_early'] ?></p>
                            <p class="text-muted"><strong>រយៈពេល:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php endif; ?>

                        <p class="text-muted"><strong>មូលហេតុ:</strong> <?= $getLate['reasons'] ?></p>
                    </div>
                </div>

                <!-- Status Badge and Action Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                        <?php if (!empty($getLate['late_in'])): ?>
                            <span class="badge bg-info">ចូលយឺត</span>
                        <?php elseif (!empty($getLate['late_out'])): ?>
                            <span class="badge bg-warning">ចេញយឺត</span>
                        <?php elseif (!empty($getLate['leave_early'])): ?>
                            <span class="badge bg-danger">ចេញមុន</span>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div>
                        <button type="button" data-bs-toggle="modal" data-bs-target="#approved<?= $getLate['id'] ?>"
                            class="btn btn-sm btn-outline-success me-2">អនុម័ត</button>
                        <button type="submit" data-bs-toggle="modal" data-bs-target="#rejected<?= $getLate['id'] ?>"
                            class="btn btn-sm btn-outline-danger">មិនអនុម័ត</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- approved modal  -->
        <div class="modal modal-blur fade" id="listapproved<?= $getLate['id'] ?>" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="staticBackdropLabel">អនុម័ត</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-status bg-success"></div>
                    <form action="/elms/adminapprovelate" method="POST" enctype="multipart/form-data">
                        <div class="modal-body text-center py-4">
                            <input type="hidden" name="id" value="<?= $getLate['late_id'] ?>">
                            <input type="hidden" name="user_email" value="<?= $getLate['email'] ?>">
                            <input type="hidden" name="action" value="Approved">
                            <div class="mb-3">
                                <label for="" class="form-label text-start fw-bold">មតិយោបល់</label>
                                <textarea name="comment" id="" placeholder="មតិយោបល់" class="form-control"></textarea>
                            </div>
                            <div class="mb-0">
                                <label for="" class="form-label text-start fw-bold">ហត្ថលេខា<span
                                        class="text-red mx-1 fw-bolder">*</span></label>
                                <input type="file" name="signature" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-success w-100">អនុម័ត</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- rejected modal  -->
        <div class="modal modal-blur fade" id="listrejected<?= $getLate['id'] ?>" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-status bg-red"></div>
                    <form action="/elms/adminapprovelate" method="POST" enctype="multipart/form-data">
                        <div class="modal-body text-center py-4">
                            <input type="hidden" name="id" value="<?= $getLate['late_id'] ?>">
                            <input type="hidden" name="user_email" value="<?= $getLate['email'] ?>">
                            <input type="hidden" name="action" value="Rejected">
                            <div class="col">
                                <label for="" class="form-label text-start fw-bold">មតិយោបល់<span
                                        class="text-red mx-1 fw-bolder">*</span></label>
                                <textarea name="comment" id="" placeholder="មតិយោបល់" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-red w-100">មិនអនុម័ត</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
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