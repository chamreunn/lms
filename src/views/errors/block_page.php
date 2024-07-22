<?php
$title = "គណនីត្រូវបានបិទបណ្តោះអាសន្ន";
include('src/common/header.php');

// Check if user clicked on the link to unset session variables
if (isset($_GET['unset_session'])) {
    unset($_SESSION['blocked_user']);
    unset($_SESSION['user_profile']);
    unset($_SESSION['user_khmer_name']);
    // Redirect or perform any other action after unsetting sessions if needed
    header('Location: /elms/dashboard'); // Redirect to dashboard or another page
    exit;
}
?>
<div class="page page-center mt-5">
    <div class="container container-normal py-4 mt-4">
        <div class="row align-items-center g-4">
            <div class="col-lg">
                <div class="container-tight">
                    <div class="text-center mb-1">
                        <a href="." class="navbar-brand navbar-brand-autodark mb-2"><img src="public/img/icons/brands/logo2.png" height="60" alt=""></a>
                    </div>
                    <form class="card card-md rounded-3" action="./" method="get" autocomplete="off" novalidate>
                        <div class="card-body text-center">
                            <div class="mb-4">
                                <h1 class="fw-bold"><?php echo $title; ?></h1>
                            </div>
                            <?php if (isset($_SESSION['blocked_user']) && $_SESSION['blocked_user']) : ?>
                                <div class="mb-4">
                                    <span class="avatar avatar-xl mb-3" style="background-image: url(<?php echo $_SESSION['user_profile']; ?>)"></span>
                                    <h3><?php echo $_SESSION['user_khmer_name']; ?></h3>
                                </div>
                            <?php endif; ?>
                            <div class="mb-4">
                                <p class="text-muted">សូមទំនាក់ទំនងទៅកាន់មន្ត្រីទទួលបន្ទុកដើម្បីបើកដំណើរការគណនីរបស់អ្នកឡើងវិញ។</p>
                            </div>
                            <div>
                                <a href="/elms/logout" class="btn btn-primary">ត្រឡប់ទៅកាន់ផ្ទាំងចូលប្រព័ន្ធ</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg d-none d-lg-block">
                <img src="public/img/illustrations/illustration_locked.png" height="600" class="d-block mx-auto" alt="">
            </div>
        </div>
    </div>
</div>
<!-- Libs JS -->
<!-- Tabler Core -->
<?php include('src/common/footer.php') ?>