<?php
$pretitle = "ទំព័រដើម";
$title = "ព័ត៌មានគណនី";
include('src/common/header.php');
?>

<div class="card mb-3 p-2">
    <ul class="nav nav-pills" id="pills-tab" role="tablist">
        <!-- user Information  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home"
                type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-id">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M3 4m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v10a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
                    <path d="M9 10m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                    <path d="M15 8l2 0" />
                    <path d="M15 12l2 0" />
                    <path d="M7 16l10 0" />
                </svg>
                <span class="mx-2">ព័ត៌មានផ្ទាល់ខ្លួន</span>
            </button>
        </li>

        <!-- work-history  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#work-history"
                type="button" role="tab" aria-controls="work-history" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-history">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M12 8l0 4l2 2" />
                    <path d="M3.05 11a9 9 0 1 1 .5 4m-.5 5v-5h5" />
                </svg>
                <span class="mx-2">ប្រវត្តិការងារ</span>
            </button>
        </li>

        <!-- certificate  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact"
                type="button" role="tab" aria-controls="pills-contact" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-certificate">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M15 15m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                    <path d="M13 17.5v4.5l2 -1.5l2 1.5v-4.5" />
                    <path d="M10 19h-5a2 2 0 0 1 -2 -2v-10c0 -1.1 .9 -2 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -1 1.73" />
                    <path d="M6 9l12 0" />
                    <path d="M6 12l3 0" />
                    <path d="M6 15l2 0" />
                </svg>
                <span class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="គ្រឿងឥស្សរិយយស ប័ណ្ណសរសើរ ឬទណ្ណកម្មវិន័យ">គ្រឿងឥស្សរិយយស</span>
            </button>
        </li>

        <!-- certificate  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-education-tab" data-bs-toggle="pill" data-bs-target="#pills-education"
                type="button" role="tab" aria-controls="pills-education" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-school">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M22 9l-10 -4l-10 4l10 4l10 -4v6" />
                    <path d="M6 10.6v5.4a6 3 0 0 0 12 0v-5.4" />
                </svg>
                <span class="mx-2" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="កម្រិតវប្បធម៌ទូទៅ ការបណ្តុះបណ្តាលជំនាញវិជ្ជាជីវៈ​ និងការបណ្តុះបណ្តាលបន្ត">កម្រិតវប្បធម៍ទូទៅ</span>
            </button>
        </li>

        <!-- familly  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-language-tab" data-bs-toggle="pill" data-bs-target="#pills-language"
                type="button" role="tab" aria-controls="pills-language" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-users-group">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 13a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M8 21v-1a2 2 0 0 1 2 -2h4a2 2 0 0 1 2 2v1" />
                    <path d="M15 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M17 10h2a2 2 0 0 1 2 2v1" />
                    <path d="M5 5a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                    <path d="M3 13v-1a2 2 0 0 1 2 -2h2" />
                </svg>
                <span class="mx-2">ស្ថានភាពគ្រួសារ</span>
            </button>
        </li>

        <!-- other documents  -->
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-document-tab" data-bs-toggle="pill" data-bs-target="#pills-document"
                type="button" role="tab" aria-controls="pills-document" aria-selected="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-file-check">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                    <path d="M9 15l2 2l4 -4" />
                </svg>
                <span class="mx-2">ឯកសារផ្សេងៗរបស់មន្រ្តី</span>
            </button>
        </li>
    </ul>
</div>

