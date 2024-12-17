<?php
$title = "All Notifications";
require_once 'src/common/header.php';
?>
<div class="text-end mb-3">
    <form action="/elms/clearAllNotifications" method="POST">
        <button type="submit" class="btn ms-auto btn-sm">Clear All</button>
    </form>
</div>
<div class="card">
    <div id="notifications-container">
        <!-- Notifications List -->
        <ul id="notifications-list" class="list-group">
            <?php if (!empty($userNotifications)): ?>
                <?php foreach ($userNotifications as $index => $notification): ?>

                    <div
                        class="list-group list-group-flush list-group-hoverable <?= $notification['is_read'] == 0 ? '' : 'bg-light' ?>">
                        <div class="list-group-header sticky-top z-2">
                            <small class="d-block text-secondary text-truncate mt-n1">
                                <?= htmlspecialchars($notification['created_at']) ?>
                            </small>
                        </div>
                        <div class="list-group-item">
                            <div class="row align-items-center">
                                <div class="col-auto"><span
                                        class="<?= $notification['is_read'] == 0 ? 'status-dot status-dot-animated bg-red' : 'badge bg-secondary' ?> d-block"></span>
                                </div>
                                <div class="col-auto">
                                    <a href="<?= $notification['url'] ?? 'NULL' ?>">
                                        <span class="avatar"
                                            style="background-image: url(<?= $notification['profile'] ?? 'NULL' ?>)">
                                        </span>
                                    </a>
                                </div>
                                <div class="col text-truncate">
                                    <a href="<?= $notification['url'] ?? 'NULL' ?>"
                                        class="text-<?= $notification['is_read'] == 0 ? 'primary' : 'secondary' ?> h3 d-block"><?= htmlspecialchars($notification['title'] ?? 'No Title') ?></a>
                                    <small class="text-muted text-truncate">
                                        <?= htmlspecialchars($notification['message']) ?>
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <a href="#" class="list-group-item-actions d-flex align-items-center">
                                        <form action="/elms/markNotification" method="POST" class="me-2">
                                            <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                            <input type="hidden" name="is_read"
                                                value="<?= $notification['is_read'] == 0 ? 1 : 0 ?>">
                                            <button type="submit"
                                                class="btn btn-sm btn-<?= $notification['is_read'] == 0 ? 'success' : 'secondary' ?>"
                                                data-bs-toggle="tooltip"
                                                title="<?= $notification['is_read'] == 0 ? 'Mark as Read' : 'Mark as Unread' ?>">
                                                <?= $notification['is_read'] == 0 ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-star me-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 17.75l-6.172 3.245l1.179 -6.873l-5 -4.867l6.9 -1l3.086 -6.253l3.086 6.253l6.9 1l-5 4.867l1.179 6.873z" /></svg>' : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-star me-0"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M8.243 7.34l-6.38 .925l-.113 .023a1 1 0 0 0 -.44 1.684l4.622 4.499l-1.09 6.355l-.013 .11a1 1 0 0 0 1.464 .944l5.706 -3l5.693 3l.1 .046a1 1 0 0 0 1.352 -1.1l-1.091 -6.355l4.624 -4.5l.078 -.085a1 1 0 0 0 -.633 -1.62l-6.38 -.926l-2.852 -5.78a1 1 0 0 0 -1.794 0l-2.853 5.78z" /></svg>' ?>
                                            </button>
                                        </form>
                                        <form action="/elms/deleteNotification" method="POST">
                                            <input type="hidden" name="notification_id" value="<?= $notification['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip"
                                                title="លុបសារជូនដំណឹង">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash me-0">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M4 7l16 0" />
                                                    <path d="M10 11l0 6" />
                                                    <path d="M14 11l0 6" />
                                                    <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                    <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                </svg>
                                            </button>
                                        </form>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-center py-5">
                    <img src="public/img/icons/svgs/empty.svg" alt="No Notifications" style="max-width: 250px;">
                    <p class="text-muted mt-3">No notifications available</p>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>

<!-- Pagination -->
<div class="mt-4 d-flex justify-content-center position-fixed fixed-bottom">
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
<?php require_once 'src/common/footer.php'; ?>