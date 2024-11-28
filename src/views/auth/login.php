<?php
$title = "ចូលប្រព័ន្ធ";
require_once 'src/common/head.php';
require_once 'src/common/alert.php'; ?>
<style>
    .background-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100vh;
        background: url('public/img/backgrounds/blue2.jpg') center center/cover no-repeat;
        filter: blur(8px);
        z-index: -2;
    }

    .color-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(24, 40, 72, 0.7);
        mix-blend-mode: overlay;
        z-index: -1;
    }

    .form-footer .btn {
        font-family: 'Khmer MEF1', sans-serif;
    }

    /* Prevent body overflow */
    body {
        margin: 0;
        padding: 0;
        overflow: hidden;
    }
</style>
<div class="background-image"></div>
<div class="color-overlay"></div>
<div class="page page-center">
    <div class="container-tight py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg">
                <div class="container-tight">
                    <div class="card card-md rounded shadow-lg">
                        <div class="card-body">
                            <div class="text-center mb-1">
                                <a href="." class="navbar-brand">
                                    <img src="public/img/icons/brands/logo2.png" height="80" alt="">
                                </a>
                            </div>
                            <h2 class="h2 text-center mb-3">ចូលប្រើប្រាស់ប្រព័ន្ធ</h2>
                            <form action="/elms/login" method="POST" autocomplete="off" novalidate>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">អាសយដ្ឋានអ៊ីមែល<span
                                            class="text-danger fw-bold mx-1">*</span></label>
                                    <input type="email"
                                        style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;"
                                        class="form-control" name="email" placeholder="អាសយដ្ឋានអ៊ីមែល"
                                        value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                        autofocus autocomplete="on">
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold">
                                        ពាក្យសម្ងាត់<span class="text-danger fw-bold mx-1">*</span>
                                    </label>
                                    <div class="input-group input-group-flat">
                                        <input type="password"
                                            style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;"
                                            class="form-control" name="password"
                                            value="<?php echo htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            placeholder="ពាក្យសម្ងាត់" autocomplete="off" id="password">
                                        <span class="input-group-text">
                                            <a href="#" class="link-secondary" title="Show password"
                                                data-bs-toggle="tooltip" onclick="togglePasswordVisibility()">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-eye" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                    fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="12" r="2" />
                                                    <path
                                                        d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                                </svg>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-footer">
                                    <button type="submit" class="btn btn-primary w-100">ចូលប្រព័ន្ធ</button>
                                </div>
                            </form>
                        </div>
                        <div class="hr-text">ឬ</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <a href="https://t.me/+Xp83cg-Pvf1lY2M1" target="_blank"
                                        class="btn btn-facebook w-100">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/brand-facebook -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-brand-telegram">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M15 10l-4 4l6 6l4 -16l-18 7l4 2l2 6l3 -4" />
                                        </svg>
                                        ទំនាក់ទំនងក្រុមការងារបច្ចេកទេស
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-footer text-center sticky-bottom">
        <a href="/elms/usage" class="text-light">របៀបប្រើប្រាស់ប្រព័ន្ធ | ជំនាន់ទី 1.0</a>
    </div>
</div>

<?php require_once 'src/common/footer.php' ?>

<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }

    function togglePasswordVisibility() {
        var passwordInput = document.getElementById('password');
        var passwordIcon = document.querySelector('.input-group-text svg');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            passwordIcon.classList.remove('icon-tabler-eye');
            passwordIcon.classList.add('icon-tabler-eye-off');
            passwordIcon.innerHTML = `
                    <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-eye-off"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" /><path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" /><path d="M3 3l18 18" /></svg>
                `;
        } else {
            passwordInput.type = 'password';
            passwordIcon.classList.remove('icon-tabler-eye-off');
            passwordIcon.classList.add('icon-tabler-eye');
            passwordIcon.innerHTML = `
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <circle cx="12" cy="12" r="2" />
                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                `;
        }
    }
</script>