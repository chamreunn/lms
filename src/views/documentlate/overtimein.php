<?php
$title = "លិខិតចូលយឺត";
ob_start();
?>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mx-1">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo $title ?? "" ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <!-- <input type="search" class="form-control d-inline-block w-9 me-3" placeholder="ស្វែងរកនាយកដ្ឋាន…" id="customSearch" /> -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#create" class="btn btn-primary">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            បន្ថែមថ្មី
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
function translateDateToKhmer($date, $format = 'D F j, Y h:i A')
{
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

    $translatedDay = $days[date('D', strtotime($date))];
    $translatedMonth = $months[date('F', strtotime($date))];
    $translatedDate = str_replace(
        [date('D', strtotime($date)), date('F', strtotime($date))],
        [$translatedDay, $translatedMonth],
        date($format, strtotime($date))
    );

    return $translatedDate;
}
?>
<!-- display office  -->
<div class="col-12">
    <div class="card rounded-3">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
        </div>
        <div class="table-responsive">
            <table id="officeTable" class="table card-table table-vcenter text-nowrap">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ឈ្មោះមន្រ្តី</th>
                        <th>កាលបរិច្ឆេទយឺត</th>
                        <th>ម៉ោង</th>
                        <th>រយៈពេលយឺត</th>
                        <th>មូលហេតុ</th>
                        <th>ស្នើនៅ</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($getovertimein)) : ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                <p>មិនទាន់មានការិយាល័យនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#create" class="btn btn-primary">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    បន្ថែមថ្មី
                                </a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($getovertimein as $getlate) : ?>
                            <tr>
                                <td><input type="checkbox" class="row-select" value="<?= $getlate['id'] ?>"></td>
                                <td><?= $getlate['khmer_name'] ?></td>
                                <td><?= $getlate['date'] ?></td>
                                <td><?= $getlate['late_in'] ?></td>
                                <td><?= $getlate['late'] ?> នាទី</td>
                                <td><?= $getlate['reasons'] ?></td>
                                <td><?= $getlate['created_at'] ?></td>
                                <td>
                                    <a href="#" onclick="printContents(<?= $getlate['id'] ?>)" class="icon me-2 edit-btn text-danger" data-bs-toggle="tooltip" title="Print" data-bs-target="#edit<?= $getlate['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                        </svg>
                                    </a>
                                    <a href="#" onclick="Export2Word('page-contents<?= $getlate['id'] ?>', 'word-content<?= $getlate['id'] ?>');" class="icon me-2 edit-btn" data-bs-toggle="tooltip" title="Export to Word">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                            <path d="M7 11l5 5l5 -5" />
                                            <path d="M12 4l0 12" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>

                            <div class="col-xl-9 col-md-8 col-12 mb-md-0 mb-4" hidden>
                                <div id="page-contents<?= $getlate['id'] ?>" class="card invoice-preview-card" style="height: 100vh">
                                    <div class="card-body">
                                        <div class="page-container hidden-on-narrow">
                                            <div class="pdf-page size-a4">
                                                <div class="pdf-header">
                                                    <center class="invoice-number" style="font-family: khmer mef2;color: #2F5496;font-size: 20px; margin-top: -5px;">ព្រះរាជាណាចក្រកម្ពុជា<br>
                                                        ជាតិ សាសនា ព្រះមហាក្សត្រ
                                                    </center>
                                                </div>
                                                <div class="from">
                                                    <div class="mb-xl-0 mb-4">
                                                        <div class="for" style="font-family: khmer mef2; font-size:20px; position: relative; color: #2F5496;">
                                                            <span class="company-logo">
                                                                <img src="public/img/icons/brands/logo2.png" class="mb-3" style="width: 150px; padding-left: 30px" />
                                                            </span>
                                                            <p style="font-size: 14px;">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                                                            <p style="font-size: 14px; text-indent: 40px; top: 0; line-height:30px;">អង្គភាពសវនកម្មផ្ទៃក្នុង <br>
                                                            <p style="font-size: 14px; text-indent: 25px;">លេខ:.......................អ.ស.ផ.</p>
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <center style="text-align: center; font-family: khmer mef2; font-size: 19px;" class="mb-3">
                                                        ពាក្យស្នើសុំបញ្ជាក់ពិការចូលបំពេញការងារយឺតយ៉ាវ
                                                    </center>
                                                    <p style="font-family: khmer mef1; font-size:18px; line-height: 30px; text-align:justify; text-indent: 50px;">
                                                        ខ្ញុំបាទ / នាងខ្ញុំឈ្មោះ <span class="fw-bold"><?= $getlate['khmer_name'] ?></span> មានតួនាទីជា <span class="fw-bold"><?= $getlate['position_name'] ?></span> នៃ <span class="fw-bold"><?= $getlate['department_name'] ?></span> បានមកបំពេញការងារយឺតពេលកំណត់នៅថ្ងៃទី <span class="fw-bold"><?= date('d', strtotime($getlate['date'])) ?> ខែ <span class="fw-bold"><?= translateDateToKhmer($getlate['date'], 'F') ?> ឆ្នាំ <span class="fw-bold"><?= date('Y', strtotime($getlate['date'])) ?>
                                                                </span> វេលាម៉ោង <span class="fw-bold"><?= $getlate['late_in'] . "នាទី" ?></span> ហើយខ្ញុំសូមបញ្ជាក់ពីមូលហេតុដែលខ្ញុំបាទ/នាងខ្ញុំមកបំពេញការងារយឺតយ៉ាវដោយមូលហេតុ <span class="fw-bolder"><?= $getlate['reasons'] ?></span>។ដូចនេះសូមមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមានខ្ញុំបាទ/នាងខ្ញុំក្នុងបញ្ជីវត្តមានរបស់មន្ត្រីនៃអង្គភាពសវនកម្មផ្ទៃក្នុងនៃ <span class="fw-bolder">អ.ស.ហ.</span>នៅថ្ងៃទី <span class="fw-bold"><?= date('d', strtotime($getlate['date'])) ?> ខែ <span class="fw-bold"><?= translateDateToKhmer($getlate['date'], 'F') ?> ឆ្នាំ <span class="fw-bold"><?= date('Y', strtotime($getlate['date'])) ?> គឺតាមមូលហេតុខាងលើនេះ។
                                                    </p>
                                                    <p style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                        សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ពិនិត្យ និងចាត់ចែងតាមការគួរ
                                                    </p>
                                                    <p style="font-family: khmer mef1; font-size:18px; text-align:justify; text-indent: 50px;">
                                                        សូម <b>មន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</b> ទទួលនូវការរាប់អានពីខ្ញុំ។
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="card-footer d-flex align-items-center rounded-bottom-3">
            <p class="m-0 text-secondary">Showing <span id="showing-start">1</span> to <span id="showing-end">8</span> of <span id="total-entries">16</span> entries</p>
            <ul id="custom-pagination" class="pagination m-0 ms-auto"></ul>
        </div>
    </div>
