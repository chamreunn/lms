<?php
$title = "ប្រតិទិន";
include('src/common/header.php');
?>
<!-- FullCalendar CSS and JS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

<!-- Include Flatpickr CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="text-primary mb-0">ប្រតិទិន</h2>
            </div>
            <div class="list-group list-group-flush">
                <div class="list-group-header sticky-top">ថ្ងៃឈប់សម្រាក</div>
                <?php if (empty($getHolidays)): ?>
                    <div class="text-center mt-3">
                        <div class="text-center">
                            <img src="public/img/icons/svgs/nodata.svg" class="w-50" alt="">
                        </div>
                        <p>មិនមានសមាជិកឈប់សម្រាកទេ។</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($getHolidays as $holiday): ?>
                        <div class="row g-3 align-items-center">
                            <div class="col">
                                <a href="#" class="list-group-item text-primary list-group-item-action" aria-current="true">
                                    <span class="badge bg-red mx-2"></span>
                                    <span
                                        class="text-red fw-bold"><?= date('d,F', strtotime($holiday['holiday_date'])); ?></span>
                                    <?= $holiday['holiday_name']; ?>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="list-group list-group-flush">
                <div class="list-group-header sticky-top">សមាជិកឈប់សម្រាកថ្ងៃនេះ</div>
                <?php if (empty($leaves)): ?>
                    <div class="text-center mt-3">
                        <div class="text-center">
                            <img src="public/img/icons/svgs/nodata.svg" class="w-50" alt="">
                        </div>
                        <p>មិនមានសមាជិកឈប់សម្រាកទេ។</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($leaves as $onleaves): ?>
                        <div class="row g-3 align-items-center">
                            <div class="col">
                                <a href="#" class="list-group-item text-primary list-group-item-action" aria-current="true">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <img class="avatar me-2" style="object-fit: cover; width: 40px; height: 40px;"
                                                src="<?= 'https://hrms.iauoffsa.us/images/' . htmlspecialchars($onleaves['leader']['image']); ?>"
                                                alt="Profile Picture of <?= htmlspecialchars($onleaves['leader']['lastNameKh'] . ' ' . $onleaves['leader']['firstNameKh']); ?>">
                                            <span class="d-flex flex-column">
                                                <?= htmlspecialchars($onleaves['leader']['lastNameKh'] . ' ' . $onleaves['leader']['firstNameKh']); ?>
                                            </span>
                                        </div>
                                        <span class="text-red fw-bold">
                                            <?= htmlspecialchars(date('d, F', strtotime($onleaves['leave_request']['start_date']))) . ' ~ ' . htmlspecialchars(date('d, F', strtotime($onleaves['leave_request']['end_date']))); ?>
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Modal to show leave details -->
    <div class="modal modal-blur fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body">
                    <div class="text-center">
                        <img id="profile-picture" src="" class="avatar avatar-xl mb-3" alt="Profile Picture"
                            style="object-fit: cover; width:100px; height:100px;" />
                        <p id="user-name"></p>
                        <p id="leave-description"></p>
                        <p class="text-red"
                            style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;"
                            id="leave-dates"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" data-bs-dismiss="modal" class="btn w-100">close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        // Declare the calendar variable globally
        window.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            events: [
                <?php
                // Check if there are any holidays or leave events
                if (!empty($getHolidays)) {
                    $events = [];
                    foreach ($getHolidays as $holiday) {
                        $events[] = "{ 
                            id: '" . $holiday['id'] . "',
                            title: '" . addslashes($holiday['holiday_name']) . "',
                            start: '" . $holiday['holiday_date'] . "',
                            description: '" . addslashes($holiday['holiday_description']) . "',
                            backgroundColor: '" . $holiday['color'] . "',
                            borderColor: '" . $holiday['color'] . "',
                            textColor: 'white'
                        }";
                    }
                    echo implode(',', $events);
                }

                if (!empty($leaves)) {
                    foreach ($leaves as $onleaves) {
                        echo ",{
                            id: '" . $onleaves['leader']['firstNameKh'] . "',
                            title: '" . addslashes($onleaves['leader']['lastNameKh'] . " " . $onleaves['leader']['firstNameKh']) . "',
                            start: '" . date('Y-m-d', strtotime($onleaves['leave_request']['start_date'])) . "',
                            end: '" . date('Y-m-d', strtotime($onleaves['leave_request']['end_date'] . ' +1 day')) . "', // Inclusive end date
                            description: 'មូលហេតុ: " . addslashes($onleaves['leave_request']['remarks']) . "',
                            extendedProps: {
                                user_name: '" . addslashes($onleaves['leader']['lastNameKh'] . " " . $onleaves['leader']['firstNameKh']) . "',
                                profile: '" . 'https://hrms.iauoffsa.us/images/' . addslashes($onleaves['leader']['image']) . "' 
                            },
                            backgroundColor: 'primary', 
                            textColor: 'white',
                            display: 'auto' // Ensure it shows in next and previous months
                        }";
                    }
                }
                ?>
            ],
            dayCellClassNames: function (info) {
                var day = info.date.getDay();
                // Add class for weekends
                if (day === 0 || day === 6) {
                    return ['weekend-text-red'];
                }
                return [];
            },
            eventClick: function (info) {
                var eventObj = info.event;

                // Check if the event is a leave
                if (eventObj.extendedProps.user_name) {
                    document.querySelector('#profile-picture').src = eventObj.extendedProps.profile;
                    document.querySelector('#user-name').textContent = eventObj.extendedProps.user_name;
                    document.querySelector('#leave-description').textContent = eventObj.extendedProps.description;

                    // Adjust end date to show correctly by subtracting one day
                    var endDate = new Date(eventObj.endStr);
                    endDate.setDate(endDate.getDate() - 1); // Subtract 1 day from the end date

                    var startDateStr = eventObj.startStr;
                    var endDateStr = endDate.toISOString().split('T')[0]; // Convert to yyyy-mm-dd format

                    document.querySelector('#leave-dates').textContent = startDateStr + ' ~ ' + endDateStr;

                    var leaveModal = new bootstrap.Modal(document.getElementById('leaveModal'));
                    leaveModal.show();
                }
            }
        });

        calendar.render();
    });
</script>


<?php include('src/common/footer.php'); ?>