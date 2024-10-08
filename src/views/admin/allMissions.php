<?php
$title = "បេសកកម្មទាំងអស់";
include('src/common/header.php');
$action = $_GET['action'] ?? 'latein';
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="col-12">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle mb-1">
                        <div>គ្រប់គ្រងសំណើ</div>
                    </div>
                    <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
                </div>

                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <div class="d-flex">
                            <!-- <input type="search" class="form-control d-inline-block w-9 me-3" placeholder="ស្វែងរកនាយកដ្ឋាន…" id="customSearch" /> -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#mission">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M12 5l0 14" />
                                    <path d="M5 12l14 0" />
                                </svg>
                                <span>បន្ថែមថ្មី</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <!-- Attendance List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link fw-bold text-primary active" href="/elms/adminmissions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-event">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path
                                    d="M4 5m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
                                <path d="M16 3l0 4" />
                                <path d="M8 3l0 4" />
                                <path d="M4 11l16 0" />
                                <path d="M8 15h2v2h-2z" />
                            </svg>
                            បេសកកម្មទាំងអស់
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/elms/admintodaymissions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-user">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12 21h-6a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4.5" />
                                <path d="M16 3v4" />
                                <path d="M8 3v4" />
                                <path d="M4 11h16" />
                                <path d="M19 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M22 22a2 2 0 0 0 -2 -2h-2a2 2 0 0 0 -2 2" />
                            </svg>
                            បេសកកម្មថ្ងៃនេះ
                            <?php if (!empty($getAllMissionCount)): ?>
                                <span class="badge bg-red text-red-fg ms-2"><?= $getAllMissionCount ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
            <!-- filter  -->
            <div class="card-header">
                <form class="w-100 mb-0" action="/elms/adminmissions" method="POST">
                    <div class="row g-3">
                        <div class="col-lg-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- SVG icon for calendar -->
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
                                <input class="form-control date-picker" placeholder="កាលបរិច្ឆេទចាប់ពី" type="text"
                                    name="start_date" id="start_date" autocomplete="off"
                                    value="<?= htmlspecialchars($_POST['start_date'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- SVG icon for calendar -->
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
                                <input class="form-control date-picker" placeholder="ដល់កាលបរិច្ឆេទ" type="text"
                                    name="end_date" id="leave_end_date" autocomplete="off"
                                    value="<?= htmlspecialchars($_POST['end_date'] ?? ''); ?>" />
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <button type="submit" class="btn btn-red w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-search">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                    <path d="M21 21l-6 -6" />
                                </svg>
                                <span>ស្វែងរក</span>
                            </button>
                        </div>
                        <div class="col-lg-2">
                            <a href="/elms/adminmissions" class="btn btn-outline w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-rotate-clockwise">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M4.05 11a8 8 0 1 1 .5 4m-.5 5v-5h5" />
                                </svg>
                                <span>សម្អាត</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
            <!-- end filter  -->
            <?php if (empty($getAllMissions)): ?>
                <div class="text-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <h3 class="text-primary mb-3">មិនទាន់មានសំណើនៅឡើយ។</h3>
                </div>
            <?php else: ?>
                <!-- Second table: All Approved Attendances (excluding today) -->
                <?php if (!empty($getAllMissions)): ?>
                    <!-- table  -->
                    <div class="table-responsive">
                        <table class="table table-vcenter table-bordered-less table-striped mb-0 sortable-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><button class="table-sort" data-sort="sort-name">ឈ្មោះ</button></th>
                                    <th><button class="table-sort" data-sort="sort-mission-name">ឈ្មោះបេសកកម្ម</button></th>
                                    <th><button class="table-sort" data-sort="sort-start-date">កាលបរិច្ឆេទចាប់ពី</button></th>
                                    <th><button class="table-sort" data-sort="sort-end-date">ដល់កាលបរិច្ឆេទ</button></th>
                                    <th>ឯកសារភ្ជាប់</th>
                                    <th><button class="table-sort" data-sort="sort-created-at">ស្នើនៅ</button></th>
                                    <th><button class="table-sort" data-sort="sort-action">សកម្មភាព</button></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getAllMissions as $key => $mission): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <div class="d-flex mb-0">
                                                <img src="<?= htmlspecialchars($mission['profile_picture']) ?>" class="avatar me-3"
                                                    style="object-fit: cover;" alt="Profile">
                                                <div class="d-flex flex-column">
                                                    <h3 class="text-primary mb-0">
                                                        <?= htmlspecialchars($mission['khmer_name']) ?>
                                                    </h3>
                                                    <div class="text-muted"><?= htmlspecialchars($mission['email']) ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($mission['missionName']) ?></td>
                                        <td><?= htmlspecialchars($mission['start_date']) ?></td>
                                        <td><?= htmlspecialchars($mission['end_date']) ?></td>
                                        <td>
                                            <a class="text-truncate"
                                                href="public/uploads/missions_attachments/<?= $mission['attachment'] ?>"
                                                target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                                </svg>
                                                ឯកសារភ្ជាប់
                                            </a>
                                        </td>
                                        <td><?= htmlspecialchars($mission['created_at']) ?></td>
                                        <td>
                                            <a href="#" data-bs-toggle="modal" data-bs-target="#edit-mission<?= $mission['id'] ?>"
                                                class="icon me-0 edit-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                    <path
                                                        d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                    <path d="M16 5l3 3" />
                                                </svg>
                                            </a>
                                            <a href="#" title="លុប" data-bs-placement="right" class="icon delete-btn text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal<?= htmlspecialchars($mission['id']) ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                                </svg>
                                            </a>
                                            <a href="#" class="d-sm-none" title="លុប" data-bs-toggle="collapse"
                                                data-bs-target="#collapseRequest<?= $mission['id'] ?>" aria-expanded="false"
                                                aria-controls="collapseRequest<?= $mission['id'] ?>">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-down">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M6 9l6 6l6 -6" />
                                                </svg>
                                            </a>

                                            <!-- Modal Edit Mission -->
                                            <div class="modal modal-blur fade" id="edit-mission<?= $mission['id'] ?>" tabindex="-1"
                                                role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" position="document">
                                                    <div class="modal-content">
                                                        <!-- Set the form action to the update endpoint -->
                                                        <form action="/elms/update-mission" method="POST"
                                                            enctype="multipart/form-data">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">កែសម្រួលលិខិតបេសកកម្ម</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <!-- Hidden field to pass the mission ID -->
                                                                <input type="hidden" name="mission_id"
                                                                    value="<?= $mission['id'] ?>">

                                                                <div class="mb-3">
                                                                    <label for="emission_start"
                                                                        class="form-label">ឈ្មោះបេសកកម្ម<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                                </path>
                                                                                <rect x="4" y="5" width="16" height="16" rx="2">
                                                                                </rect>
                                                                                <line x1="16" y1="3" x2="16" y2="7"></line>
                                                                                <line x1="8" y1="3" x2="8" y2="7"></line>
                                                                                <line x1="4" y1="11" x2="20" y2="11"></line>
                                                                                <rect x="8" y="15" width="2" height="2"></rect>
                                                                            </svg>
                                                                        </span>
                                                                        <!-- Pre-fill the input with the current start date -->
                                                                        <input type="text" value="<?= $mission['missionName'] ?>"
                                                                            class="form-control" name="mission_name" required>
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label for="emission_start"
                                                                        class="form-label">កាលបរិច្ឆេទចាប់ពី<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon"
                                                                                width="24" height="24" viewBox="0 0 24 24"
                                                                                stroke-width="2" stroke="currentColor" fill="none"
                                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                                </path>
                                                                                <rect x="4" y="5" width="16" height="16" rx="2">
                                                                                </rect>
                                                                                <line x1="16" y1="3" x2="16" y2="7"></line>
                                                                                <line x1="8" y1="3" x2="8" y2="7"></line>
                                                                                <line x1="4" y1="11" x2="20" y2="11"></line>
                                                                                <rect x="8" y="15" width="2" height="2"></rect>
                                                                            </svg>
                                                                        </span>
                                                                        <!-- Pre-fill the input with the current start date -->
                                                                        <input type="text" value="<?= $mission['start_date'] ?>"
                                                                            class="form-control date-picker" id="emission_start"
                                                                            name="start_date" required>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="emission_end" class="form-label">ដល់កាលបរិច្ឆេទ<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                <path
                                                                                    d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                                                <path d="M12 7v5l.5 .5" />
                                                                                <path
                                                                                    d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                                                <path d="M15 21v-6" />
                                                                            </svg>
                                                                        </span>
                                                                        <!-- Pre-fill the input with the current end date -->
                                                                        <input type="text" value="<?= $mission['end_date'] ?>"
                                                                            class="form-control date-picker" id="emission_end"
                                                                            name="end_date" required>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label">រយៈពេល<span
                                                                            class="text-danger mx-1 fw-bold">*</span></label>
                                                                    <div class="input-icon">
                                                                        <span class="input-icon-addon">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                <path
                                                                                    d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                                                                <path d="M12 7v5l.5 .5" />
                                                                                <path
                                                                                    d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                                                                <path d="M15 21v-6" />
                                                                            </svg>
                                                                        </span>
                                                                        <!-- Pre-fill the input with the current end date -->
                                                                        <input disabled type="text"
                                                                            value="<?= $mission['num_date'] ?>ថ្ងៃ"
                                                                            class="form-control" id="emission_end" name="end_date"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                                <?php if (!empty($mission['attachment'])): ?>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">ឯកសារដែលភ្ជាប់មកជាមួយ:</label>
                                                                        <a href="public/uploads/missions_attachments/<?= htmlspecialchars($mission['attachment']) ?>"
                                                                            target="_blank"
                                                                            class="btn btn-outline-secondary w-100 text-start">
                                                                            <span class="p-1">
                                                                                <!-- Attachment Icon SVG -->
                                                                            </span>
                                                                            <span><?= htmlspecialchars($mission['attachment']) ?></span>
                                                                        </a>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <div class="mb-3">
                                                                    <label for="emissionDoc" class="form-label">ឯកសារភ្ជាប់ថ្មី
                                                                        (ជម្រើស)</label>
                                                                    <label id="missionName" for="emissionDoc"
                                                                        class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                                                                        <span class="p-1">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                <path
                                                                                    d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                                                            </svg>
                                                                        </span>
                                                                        <span id="emissionName">ជ្រើសឯកសារថ្មី</span>
                                                                    </label>
                                                                    <input type="file" name="missionDoc" id="emissionDoc"
                                                                        accept=".pdf, .docx, .xlsx" hidden
                                                                        onchange="displayFileName('emissionDoc', 'emissionName')" />
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-check cursor-pointer">
                                                                        <input class="form-check-input" type="checkbox" name="agree"
                                                                            checked required>
                                                                        <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span
                                                                                class="text-danger fw-bold mx-1">*</span></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light border-top">
                                                                <div class="w-100">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <button type="button" class="btn w-100"
                                                                                data-bs-dismiss="modal">បោះបង់</button>
                                                                        </div>
                                                                        <div class="col">
                                                                            <button type="submit"
                                                                                class="btn w-100 btn-primary ms-auto">កែប្រែ</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- delete  -->
                                            <div class="modal modal-blur fade"
                                                id="deleteModal<?= htmlspecialchars($mission['id']) ?>" tabindex="-1" role="dialog"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-status bg-danger"></div>

                                                        <div class="modal-body text-center py-4 mb-0">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon mb-2 text-danger icon-lg">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                                <path d="M12 9v4"></path>
                                                                <path
                                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                </path>
                                                                <path d="M12 16h.01"></path>
                                                            </svg>
                                                            <h5 class="modal-title fw-bold text-danger">លុបបេសកកម្ម</h5>
                                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបបេសកកម្មនេះ?</p>
                                                        </div>
                                                        <form action="/elms/delete-mission" method="POST">
                                                            <input type="hidden" name="id"
                                                                value="<?= htmlspecialchars($mission['id']) ?>">
                                                            <div class="modal-footer bg-light border-top">
                                                                <div class="w-100">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <button type="button" class="btn w-100"
                                                                                data-bs-dismiss="modal">បោះបង់</button>
                                                                        </div>
                                                                        <div class="col">
                                                                            <button type="submit"
                                                                                class="btn btn-danger ms-auto w-100">យល់ព្រម</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination Logic -->
                    <div class="card-footer">
                        <?php
                        // Fetch total approved records for pagination calculation
                        $totalRecords = $adminModel->getTotalMission(); // Use the method to get total records
                        $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages
                        ?>

                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" tabindex="-1" aria-disabled="true">
                                    <!-- Chevron left icon -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="icon">
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
</div>

<?php include('src/common/footer.php'); ?>