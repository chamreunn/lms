<?php
$pretitle = "ទំព័រដើម";
$title = "ថ្ងៃឈប់សម្រាក";
// Define the button HTML
$customButton = '
    <div class="d-flex">
        <a class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
            data-bs-target="#createHoliday">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-plus">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <path d="M12.5 21h-6.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v5"></path>
                <path d="M16 3v4"></path>
                <path d="M8 3v4"></path>
                <path d="M4 11h16"></path>
                <path d="M16 19h6"></path>
                <path d="M19 16v6"></path>
            </svg>
            <span>បន្ថែមថ្ងៃឈប់សម្រាក</span>
        </a>
        <a href="/elms/createHoliday" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
            data-bs-target="#createHoliday">
            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
        </a>
    </div>
';
include('src/common/header.php');
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title text-primary"><?= $title ?></h3>
        <div class="row">
            <div class="col me-4">
                <div class="btn-group me-2">
                    <button class="btn btn-outline-primary" id="prevBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left m-0">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M15 6l-6 6l6 6" />
                        </svg>
                    </button>
                    <button class="btn btn-outline-primary" id="nextBtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-right m-0">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 6l6 6l-6 6" />
                        </svg>
                    </button>
                    <button class="btn btn-outline-primary" id="todayBtn">Today</button>
                </div>
            </div>
            <div class="col ms-auto">
                <select class="form-select" id="calendarView">
                    <option value="dayGridMonth" selected>Month View</option>
                    <option value="timeGridWeek">Week View</option>
                    <option value="timeGridDay">Day View</option>
                    <option value="listMonth">List View</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div id="holidayCalendar"></div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>

<!-- create  -->
<div class="modal modal-blur fade" id="createHoliday" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/createHoliday" method="POST">
                <div class="modal-body">
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label">ឈ្មោះថ្ងៃឈប់សម្រាក</label>
                            <input type="text" name="holidayName" placeholder="ឈ្មោះថ្ងៃឈប់សម្រាក" autocomplete="off"
                                class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">កាលបរិច្ឆេទ</label>
                            <input type="text" name="holidayDate" autocomplete="off" placeholder="កាលបរិច្ឆេទ"
                                class="form-control date-picker" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ប្រភេទថ្ងៃឈប់សម្រាក<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" name="holidayType" required>
                                <option selected disabled>ជ្រើសរើសប្រភេទថ្ងៃឈប់សម្រាក</option>
                                <option value="National">National</option>
                                <option value="Regional">Regional</option>
                                <option value="Religious">Religious</option>
                                <option value="Public">Public</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ពណ៌នា</label>
                        <textarea class="form-control" name="holidayDescription" placeholder="ពណ៌នា"></textarea>
                    </div>
                    <div>
                        <label class="form-label">ជ្រើសរើសពណ៌</label>
                        <div class="row g-2">
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="dark" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-dark"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput form-colorinput-light">
                                    <input name="color" type="radio" value="white" class="form-colorinput-input"
                                        checked="">
                                    <span class="form-colorinput-color bg-white"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="blue" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-blue"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="azure" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-azure"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="indigo" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-indigo"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="purple" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-purple"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="pink" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-pink"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="red" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-red"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="orange" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-orange"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="yellow" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-yellow"></span>
                                </label>
                            </div>
                            <div class="col-auto">
                                <label class="form-colorinput">
                                    <input name="color" type="radio" value="lime" class="form-colorinput-input">
                                    <span class="form-colorinput-color bg-lime"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row w-100">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បិទ</button>
                        </div>
                        <div class="col">
                            <button type="submit" class="btn btn-primary w-100">បន្ថែម</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('holidayCalendar');
        // Declare the calendar variable globally
        window.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: '',
                center: 'title',
                right: ''
            },
            events: [
                <?php
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
                ?>
            ],
            dayCellClassNames: function (info) {
                var day = info.date.getDay();
                if (day === 0 || day === 6) {
                    return ['weekend-red'];
                }
                return [];
            },
            eventClick: function (info) {
                var eventObj = info.event;

                document.querySelector('#editHolidayModal input[name="id"]').value = eventObj.id;
                document.querySelector('#editHolidayModal input[name="holidayName"]').value = eventObj.title;
                document.querySelector('#editHolidayModal input[name="holidayDate"]').value = eventObj.startStr;
                document.querySelector('#editHolidayModal textarea[name="holidayDescription"]').value = eventObj.extendedProps.description;

                // Set the holiday ID for deletion
                document.querySelector('#deleteHolidayId').value = eventObj.id; // New line added

                var colorRadios = document.querySelectorAll('#editHolidayModal input[name="color"]');
                colorRadios.forEach(function (radio) {
                    radio.checked = radio.value === eventObj.backgroundColor;
                });

                var editModal = new bootstrap.Modal(document.getElementById('editHolidayModal'));
                editModal.show();
            }
        });

        calendar.render();

        // Add event listeners for the navigation buttons
        document.getElementById('prevBtn').addEventListener('click', function () {
            calendar.prev();
        });

        document.getElementById('nextBtn').addEventListener('click', function () {
            calendar.next();
        });

        document.getElementById('todayBtn').addEventListener('click', function () {
            calendar.today();
        });

        // Change the calendar view when the dropdown changes
        document.getElementById('calendarView').addEventListener('change', function () {
            calendar.changeView(this.value);
        });
    });
