<?php
$title = "ALL User";
include('src/common/header.php');
?>
<?= $_SESSION['user_id'] ." ". $_SESSION['token']?>
<div class="card">
    <div class="table-responsive">
        <table class="table table-vcenter">
            <thead>
                <th>ឈ្មោះ</th>
                <th>គ្រប់គ្រងសំណើ</th>
                <th>គ្រប់គ្រង</th>
                <th class="text-center">manage</th>
            </thead>
            <tbody>
                <?php foreach ($allUserDetails as $index => $user): ?>
                    <tr>
                        <!-- User Information -->
                        <td class="text-center">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($user['profile_picture']) ?>" class="avatar me-2"
                                    style="object-fit: cover;" alt="User Avatar">
                                <p class="mb-0"><?= htmlspecialchars($user['user_name']) ?></p>
                            </div>
                        </td>
                        <!-- Permission: Manage Requests -->
                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" disabled <?= $user['manage_requests'] ? 'checked' : '' ?>>
                            </div>
                        </td>
                        <!-- Permission: General Management -->
                        <td class="text-center">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" disabled <?= $user['general_management'] ? 'checked' : '' ?>>
                            </div>
                        </td>
                        <!-- Manage Button -->
                        <td class="text-center">
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                data-bs-target="#permissionModal<?= $index ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-user-cog">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h2.5" />
                                    <path d="M19.001 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                    <path d="M19.001 15.5v1.5" />
                                    <path d="M19.001 21v1.5" />
                                    <path d="M22.032 17.25l-1.299 .75" />
                                    <path d="M17.27 20l-1.3 .75" />
                                    <path d="M15.97 17.25l1.3 .75" />
                                    <path d="M20.733 20l1.3 .75" />
                                </svg>
                                <span class="mx-1">Manage</span>
                            </button>

                            <!-- Modal for Managing Permissions -->
                            <div class="modal modal-blur fade" id="permissionModal<?= $index ?>" tabindex="-1"
                                aria-labelledby="permissionModalLabel<?= $index ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form method="POST" action="/elms/update-permissions">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="permissionModalLabel<?= $index ?>">Manage
                                                    Permissions
                                                    for <?= htmlspecialchars($user['user_name']) ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="userId" value="<?= $user['user_id'] ?>">

                                                <!-- Permission: Manage Requests -->
                                                <div class="form-check form-switch cursor-pointer mb-2">
                                                    <input class="form-check-input cursor-pointer" type="checkbox" name="manage_requests"
                                                        id="manageRequests<?= $index ?>" value="1"
                                                        <?= $user['manage_requests'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label cursor-pointer" for="manageRequests<?= $index ?>">Manage
                                                        Requests</label>
                                                </div>

                                                <!-- Permission: General Management -->
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input cursor-pointer" type="checkbox"
                                                        name="general_management" id="generalManagement<?= $index ?>"
                                                        value="1" <?= $user['general_management'] ? 'checked' : '' ?>>
                                                    <label class="form-check-label cursor-pointer"
                                                        for="generalManagement<?= $index ?>">General
                                                        Management</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <div class="row w-100">
                                                    <div class="col">
                                                        <button type="button" class="btn w-100"
                                                            data-bs-dismiss="modal">Close
                                                        </button>
                                                    </div>
                                                    <div class="col">
                                                        <button type="submit" class="btn btn-primary w-100">Save
                                                            Changes</button>
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
</div>

<?php include('src/common/footer.php'); ?>