<?php
$pretitle = "ទំព័រដើម";
$title = "ព័ត៌មានគណនី";
include('src/common/header.php');
?>

<div class="card mb-3 p-2">
    <ul class="nav nav-pills" id="pills-tab" role="tablist">
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
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile"
                type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Profile</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill" data-bs-target="#pills-contact"
                type="button" role="tab" aria-controls="pills-contact" aria-selected="false">Contact</button>
        </li>
    </ul>
</div>

<div class="tab-content mb-3" id="pills-tabContent">
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
                                            src="<?= $userDetails['profile_picture'] ?>" style="object-fit: cover;" alt="">
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
                                        <h1 class="mb-2"><?= $_SESSION['user_khmer_name'] ?></h1>
                                        <p class="mb-2">
                                            <span
                                                class="badge <?= $_SESSION['position_color'] ?>"><?= $_SESSION['position'] ?>
                                            </span>
                                            | <?= $_SESSION['departmentName'] ?>
                                        </p>

                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-7 d-flex align-items-center justify-content-center mb-0">
                                <div class="row g-3">
                                    <!-- Row 1 -->
                                    <div class="row g-3">
                                        <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                            <div class="col-auto text-start">
                                                <dd class="text-muted">លេខកាត:</dd>
                                            </div>
                                            <div class="col-auto text-start">
                                                <p class="fw-bold">
                                                    <?= htmlspecialchars($_SESSION['idCard']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                            <div class="col-auto text-start">
                                                <dd class="text-muted">ទំនាក់ទំនង:</dd>
                                            </div>
                                            <div class="col-auto text-start">
                                                <p class="fw-bold">
                                                    <?= htmlspecialchars($_SESSION['contact']) ?>
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
                                                    <?= htmlspecialchars($_SESSION['email']) ?>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12 col-md-12 d-flex justify-content-between">
                                            <div class="col-auto text-start">
                                                <dd class="text-muted">ឈ្មោះជាអក្សរឡាតាំង:</dd>
                                            </div>
                                            <div class="col-auto text-start">
                                                <p class="fw-bold">
                                                    <?= htmlspecialchars($_SESSION['user_eng_name']) ?>
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

            <!-- personal information  -->
            <div class="col-lg-4 col-sm-12 col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="mb-0">ព័ត៌មានផ្ទាល់ខ្លួន</h3>
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
                                    <dd class="text-muted">ភេទ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?php
                                        // Display gender based on the value in $_SESSION['gender']
                                        if ($_SESSION['gender'] === 'm') {
                                            echo 'ប្រុស'; // Male
                                        } else {
                                            echo 'ស្រី'; // Female
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ថ្ងៃខែឆ្នាំកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['dob']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អត្តសញ្ញាណបណ្ណ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['identifyCard']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['exprireDateIdenCard']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">លិខិតឆ្លងដែន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['passport']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['exprirePassport']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">សញ្ជាតិ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['nationality']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាពគ្រួសារ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?php
                                        if ($_SESSION['status'] === '1') {
                                            echo 'នៅលីវ'; // sigle
                                        } elseif ($_SESSION['status'] === '2') {
                                            echo 'រៀបការ'; // maried
                                        } else {
                                            echo 'មានគ្រួសារ';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['pobAddress']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អាសយដ្ឋានបច្ចុប្បន្ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['currentAddress']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 col-sm-12 col-md-8">
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="card-tittle">
                            <h3 class="mb-0">ព័ត៌មានផ្ទាល់ខ្លួន</h3>
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
                                    <dd class="text-muted">ភេទ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?php
                                        // Display gender based on the value in $_SESSION['gender']
                                        if ($_SESSION['gender'] === 'm') {
                                            echo 'ប្រុស'; // Male
                                        } else {
                                            echo 'ស្រី'; // Female
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ថ្ងៃខែឆ្នាំកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['dob']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អត្តសញ្ញាណបណ្ណ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['identifyCard']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['exprireDateIdenCard']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">លិខិតឆ្លងដែន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['passport']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">កាលបរិច្ឆេទផុតកំណត់:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['exprirePassport']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">សញ្ជាតិ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['nationality']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-lg-6 col-sm-12 col-md-6 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ស្ថានភាពគ្រួសារ:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['dob']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">ទីកន្លែងកំណើត:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['pobAddress']) ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-12 d-flex flex-column justify-content-between">
                                <div class="col-auto text-start">
                                    <dd class="text-muted">អាសយដ្ឋានបច្ចុប្បន្ន:</dd>
                                </div>
                                <div class="col-auto text-start">
                                    <p class="fw-bold">
                                        <?= htmlspecialchars($_SESSION['currentAddress']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">...</div>
    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">...</div>
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

<?php include('src/common/footer.php'); ?>