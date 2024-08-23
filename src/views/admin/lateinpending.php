<?php
$title = "កំពុងរង់ចាំអនុម័ត";
include('src/common/header.php');
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

<!-- button view as card or list  -->
<div class="d-flex justify-content-end mb-3">
    <div class="btn-group">
        <button id="cardViewBtn" class="btn btn-outline-primary active">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-layout-dashboard me-0">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                <path d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
                <path d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
                <path d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1" />
            </svg>
        </button>
        <button id="listViewBtn" class="btn btn-outline-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-list me-0">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M9 6l11 0" />
                <path d="M9 12l11 0" />
                <path d="M9 18l11 0" />
                <path d="M5 6l0 .01" />
                <path d="M5 12l0 .01" />
                <path d="M5 18l0 .01" />
            </svg>
        </button>
    </div>
</div>

<div id="cardView" class="row row-cards">
    <?php foreach ($getAll as $getLate) : ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <?php if (!empty($getLate['late_in'])) : ?>
                        <span class="badge bg-info ms-auto">ចូលយឺត</span>
                    <?php elseif (!empty($getLate['late_out'])) : ?>
                        <span class="badge bg-warning ms-auto">ចេញយឺត</span>
                    <?php elseif (!empty($getLate['leave_early'])) : ?>
                        <span class="badge bg-danger ms-auto">ចេញមុន</span>
                    <?php endif; ?>
                </div>
                <div class="card-body d-flex align-items-center">
                    <img src="<?= $getLate['profile_picture'] ?>" class="avatar avatar-lg" style="object-fit: cover;" alt="">
                    <div class="ms-3">
                        <div class="d-flex justify-content-between">
                            <h3 class="h4"><?= $getLate['khmer_name'] ?></h3>
                            <div class="badge bg-warning mb-2 mx-2"><?= $getLate['late_status'] ?></div>
                        </div>
                        <p class="text-muted mb-2"><?= $getLate['email'] ?></p>
                        <p><strong>កាលបរិច្ឆេទ:</strong> <?= $getLate['date'] ?></p>

                        <?php if (!empty($getLate['late_in'])) : ?>
                            <p><strong>ម៉ោងចូល:</strong> <?= $getLate['late_in'] ?></p>
                            <p><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php elseif (!empty($getLate['late_out'])) : ?>
                            <p><strong>ម៉ោងចេញ:</strong> <?= $getLate['late_out'] ?></p>
                            <p><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php elseif (!empty($getLate['leave_early'])) : ?>
                            <p><strong>ម៉ោងចេញមុន:</strong> <?= $getLate['leave_early'] ?></p>
                            <p><strong>រយៈពេល:</strong> <?= $getLate['late'] ?> នាទី</p>
                        <?php endif; ?>
                        <p><strong>មូលហេតុ:</strong> <?= $getLate['reasons'] ?></p>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" data-bs-toggle="modal" data-bs-target="#approved<?= $getLate['id'] ?>" class="btn w-100 btn-outline-success">អនុម័ត</button>
                            </div>
                            <div class="col">
                                <button type="submit" data-bs-toggle="modal" data-bs-target="#rejected<?= $getLate['id'] ?>" class="btn w-100 btn-outline-danger ms-auto">មិនអនុម័ត</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- approved modal  -->
        <div class="modal modal-blur fade" id="approved<?= $getLate['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                <label for="" class="form-label text-start fw-bold">ហត្ថលេខា<span class="text-red mx-1 fw-bolder">*</span></label>
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
        <div class="modal modal-blur fade" id="rejected<?= $getLate['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-status bg-red"></div>
                    <form action="/elms/adminapprovelate" method="POST" enctype="multipart/form-data">
                        <div class="modal-body text-center py-4">
                            <input type="hidden" name="id" value="<?= $getLate['late_id'] ?>">
                            <input type="hidden" name="user_email" value="<?= $getLate['email'] ?>">
                            <input type="hidden" name="action" value="Rejected">
                            <div class="col">
                                <label for="" class="form-label text-start fw-bold">មតិយោបល់<span class="text-red mx-1 fw-bolder">*</span></label>
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

