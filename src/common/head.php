<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title><?= $title ?? "No Title" ?></title>
    <link rel="icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="public/img/favicon/favicon.ico" type="image/x-icon" />
    <!-- CSS -->
    <link href="public/dist/css/tabler.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-flags.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-payments.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/tabler-vendors.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/css/demo.min.css?1668287865" rel="stylesheet" />
    <link href="public/dist/libs/animate/animate.css?1668287865" rel="stylesheet" />
    <link href="public/dist/libs/litepicker/dist/css/plugins/multiselect.js.css?1668287865" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />

    <!-- map  -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">

    <!-- spinner button  -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all forms
            const forms = document.querySelectorAll('form');

            forms.forEach(function (form) {
                // Attach event listener for form submission
                form.addEventListener('submit', function (event) {
                    const submitBtn = form.querySelector('button[type="submit"]');

                    // Check if the spinner already exists; if not, create and append it
                    if (!submitBtn.querySelector('.spinner-border')) {
                        const spinner = document.createElement('span');
                        spinner.classList.add('spinner-border', 'spinner-border-sm', 'mx-2');
                        spinner.setAttribute('role', 'status');
                        spinner.setAttribute('aria-hidden', 'true');
                        spinner.style.display = 'none';
                        submitBtn.appendChild(spinner);
                    }

                    // Show the spinner
                    const spinner = submitBtn.querySelector('.spinner-border');
                    spinner.style.display = 'inline-block';

                    // Disable the button after a slight delay to allow form submission
                    setTimeout(function () {
                        submitBtn.setAttribute('disabled', 'true');
                    }, 50); // Delay the button disable by 50ms, giving the form time to submit
                });
            });
        });
    </script>
</head>

<body class=" border-top-wide border-primary d-flex flex-column">
    <!-- Loader HTML -->
    <!-- <div id="loader-wrapper" class="loader-wrapper">
        <div class="loader"></div>
    </div> -->
    <script src="public/dist/js/demo-theme.min.js?1668287865"></script>