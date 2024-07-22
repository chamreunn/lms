<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta16
* @link https://tabler.io
* Copyright 2018-2022 The Tabler Authors
* Copyright 2018-2022 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>ចូលប្រព័ន្ធ</title>
    <!-- CSS files -->
    <link rel="icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <link href="public/dist/css/tabler.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-flags.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-payments.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-vendors.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/demo.min.css?1668287865" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <style>
        body {
            position: relative;
            overflow: hidden;
            font-family: -apple-system, 'Khmer MEF1', sans-serif;
        }

        .background-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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

        .wave {
            position: absolute;
            bottom: -150px;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .wave div {
            position: absolute;
            bottom: 0;
            width: 200%;
            height: 100px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            opacity: 0.6;
            animation: wave 6s linear infinite;
        }

        .wave div:nth-child(1) {
            left: 10%;
            animation-delay: 0s;
            background: rgba(255, 255, 255, 0.5);
        }

        .wave div:nth-child(2) {
            left: 30%;
            animation-delay: 1s;
            background: rgba(255, 255, 255, 0.4);
        }

        .wave div:nth-child(3) {
            left: 50%;
            animation-delay: 2s;
            background: rgba(255, 255, 255, 0.3);
        }

        .wave div:nth-child(4) {
            left: 70%;
            animation-delay: 3s;
            background: rgba(255, 255, 255, 0.2);
        }

        .wave div:nth-child(5) {
            left: 90%;
            animation-delay: 4s;
            background: rgba(255, 255, 255, 0.1);
        }

        @keyframes wave {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-50px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .form-footer .btn {
            font-family: 'Khmer MEF1', sans-serif;
        }
    </style>

</head>
<?php include ('src/common/alert.php'); ?>
<body class="border-top-wide border-primary d-flex flex-column">
    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>
    <div class="background-image"></div>
    <div class="color-overlay"></div>
    <div class="wave">
        <div style="width: 200px; height: 100px;"></div>
        <div style="width: 300px; height: 100px;"></div>
        <div style="width: 400px; height: 100px;"></div>
        <div style="width: 500px; height: 100px;"></div>
        <div style="width: 600px; height: 100px;"></div>
    </div>
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="card card-md rounded-4 shadow-xl">
                            <div class="card-body">
                                <div class="text-center mb-1">
                                    <a href="." class="navbar-brand navbar-brand-autodark"><img src="public/img/icons/brands/logo2.png" height="80" alt=""></a>
                                </div>
                                <h2 class="h2 text-center mb-3">ចូលប្រព័ន្ធ</h2>
                                <form action="/elms/login" method="POST" autocomplete="off" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label">ឈ្មោះមន្ត្រី ឬអាសយដ្ឋានអ៊ីមែល<span class="text-danger fw-bold mx-1">*</span></label>
                                        <input type="email" style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;" class="form-control" name="email" placeholder="ឈ្មោះមន្ត្រី ឬអាសយដ្ឋានអ៊ីមែល" value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autofocus autocomplete="on">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">
                                            ពាក្យសម្ងាត់<span class="text-danger fw-bold mx-1">*</span>
                                            <span class="form-label-description">
                                                <a class="mef1" href="public/forgot-password.html" style="font-family: 'Khmer MEF1'">ភ្លេចពាក្យសម្ងាត់ ?</a>
                                            </span>
                                        </label>
                                        <div class="input-group input-group-flat">
                                            <input type="password" style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;" class="form-control" name="password" value="<?php echo htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ពាក្យសម្ងាត់" autocomplete="off" id="password">
                                            <span class="input-group-text">
                                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip" onclick="togglePasswordVisibility()">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
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
                                        <a href="#" class="btn w-100" style="font-family: khmer mef1">
                                            ត្រឡប់ទៅកាន់ទំព័រដើម
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="public/dist/js/tabler.min.js?1668287865" defer></script>
    <script src="public/dist/js/demo.min.js?1668287865" defer></script>
</body>
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

</html>
<!-- <div class="col-lg d-none d-lg-block">
    <img src="public/img/icons/svgs/login.svg" height="600" class="d-block mx-auto" alt="">
</div> -->