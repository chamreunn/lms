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

    <!-- lottie animation icon loop  -->
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

    <!-- Include AOS CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <!-- spinner button  -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Attach event listener for all buttons
            const buttons = document.querySelectorAll('button');

            buttons.forEach(function (button) {
                button.addEventListener('click', function (event) {
                    // Check if the button has a `history-back` attribute or similar action
                    if (button.getAttribute('data-history-back') === 'true') {
                        // Prevent immediate navigation
                        event.preventDefault();

                        // Check if the spinner already exists; if not, create and append it
                        if (!button.querySelector('.spinner-border')) {
                            // Remove any existing SVG icon
                            const svgIcon = button.querySelector('svg');
                            if (svgIcon) {
                                svgIcon.remove();
                            }

                            // Create spinner element
                            const spinner = document.createElement('span');
                            spinner.classList.add('spinner-border', 'spinner-border-sm', 'mx-2');
                            spinner.setAttribute('role', 'status');
                            spinner.setAttribute('aria-hidden', 'true');

                            // Append spinner to the button
                            button.appendChild(spinner);
                        }

                        // Show the spinner
                        const spinner = button.querySelector('.spinner-border');
                        spinner.style.display = 'inline-block';

                        // Disable the button to prevent multiple clicks
                        button.setAttribute('disabled', 'true');

                        // Perform the history back action after a short delay
                        setTimeout(function () {
                            history.back();
                        }, 500); // Adjust the delay as needed
                    }
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