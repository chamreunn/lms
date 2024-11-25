<?php
$title = "IP Address";
$subtitle = "គ្រប់គ្រង";
include('src/common/header.php');
$currentIP = $_SERVER['REMOTE_ADDR']; // Capture the current user's IP address
?>
<!-- Page Header -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <?= $subtitle ?? 'No Sub Title' ?>
                </div>
                <h2 class="page-title"><?= $title ?? 'Title Not Found' ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">
            <!-- Create IP Form -->
            <div class="col-6">
                <div class="card">
                    <div class="card-body">
                        <h3>Add New IP Address</h3>
                        <form method="POST" action="/elms/ipaddress">
                            <div class="mb-3">
                                <label class="form-label">IP Address</label>
                                <input type="text" name="new_ip" class="form-control"
                                    value="<?= $_SERVER['REMOTE_ADDR'] ?>" placeholder="Enter IP address" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Add IP</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Display IP List -->
            <div class="col-6">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table table-striped">
                            <thead>
                                <tr>
                                    <th>IP Address</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ipList as $ip): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($ip['ip_address']) ?></td>
                                        <td class="d-flex justify-content-end text-end">
                                            <form method="POST" action="/elms/ipaddress" class="d-inline">
                                                <input type="hidden" name="id" value="<?= $ip['id'] ?>">
                                                <label class="form-check form-switch form-switch-lg cursor-pointer">
                                                    <input class="form-check-input cursor-pointer" type="checkbox"
                                                        name="status" onchange="this.form.submit()" <?= $ip['status'] ? 'checked' : '' ?>>
                                                    <span class="form-check-label form-check-label-on">បើក</span>
                                                    <span class="form-check-label form-check-label-off">បិទ</span>
                                                </label>
                                            </form>
                                            <form method="POST" action="/elms/ipaddress" class="d-inline ms-2">
                                                <input type="hidden" name="delete_id" value="<?= $ip['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>