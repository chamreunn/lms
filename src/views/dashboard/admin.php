<?php
$title = "ទំព័រដើម";
include('src/common/header.php');
?>
<div class="container">
    <h2>Admin Dashboard</h2>
    <a href="/elms/approve-leave" class="btn btn-primary">Approve Leave Requests</a>
    <a href="/elms/leave-requests" class="btn btn-secondary">My Leave Requests</a>
    <a href="/elms/leave-calendar" class="btn btn-info">Leave Calendar</a>
    <a href="/elms/notifications" class="btn btn-warning">Notifications</a>
</div>
<?php include('src/common/footer.php'); ?>