<?php
$title = "កែប្រែព័ត៌មានគណនី";
include('src/common/header.php');
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
    // Define Khmer translations for days and months
    $days = [
        'Mon' => 'ច័ន្ទ',
        'Tue' => 'អង្គារ',
        'Wed' => 'ពុធ',
        'Thu' => 'ព្រហស្បតិ៍',
        'Fri' => 'សុក្រ',
        'Sat' => 'សៅរ៍',
        'Sun' => 'អាទិត្យ'
    ];
    $months = [
        'January' => 'មករា',
        'February' => 'កុម្ភៈ',
        'March' => 'មីនា',
        'April' => 'មេសា',
        'May' => 'ឧសភា',
        'June' => 'មិថុនា',
        'July' => 'កក្កដា',
        'August' => 'សីហា',
        'September' => 'កញ្ញា',
        'October' => 'តុលា',
        'November' => 'វិច្ឆិកា',
        'December' => 'ធ្នូ'
    ];

    // Define Khmer numerals
    $numerals = [
        '0' => '០',
        '1' => '១',
        '2' => '២',
        '3' => '៣',
        '4' => '៤',
        '5' => '៥',
        '6' => '៦',
        '7' => '៧',
        '8' => '៨',
        '9' => '៩'
    ];

    // Get the English day and month names
    $englishDay = date('D', strtotime($date));
    $englishMonth = date('F', strtotime($date));

    // Translate English day and month names to Khmer
    $translatedDay = $days[$englishDay] ?? $englishDay;
    $translatedMonth = $months[$englishMonth] ?? $englishMonth;

    // Format the date in English
    $formattedDate = date($format, strtotime($date));

    // Replace day and month with Khmer
    $translatedDate = str_replace(
        [$englishDay, $englishMonth],
        [$translatedDay, $translatedMonth],
        $formattedDate
    );

    // Replace Arabic numerals with Khmer numerals
    $translatedDate = strtr($translatedDate, $numerals);

    return $translatedDate;
}
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mb-1">
                    <div>ទំព័រដើម</div>
                </div>
                <h2 class="page-title text-primary mb-0"><?= $title ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="row g-0">
        <div class="col-3 d-none d-md-block border-end">
            <div class="card-body">
                <h4 class="subheader">គណនីរបស់ខ្ញុំ</h4>
                <div class="list-group list-group-transparent">
                    <a href="/elms/edit_user_detail?user_id=<?= $userDetails['user_id'] ?>"
                        class="list-group-item list-group-item-action d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                        <span class="mx-2">គណនីរបស់ខ្ញុំ</span>
                    </a>
                    <a href="/elms/setting_security?user_id=<?= $userDetails['user_id'] ?>" data-bs-toggle="tooltip"
                        title="ផ្លាស់ប្តូរអ៊ីម៉ែល និងពាក្យសម្ងាត់" data-bs-target="top"
                        class="list-group-item list-group-item-action d-flex align-items-center active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-shield-lock">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
                            <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                            <path d="M12 12l0 2.5" />
                        </svg>
                        <span class="mx-2">សុវត្ថិភាព</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col d-flex flex-column">
            <div class="card-body">
                <h2 class="mb-4">សុវត្ថិភាព</h2>
                <h3 class="card-title mt-4">អាសយដ្ឋានអ៊ីម៉ែល</h3>
                <p class="card-subtitle text-danger">សូមពិនិត្យអាសយដ្ឋានអ៊ីម៉ែលអោយបានត្រឹមត្រូវ។​ ការស្នើសុំច្បាប់
                    ការដាក់លិខិតផ្សេងៗនិងត្រូវបានផ្ញើទៅកាន់អាសយដ្ឋានអ៊ីម៉ែលរបស់អ្នក។</p>
                <div class="mb-3">
                    <!-- Email Update Form -->
                    <div class="row">
                        <div class="col-10">
                            <input type="email" name="email" disabled class="form-control"
                                value="<?= htmlspecialchars($userDetails['email'], ENT_QUOTES, 'UTF-8'); ?>"
                                style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-red w-100" data-bs-toggle="modal"
                                data-bs-target="#emailUpdateModal">
                                ផ្លាស់ប្តូរ
                            </button>
                        </div>
                    </div>

                    <!-- Telegram Connect Button -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <!-- Replace your_bot_username and unique_user_token dynamically -->
                            <a href="https://t.me/your_bot_username?start=(unique_user_token)"
                                class="btn btn-primary w-100">
                                Connect Telegram
                            </a>
                        </div>
                    </div>

                    <!-- Modal Structure -->
                    <div class="modal modal-blur fade" id="emailUpdateModal" tabindex="-1"
                        aria-labelledby="emailUpdateModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="emailUpdateModalLabel">អាសយដ្ឋានអ៊ីម៉ែលថ្មី</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Email Update Form Inside Modal -->
                                    <form action="/elms/update-email" method="post">
                                        <div class="row g-2">
                                            <div class="col-12 mb-3">
                                                <input type="hidden" name="user_id"
                                                    value="<?= $userDetails['user_id'] ?>">
                                                <h5 for="new_email" class="form-label mb-3 mt-0">អាសយដ្ឋានអ៊ីម៉ែលថ្មី
                                                </h5>
                                                <input type="email" id="new_email" name="email" class="form-control"
                                                    placeholder="សូមបញ្ចូលអាសយដ្ឋានអ៊ីម៉ែលថ្មី">
                                            </div>
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-red w-100">ផ្លាស់ប្តូរ</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h3 class="card-title mt-4">ពាក្យសម្ងាត់</h3>
                <p class="card-subtitle text-danger">សូមប្រើប្រាស់ពាក្យសម្ងាត់ដែលមានសុវត្ថិភាពខ្ពស់។ ត្រូវមានអក្សរធំ
                    អក្សរតូច លេខ និងសញ្ញាជាដើម។</p>
                <form action="/elms/update-password" method="POST" class="mb-3">
                    <input type="hidden" name="user_id" value="<?= $userDetails['user_id'] ?>">
                    <div class="row g-3 mb-3">
                        <div class="col-lg-5 col-md-12 col-sm-12">
                            <div class="input-group input-group-flat">
                                <input type="password"
                                    style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;"
                                    class="form-control" name="password"
                                    value="<?php echo htmlspecialchars($_POST['password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="ពាក្យសម្ងាត់ថ្មី" autocomplete="off" id="password">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip"
                                        onclick="togglePasswordVisibility('password', 'password_icon')">
                                        <span id="password_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-eye" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <circle cx="12" cy="12" r="2" />
                                                <path
                                                    d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                            </svg>
                                        </span>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-5 col-md-12 col-sm-12">
                            <div class="input-group input-group-flat">
                                <input type="password"
                                    style="font-family: system-ui, 'khmer mef1', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;"
                                    class="form-control" name="confirm_password"
                                    value="<?php echo htmlspecialchars($_POST['confirm_password'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                                    placeholder="បញ្ជាក់ពាក្យសម្ងាត់ថ្មី" autocomplete="off" id="confirm_password">
                                <span class="input-group-text">
                                    <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip"
                                        onclick="togglePasswordVisibility('confirm_password', 'confirm_password_icon')">
                                        <span id="confirm_password_icon">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                class="icon icon-tabler icon-tabler-eye" width="24" height="24"
                                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <circle cx="12" cy="12" r="2" />
                                                <path
                                                    d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                                            </svg>
                                        </span>
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-12 col-sm-12">
                            <button class="btn btn-danger w-100">ផ្លាស់ប្តូរ</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>
