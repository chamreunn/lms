<?php
$title = "ប្រតិទិន";
include('src/common/header.php');
?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>

<!-- Include Flatpickr CSS and JS -->
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<div class="card rounded-4 shadow-sm">
    <div class="card-body">
        <h2>Leave Calendar</h2>
        <div id="calendar"></div>
    </div>
</div>

<!-- Modal Structure -->
<div class="modal fade" id="leaveModal" tabindex="-1" aria-labelledby="leaveModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaveModalLabel">Leave Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <img id="profile-picture" src="" alt="Profile Picture" class="img-thumbnail mb-3" style="width: 100px; height: 100px;">
                <h5 id="user-name"></h5>
                <p id="leave-description"></p>
                <p id="leave-dates"></p>
                <!-- Date picker inputs using Flatpickr -->
                <input type="text" id="edit-start-date" class="form-control mb-3" placeholder="Start Date">
                <input type="text" id="edit-end-date" class="form-control mb-3" placeholder="End Date">
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'km',
            events: [
                <?php foreach ($leaves as $leave) : ?> {
                        title: 'ប្រភេទច្បាប់ :<?= addslashes($leave['leave_type']) ?>',
                        description: 'មូលហេតុ :<?= addslashes($leave['remarks']) ?>',
                        start: '<?= $leave['start_date'] ?>',
                        end: '<?= $leave['end_date'] ?>',
                        user_name: '<?= addslashes($leave['user_name']) ?>',
                        profile: '<?= addslashes($leave['profile']) ?>'
                    },
                <?php endforeach; ?>
            ],
            eventClick: function(info) {
                // Populate the modal with event details
                document.getElementById('profile-picture').src = info.event.extendedProps.profile;
                document.getElementById('user-name').textContent = info.event.extendedProps.user_name;
                document.getElementById('leave-description').textContent = info.event.extendedProps.description;
                document.getElementById('leave-dates').textContent = 'From: ' + info.event.startStr + ' To: ' + info.event.endStr;

                // Initialize Flatpickr for editing dates
                flatpickr("#edit-start-date", {
                    defaultDate: info.event.start,
                    dateFormat: "Y-m-d"
                });
                flatpickr("#edit-end-date", {
                    defaultDate: info.event.end,
                    dateFormat: "Y-m-d"
                });

                // Show the modal
                var leaveModal = new bootstrap.Modal(document.getElementById('leaveModal'));
                leaveModal.show();
            }
        });
        calendar.render();
    });
</script>

<?php include('src/common/footer.php'); ?>
