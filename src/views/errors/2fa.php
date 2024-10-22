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
    <link href="public/dist/libs/animate/animate.css?1668287865" rel="stylesheet" />
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
<?php include('src/common/alert.php'); ?>

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
                        <div class="card card-md rounded">
                            <div class="card-body">
                                <form action="/elms/verifyAuth2Fa" method="POST" autocomplete="off" novalidate
                                    id="2faForm">
                                    <div class="card-body">
                                        <h2 class="card-title card-title-lg text-center"
                                            style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                                            Authenticate Your Account
                                        </h2>
                                        <div class="text-center">
                                            <span class="avatar avatar-xl mb-3"
                                                style="background-image: url(<?php echo $_SESSION['user_profile']; ?>)"></span>
                                            <h3><?php echo $_SESSION['user_khmer_name']; ?></h3>
                                            <p class="text-muted"><?= $_SESSION['position'] ?></p>
                                        </div>
                                        <div class="my-3">
                                            <div class="row g-4">
                                                <div class="col">
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <input type="text" name="digit1"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code1"
                                                                autofocus>
                                                        </div>
                                                        <div class="col">
                                                            <input type="text" name="digit2"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code2">
                                                        </div>
                                                        <div class="col">
                                                            <input type="text" name="digit3"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code3">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col">
                                                    <div class="row g-2">
                                                        <div class="col">
                                                            <input type="text" name="digit4"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code4">
                                                        </div>
                                                        <div class="col">
                                                            <input type="text" name="digit5"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code5">
                                                        </div>
                                                        <div class="col">
                                                            <input type="text" name="digit6"
                                                                class="form-control form-control-lg text-center py-3"
                                                                maxlength="1" inputmode="numeric" pattern="[0-9]*"
                                                                oninput="moveFocus(this)"
                                                                onkeypress="return isNumber(event)" id="code6">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden input to hold the full 6-digit code -->
                                        <input type="hidden" name="code" id="fullCode">

                                        <p class="text-red fw-bold text-center">
                                            សូមវាយបញ្ចូលលេខកូដ៦ខ្ទង់ដែលមានបង្ហាញក្នុងកម្មវិធី <span
                                                style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                                                Google Authenticator
                                            </span>
                                        </p>
                                        <div class="form-footer">
                                            <div class="btn-list flex-nowrap">
                                                <a href="/elms/logout" class="btn w-100">បដិសេធ</a>
                                                <button type="submit" class="btn btn-primary w-100">បញ្ជាក់</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
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
    <script>
        function moveFocus(currentInput) {
            // Get the current input element's ID
            const currentId = parseInt(currentInput.id.replace('code', ''));

            // Move to the next input if the current one has a value
            if (currentInput.value.length === 1) {
                const nextInput = document.getElementById('code' + (currentId + 1));
                if (nextInput) {
                    nextInput.focus();
                }
            }

            // If the backspace key is pressed and input is empty, move focus back
            currentInput.addEventListener('keydown', function (event) {
                if (event.key === 'Backspace' && currentInput.value === '') {
                    const prevInput = document.getElementById('code' + (currentId - 1));
                    if (prevInput) {
                        prevInput.focus();
                    }
                }
            });
        }

        function isNumber(evt) {
            // Allow only numeric characters
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            return (charCode >= 48 && charCode <= 57);
        }

        document.getElementById('2faForm').addEventListener('submit', function (event) {
            // Prevent the default form submission
            event.preventDefault();

            // Collect all input values
            let code = '';
            for (let i = 1; i <= 6; i++) {
                const input = document.getElementById('code' + i);
                code += input.value; // Concatenate values
            }

            // Create a hidden input to hold the concatenated code
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'code'; // This should match the expected name in your backend
            hiddenInput.value = code;

            // Append the hidden input to the form
            this.appendChild(hiddenInput);

            // Now submit the form
            this.submit();
        });
    </script>
</body>

</html>
<!-- <div class="col-lg d-none d-lg-block">
    <img src="public/img/icons/svgs/login.svg" height="600" class="d-block mx-auto" alt="">
</div> -->