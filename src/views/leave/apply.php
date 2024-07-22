<?php
$title = "បង្កើតសំណើច្បាប់ឈប់សម្រាក";
ob_start();
?>
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
                    <a href="/elms/leave-requests" class="btn btn-primary d-none d-sm-inline-block">
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
                        ច្បាប់ឈប់សម្រាករបស់ខ្ញុំ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$pageheader = ob_get_clean();
include('src/common/header.php');
require_once 'src/controllers/LeavetypeController.php';
?>
<div class="card rounded-3">
    <div class="card-header">
        <h2 class="mb-0">ទម្រង់ក្នុងការស្នើ</h2>
    </div>
    <form method="POST" action="/elms/apply-leave" enctype="multipart/form-data">
        <div class="card-body">
            <div class="mb-3">
                <label for="leave_type" class="form-label">ប្រភេទច្បាប់<span class="text-danger mx-1 fw-bold">*</span></label>
                <select class="form-select" id="leave_type" name="leave_type_id" required>
                    <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                    <?php foreach ($leavetypes as $leavetype) : ?>
                        <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>" data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>' <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                            <?= $leavetype['name'] ?> (<?= $leavetype['duration'] ?>ថ្ងៃ)
                        </option>
                    <?php endforeach; ?>
                </select>
                <input type="hidden" id="leave_type_name" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            </div>
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
                        <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control" id="start_date" name="start_date" required>
                    </div>
                </div>
                <div class="col-lg-6 mb-3">
                    <label for="end_date" class="form-label">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
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
                        <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ដល់កាលបរិច្ឆេទ" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
            </div>
            <div class="p-3 rounded-3 bg-light border">
                <div class="mb-3">
                    <label for="reason" class="form-label">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                    <div class="input-icon">
                        <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M8 9h8" />
                                <path d="M8 13h6" />
                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                            </svg>
                        </span>
                        <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="remarks" name="remarks" required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="attachment" class="form-label">ឯកសារភ្ជាប់
                        <span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-paperclip">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M15 7l-6.5 6.5a1.5 1.5 0 0 0 3 3l6.5 -6.5a3 3 0 0 0 -6 -6l-6.5 6.5a4.5 4.5 0 0 0 9 9l6.5 -6.5" />
                            </svg>
                        </span>
                    </label>
                    <div id="attachmentDropZone" class="dropzone text-center cursor-pointer rounded-3 border">
                        <div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-file-invoice mb-3">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M9 7l1 0" />
                                <path d="M9 13l6 0" />
                                <path d="M13 17l2 0" />
                            </svg>
                        </div>
                        <div class="drop-zone-text fw-bold">ចុចដើម្បីជ្រើសរើសឯកសារ ឬចុចដំណើរទាក់ទង</div>
                    </div>
                    <input type="file" class="form-control" id="attachment" name="attachment" style="display: none;">
                </div>
            </div>
        </div>
        <div class="card-footer text-end rounded-3">
            <button type="submit" class="btn btn-primary">
                បង្កើតសំណើ
                <span class="input-icon mx-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M10 14l11 -11" />
                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                    </svg>
                </span>
            </button>
        </div>
    </form>
</div>
<?php include('src/common/footer.php'); ?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Initialize TomSelect
        new TomSelect("#leave_type", {
            copyClassesToDropdown: false,
            dropdownClass: "dropdown-menu ts-dropdown",
            optionClass: "dropdown-item",
            controlInput: "<input>",
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                    }
                    return "<div>" + escape(data.text) + "</div>";
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                    }
                    return "<div>" + escape(data.text) + "</div>";
                },
            },
        });

        document.getElementById('leave_type').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var leaveTypeName = selectedOption.getAttribute('data-leave-name');
            document.getElementById('leave_type_name').value = leaveTypeName;
        });

        // Initialize Litepicker with multiselect plugin for start date
        new Litepicker({
            element: document.getElementById("start_date"),
            singleMode: true,
            format: "YYYY-MM-DD",
            plugins: ['multiselect'],
            minDate: new Date().toISOString().split('T')[0], // Prevent selection of past dates
            lang: 'kh', // Set language to Khmer
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        });

        new Litepicker({
            element: document.getElementById("end_date"),
            singleMode: true,
            format: "YYYY-MM-DD",
            plugins: ['multiselect'],
            minDate: new Date().toISOString().split('T')[0], // Prevent selection of past dates
            lang: 'kh', // Set language to Khmer
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
            }
        });
    });
</script>
<!-- attachment drag&drop  -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropZone = document.getElementById("attachmentDropZone");
        const fileInput = document.getElementById("attachment");

        dropZone.addEventListener("dragover", function(e) {
            e.preventDefault();
            dropZone.classList.add("drop-zone-over");
        });

        dropZone.addEventListener("dragleave", function(e) {
            e.preventDefault();
            dropZone.classList.remove("drop-zone-over");
        });

        dropZone.addEventListener("drop", function(e) {
            e.preventDefault();
            dropZone.classList.remove("drop-zone-over");

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                // Optionally update UI to show the selected files
                const fileName = files[0].name; // Display file name or other UI updates
                dropZone.innerHTML = `Selected File: ${fileName}`;
            }
        });

        // Handle clicks on drop zone to trigger file input click
        dropZone.addEventListener("click", function() {
            fileInput.click();
        });

        // Update file input when files are selected via input
        fileInput.addEventListener("change", function() {
            const files = fileInput.files;
            if (files.length > 0) {
                // Optionally update UI to show the selected files
                const fileName = files[0].name; // Display file name or other UI updates
                dropZone.innerHTML = `Selected File: ${fileName}`;
            }
        });
    });
</script>