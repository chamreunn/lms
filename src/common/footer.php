</div>
</div>
</div>
</div>

<!-- Modal Apply Late In -->
<div class="modal modal-blur fade" id="apply-late-in" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/apply_latein" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label for="lateindate" class="form-label">កាលបរិច្ឆេទចាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                        <line x1="16" y1="3" x2="16" y2="7"></line>
                                        <line x1="8" y1="3" x2="8" y2="7"></line>
                                        <line x1="4" y1="11" x2="20" y2="11"></line>
                                        <rect x="8" y="15" width="2" height="2"></rect>
                                    </svg>
                                </span>
                                <input type="text" autocomplete="off"
                                    value="<?= isset($_POST['date']) ? translateDateToKhmer($_POST['date'], 'j F, Y') : '' ?>"
                                    placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker" name="date">
                            </div>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="form-label">ម៉ោង<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5l.5 .5" />
                                        <path
                                            d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                        <path d="M15 21v-6" />
                                    </svg>
                                </span>
                                <input type="text" autocomplete="off" value="09:00" placeholder="ម៉ោង"
                                    class="form-control time-picker" id="time" name="time">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">មូលហេតុ<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                                name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-check cursor-pointer">
                                <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                                <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span
                                        class="text-danger fw-bold mx-1">*</span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">
                                    បញ្ចូន
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Late Out -->
<div class="modal modal-blur fade" id="apply-late-out" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply_lateout" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">បង្កើតលិខិតថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">កាលបរិច្ឆេទ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទចាប់ពី"
                                class="form-control date-picker" name="date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">ម៉ោង<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" value="17:30" placeholder="ម៉ោង"
                                class="form-control time-picker" id="time" name="time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">មូលហេតុ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                            name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span
                                    class="text-danger fw-bold mx-1">*</span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">បញ្ចូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Apply Leave Early  -->
<div class="modal modal-blur fade" id="apply-leaveearly" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply_leaveearly" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">បង្កើតលិខិតថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="leftbefore" class="form-label">កាលបរិច្ឆេទ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទ"
                                class="form-control date-picker" id="leftbefore" name="date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ម៉ោង<span class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" value="16:00" placeholder="ម៉ោង"
                                class="form-control time-picker" id="time" name="time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">មូលហេតុ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                            name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span
                                    class="text-danger fw-bold mx-1">*</span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">បញ្ចូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Apply Mission -->
<div class="modal modal-blur fade" id="mission" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply-mission" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">បង្កើតលិខិតថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះបេសកកម្ម</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="ឈ្មោះបេសកកម្ម" class="form-control"
                                id="mission_start" name="mission_name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">កាលបរិច្ឆេទចាប់ពី<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទ"
                                class="form-control date-picker" name="start_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ដល់កាលបរិចេ្ឆទ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="ដល់កាលបរិចេ្ឆទ"
                                class="form-control date-picker" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="createMissionDoc" class="form-label">ឯកសារភ្ជាប់</label>
                        <label id="createMissionName" for="createMissionDoc"
                            class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                            <span class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                </svg>
                            </span>
                            <span>ឯកសារភ្ជាប់</span>
                        </label>
                        <input type="file" name="missionDoc" id="createMissionDoc" accept=".pdf, .docx, .xlsx" required
                            hidden onchange="displayFileName('createMissionDoc', 'createMissionName')" />
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span
                                    class="text-danger fw-bold mx-1">*</span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">បញ្ចូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabler Core -->
<script src="public/dist/js/tabler.min.js?1668287865" defer></script>
<script src="public/dist/js/demo.min.js?1668287865" defer></script>

<script src="public/dist/libs/apexcharts/dist/apexcharts.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world-merc.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.js?1668287865" defer></script>

<script src="public/dist/libs/nouislider/dist/nouislider.min.js?1668287865" defer></script>
<script src="public/dist/libs/litepicker/dist/litepicker.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.min.js?1668287865" defer></script>
<!-- other link  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- datatables  -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

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
<!-- Script to hide the loader with delay -->
<script>
    window.addEventListener('load', function () {
        // Add a delay before removing the loader
        setTimeout(function () {
            var loader = document.getElementById('loader-wrapper');
            document.body.classList.remove('loading');
            loader.style.display = 'none';
        }, 500); // Delay for 0.5 second (500 milliseconds)
    });
</script>
<!-- end  -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if element exists before initializing TomSelect
        const leaveTypeElement = document.getElementById("leave_type");
        if (leaveTypeElement) {
            new TomSelect(leaveTypeElement, {
                copyClassesToDropdown: false,
                dropdownClass: "dropdown-menu ts-dropdown",
                optionClass: "dropdown-item",
                controlInput: "<input>",
                render: {
                    item: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                    option: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                },
            });

            leaveTypeElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const leaveTypeName = selectedOption.getAttribute('data-leave-name');
                document.getElementById('leave_type_name').value = leaveTypeName;
            });
        }

        // Check if elements with class 'date-picker' exist before initializing Litepicker
        const dateInputs = document.querySelectorAll('.date-picker');
        dateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });

        // Initialize Flatpickr for elements with the 'time-picker' class
        const timeInputs = document.querySelectorAll(".time-picker");
        timeInputs.forEach(timeInput => {
            flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
                defaultHour: 12,
                defaultMinute: 0,
                locale: 'km'
            });
        });

        // Initial setup to ensure signature file input is visible if checkbox is checked
        const signatureCheckbox = document.getElementById('signature');
        if (signatureCheckbox && signatureCheckbox.checked) {
            const signatureFileInput = document.getElementById('signatureFile');
            if (signatureFileInput) {
                signatureFileInput.style.display = 'block';
            }
        }
    });

    function toggleFileInput(checkbox, fileInputId) {
        const fileInput = document.getElementById(fileInputId);
        if (fileInput) {
            fileInput.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    function displayFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const fileNameLabel = document.getElementById(labelId);
        if (input && fileNameLabel) {
            fileNameLabel.textContent = input.files[0] ? input.files[0].name : '';
        }
    }
</script>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));
    });
    // @formatter:on
</script>

</body>

</html>