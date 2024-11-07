<?php
$title = "លិខិតបន្តការងារ";
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
                <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#backwork">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 5l0 14" />
                        <path d="M5 12l14 0" />
                    </svg>
                    <span>បង្កើត<?= $title ?></span>
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
                        <p class="text-primary fw-bold">មិនមានលិខិតបន្តការងារ។</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>ល.រ</th>
                                    <th>ប្រភេទលិខិត</th>
                                    <th class="text-center">កាលបរិច្ឆេទ</th>
                                    <th>រយៈពេល</th>
                                    <th>ឯកសារភ្ជាប់</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                    <th class="w-1">សកម្មភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getHolds as $key => $hold): ?>
                                    <tr>
                                        <td><?= $key + 1 ?></td>
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
                                        <td class="text-secondary text-center">
                                            <?= $hold['start_date'] . " ~ " . $hold['end_date'] ?>
                                        </td>
                                        <td class="text-secondary"><?= $hold['duration'] ?></td>
                                        <td class="text-secondary">
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
                                        <td class="text-secondary"><?= $hold['reason'] ?></td>
                                        <td class="text-secondary">
                                            <?php if ($hold['status'] === 'approved'): ?>
                                                <span class="badge bg-success">អនុម័ត</span>
                                            <?php elseif ($hold['status'] === 'pending'): ?>
                                                <span class="badge bg-warning">រង់ចាំអនុម័ត</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
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
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
<?php include('src/common/footer.php'); ?>