<?php
$title = "ច្បាប់ឈប់សម្រាកទាំងអស់";
include('src/common/header.php');
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="col-12">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle mb-1">
                        <div>សំណើចេញ ចូលយឺត</div>
                    </div>
                    <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-xl">
    <div class="row mt-3 mb-3">

        <!-- Main Content Area -->
        <div class="col-12">
            <div class="card shadow-sm">
                <?php if (empty($getAllLeaves)): ?>
                    <div class="card-header bg-light">
                        <h3 class="card-title text-primary mb-0"><?= $title ?></h3>
                    </div>
                    <div class="text-center p-5">
                        <img src="public/img/icons/svgs/empty.svg" alt="No Image" class="img-fluid mb-3"
                            style="width: 100px;">
                        <div class="text-muted h4">មិនទាន់មានសំណើនៅឡើយ។</div>
                    </div>
                <?php else: ?>
                    <div class="card-header bg-light">
                        <h3 class="card-title text-primary mb-0">សំណើថ្ងៃនេះ (<?= date('Y-m-d') ?>)</h3>
                    </div>

                    <!-- Table for Today's Leaves -->
                    <div class="mb-0">
                        <table class="table table-responsive mb-3">
                            <thead>
                                <tr>
                                    <th>ឈ្មោះថ្នាក់ដឹកនាំ និងមន្ត្រី</th>
                                    <th>ប្រភេទច្បាប់</th>
                                    <th>កាលបរិច្ឆេទចាប់ពី</th>
                                    <th>ដល់កាលបរិច្ឆេទ</th>
                                    <th>មូលហេតុ</th>
                                    <th>ស្ថានភាព</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getAllLeaves as $leave): ?>
                                    <?php if (date('Y-m-d') >= $leave['start_date'] && date('Y-m-d') <= $leave['end_date']): ?>
                                        <!-- Display only today's leaves -->
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $leave['profile'] ?>" class="rounded-circle"
                                                        style="width: 50px; height: 50px; object-fit: cover;" alt="">
                                                    <div class="d-flex flex-column mx-2">
                                                        <h6 class="mx-0 mb-1 text-primary"><?= $leave['khmer_name'] ?></h6>
                                                        <span class="text-muted"><?= $leave['email'] ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge <?= $leave['ltColor'] ?>"><?= $leave['leave_type'] ?></span></td>
                                            <td><?= $leave['start_date'] ?></td>
                                            <td><?= $leave['end_date'] ?></td>
                                            <td><?= $leave['remarks'] ?></td>
                                            <td><span class="badge bg-success"><?= $leave['status'] ?></span></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Collapsible Section for Other Dates -->
                    <div class="mb-0">
                        <?php
                        $groupedLeaves = [];
                        foreach ($getAllLeaves as $leave) {
                            if (date('Y-m-d') < $leave['start_date'] || date('Y-m-d') > $leave['end_date']) {
                                $groupedLeaves[$leave['start_date']][] = $leave; // Group by start date
                            }
                        }
                        ?>

                        <?php foreach ($groupedLeaves as $date => $leaves): ?>
                            <!-- Date Grouping with Collapsible Section -->
                            <div class="mb-3">
                                <button class="btn btn-link text-primary" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse<?= $date ?>" aria-expanded="false">
                                    <strong>(<?= $date ?>)</strong>
                                </button>
                                <div id="collapse<?= $date ?>" class="collapse">
                                    <table class="table table-responsive mb-0">
                                        <thead>
                                            <tr>
                                                <th>ឈ្មោះថ្នាក់ដឹកនាំ និងមន្ត្រី</th>
                                                <th>ប្រភេទច្បាប់</th>
                                                <th>កាលបរិច្ឆេទចាប់ពី</th>
                                                <th>ដល់កាលបរិច្ឆេទ</th>
                                                <th>មូលហេតុ</th>
                                                <th>ស្ថានភាព</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($leaves as $leave): ?>
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <img src="<?= $leave['profile'] ?>" class="rounded-circle"
                                                                style="width: 50px; height: 50px; object-fit: cover;" alt="">
                                                            <div class="d-flex flex-column mx-2">
                                                                <h6 class="mx-0 mb-1 text-primary"><?= $leave['khmer_name'] ?></h6>
                                                                <span class="text-muted"><?= $leave['email'] ?></span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span
                                                            class="badge <?= $leave['ltColor'] ?>"><?= $leave['leave_type'] ?></span>
                                                    </td>
                                                    <td><?= $leave['start_date'] ?></td>
                                                    <td><?= $leave['end_date'] ?></td>
                                                    <td><?= $leave['remarks'] ?></td>
                                                    <td><span class="badge bg-success"><?= $leave['status'] ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>