</div>

<!-- Create office Modal -->
<div class="modal modal-blur fade" id="create" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply_latein" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">បន្ថែមថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
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
                    <div class="mb-3">
                        <label for="end_date" class="form-label">ម៉ោង<span class="text-danger mx-1 fw-bold">*</span></label>
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
                            <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ម៉ោង" class="form-control" id="time" name="time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                        <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason" name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើកាបញ្ចូល<span class="text-danger fw-bold mx-1">*</span></span>
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

<?php include('src/common/footer.php'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- Include Flatpickr Khmer locale -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/km.js"></script>

<script>
    // Function to print the contents
    function printContents(id) {
        var printContent = document.getElementById('page-contents' + id).innerHTML;
        var originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
    }

    // Function to export the table data to a Word document
    function Export2Word(elementId, filename = '') {
        var preHtml = `
        <html xmlns:o='urn:schemas-microsoft-com:office:office'
              xmlns:w='urn:schemas-microsoft-com:office:word'
              xmlns='http://www.w3.org/TR/REC-html40'>
        <head>
            <meta charset='utf-8'>
            <title>Export HTML To Doc</title>
            <style>
                body { font-family: Arial, sans-serif; }
            </style>
        </head>
        <body>`;
        var postHtml = `</body></html>`;
        var html = preHtml + document.getElementById(elementId).innerHTML + postHtml;

        var blob = new Blob(['\ufeff', html], {
            type: 'application/msword'
        });

        // Create a download link element
        var downloadLink = document.createElement("a");
        document.body.appendChild(downloadLink);

        if (navigator.msSaveOrOpenBlob) {
            navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            // Create a link to the file
            var url = URL.createObjectURL(blob);
            downloadLink.href = url;

            // Setting the file name
            downloadLink.download = filename;

            // Triggering the function
            downloadLink.click();

            // Clean up the URL object after download
            URL.revokeObjectURL(url);
        }

        document.body.removeChild(downloadLink);
    }
</script>

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
            dateFormat: "H:i", // Time in HH:MM format
            time_24hr: false,
            defaultHour: 12,
            defaultMinute: 0,
            locale: 'km' // Set locale to Khmer for time as well
        });

    });
