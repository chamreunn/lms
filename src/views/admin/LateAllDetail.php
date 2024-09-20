<?php
$title = "ព័ត៌មានលម្អិត";
include('src/common/header.php');
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div>ទំព័រដើម</div>
                </div>
                <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row row-card">
    <div class="col-xl-4 mb-3">
        <div class="card">
            <div class="card-body d-flex flex-column align-items-center">
                <div class="text-center text-primary">
                    <img class="avatar avatar-lg mb-3" src="<?= $detail['profile_picture'] ?>" alt="">
                    <h4 class="mb-0"><?= $detail['khmer_name'] ?></h4>
                    <small class="text-secondary"><?= $detail['role'] ?></small>
                </div>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-header sticky-top">ទំនាក់ទនង</div>
                <li class="list-group-item d-flex align-items-center">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-phone-outgoing text-secondary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2c-8.072 -.49 -14.51 -6.928 -15 -15a2 2 0 0 1 2 -2" />
                            <path d="M15 5h6" />
                            <path d="M18.5 7.5l2.5 -2.5l-2.5 -2.5" />
                        </svg>
                        <strong class="mx-1">លេខទូរស័ព្ទ : </strong>
                    </div>
                    <div class="ms-auto"><?= $detail['contact'] ?></div>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-mail text-secondary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                            <path d="M3 7l9 6l9 -6" />
                        </svg>
                        <strong class="mx-1">អាសយដ្ឋានអ៊ីម៉ែល : </strong>
                    </div>
                    <a href="mailto:<?= $detail['email'] ?>" class="text-primary ms-auto"><?= $detail['email'] ?></a>
                </li>
                <li class="list-group-item d-flex align-items-center">
                    <div class="d-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin text-secondary">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                            <path
                                d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                        </svg>
                        <strong class="mx-1">អាសយដ្ឋានបច្ចុប្បន្ន : </strong>
                    </div>
                    <div class="ms-auto"><?= $detail['address'] ?></div>
                </li>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h3 class="card-title mb-0">ព័ត៌មានលម្អិត</h3>
                <!-- Check for status, display success badge if approved -->
                <?php if ($detail['status'] === 'Approved'): ?>
                    <div class="badge bg-success">
                        <?= $detail['status'] ?>
                    </div>
                <?php else: ?>
                    <div class="badge bg-danger">
                        <?= $detail['status'] ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form action="" class="mb-3">
                    <div class="row">
                        <div class="col-xl-6 mb-3">
                            <label class="form-label fw-bold">កាលបរិច្ឆេទ</label>
                            <input type="text" class="form-control" value="<?= $detail['date'] ?>" disabled>
                        </div>

                        <!-- Display the time details only if they are not null -->
                        <?php if (!is_null($detail['late_in'])): ?>
                            <div class="col-xl-6 mb-3">
                                <label class="form-label fw-bold">ម៉ោងចូល</label>
                                <input type="text" class="form-control" value="<?= $detail['late_in'] ?> នាទី" disabled>
                            </div>
                        <?php endif; ?>

                        <?php if (!is_null($detail['late_out'])): ?>
                            <div class="col-xl-6 mb-3">
                                <label class="form-label fw-bold">ម៉ោងចេញ</label>
                                <input type="text" class="form-control" value="<?= $detail['late_out'] ?> នាទី" disabled>
                            </div>
                        <?php endif; ?>

                        <?php if (!is_null($detail['leave_early'])): ?>
                            <div class="col-xl-6 mb-3">
                                <label class="form-label fw-bold">ចេញមុន</label>
                                <input type="text" class="form-control" value="<?= $detail['leave_early'] ?> នាទី" disabled>
                            </div>
                        <?php endif; ?>

                        <div class="col-xl-12 mb-3 text-success">
                            <label class="form-label fw-bold">រយៈពេលយឺត</label>
                            <input type="text" class="form-control text-success border-success"
                                value="<?= $detail['late'] ?> នាទី" disabled>
                        </div>

                        <div class="col-xl-12">
                            <label class="form-label fw-bold">មូលហេតុ</label>
                            <textarea type="text" class="form-control" disabled><?= $detail['reasons'] ?></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

<?php include('src/common/footer.php'); ?>