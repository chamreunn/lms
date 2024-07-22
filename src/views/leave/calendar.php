<?php
$title = "ប្រតិទិន";
include('src/common/header.php');
?>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/locales-all.min.js"></script>
<div class="card rounded-4 shadow-sm">
    <div class="card-body">
        <h2>Leave Calendar</h2>
        <div id="calendar"></div>
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
                        end: '<?= $leave['end_date'] ?>'
                    },
                <?php endforeach; ?>
            ],
            eventContent: function(arg) {
                // Create a custom HTML structure for the event content
                let customHtml = `
                    <div class="fc-event-title">${arg.event.title}</div>
                    <div class="fc-event-description">${arg.event.extendedProps.description}</div>
                `;
                return {
                    html: customHtml
                };
            }
        });
        calendar.render();
    });
</script>

<?php include('src/common/footer.php'); ?>