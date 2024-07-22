<?php
$title = "សារជូនដំណឹង";
include('src/common/header.php');
?>
<div class="container">
    <h2>Notifications</h2>
    <ul class="list-group">
        <?php foreach ($notifications as $notification) : ?>
            <li class="list-group-item"><?= $notification['message'] ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php include('src/common/footer.php'); ?>