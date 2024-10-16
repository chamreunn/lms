<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start or resume session
}

if (!isset($_SESSION['user_id'])) {
    header('Location: elms/login');
}

require_once 'src/models/Notification.php';
$notification = new Notification();
$getnotifications = $notification->getNotificationsByUserId($_SESSION['user_id']);
?>
<header class="navbar navbar-expand-md navbar-light d-print-none">
    <div class="container-xl">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu"
            aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand d-none-navbar-horizontal pe-0 pe-md-3">
            <a href=".">
                <img src="public/img/icons/brands/logo2.png" width="310" height="32" alt="អង្គភាពសវនកម្មផ្ទៃក្នុង"
                    class="navbar-brand-image">
            </a>
        </h1>
        <div class="navbar-nav flex-row order-md-last">
            <div class="d-none d-md-flex me-3">
                <a href="?theme=dark" class="nav-link px-0 hide-theme-dark" title="Enable dark mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <!-- Download SVG icon from http://tabler-icons.io/i/moon -->
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M12 3c.132 0 .263 0 .393 0a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
                    </svg>
                </a>
                <a href="?theme=light" class="nav-link px-0 hide-theme-light" title="Enable light mode"
                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                        stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <circle cx="12" cy="12" r="4" />
                        <path
                            d="M3 12h1m8 -9v1m8 8h1m-9 8v1m-6.4 -15.4l.7 .7m12.1 -.7l-.7 .7m0 11.4l.7 .7m-12.1 -.7l-.7 .7" />
                    </svg>
                </a>
            </div>
            <div class="nav-item dropdown">
                <a href="#" class="nav-link d-flex lh-1 p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                    <img src="<?= htmlspecialchars($_SESSION['user_profile'], ENT_QUOTES, 'UTF-8') ?>" class="avatar"
                        alt="User Avatar" style="object-fit: cover;">
                    <div class="d-none d-xl-block ps-2">
                        <h3 class="mb-0 text-primary">
                            <?= htmlspecialchars($_SESSION['user_khmer_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <span
                            class="small text-muted"><?= htmlspecialchars($_SESSION['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                </a>


                <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" style="width: 300px;">
                    <div class="d-flex flex-column align-items-center justify-content-center text-center">
                        <img src="<?= $_SESSION['user_profile'] ?>" class="avatar avatar-lg mb-3 mt-2 me-0" alt=""
                            style="object-fit: cover;">
                        <div class="d-none d-xl-block ps-2">
                            <h3 class="text-primary mb-0"><?= $_SESSION['user_khmer_name'] ?></h3>
                            <span
                                class="badge <?= htmlspecialchars($_SESSION['position_color'], ENT_QUOTES, 'UTF-8') ?>"
                                style="margin-top: 4px;"><?= htmlspecialchars($_SESSION['position'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                    <div class="dropdown-divider"></div>
                    <a href="/elms/edit_user_detail?user_id=<?= $_SESSION['user_id'] ?>" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                        <span class="mx-1">គណនីរបស់ខ្ញុំ</span>
                    </a>
                    <a href="/elms/setting_security?user_id=<?= $_SESSION['user_id'] ?>" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-settings">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
                            <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                        </svg>
                        <span class="mx-1">ការកំណត់</span>
                    </a>
                    <a href="/elms/logout" class="dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-logout">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
                            <path d="M9 12h12l-3 -3" />
                            <path d="M18 15l3 -3" />
                        </svg>
                        <span class="mx-1">ចាកចេញ</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>