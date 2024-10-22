<?php
$title = "លិខិតព្យួរ";
require_once 'src/common/header.php';
?>

<!-- Page header -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo $title ?? "" ?>
                </h2>
            </div>
            <div class="col text-end ms-auto">
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#hold">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    <span>បង្កើតលិខិតព្យួរ</span>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="row row-card">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills card-header-pills">
                        <li class="nav-item">
                            <h3 class="mb-0 text-primary"><span><?= $title ?></span></h3>
                        </li>
                    </ul>
                </div>
                <!-- Table -->
                <?php if (empty($getHolds)): ?>
                    <div class="text-center">
                        <img src="public/img/icons/svgs/empty.svg" alt="">
                        <p class="text-primary fw-bold">មិនមានលិខិតព្យួរ។</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th class="d-xl-none d-xl-table-cell"></th>
                                    <th class="d-none d-xl-table-cell">ល.រ</th>
                                    <th>ប្រភេទលិខិត</th>
                                    <th class="text-center d-none d-xl-table-cell">កាលបរិច្ឆេទ</th>
                                    <th class="d-none d-xl-table-cell">រយៈពេល</th>
                                    <th class="d-none d-xl-table-cell">ឯកសារភ្ជាប់</th>
                                    <th class="d-none d-xl-table-cell">មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                    <th class="w-1 d-none d-xl-table-cell">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getHolds as $key => $hold): ?>
                                    <tr>
                                        <td class="d-none d-xl-table-cell"><?= $key + 1 ?></td>
                                        <td class="d-xl-none d-xl-table-cell">
                                            <div class="d-flex">
                                                <!-- Plus button for showing hidden data on small screens -->
                                                <button class="btn d-sm-none btn-link p-0 me-2" data-bs-toggle="collapse"
                                                    data-bs-target="#details<?= $hold['id'] ?>" aria-expanded="false"
                                                    aria-controls="details<?= $hold['id'] ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icon-tabler-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                        <path d="M12 5v14m-7 -7h14"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <!-- Collapsed content for small screens -->
                                            <div id="details<?= $hold['id'] ?>" class="collapse d-sm-block d-xl-none mt-2">
                                                <div class="mb-1"><strong>រយៈពេល:</strong> <?= $hold['duration'] ?>
                                                </div>
                                                <div class="mb-1"><strong>កាលបរិច្ឆេទចាប់ពី:</strong> <?= $hold['start_date'] ?>
                                                </div>
                                                <div class="mb-1"><strong>ដល់កាលបរិច្ឆេទ:</strong> <?= $hold['end_date'] ?>
                                                </div>
                                                <div class="mb-1"><strong>មូលហេតុ:</strong> <?= $hold['reason'] ?></div>
                                                <div class="mb-1"><strong>ស្នើនៅ:</strong> <?= $hold['created_at'] ?></div>
                                                <div class="mb-0">
                                                    <?php if ($hold['status'] == 'Approved'): ?>
                                                        <a href="#" onclick="printContents(<?= $hold['id'] ?>)"
                                                            class="icon me-2 edit-btn text-danger" data-bs-toggle="tooltip"
                                                            title="Print" data-bs-target="#edit<?= $hold['id'] ?>">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path
                                                                    d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                                                <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                                                <path
                                                                    d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                                            </svg>
                                                        </a>
                                                        <a href="#"
                                                            onclick="Export2Word('page-contents<?= $hold['id'] ?>', 'word-content<?= $hold['id'] ?>');"
                                                            class="icon me-2 edit-btn" data-bs-toggle="tooltip"
                                                            title="Export to Word">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                                <path d="M7 11l5 5l5 -5" />
                                                                <path d="M12 4l0 12" />
                                                            </svg>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="#" class="icon me-2 edit-btn text-danger"
                                                            data-bs-target="#deleted<?= htmlspecialchars($hold['id']) ?>"
                                                            data-bs-toggle="modal">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M4 7l16 0" />
                                                                <path d="M10 11l0 6" />
                                                                <path d="M14 11l0 6" />
                                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                            </svg>
                                                        </a>

                                                        <!-- delete hold modal  -->
                                                        <div class="modal modal-blur fade"
                                                            id="deleted<?= htmlspecialchars($hold['id']) ?>" tabindex="-1"
                                                            role="dialog" aria-hidden="true">
                                                            <div class="modal-dialog modal-sm modal-dialog-centered"
                                                                role="document">
                                                                <div class="modal-content">
                                                                    <div class="modal-status bg-danger"></div>
                                                                    <form action="/elms/delete-hold" method="POST">
                                                                        <div class="modal-body text-center py-4 mb-0">
                                                                            <input type="hidden" name="id"
                                                                                value="<?= htmlspecialchars($hold['id']) ?>">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                                stroke="currentColor" stroke-width="2"
                                                                                stroke-linecap="round" stroke-linejoin="round"
                                                                                class="icon mb-2 text-danger icon-lg">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                                </path>
                                                                                <path d="M12 9v4"></path>
                                                                                <path
                                                                                    d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                                </path>
                                                                                <path d="M12 16h.01"></path>
                                                                            </svg>
                                                                            <h5 class="modal-title fw-bold text-danger">លុបសំណើ
                                                                            </h5>
                                                                            <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបសំណើ <span
                                                                                    class="text-red fw-bold"><?= $title ?></span>
                                                                                នេះ?
                                                                            </p>
                                                                        </div>
                                                                        <div class="modal-footer bg-light">
                                                                            <div class="w-100">
                                                                                <div class="row">
                                                                                    <div class="col">
                                                                                        <button type="button" class="btn w-100"
                                                                                            data-bs-dismiss="modal">បោះបង់</button>
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <button type="submit"
                                                                                            class="btn btn-danger ms-auto w-100">យល់ព្រម
                                                                                        </button>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-secondary">
                                            <?php if ($hold['type'] === 'hold'): ?>
                                                <span class="badge <?= $hold['color'] ?>">លិខិតព្យួរ</span>
                                            <?php elseif ($hold['type'] === 'resign'): ?>
                                                <span class="badge <?= $hold['color'] ?>">លិខិតលារឈប់</span>
                                            <?php elseif ($hold['type'] === 'transferout'): ?>
                                                <span class="badge <?= $hold['color'] ?>">លិខិតផ្ទេរចេញ</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">លិខិតចូលបម្រើការងារវិញ</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary text-center d-none d-xl-table-cell">
                                            <?= $hold['start_date'] . " ~ " . $hold['end_date'] ?>
                                        </td>
                                        <td class="text-secondary d-none d-xl-table-cell"><?= $hold['duration'] ?></td>
                                        <td class="text-secondary d-none d-xl-table-cell">
                                            <?php if (!empty($hold['attachment'])): ?>
                                                <a href="public/uploads/hold-attachments/<?= $hold['attachment'] ?>"
                                                    target="blank_">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                                                    </svg>
                                                    ឯកសារភ្ជាប់</a>
                                            <?php else: ?>
                                                មិនមានឯកសារភ្ជាប់
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary d-none d-xl-table-cell"><?= $hold['reason'] ?></td>
                                        <td class="text-secondary">
                                            <?php if ($hold['status'] === 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($hold['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="d-none d-xl-table-cell">
                                            <div class="d-flex">
                                                <a href="/elms/view&edit-hold?holdId=<?= $hold['id'] ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-eye">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                        <path
                                                            d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                    </svg>
                                                </a>
                                                <a href="#" data-bs-toggle="modal"
                                                    data-bs-target="#delete<?= htmlspecialchars($hold['id']) ?>"
                                                    class="mx-2 text-red">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M4 7l16 0" />
                                                        <path d="M10 11l0 6" />
                                                        <path d="M14 11l0 6" />
                                                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                    </svg>
                                                </a>

                                                <!-- delete hold modal  -->
                                                <div class="modal modal-blur fade"
                                                    id="delete<?= htmlspecialchars($hold['id']) ?>" tabindex="-1" role="dialog"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-status bg-danger"></div>
                                                            <form action="/elms/delete-hold" method="POST">
                                                                <div class="modal-body text-center py-4 mb-0">
                                                                    <input type="hidden" name="id"
                                                                        value="<?= htmlspecialchars($hold['id']) ?>">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                        height="24" viewBox="0 0 24 24" fill="none"
                                                                        stroke="currentColor" stroke-width="2"
                                                                        stroke-linecap="round" stroke-linejoin="round"
                                                                        class="icon mb-2 text-danger icon-lg">
                                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none">
                                                                        </path>
                                                                        <path d="M12 9v4"></path>
                                                                        <path
                                                                            d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                                        </path>
                                                                        <path d="M12 16h.01"></path>
                                                                    </svg>
                                                                    <h5 class="modal-title fw-bold text-danger">លុបសំណើ
                                                                    </h5>
                                                                    <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបសំណើ <span
                                                                            class="text-red fw-bold"><?= $title ?></span> នេះ?
                                                                    </p>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <div class="w-100">
                                                                        <div class="row">
                                                                            <div class="col">
                                                                                <button type="button" class="btn w-100"
                                                                                    data-bs-dismiss="modal">បោះបង់</button>
                                                                            </div>
                                                                            <div class="col">
                                                                                <button type="submit"
                                                                                    class="btn btn-danger ms-auto w-100">យល់ព្រម
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
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Logic -->
                    <div class="card-footer">
                        <?php
                        $totalRecords = $holdModel->getHoldCounts(); // Use the method to get total records
                        $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages
                        ?>

                        <ul class="pagination justify-content-end mb-0">
                            <li class="page-item <?= $currentPage == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $currentPage - 1 ?>" tabindex="-1"
                                    aria-disabled="true">
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
            </div>
        </div>
    </div>
</div>
<?php include('src/common/footer.php'); ?>