<!-- toggle password  -->
<script>
    function togglePasswordVisibility(passwordFieldId, iconId) {
        var passwordField = document.getElementById(passwordFieldId);
        var passwordIcon = document.getElementById(iconId);

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            passwordIcon.classList.remove('icon-tabler-eye');
            passwordIcon.classList.add('icon-tabler-eye-off');
            passwordIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye-off">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828" />
                    <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87" />
                    <path d="M3 3l18 18" />
                </svg>
            `;
        } else {
            passwordField.type = 'password';
            passwordIcon.classList.remove('icon-tabler-eye-off');
            passwordIcon.classList.add('icon-tabler-eye');
            passwordIcon.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-eye">
                    <circle cx="12" cy="12" r="2" />
                    <path d="M22 12c-2.667 4.667 -6 7 -10 7s-7.333 -2.333 -10 -7c2.667 -4.667 6 -7 10 -7s7.333 2.333 10 7" />
                </svg>
            `;
        }
    }
</script>

<!-- card&list view  -->
<script>
    // Function to handle switching views and saving the preference
    function switchView(view) {
        if (view === 'card') {
            document.getElementById('cardView').classList.remove('d-none');
            document.getElementById('listView').classList.add('d-none');
            document.getElementById('cardViewBtn').classList.add('active');
            document.getElementById('listViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'card');
        } else if (view === 'list') {
            document.getElementById('cardView').classList.add('d-none');
            document.getElementById('listView').classList.remove('d-none');
            document.getElementById('listViewBtn').classList.add('active');
            document.getElementById('cardViewBtn').classList.remove('active');
            localStorage.setItem('preferredView', 'list');
        }
    }

    // Event listeners for buttons
    document.getElementById('cardViewBtn').addEventListener('click', function () {
        switchView('card');
    });

    document.getElementById('listViewBtn').addEventListener('click', function () {
        switchView('list');
    });

    // On page load, set the view based on the user's previous choice
    window.onload = function () {
        const preferredView = localStorage.getItem('preferredView') || 'card';
        switchView(preferredView);
    };
</script>

<!-- khmer number  -->
<script>
    function convertToKhmerNumerals(num) {
        const khmerNumerals = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
        return num.toString().split('').map(digit => khmerNumerals[digit]).join('');
    }

    function updateDateTime() {
        const clockElement = document.getElementById('real-time-clock');
        const currentTime = new Date();

        // Define Khmer arrays for days of the week and months.
        const daysOfWeek = ['អាទិត្យ', 'ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
        const dayOfWeek = daysOfWeek[currentTime.getDay()];

        const months = ['មករា', 'កុម្ភៈ', 'មិនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        const month = months[currentTime.getMonth()];

        const day = convertToKhmerNumerals(currentTime.getDate());
        const year = convertToKhmerNumerals(currentTime.getFullYear());

        // Calculate and format hours, minutes, seconds, and time of day in Khmer.
        let hours = currentTime.getHours();
        let period;

        if (hours >= 5 && hours < 12) {
            period = 'ព្រឹក'; // Khmer for AM (morning)
        } else if (hours >= 12 && hours < 17) {
            period = 'រសៀល'; // Khmer for afternoon
        } else if (hours >= 17 && hours < 20) {
            period = 'ល្ងាច'; // Khmer for evening
        } else {
            period = 'យប់'; // Khmer for night
        }

        hours = hours % 12 || 12;
        const khmerHours = convertToKhmerNumerals(hours);
        const khmerMinutes = convertToKhmerNumerals(currentTime.getMinutes().toString().padStart(2, '0'));
        const khmerSeconds = convertToKhmerNumerals(currentTime.getSeconds().toString().padStart(2, '0'));

        // Construct the date and time string in the desired Khmer format.
        const dateTimeString = `${dayOfWeek}, ${day} ${month} ${year} ${khmerHours}:${khmerMinutes}:${khmerSeconds} ${period}`;
        clockElement.textContent = dateTimeString;
    }

    // Update the date and time every second (1000 milliseconds).
    setInterval(updateDateTime, 1000);

    // Initial update.
    updateDateTime();
</script>