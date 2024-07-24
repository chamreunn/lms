<?php
$title = "បង្កើតសំណើលិខិតចេញយឺត";
ob_start();
?>
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo $title ?? "" ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <a href="/elms/late-requests" class="btn btn-primary d-none d-sm-inline-block">
                        <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-time">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
                            <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
                            <path d="M15 3v4" />
                            <path d="M7 3v4" />
                            <path d="M3 11h16" />
                            <path d="M18 16.496v1.504l1 1" />
                        </svg>
                        លិខិតចេញ និងចូលយឺត
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
?>
<div class="card rounded-3">
    <div class="card-header">
        <h2 class="mb-0">ទម្រង់ក្នុងការស្នើ</h2>
    </div>
    <form method="POST" action="/elms/apply_lateout" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-lg-6 mb-3">
                    <label for="start_date" class="form-label">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                <line x1="16" y1="3" x2="16" y2="7"></line>
                                <line x1="8" y1="3" x2="8" y2="7"></line>
                                <line x1="4" y1="11" x2="20" y2="11"></line>
                                <rect x="8" y="15" width="2" height="2"></rect>
                            </svg>
                        </span>
                        <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control" id="date" name="date">
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <label for="end_date" class="form-label">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                <path d="M12 7v5l.5 .5" />
                                <path d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                <path d="M15 21v-6" />
                            </svg>
                        </span>
                        <input type="text" autocomplete="off" value="5:00 PM" placeholder="ម៉ោង" class="form-control" id="time" name="time">
                    </div>
                </div>
                <div class="col-12 mb-3">
                    <label for="reason" class="form-label">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                    <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason" name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                </div>
                <div class="col-lg-6 mb-3">
                    <label class="form-check cursor-pointer">
                        <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                        <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span class="text-danger fw-bold mx-1">*</span></span>
                    </label>
                </div>
            </div>
        </div>
        <div class="card-footer text-end rounded-bottom-3">
            <button type="submit" class="btn btn-primary">
                <span class="me-2">បង្កើតសំណើ</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send mx-0">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M10 14l11 -11" />
                    <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                </svg>
            </button>
        </div>
    </form>
</div>
<?php include('src/common/footer.php'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Include Flatpickr Khmer locale -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/km.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize TomSelect
        flatpickr("#date", {
            dateFormat: "Y-m-d",
            allowInput: true,
            defaultDate: new Date(),
            monthSelectorType: "static",
            nextArrow: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>',
            prevArrow: '<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>',
            locale: 'km' // Set locale to Khmer
        });

        // Initialize Flatpickr for time input
        flatpickr("#time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "h:i K", // Time in HH:MM format
            time_24hr: false,
            defaultHour: 12,
            defaultMinute: 0,
            locale: 'km' // Set locale to Khmer for time as well
        });

    });
</script>