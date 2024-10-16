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
    <div class="card-body">
        <!-- Tabs Navigation with Single Link -->
        <ul class="nav nav-tabs mb-3" id="accountTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="profile-tab" data-bs-toggle="tab" href="#profile-info" role="tab" aria-controls="profile-info" aria-selected="true">
                    ពត៌មានគណនី
                </a>
            </li>
        </ul>

        <!-- Tab Content (Everything Combined) -->
        <div class="tab-content mb-3" id="accountTabContent">
            <!-- Profile Information (All Combined) -->
            <div class="tab-pane fade show active" id="profile-info" role="tabpanel" aria-labelledby="profile-tab">
                <div class="row align-items-center mb-3">
                    <div class="col-auto">
                        <div class="avatar avatar-xl" style="background-image: url('<?= $userDetails['profile_picture'] ?>');"></div>
                    </div>
                    <div class="col">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModel">
                            ផ្លាស់ប្តូររូបភាព
                        </button>
                    </div>
                </div>

                <!-- Profile Details -->
                <div class="row g-3 mb-3">
                    <div class="col-md">
                        <label class="form-label fw-bold">ឈ្មោះមន្ត្រី</label>
                        <input type="text" class="form-control" value="<?= $userDetails['user_name'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <label class="form-label fw-bold">USERNAME</label>
                        <input type="text" class="form-control" value="<?= $userDetails['user_eng_name'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <label class="form-label fw-bold">ភេទ</label>
                        <input type="text" class="form-control" value="<?= $userDetails['gender'] ?>" disabled>
                    </div>
                </div>

                <!-- Role and Department Information -->
                <div class="row g-3 mb-3">
                    <div class="col-md">
                        <label class="form-label fw-bold">តួនាទី</label>
                        <input type="text" class="form-control" value="<?= $userDetails['rolename'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <label class="form-label">នាយកដ្ឋាន</label>
                        <input type="text" class="form-control" value="<?= $userDetails['department_name'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <label class="form-label">ការិយាល័យ</label>
                        <input type="text" class="form-control" value="<?= $userDetails['office_name'] ?>" disabled>
                    </div>
                </div>

                <!-- Contact and Address Information -->
                <div class="row g-3 mb-3">
                    <div class="col-md">
                        <label class="form-label fw-bold">ទំនាក់ទំនង</label>
                        <input type="text" class="form-control" value="<?= $userDetails['phone_number'] ?>" disabled>
                    </div>
                    <div class="col-md">
                        <label class="form-label fw-bold">ថ្ងៃខែឆ្នាំកំណើត</label>
                        <input type="text" class="form-control date-picker" value="<?= $userDetails['date_of_birth'] ?>" disabled>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="row g-3 mb-3">
                    <div class="col-md">
                        <label class="form-label fw-bold">ទីកន្លែងកំណើត</label>
                        <textarea class="form-control" disabled><?= $userDetails['address'] ?></textarea>
                    </div>
                    <div class="col-md">
                        <label class="form-label fw-bold">អាសយដ្ឋានបច្ចុប្បន្ន</label>
                        <textarea class="form-control" disabled><?= $userDetails['curaddress'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal to change profile picture -->
<div class="modal modal-blur fade" id="editModel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ផ្លាស់ប្តូររូបភាព</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="avatar avatar-xl mb-3" style="background-image: url('<?= $userDetails['profile_picture'] ?>');"></div>
                <form action="/elms/change-profile-picture" method="POST" enctype="multipart/form-data">
                    <label class="btn btn-outline-primary">
                        ជ្រើសរើសរូបភាព
                        <input type="file" name="profile_picture" accept="image/*" hidden onchange="this.form.submit()">
                    </label>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn" data-bs-dismiss="modal">បោះបង់</button>
            </div>
        </div>
    </div>
</div>


<?php include('src/common/footer.php'); ?>

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
    document.getElementById('cardViewBtn').addEventListener('click', function() {
        switchView('card');
    });

    document.getElementById('listViewBtn').addEventListener('click', function() {
        switchView('list');
    });

    // On page load, set the view based on the user's previous choice
    window.onload = function() {
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