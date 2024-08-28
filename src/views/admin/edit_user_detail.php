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
                    <a href="/elms/edit_user_detail?user_id=<?= $userDetails['user_id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center active">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-user-circle">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
                            <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                            <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
                        </svg>
                        <span class="mx-2">គណនីរបស់ខ្ញុំ</span>
                    </a>
                    <a href="/elms/setting_security?user_id=<?= $userDetails['user_id'] ?>" data-bs-toggle="tooltip" title="ផ្លាស់ប្តូរអ៊ីម៉ែល និងពាក្យសម្ងាត់" data-bs-target="top" class="list-group-item list-group-item-action d-flex align-items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-shield-lock">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
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
                <h2 class="mb-4">គណនីរបស់ខ្ញុំ</h2>
                <h3 class="card-title">ពត៌មានគណនី</h3>
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="avatar avatar-xl" style="background-image: url('<?= $userDetails['profile_picture'] ?>')" ;></span>
                    </div>
                    <div class="col-auto">
                        <!-- Form to change the profile picture -->
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModel">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            ផ្លាស់ប្តូររូបភាព
                        </button>
                    </div>
                    <div class="col-auto">
                        <!-- Form to reset the profile picture -->
                        <button type="submit" class="btn btn-outline-danger" data-bs-target="#deleteModal" data-bs-toggle="modal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                            លុបរូបភាព
                        </button>
                    </div>

                    <div class="modal modal-blur fade" id="deleteModal" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-status bg-danger"></div>
                                <form action="/elms/reset-profile-picture" method="POST">
                                    <div class="modal-body text-center py-4 mb-0">
                                        <h5 class="modal-title fw-bold text-danger mb-3">លុបរូបភាព</h5>
                                        <div class="col-auto">
                                            <span class="avatar avatar-xl mb-3" style="background-image: url('<?= $userDetails['profile_picture'] ?>')" ;></span>
                                        </div>
                                        <p class="mb-0">តើអ្នកប្រាកដទេថានិងលុបរូបភាពនេះ?</p>
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                </div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-danger ms-auto w-100">លុប</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- model change pic  -->
                    <div class="modal modal-blur fade" id="editModel" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-status bg-danger"></div>
                                <form action="/elms/reset-profile-picture" method="POST">
                                    <div class="modal-body text-center py-4 mb-0">
                                        <h5 class="modal-title fw-bold text-danger mb-3">ផ្លាស់ប្តូររូបភាព</h5>
                                        <div class="col-auto">
                                            <span class="avatar avatar-xl mb-3" style="background-image: url('<?= $userDetails['profile_picture'] ?>')" ;></span>
                                        </div>
                                        <form action="/elms/change-profile-picture" method="POST" enctype="multipart/form-data">
                                            <label class="btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                                    <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                                                </svg>
                                                ផ្លាស់ប្តូររូបភាព
                                                <input type="file" name="profile_picture" accept="image/*" onchange="this.form.submit()" style="display:none;">
                                            </label>
                                        </form>
                                    </div>
                                    <div class="modal-footer bg-light border-top">
                                        <div class="w-100">
                                            <div class="row">
                                                <div class="col">
                                                    <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                </div>
                                                <div class="col">
                                                    <button type="submit" class="btn btn-danger ms-auto w-100">រក្សាទុក</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">ឈ្មោះមន្ត្រី</div>
                        <input type="text" class="form-control" value="<?= $userDetails['user_name'] ?>">
                    </div>
                    <div class="col-md">
                        <div class="form-label fw-bold">USERNAME</div>
                        <input type="text" class="form-control" value="<?= $userDetails['user_eng_name'] ?>">
                    </div>
                    <div class="col-md">
                        <div class="form-label fw-bold">ភេទ</div>
                        <input type="text" class="form-control" value="<?= $userDetails['gender'] ?>">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">តួនាទី</div>
                        <input type="text" class="form-control" value="<?= $userDetails['rolename'] ?>">
                    </div>
                    <div class="col-md">
                        <div class="form-label">នាយកដ្ឋាន</div>
                        <input type="text" class="form-control" value="<?= $userDetails['department_name'] ?>">
                    </div>
                    <div class="col-md">
                        <div class="form-label">ការិយាល័យ</div>
                        <input type="text" class="form-control" value="<?= $userDetails['office_name'] ?>">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">ទំនាក់ទំនង</div>
                        <input type="text" class="form-control" value="<?= $userDetails['phone_number'] ?>">
                    </div>
                    <div class="col-md">
                        <div class="form-label">ស្ថានភាពគណនី</div>
                        <!-- <input type="text" class="form-control" value="<?= $userDetails['active'] ?>"> -->
                        <select class="form-select" id="leave_type" name="leave_type_id" required>
                            <option value="<?= $userDetails['activeStatus'] ?>" selected><?= $userDetails['active'] ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md">
                        <div class="form-label fw-bold">ថ្ងៃខែឆ្នាំកំណើត</div>
                        <input type="text" class="form-control date-picker" value="<?= $userDetails['date_of_birth'] ?>">
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">ទីកន្លែងកំណើត</div>
                        <textarea type="text" class="form-control"><?= $userDetails['address'] ?></textarea>
                    </div>
                </div>
                <div class="row g-3 mt-3">
                    <div class="col-md">
                        <div class="form-label fw-bold">អាសយដ្ឋានបច្ចុប្បន្ន</div>
                        <textarea type="text" class="form-control"><?= $userDetails['curaddress'] ?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex">
                <button class="btn btn-danger ms-auto">រក្សាទុក</button>
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