<div id="listView" class="row d-none">
    <?php foreach ($getAll as $getLate) : ?>
        <div class="col-12">
            <div class="card mb-3">
                <div class="list-group">
                    <div class="list-group-item d-flex align-items-center">
                        <img src="<?= $getLate['profile_picture'] ?>" class="avatar avatar-md me-3" style="object-fit: cover;" alt="">
                        <div class="flex-grow-1">
                            <div class="d-flex">
                                <h3 class="h4"><?= $getLate['khmer_name'] ?></h3>
                                <div class="mb-2 mx-2">
                                    <?php if (!empty($getLate['late_in'])) : ?>
                                        <span class="badge bg-info mb-2">ចូលយឺត</span>
                                    <?php elseif (!empty($getLate['late_out'])) : ?>
                                        <span class="badge bg-warning mb-2">ចេញយឺត</span>
                                    <?php elseif (!empty($getLate['leave_early'])) : ?>
                                        <span class="badge bg-danger mb-2">ចេញមុន</span>
                                    <?php endif; ?>
                                    <div class="badge bg-warning mb-2 mx-2"><?= $getLate['late_status'] ?></div>
                                </div>
                            </div>

                            <p><strong>កាលបរិច្ឆេទ:</strong> <?= $getLate['date'] ?></p>

                            <?php if (!empty($getLate['late_in'])) : ?>
                                <p><strong>ម៉ោងចូល:</strong> <?= $getLate['late_in'] ?></p>
                                <p><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                            <?php elseif (!empty($getLate['late_out'])) : ?>
                                <p><strong>ម៉ោងចេញ:</strong> <?= $getLate['late_out'] ?></p>
                                <p><strong>រយៈពេលយឺត:</strong> <?= $getLate['late'] ?> នាទី</p>
                            <?php elseif (!empty($getLate['leave_early'])) : ?>
                                <p><strong>ម៉ោងចេញមុន:</strong> <?= $getLate['leave_early'] ?></p>
                                <p><strong>រយៈពេល:</strong> <?= $getLate['late'] ?> នាទី</p>
                            <?php endif; ?>
                            <p><strong>មូលហេតុ:</strong> <?= $getLate['reasons'] ?></p>
                        </div>
                        <div class="ms-auto d-flex flex-column">
                            <button type="button" data-bs-toggle="modal" data-bs-target="#listapproved<?= $getLate['id'] ?>" class="btn btn-outline-success mb-2">អនុម័ត</button>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#listrejected<?= $getLate['id'] ?>" class="btn btn-outline-danger">មិនអនុម័ត</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- approved modal  -->
        <div class="modal modal-blur fade" id="listapproved<?= $getLate['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
                                <label for="" class="form-label text-start fw-bold">ហត្ថលេខា<span class="text-red mx-1 fw-bolder">*</span></label>
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
        <div class="modal modal-blur fade" id="listrejected<?= $getLate['id'] ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-status bg-red"></div>
                    <form action="/elms/adminapprovelate" method="POST" enctype="multipart/form-data">
                        <div class="modal-body text-center py-4">
                            <input type="hidden" name="id" value="<?= $getLate['late_id'] ?>">
                            <input type="hidden" name="user_email" value="<?= $getLate['email'] ?>">
                            <input type="hidden" name="action" value="Rejected">
                            <div class="col">
                                <label for="" class="form-label text-start fw-bold">មតិយោបល់<span class="text-red mx-1 fw-bolder">*</span></label>
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

<!-- card&list view  -->
<script>
    // Function to handle switching views and saving the preference
    function switchView(view) {
        if (view === 'card') {
            document.getElementById('cardView').classList.remove('d-none');
            document.getElementById('listView').classList.add('d-none');
            document.getElementById('cardViewBtn').classList.add('active');
            document.getElementById('listViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'card');
        } else if (view === 'list') {
            document.getElementById('cardView').classList.add('d-none');
            document.getElementById('listView').classList.remove('d-none');
            document.getElementById('listViewBtn').classList.add('active');
            document.getElementById('cardViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'list');
        }
    }

    // Event listeners for buttons
    document.getElementById('cardViewBtn').addEventListener('click', function() {
        switchView('card');
    });

    document.getElementById('listViewBtn').addEventListener('click', function() {
        switchView('list');
    });

    // On page load, set the view based on the user's previous choice
    window.onload = function() {
        const preferredView = localStorage.getItem('preferredView') || 'card';
        switchView(preferredView);
    };
</script>

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