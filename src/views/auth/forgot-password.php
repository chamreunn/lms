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
    <title>ភ្លេចពាក្យសម្ងាត់</title>
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
<?php include ('src/common/alert.php'); ?>

<body class="border-top-wide border-primary d-flex flex-column" style="min-height: 100vh;">
    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>

    <!-- Background with overlay -->
    <div class="background-image">
    </div>
    <div class="color-overlay">
    </div>

    <div class="page page-center d-flex align-items-center justify-content-center"
        style="position: relative; z-index: 1;">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <!-- Card with shadow -->
                        <div class="card card-md rounded shadow-lg" style="background-color: white;">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <a href="." class="navbar-brand navbar-brand-autodark"><img
                                            src="public/img/icons/brands/logo2.png" height="80" alt=""></a>
                                </div>
                                <h2 class="h2 text-center mb-3">ភ្លេចពាក្យសម្ងាត់</h2>

                                <!-- Form -->
                                <form action="/elms/forgot-password" method="POST" class="mb-3" autocomplete="off"
                                    novalidate>
                                    <div class="mb-3">
                                        <label class="form-label mb-3">អាសយដ្ឋានអ៊ីម៉ែល<span
                                                class="text-danger fw-bold mx-1">*</span></label>
                                        <input type="email" class="form-control" name="email"
                                            placeholder="អាសយដ្ឋានអ៊ីម៉ែល"
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                            autofocus autocomplete="on" required>
                                        <div class="invalid-feedback">
                                            សូមបញ្ចូលអាសយដ្ឋានអ៊ីម៉ែលត្រឹមត្រូវ!
                                        </div>
                                    </div>

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <span class="spinner-border spinner-border-sm" role="status"
                                                aria-hidden="true" style="display: none;"></span>
                                            បញ្ជូន
                                        </button>
                                    </div>
                                    <div class="hr-text">ឬ</div>
                                    <div class="text-center mt-3">
                                        <a href="/elms/login" class="btn mb-0 w-100">ត្រឡប់ទៅការចូលប្រព័ន្ធ</a>
                                    </div>
                                </form>
                                <!-- Code Input (Appears after sending code) -->
                                <?php if (isset($_SESSION['verification_code_sent'])): ?>
                                    <div class="hr"></div>
                                    <!-- Code Input (Appears after sending code) -->
                                    <form action="/elms/verify_code" method="POST" id="verificationForm" class="mb-3">
                                        <div class="mb-3">
                                            <label class="form-label mb-3">បញ្ចូលលេខកូដ 6 ខ្ទង់<span
                                                    class="text-danger fw-bold mx-1">*</span></label>

                                            <!-- 6 inputs for each digit of the code -->
                                            <div class="d-flex justify-content-between">
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit1" required>
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit2" required>
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit3" required>
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit4" required>
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit5" required>
                                                <input type="text" class="form-control text-center mx-1 code-input"
                                                    maxlength="1" name="digit6" required>
                                            </div>
                                            <div class="invalid-feedback">
                                                សូមបញ្ចូលលេខកូដ 6 ខ្ទង់!
                                            </div>
                                        </div>

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-success w-100">ផ្ទៀងផ្ទាត់</button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Libs JS -->
    <script src="public/dist/js/tabler.min.js?1668287865" defer></script>
    <script src="public/dist/js/demo.min.js?1668287865" defer></script>
</body>

<script>
    // Email form submission logic
    document.querySelector('form[action="/elms/forgot-password"]').addEventListener('submit', function (e) {
        const emailInput = document.querySelector('input[name="email"]');
        if (!emailInput.checkValidity()) {
            emailInput.classList.add('is-invalid');
            e.preventDefault(); // Prevent form submission if invalid
        } else {
            emailInput.classList.remove('is-invalid');
            // Show loading spinner
            const submitBtn = document.querySelector('.btn-primary');
            submitBtn.querySelector('.spinner-border').style.display = 'inline-block';
        }
    });

    // Get all input elements for the 6-digit code input
    const inputs = document.querySelectorAll('.code-input');

    inputs.forEach((input, index) => {
        // Listen for input event on each input field
        input.addEventListener('input', (event) => {
            if (event.target.value.length === 1 && index < inputs.length - 1) {
                // Move to the next input field when a digit is entered
                inputs[index + 1].focus();
            }
        });

        // Listen for keydown event to handle backspace functionality
        input.addEventListener('keydown', (event) => {
            if (event.key === "Backspace" && input.value === '' && index > 0) {
                // Move back to the previous input when backspace is pressed
                inputs[index - 1].focus();
            }
        });
    });

    // Combine 6-digit code into one hidden input when the verification form is submitted
    document.getElementById('verificationForm').addEventListener('submit', function (e) {
        let fullCode = '';
        inputs.forEach(input => {
            fullCode += input.value; // Concatenate the values from each input
        });

        if (fullCode.length < 6) {
            e.preventDefault(); // Prevent form submission if the code is incomplete
            alert('សូមបញ្ចូលលេខកូដ 6 ខ្ទង់!');
            return;
        }

        // Create a hidden input to store the full 6-digit code
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'verification_code';
        hiddenInput.value = fullCode;
        this.appendChild(hiddenInput);

        // Remove individual digit inputs' names to avoid submitting them separately
        inputs.forEach(input => {
            input.removeAttribute('name');
        });
    });
</script>


</html>
<!-- <div class="col-lg d-none d-lg-block">
    <img src="public/img/icons/svgs/login.svg" height="600" class="d-block mx-auto" alt="">
</div> -->