</script>
<style>
    /* Hide the default pagination controls */
    #officeTable_paginate {
        display: none;
    }
</style>
<!-- pagination footer  -->
<script>
    $(document).ready(function() {
        var table = $('#officeTable').DataTable({
            "paging": true,
            "searching": false,
            "info": false,
            "lengthChange": false,
            "pageLength": 8,
            "language": {
                "paginate": {
                    "previous": "<svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M15 6l-6 6l6 6'></path></svg> prev",
                    "next": "next <svg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='icon'><path stroke='none' d='M0 0h24v24H0z' fill='none'></path><path d='M9 6l6 6l-6 6'></path></svg>"
                }
            },
            "drawCallback": function(settings) {
                var api = this.api();
                var start = api.page.info().start + 1;
                var end = api.page.info().end;
                var total = api.page.info().recordsTotal;

                $('#showing-start').text(start);
                $('#showing-end').text(end);
                $('#total-entries').text(total);

                // Create custom pagination
                var paginationHtml = '';
                var currentPage = api.page.info().page;
                var totalPages = api.page.info().pages;

                paginationHtml += '<li class="page-item ' + (currentPage === 0 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" tabindex="-1" aria-disabled="' + (currentPage === 0 ? 'true' : 'false') + '" data-page="prev">';
                paginationHtml += '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">';
                paginationHtml += '<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                paginationHtml += '<path d="M15 6l-6 6l6 6"></path>';
                paginationHtml += '</svg> prev</a></li>';

                for (var i = 0; i < totalPages; i++) {
                    paginationHtml += '<li class="page-item ' + (currentPage === i ? 'active' : '') + '">';
                    paginationHtml += '<a class="page-link" href="#" data-page="' + i + '">' + (i + 1) + '</a></li>';
                }

                paginationHtml += '<li class="page-item ' + (currentPage === totalPages - 1 ? 'disabled' : '') + '">';
                paginationHtml += '<a class="page-link" href="#" data-page="next">next <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">';
                paginationHtml += '<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>';
                paginationHtml += '<path d="M9 6l6 6l-6 6"></path>';
                paginationHtml += '</svg></a></li>';

                $('#custom-pagination').html(paginationHtml);

                // Add click event to custom pagination links
                $('#custom-pagination .page-link').on('click', function(e) {
                    e.preventDefault();
                    var pageNum = $(this).data('page');
                    if (pageNum === 'prev') {
                        table.page('previous').draw('page');
                    } else if (pageNum === 'next') {
                        table.page('next').draw('page');
                    } else {
                        table.page(parseInt(pageNum)).draw('page');
                    }
                });
            }
        });

        // Custom search bar functionality
        $('#customSearch').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>