<div class="tab-content mb-3" id="pills-tabContent">
    <!-- user-information  -->
    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
        <div class="row g-3">
            <div class="row-cards">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-5 border-end mb-0">
                                <div class="d-flex align-items-center justify-contenter-center">
                                    <div class="d-flex flex-column">
                                        <img class="avatar avatar-xl rounded mb-2"
                                            src="<?= htmlspecialchars($userDetails['profile_picture']) ?>"
                                            style="object-fit: cover;" alt="">
                                        <button class="btn btn-sm btn-primary mb-0" data-bs-toggle="modal"
                                            data-bs-target="#editModel">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round"
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-photo">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M15 8h.01" />
                                                <path
                                                    d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
                                                <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
                                                <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
                                            </svg>
                                            ផ្លាស់ប្តូររូបភាព
                                        </button>
                                    </div>
                                    <div class="d-flex flex-column mx-2">
                                        <h1 class="mb-2"><?= htmlspecialchars($userDetails['user_name']) ?></h1>
                                        <p class="mb-2">
                                            <span
                                                class="badge <?= htmlspecialchars($userDetails['position_color']) ?>"><?= htmlspecialchars($userDetails['rolename']) ?></span>
                                            | <?= htmlspecialchars($userDetails['department_name']) ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 d-flex align-items-center justify-content-center mb-0">
                                <div class="p-3 rounded bg-light">
                                    <div class="row g-3">
                                        <!-- Row 1 -->
                                        <div class="row g-3">
                                            <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                                <div class="col-auto text-start">
                                                    <dd class="text-muted">លេខកាត:</dd>
                                                </div>
                                                <div class="col-auto text-start">
                                                    <p class="fw-bold">
                                                        <?= htmlspecialchars($userDetails['user_id']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                                <div class="col-auto text-start">
                                                    <dd class="text-muted">ទំនាក់ទំនង:</dd>
                                                </div>
                                                <div class="col-auto text-start">
                                                    <p class="fw-bold">
                                                        <?= htmlspecialchars($userDetails['phone_number']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Row 2 -->
                                        <div class="row g-3">
                                            <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                                <div class="col-auto text-start">
                                                    <dd class="text-muted">អាសយដ្ឋានអ៊ីម៉ែល:</dd>
                                                </div>
                                                <div class="col-auto text-start">
                                                    <p class="fw-bold">
                                                        <?= htmlspecialchars($userDetails['email']) ?>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                                <div class="col-auto text-start">
                                                    <dd class="text-muted">ឈ្មោះជាអក្សរឡាតាំង:</dd>
                                                </div>
                                                <div class="col-auto text-start">
                                                    <p class="fw-bold">
                                                        <?= htmlspecialchars($userDetails['user_eng_name']) ?>
                                                    </p>
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

            <div class="col-lg-4 col-sm-12 col-md-12">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ព័ត៌មានផ្ទាល់ខ្លួន</h3>
                        </div>
                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Gender -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ភេទ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= $userDetails['gender'] ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Date of Birth -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ថ្ងៃខែឆ្នាំកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['dob']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អត្តសញ្ញាណបណ្ណ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['identify_card']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card Expiry -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['exprireDateIdenCard']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">លិខិតឆ្លងដែន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['passport']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport Expiry -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['exprirePassport']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Nationality -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">សញ្ជាតិ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['nationality']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Marital Status -->
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាពគ្រួសារ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['marital_status']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Place of Birth -->
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['address']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Current Address -->
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អាសយដ្ឋានបច្ចុប្បន្ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['curaddress']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">សមត្ថភាពភាសាបរទេស</h3>
                        </div>
                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <?php if (!empty($userDetails['user_abillity_language'])): ?>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>ភាសា</th>
                                        <th>អាន</th>
                                        <th>សរសេរ</th>
                                        <th>និយាយ</th>
                                        <th>ស្តាប់</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_abillity_language'] as $language): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($language['language']); ?></strong></td>
                                            <td>
                                                <span class="stars" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Read">
                                                    <?php echo str_repeat('⭐', $language['read']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="stars" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Write">
                                                    <?php echo str_repeat('⭐', $language['write']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="stars" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Speak">
                                                    <?php echo str_repeat('⭐', $language['speak']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="stars" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    title="Listen">
                                                    <?php echo str_repeat('⭐', $language['listen']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No language abilities found.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- language -->
            <div class="col-lg-8 col-sm-12 col-md-8">
                <!-- first of work  -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="mb-0 text-primary">ចូលបម្រើការងាររដ្ឋដំបូង</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទចូលបម្រើការងាររដ្ឋដំបូង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['date_enteing_public_service']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (!empty($userDetails['comfirm_date'])): ?>
                                <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                    <div class="col-auto text-start">
                                        <dd class="text-muted">កាលបរិច្ឆេទតាំងស៊ប់ :</dd>
                                    </div>
                                    <div class="col-auto text-start">
                                        <p class="fw-bold">
                                            <?= htmlspecialchars($userDetails['comfirm_date']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ក្របខណ្ឌ ឋានន្តរស័ក្ត​ និងថ្នាក់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['constitution']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">មុខតំណែង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['roleName']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ក្រសួង/ស្ថាប័ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['ministry_enteing_public_service']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អង្គភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['economy_enteing_public_service']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">នាយកដ្ឋាន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['departmentName']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ការិយាល័យ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['officeName']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- current work  -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ស្ថានភាពមុខងារបច្ចុប្បន្ន</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ក្របខណ្ឌ​​ ឋានន្តរស័ក្ត​ និងថ្នាក់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['constitution_misitry_rank']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទប្តូរក្របខណ្ឌ ឋានន្តរស័ក្ត និងថ្នាក់ចុងក្រោយ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['constitution_amendment_date']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទទទូលមុខតំណែងចុងក្រោយ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['effective_date_of_last_promotion']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">មុខតំណែង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['roleNameCur']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អង្គភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['economy_current_job_situation']) ?>
                                    </p>
                                </div>
                            </div>
                            <?php if (!empty($userDetails['departmentNameCur'])): ?>
                                <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                    <div class="col-auto text-start">
                                        <dd class="text-muted">នាយកដ្ឋាន:</dd>
                                    </div>
                                    <div class="col-auto text-start">
                                        <p class="fw-bold">
                                            <?= htmlspecialchars($userDetails['departmentNameCur']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($userDetails['officeNameCur'])): ?>
                                <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                    <div class="col-auto text-start">
                                        <dd class="text-muted">ការិយាល័យ:</dd>
                                    </div>
                                    <div class="col-auto text-start">
                                        <p class="fw-bold">
                                            <?= htmlspecialchars($userDetails['officeNameCur']) ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- more work  -->
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">តួនាទីបន្ថែមលើមុខងារបច្ចុប្បន្ន(ឋានៈស្មើ)</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <?php if (!empty($userDetails['additional_work'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ថ្ងៃ-ខែ​​​-ឆ្នាំ​</th>
                                        <th>មុខតំណែង</th>
                                        <th>ឋានៈស្មើ</th>
                                        <th>អង្គភាព</th>
                                        <th>ឯកសារ</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php foreach ($userDetails['additional_work'] as $index => $position): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td> <!-- Serial Number -->
                                            <td><?= htmlspecialchars($position['date'] ?? 'N/A') ?></td> <!-- Date -->
                                            <td><?= htmlspecialchars($position['position'] ?? 'N/A') ?></td>
                                            <!-- Position -->
                                            <td><?= htmlspecialchars($position['equivalent'] ?? 'N/A') ?></td>
                                            <!-- Equivalent -->
                                            <td><?= htmlspecialchars($position['economy'] ?? 'N/A') ?></td>
                                            <!-- Economy/Unit -->
                                            <td>
                                                <?php if (!empty($position['document'])): ?>
                                                    <a href="<?= htmlspecialchars($position['document']) ?>"
                                                        target="_blank">ឯកសារ</a>
                                                <?php else: ?>
                                                    N/A
                                                <?php endif; ?>
                                            </td> <!-- Document -->
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- work-history  -->
    <div class="tab-pane fade" id="work-history" role="tabpanel" aria-labelledby="work-history-tab">
        <div class="row g-3">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ក្នុងវិស័យមុខងារសារធារណៈ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <?php if (!empty($userDetails['user_public_sector'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ថ្ងៃ-ខែ​​​-ឆ្នាំ​ ចូល​</th>
                                        <th>ថ្ងៃ-ខែ​​​-ឆ្នាំ ចេញ</th>
                                        <th>ក្រសួង/ស្ថាប័ន</th>
                                        <th>អង្គភាព</th>
                                        <th>មុខតំណែង</th>
                                        <th>ផ្សេងៗ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_public_sector'] as $index => $publicSector): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td> <!-- Serial Number -->
                                            <td><?= htmlspecialchars($publicSector['dateIn'] ?? 'N/A') ?></td> <!-- Date -->
                                            <td><?= htmlspecialchars($publicSector['dateOut'] ?? 'N/A') ?></td>
                                            <!-- Position -->
                                            <td><?= htmlspecialchars($publicSector['ministry'] ?? 'N/A') ?></td>
                                            <!-- Equivalent -->
                                            <td><?= htmlspecialchars($publicSector['economy'] ?? 'N/A') ?></td>
                                            <!-- Economy/Unit -->
                                            <td><?= htmlspecialchars($publicSector['position'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($publicSector['other']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ក្នុងវិស័យឯកជន</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <?php if (!empty($userDetails['user_private_sector'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ថ្ងៃ-ខែ​​​-ឆ្នាំ​ ចូល​</th>
                                        <th>ថ្ងៃ-ខែ​​​-ឆ្នាំ ចេញ</th>
                                        <th>ក្រសួង/ស្ថាប័ន</th>
                                        <th>មុខតំណែង</th>
                                        <th>ជំនាញ/បច្ចេកទេស</th>
                                        <th>ផ្សេងៗ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_private_sector'] as $index => $publicSector): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td> <!-- Serial Number -->
                                            <td><?= htmlspecialchars($publicSector['dateIn'] ?? 'N/A') ?></td> <!-- Date -->
                                            <td><?= htmlspecialchars($publicSector['dateOut'] ?? 'N/A') ?></td>
                                            <!-- Equivalent -->
                                            <td><?= htmlspecialchars($publicSector['economy'] ?? 'N/A') ?></td>
                                            <!-- Economy/Unit -->
                                            <td><?= htmlspecialchars($publicSector['position'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($publicSector['tecnology'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($publicSector['other']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- certificate  -->
    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
        <div class="row g-3">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">គ្រឿងឥស្សរិយយស ប័ណ្ណសរសើរ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>
                    <!-- certificate​  -->
                    <?php if (!empty($userDetails['user_certificate'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>លេខឯកសារ​</th>
                                        <th>ការបរិច្ឆេទ</th>
                                        <th>ស្ថាប័ន/អង្គភាព(ស្នើសុំ)</th>
                                        <th>ខ្លឺមសារ</th>
                                        <th>ប្រភេទ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_certificate'] as $index => $certificate): ?>
                                        <?php if (isset($certificate['statusCert']) && $certificate['statusCert'] == '1'): ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($certificate['document'] ?? 'N/A') ?></td>
                                                <!-- Document -->
                                                <td><?= htmlspecialchars($certificate['date'] ?? 'N/A') ?></td> <!-- Date -->
                                                <td><?= htmlspecialchars($certificate['economy'] ?? 'N/A') ?></td>
                                                <!-- Institution/Unit -->
                                                <td><?= htmlspecialchars($certificate['decription'] ?? 'N/A') ?></td>
                                                <!-- Description -->
                                                <td><?= htmlspecialchars($certificate['type'] ?? 'N/A') ?></td> <!-- Type -->
                                            </tr>
                                        <?php else: ?>
                                            <div class="text-center">
                                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                                <p class="text-muted">មិនមានទិន្នន័យ</p>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ទណ្ណកម្មវិន័យ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <!-- ទណ្ណកម្មវិន័យ​  -->
                    <?php if (!empty($userDetails['user_certificate']) && $certificate['statusCert'] != '1'): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>លេខឯកសារ​</th>
                                        <th>ការបរិច្ឆេទ</th>
                                        <th>ស្ថាប័ន/អង្គភាព(ស្នើសុំ)</th>
                                        <th>ខ្លឺមសារ</th>
                                        <th>ប្រភេទ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_certificate'] as $index => $certificate): ?>
                                        <?php if (isset($certificate['statusCert']) && $certificate['statusCert'] != '1'): ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($certificate['document'] ?? 'N/A') ?></td>
                                                <!-- Document -->
                                                <td><?= htmlspecialchars($certificate['date'] ?? 'N/A') ?></td> <!-- Date -->
                                                <td><?= htmlspecialchars($certificate['economy'] ?? 'N/A') ?></td>
                                                <!-- Institution/Unit -->
                                                <td><?= htmlspecialchars($certificate['decription'] ?? 'N/A') ?></td>
                                                <!-- Description -->
                                                <td><?= htmlspecialchars($certificate['type'] ?? 'N/A') ?></td> <!-- Type -->
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- education  -->
    <div class="tab-pane fade" id="pills-education" role="tabpanel" aria-labelledby="pills-education-tab">
        <div class="row g-3">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <!-- Education -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">កម្រិតវប្បធម៍ទូទៅ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php if (!empty($userDetails['user_education'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>វគ្គ ឬ​កម្រិតសិក្សា</th>
                                        <th>គ្រឺស្ថានសិក្សាបណ្តោះបណ្តាល </th>
                                        <th>សញ្ញាបត្រដែលទទួលបាន</th>
                                        <th>កាលបរិច្ឆេទចូលសិក្សា</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់ការសិក្សា</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $index = 0; // Initialize the serial number
                                    $hasValidData = false; // Flag to check if any data matches the condition
                                    foreach ($userDetails['user_education'] as $education): ?>
                                        <?php if (isset($education['status']) && $education['status'] == '1'): ?>
                                            <?php $hasValidData = true; // Set the flag if a matching record is found ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($education['level'] ?? 'N/A') ?></td> <!-- Level -->
                                                <td><?= htmlspecialchars($education['education_intitution'] ?? 'N/A') ?></td>
                                                <!-- Institution -->
                                                <td><?= htmlspecialchars($education['cetificate'] ?? 'N/A') ?></td>
                                                <!-- Certificate -->
                                                <td><?= htmlspecialchars($education['start_date'] ?? 'N/A') ?></td>
                                                <!-- Start Date -->
                                                <td><?= htmlspecialchars($education['end_date'] ?? 'N/A') ?></td> <!-- End Date -->
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (!$hasValidData): ?> <!-- If no matching records were found -->
                                        <tr>
                                            <td colspan="6" class="text-center">មិនមានទិន្នន័យ</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Certificate -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">កម្រិតសញ្ញាបត្រ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php if (!empty($userDetails['user_education'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>វគ្គ ឬ​កម្រិតសិក្សា</th>
                                        <th>គ្រឺស្ថានសិក្សាបណ្តោះបណ្តាល </th>
                                        <th>សញ្ញាបត្រដែលទទួលបាន</th>
                                        <th>កាលបរិច្ឆេទចូលសិក្សា</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់ការសិក្សា</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $index = 0; // Initialize the serial number
                                    $hasValidData = false; // Flag to check if any data matches the condition
                                    foreach ($userDetails['user_education'] as $education): ?>
                                        <?php if (isset($education['status']) && $education['status'] == '2'): ?>
                                            <?php $hasValidData = true; // Set the flag if a matching record is found ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($education['level'] ?? 'N/A') ?></td> <!-- Level -->
                                                <td><?= htmlspecialchars($education['education_intitution'] ?? 'N/A') ?></td>
                                                <!-- Institution -->
                                                <td><?= htmlspecialchars($education['cetificate'] ?? 'N/A') ?></td>
                                                <!-- Certificate -->
                                                <td><?= htmlspecialchars($education['start_date'] ?? 'N/A') ?></td>
                                                <!-- Start Date -->
                                                <td><?= htmlspecialchars($education['end_date'] ?? 'N/A') ?></td> <!-- End Date -->
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (!$hasValidData): ?> <!-- If no matching records were found -->
                                        <tr>
                                            <td colspan="6" class="text-center">មិនមានទិន្នន័យ</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Skill -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ជំនាញឯករទេស</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php if (!empty($userDetails['user_education'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>វគ្គ ឬ​កម្រិតសិក្សា</th>
                                        <th>គ្រឺស្ថានសិក្សាបណ្តោះបណ្តាល </th>
                                        <th>សញ្ញាបត្រដែលទទួលបាន</th>
                                        <th>កាលបរិច្ឆេទចូលសិក្សា</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់ការសិក្សា</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $index = 0; // Initialize the serial number
                                    $hasValidData = false; // Flag to check if any data matches the condition
                                    foreach ($userDetails['user_education'] as $education): ?>
                                        <?php if (isset($education['status']) && $education['status'] == '3'): ?>
                                            <?php $hasValidData = true; // Set the flag if a matching record is found ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($education['level'] ?? 'N/A') ?></td> <!-- Level -->
                                                <td><?= htmlspecialchars($education['education_intitution'] ?? 'N/A') ?></td>
                                                <!-- Institution -->
                                                <td><?= htmlspecialchars($education['cetificate'] ?? 'N/A') ?></td>
                                                <!-- Certificate -->
                                                <td><?= htmlspecialchars($education['start_date'] ?? 'N/A') ?></td>
                                                <!-- Start Date -->
                                                <td><?= htmlspecialchars($education['end_date'] ?? 'N/A') ?></td> <!-- End Date -->
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (!$hasValidData): ?> <!-- If no matching records were found -->
                                        <tr>
                                            <td colspan="6" class="text-center">មិនមានទិន្នន័យ</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Practice -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">វគ្គបណ្តុះបណ្តាលវិជ្ជាជីវៈ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php if (!empty($userDetails['user_education'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>វគ្គ ឬ​កម្រិតសិក្សា</th>
                                        <th>គ្រឺស្ថានសិក្សាបណ្តោះបណ្តាល </th>
                                        <th>សញ្ញាបត្រដែលទទួលបាន</th>
                                        <th>កាលបរិច្ឆេទចូលសិក្សា</th>
                                        <th>កាលបរិច្ឆេទបញ្ចប់ការសិក្សា</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $index = 0; // Initialize the serial number
                                    $hasValidData = false; // Flag to check if any data matches the condition
                                    foreach ($userDetails['user_education'] as $education): ?>
                                        <?php if (isset($education['status']) && $education['status'] == '4'): ?>
                                            <?php $hasValidData = true; // Set the flag if a matching record is found ?>
                                            <tr>
                                                <td><?= ++$index ?></td> <!-- Increment serial number -->
                                                <td><?= htmlspecialchars($education['level'] ?? 'N/A') ?></td> <!-- Level -->
                                                <td><?= htmlspecialchars($education['education_intitution'] ?? 'N/A') ?></td>
                                                <!-- Institution -->
                                                <td><?= htmlspecialchars($education['cetificate'] ?? 'N/A') ?></td>
                                                <!-- Certificate -->
                                                <td><?= htmlspecialchars($education['start_date'] ?? 'N/A') ?></td>
                                                <!-- Start Date -->
                                                <td><?= htmlspecialchars($education['end_date'] ?? 'N/A') ?></td> <!-- End Date -->
                                            </tr>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if (!$hasValidData): ?> <!-- If no matching records were found -->
                                        <tr>
                                            <td colspan="6" class="text-center">មិនមានទិន្នន័យ</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- family  -->
    <div class="tab-pane fade" id="pills-language" role="tabpanel" aria-labelledby="pills-language-tab">
        <div class="row g-3">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <!-- Father & Mother -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ព័ត៌មានឪពុកម្តាយ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Gender -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">
                                        ឈ្មោះឪពុក :
                                    </dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Date of Birth -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ជាអក្សរឡាតាំង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_name_in_english']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាព :</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_status']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card Expiry -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ថ្ងៃខែឆ្នាំកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_date']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">សញ្ជាតិ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_national']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport Expiry -->
                            <!-- Nationality -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">មុខរបរ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['father_job']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថាប័ន/អង្គភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['f_institute']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['m_address']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីលំនៅបច្ចុប្បន្ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['f_current_residence']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="hr mt-0"></div>
                            <!-- Marital Status -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ឈ្មោះម្តាយ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Place of Birth -->
                            <div class="col-4 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ជាអក្សរឡាតាំង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_name_in_english']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Current Address -->
                            <div class="col-4 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_status']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ថ្ងៃខែឆ្នាំកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_date']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">សញ្ជាតិ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_national']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport Expiry -->
                            <!-- Nationality -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">មុខរបរ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['mother_job']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថាប័ន/អង្គភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['m_institute']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['f_address']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីលំនៅបច្ចុប្បន្ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['m_current_residence']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Father & Mother -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ព័ត៌មានសហព័ន្ធ</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <div class="card-body">
                        <div class="row">
                            <!-- Gender -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">
                                        ឈ្មោះប្តី​ ឬ​ ប្រពន្ធ :
                                    </dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_name']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Date of Birth -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ជាអក្សរឡាតាំង:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_name_in_english']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាព :</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_status']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Passport -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">មុខរបរ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_job']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Nationality -->
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាព/អង្គភាព:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_institute']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ប្រាក់ឧបត្ថម្ភ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_allowance']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">លេខទូរស័ព្ទ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_phone_number']) ?>
                                    </p>
                                </div>
                            </div>
                            <!-- Identity Card Expiry -->
                            <div class="col-lg-12 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($userDetails['federation_current_residence']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- បងប្អូន -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ព័ត៌មានបងប្អូន</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php
                    // Unserialize or decode each field if necessary
                    function safe_unserialize($data)
                    {
                        return @unserialize($data) ?: [];
                    }

                    $relativeNames = safe_unserialize($userDetails['relative_name']);
                    $relativeNamesEnglish = safe_unserialize($userDetails['relative_name_in_english']);
                    $relativeGenders = safe_unserialize($userDetails['relative_gender']);
                    $relativeJobs = safe_unserialize($userDetails['relative_job']);
                    $relativeDates = safe_unserialize($userDetails['relative_date']);

                    // Ensure all arrays are aligned by index (handle missing data gracefully)
                    $rows = max(
                        count($relativeNames),
                        count($relativeNamesEnglish),
                        count($relativeGenders),
                        count($relativeJobs),
                        count($relativeDates)
                    );

                    // Display in a table
                    ?>
                    <table class="table table-vcenter table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>គោត្តនាម និង នាម</th>
                                <th>ជាអក្សរឡាតាំង</th>
                                <th>ភេទ</th>
                                <th>ថ្ងៃខែឆ្នាំកំណើត</th>
                                <th>មុខរបរ(អង្គភាព)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < $rows; $i++): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($relativeNames[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeNamesEnglish[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeGenders[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeJobs[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeDates[$i] ?? 'N/A') ?></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>

                <!-- បងប្អូន -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ព័ត៌មានកូន</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php

                    $relativeNames = safe_unserialize($userDetails['children_name']);
                    $relativeNamesEnglish = safe_unserialize($userDetails['children_name_in_english']);
                    $relativeGenders = safe_unserialize($userDetails['children_gender']);
                    $relativeJobs = safe_unserialize($userDetails['children_job']);
                    $relativeAllowance = safe_unserialize($userDetails['children_allowance']);
                    $relativeDates = safe_unserialize($userDetails['children_date']);

                    // Ensure all arrays are aligned by index (handle missing data gracefully)
                    $rows = max(
                        count($relativeNames),
                        count($relativeNamesEnglish),
                        count($relativeGenders),
                        count($relativeJobs),
                        count($relativeAllowance),
                        count($relativeDates)
                    );

                    // Display in a table
                    ?>
                    <table class="table table-vcenter table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>គោត្តនាម និង នាម</th>
                                <th>ជាអក្សរឡាតាំង</th>
                                <th>ភេទ</th>
                                <th>ថ្ងៃខែឆ្នាំកំណើត</th>
                                <th>មុខរបរ(អង្គភាព)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for ($i = 0; $i < $rows; $i++): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td><?= htmlspecialchars($relativeNames[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeNamesEnglish[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeGenders[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeJobs[$i] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($relativeDates[$i] ?? 'N/A') ?></td>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- education  -->
    <div class="tab-pane fade" id="pills-document" role="tabpanel" aria-labelledby="pills-document-tab">
        <div class="row g-3">
            <div class="col-lg-12 col-sm-12 col-md-12">
                <!-- Education -->
                <div class="card mb-3">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="text-primary mb-0">ឯកសារផ្សេងៗរបស់មន្រ្តី</h3>
                        </div>

                        <a href="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                <path d="M13.5 6.5l4 4" />
                            </svg>
                        </a>
                    </div>

                    <?php if (!empty($userDetails['user_document'])): ?>
                        <div class="table-responsive">
                            <table class="table vcenter">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>ប្រភេទឯកសារ</th>
                                        <th>បរិយាយឈ្មោះឯកសារ</th>
                                        <th>ឯកសារដែលបានភ្ជាប់</th>
                                        <th>កាលបរិច្ឆទភ្ជាប់</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userDetails['user_document'] as $index => $document): ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td> <!-- Increment serial number -->
                                            <td><?= htmlspecialchars($document['document_type'] ?? 'N/A') ?></td> <!-- Level -->
                                            <td><?= htmlspecialchars($document['description'] ?? 'N/A') ?></td>
                                            <!-- Institution -->
                                            <td><a target="_blank" href="<?= htmlspecialchars($document['document_file'] ?? 'N/A') ?>">view</a></td>
                                            <!-- Certificate -->
                                            <td><?= htmlspecialchars($document['date'] ?? 'N/A') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <img src="public/img/icons/svgs/empty.svg" alt="">
                            <p class="text-muted">មិនមានទិន្នន័យ</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal to change profile picture -->
<div class="modal modal-blur fade" id="editModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">ផ្លាស់ប្តូររូបភាព</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/change-profile-picture" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl rounded border border-primary" id="preview"
                            style="background-image: url('<?= $userDetails['profile_picture'] ?>'); background-size: cover; background-position: center;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-center">
                        <label class="btn btn-outline-primary py-2 px-3 mb-3" style="cursor: pointer;">
                            ជ្រើសរើសរូបភាព
                            <input type="file" name="profile_picture" accept="image/*" hidden
                                onchange="showPreview(event, 'preview')">
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">ផ្លាស់ប្តូរ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

<script>
    function showPreview(event, previewId) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById(previewId).style.backgroundImage = `url(${e.target.result})`;
                document.getElementById(previewId).style.backgroundSize = 'cover';
                document.getElementById(previewId).style.backgroundPosition = 'center';
                document.getElementById('previewContainer').classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    }
</script>