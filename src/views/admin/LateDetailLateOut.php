<?php
$title = "កែប្រែព័ត៌មានគណនី";
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
    <div class="col-xl-4">
        <div class="card">
            <div class="card-body">
                <div class="text-center text-primary">
                    <img class="avatar avatar-lg mb-3" src="<?= $detail['profile_picture'] ?>" alt="">
                    <h4 class="mb-0"><?= $detail['khmer_name'] ?></h4>
                    <small class="text-secondary"><?= $detail['role'] ?></small>
                </div>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-header sticky-top">ទំនាក់ទនង</div>
                <li class="list-group-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-phone-outgoing text-secondary">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path
                            d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2c-8.072 -.49 -14.51 -6.928 -15 -15a2 2 0 0 1 2 -2" />
                        <path d="M15 5h6" />
                        <path d="M18.5 7.5l2.5 -2.5l-2.5 -2.5" />
                    </svg>
                    <strong>លេខទូរស័ព្ទ : </strong><?= $detail['contact'] ?>
                </li>
                <li class="list-group-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-mail text-secondary">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                        <path d="M3 7l9 6l9 -6" />
                    </svg>
                    <strong>អាសយដ្ឋានអ៊ីម៉ែល : </strong><a href="mailto:<?= $detail['email'] ?>"
                        class="text-primary"><?= $detail['email'] ?></a>
                </li>
                <li class="list-group-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-map-pin text-secondary">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
                    </svg>
                    <strong>អាសយដ្ឋានបច្ចុប្បន្ន : </strong><?= $detail['address'] ?>
                </li>
            </div>
        </div>
    </div>
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">ព័ត៌មានលម្អិត</h3>
            </div>
            <div class="card-body">
                <form action="" class="mb-3">
                    <div class="row">
                        <div class="col-xl-6 mb-3">
                            <label class="form-label fw-bold">កាលបរិច្ឆេទ</label>
                            <input type="text" class="form-control" value="<?= $detail['date'] ?>" disabled>
                        </div>
                        <div class="col-xl-6 mb-3">
                            <label class="form-label fw-bold">ម៉ោងចូល</label>
                            <input type="text" class="form-control" value="<?= $detail['late_out'] ?> នាទី" disabled>
                        </div>
                        <div class="col-xl-12 mb-3 text-red">
                            <label class="form-label fw-bold">រយៈពេលយឺត</label>
                            <input type="text" class="form-control text-red border-red"
                                value="<?= $detail['late'] ?> នាទី" disabled>
                        </div>
                        <div class="col-xl-12">
                            <label class="form-label fw-bold">មូលហេតុ</label>
                            <textarea type="text" class="form-control" disabled><?= $detail['reasons'] ?></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <div class="card-footer">
                <div class="row w-50 ms-auto">
                    <div class="col">
                        <button type="button" class="btn btn-success w-100"
                            data-bs-target="#approved<?= $detail['id'] ?>" data-bs-toggle="modal">Approved</button>
                    </div>
                    <div class="col">
                        <button class="btn btn-danger w-100" data-bs-target="#rejected<?= $detail['id'] ?>"
                            data-bs-toggle="modal">Rejected</button>
                    </div>
                </div>
            </div>

            <!-- Approved  -->
            <div class="modal modal-blur fade" id="approved<?= $detail['id'] ?>" tabindex="-1" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-success"></div>
                        <form action="/elms/actionLateOut" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="lateId" value="<?= $detail['id'] ?>">
                            <input type="hidden" name="uId" value="<?= $detail['user_id'] ?>">
                            <input type="hidden" name="checkOut" value="<?= $detail['date'] ?>">
                            <input type="hidden" name="lateOut" value="<?= $detail['late_out'] ?>">
                            <input type="hidden" name="uEmail" value="<?= $detail['email'] ?>">
                            <input type="hidden" name="status" value="Approved">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon mb-2 text-green icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M9 12l2 2l4 -4"></path>
                                </svg>
                                <h3 class="text-success fw-bolder">អនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-success fw-bolder">បន្ត</span>
                                    ដើម្បីអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-green w-100" data-bs-toggle="collapse" href="#approved" role="button"
                                    aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="approved">
                                    <input name="remarks" class="form-control" list="datalistOptions"
                                        placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="អាចឈប់សម្រាកបាន"></option>
                                    </datalist>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                                បោះបង់
                                            </a>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-success w-100">
                                                បន្ត
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Rejected  -->
            <div class="modal modal-blur fade" id="rejected<?= $detail['id'] ?>" tabindex="-1" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-danger"></div>
                        <form action="/elms/actionLate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="lateId" value="<?= $detail['id'] ?>">
                            <input type="hidden" name="uId" value="<?= $detail['user_id'] ?>">
                            <input type="hidden" name="checkIn" value="<?= $detail['date'] ?>">
                            <input type="hidden" name="lateIn" value="<?= $detail['late_in'] ?>">
                            <input type="hidden" name="uEmail" value="<?= $detail['email'] ?>">
                            <input type="hidden" name="status" value="Rejected">
                            <div class="modal-body text-center py-4">
                                <!-- Download SVG icon from http://tabler-icons.io/i/circle-check -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="icon mb-2 text-green icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path>
                                    <path d="M9 12l2 2l4 -4"></path>
                                </svg>
                                <h3 class="text-danger fw-bolder">អនុម័ត</h3>
                                <div class="text-secondary mb-3">សូមចុច <span class="text-danger fw-bolder">បន្ត</span>
                                    ដើម្បីអនុម័តច្បាប់ឈប់សម្រាកនេះ។</div>
                                <a class="btn text-green w-100" data-bs-toggle="collapse" href="#approved" role="button"
                                    aria-expanded="false" aria-controls="multiCollapseExample1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="currentColor"
                                        class="icon icon-tabler icons-tabler-filled icon-tabler-message">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path
                                            d="M18 3a4 4 0 0 1 4 4v8a4 4 0 0 1 -4 4h-4.724l-4.762 2.857a1 1 0 0 1 -1.508 -.743l-.006 -.114v-2h-1a4 4 0 0 1 -3.995 -3.8l-.005 -.2v-8a4 4 0 0 1 4 -4zm-4 9h-6a1 1 0 0 0 0 2h6a1 1 0 0 0 0 -2m2 -4h-8a1 1 0 1 0 0 2h8a1 1 0 0 0 0 -2" />
                                    </svg>
                                    <span>មតិយោបល់</span>
                                </a>
                                <div class="collapse collapse-multiple mt-3" id="approved">
                                    <input name="remarks" class="form-control" list="datalistOptions"
                                        placeholder="សូមបញ្ចូលមតិយោបល់...">
                                    <datalist id="datalistOptions">
                                        <option value="អាចឈប់សម្រាកបាន"></option>
                                    </datalist>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                                បោះបង់
                                            </a>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-success w-100">
                                                បន្ត
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>