</script>
<!-- Edit Holiday Modal -->
<div class="modal modal-blur fade" id="editHolidayModal" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">កែប្រែថ្ងៃឈប់សម្រាក</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/updateHoliday" method="POST">
                <input type="hidden" name="id" value=""> <!-- Hidden field for holiday ID -->
                <div class="modal-body">
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-12">
                            <label class="form-label">ឈ្មោះថ្ងៃឈប់សម្រាក <span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <input type="text" name="holidayName" value="" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">កាលបរិច្ឆេទ <span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <input type="text" name="holidayDate" value="" class="form-control date-picker" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">ប្រភេទថ្ងៃឈប់សម្រាក <span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" name="holidayType" required>
                                <option value="National">National</option>
                                <option value="Regional">Regional</option>
                                <option value="Religious">Religious</option>
                                <option value="Public">Public</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ពណ៌នា</label>
                        <textarea class="form-control" name="holidayDescription"></textarea>
                    </div>
                    <div>
                        <label class="form-label">ជ្រើសរើសពណ៌</label>
                        <div class="row g-2">
                            <?php
                            $colors = ['dark', 'white', 'blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime'];
                            foreach ($colors as $color):
                                ?>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="<?= $color ?>"
                                            class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-<?= $color ?>"></span>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">បិទ</button>
                    <button type="submit" class="btn btn-primary">កែប្រែ</button>
                    <!-- Delete Button to trigger confirmation modal -->
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteHolidayModal">
                        លុប
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Confirmation Modal -->
<div class="modal modal-blur fade" id="deleteHolidayModal" tabindex="-1" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <div class="modal-body text-center py-4 mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon mb-2 text-danger icon-lg">
                    <path stroke="none" d="M0 0h24v24H0z"></path>
                    <path d="M12 9v4"></path>
                    <path
                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                    </path>
                    <path d="M12 16h.01"></path>
                </svg>
                <h5 class="modal-title">លុបថ្ងៃឈប់សម្រាក</h5>
                <p>តើអ្នកប្រាកដទេថានឹងលុបថ្ងៃឈប់សម្រាក <span class="text-danger fw-bold">នេះ?</span></p>
            </div>
            <div class="modal-footer bg-light">
                <div class="w-100">
                    <div class="row">
                        <div class="col">
                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                        </div>
                        <div class="col">
                            <form action="/elms/deleteHoliday" method="POST">
                                <input type="hidden" name="id" id="deleteHolidayId" value="">
                                <button type="submit" class="btn btn-danger w-100">លុប</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>