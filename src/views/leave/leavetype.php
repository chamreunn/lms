<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /elms/login");
    exit();
}

require_once 'src/controllers/LeavetypeController.php';
$title = "ប្រភេទច្បាប់ឈប់សម្រាក";
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
                    <div class="d-flex">
                        <!-- <input type="search" class="form-control d-inline-block w-9 me-3" placeholder="ស្វែងរកនាយកដ្ឋាន…" id="customSearch" /> -->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            បង្កើតប្រភេទច្បាប់ថ្មី
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
?>
<style>
    /* Hide the default pagination controls */
    #leavetypeTable_paginate {
        display: none;
    }
</style>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- display leavetype$leavetype  -->
<div class="col-12">
    <div class="card rounded-3">
        <div class="card-header">
            <h3 class="card-title">Invoices</h3>
        </div>
        <div class="table-responsive">
            <table id="leavetypeTable" class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th></th>
                        <th>ឈ្មោះប្រភេទច្បាប់</th>
                        <th>ពណ៌</th>
                        <th>រយៈពេល</th>
                        <th width="20">ពិពណ៌នា</th>
                        <th>តម្រូវការឯកសារភ្ជាប់</th>
                        <th>ថ្ងៃបង្កើត</th>
                        <th>ថ្ងៃកែប្រែ</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leavetypes)) : ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    បង្កើតប្រភេទច្បាប់ថ្មី
                                </a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($leavetypes as $leavetype) : ?>
                            <tr>
                                <td><?= $leavetype['id'] ?></td>
                                <td><?= $leavetype['name'] ?></td>
                                <td>
                                    <span class="badge <?= $leavetype['color'] ?>"><?= $leavetype['name'] ?></span>
                                </td>
                                <td><?= $leavetype['duration'] ?></td>
                                <td><?= $leavetype['description'] ?></td>
                                <td><?= $leavetype['attachment_required'] ? 'Yes' : 'No' ?></td>
                                <td><?= $leavetype['created_at'] ?></td>
                                <td><?= $leavetype['updated_at'] ?></td>
                                <td>
                                    <a href="#" class="icon me-2 edit-btn" data-id="<?= $leavetype['id'] ?>" data-name="<?= $leavetype['name'] ?>" data-duration="<?= $leavetype['duration'] ?>" data-color="<?= $leavetype['color'] ?>" data-description="<?= $leavetype['description'] ?>" data-attachment="<?= $leavetype['attachment_required'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                    <a href="#" class="icon delete-btn text-danger" data-id="<?= $leavetype['id'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7l0 -3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1l0 3" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex align-items-center rounded-3">
            <p class="m-0 text-secondary">Showing <span id="showing-start">1</span> to <span id="showing-end">8</span> of <span id="total-entries">16</span> entries</p>
            <ul id="custom-pagination" class="pagination m-0 ms-auto"></ul>
        </div>
    </div>
</div>
<!-- Create Modal -->
<div class="modal modal-blur fade" id="modal-team" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">បង្កើតប្រភេទច្បាប់ថ្មី</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/create_leavetype" method="POST">
                <div class="modal-body">
                    <div class="col mb-3">
                        <label class="form-label">ឈ្មោះប្រភេទច្បាប់<span class="text-danger fw-bold mx-1">*</span></label>
                        <input type="text" name="name" placeholder="ឈ្មោះប្រភេទច្បាប់" class="form-control" autofocus required>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">រយៈពេល (ថ្ងៃ)<span class="text-danger fw-bold mx-1">*</span></label>
                        <input type="number" name="duration" placeholder="រយៈពេល (ថ្ងៃ)" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ជ្រើសរើសព័ណ៌<span class="text-danger fw-bold mx-1">*</span></label>
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-dark" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-dark"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput form-colorinput-light">
                                        <input name="color" type="radio" value="bg-white" class="form-colorinput-input" checked="">
                                        <span class="form-colorinput-color bg-white"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-blue" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-blue"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-azure" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-azure"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-indigo" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-indigo"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-purple" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-purple"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-pink" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-pink"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-red" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-red"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-orange" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-orange"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-yellow" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-yellow"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-lime" class="form-colorinput-input">
                                        <span class="form-colorinput-color bg-lime"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ពិពណ៌នា<span class="text-danger fw-bold mx-1">*</span></label>
                        <textarea class="form-control" name="description" placeholder="ពិពណ៌នា"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="attachment_required">
                            <span class="form-check-label">ត្រូវការឯកសារភ្ជាប់</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn me-auto w-100" data-bs-dismiss="modal">បិទ</button>
                            </div>
                            <div class="col">
                                <button type="submit" name="create" class="btn btn-primary w-100">បង្កើតប្រភេទច្បាប់</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit Modal -->
<div class="modal modal-blur fade" id="editModal" tabindex="-1" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">កែប្រែប្រភេទច្បាប់</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" action="/elms/create_leavetype" method="POST">
                <input type="hidden" name="id" id="edit-id">
                <div class="modal-body">
                    <div class="col mb-3">
                        <label class="form-label">ឈ្មោះប្រភេទច្បាប់<span class="text-danger fw-bold mx-1">*</span></label>
                        <input type="text" name="name" id="edit-name" placeholder="ឈ្មោះប្រភេទច្បាប់" class="form-control" required>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">រយៈពេល (ថ្ងៃ)<span class="text-danger fw-bold mx-1">*</span></label>
                        <input type="number" name="duration" id="edit-duration" placeholder="រយៈពេល (ថ្ងៃ)" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ជ្រើសរើសព័ណ៌<span class="text-danger fw-bold mx-1">*</span></label>
                        <div class="mb-3">
                            <div class="row g-2">
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-dark-lt" class="form-colorinput-input" id="editColor1">
                                        <span class="form-colorinput-color bg-dark"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput form-colorinput-light">
                                        <input name="color" type="radio" value="bg-white" class="form-colorinput-input" id="editColor2">
                                        <span class="form-colorinput-color bg-white"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-blue" class="form-colorinput-input" id="editColor3">
                                        <span class="form-colorinput-color bg-blue"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-azure" class="form-colorinput-input" id="editColor4">
                                        <span class="form-colorinput-color bg-azure"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-indigo" class="form-colorinput-input" id="editColor5">
                                        <span class="form-colorinput-color bg-indigo"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-purple" class="form-colorinput-input" id="editColor6">
                                        <span class="form-colorinput-color bg-purple"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-pink" class="form-colorinput-input" id="editColor7">
                                        <span class="form-colorinput-color bg-pink"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-red" class="form-colorinput-input" id="editColor8">
                                        <span class="form-colorinput-color bg-red"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-orange" class="form-colorinput-input" id="editColor9">
                                        <span class="form-colorinput-color bg-orange"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-yellow" class="form-colorinput-input" id="editColor10">
                                        <span class="form-colorinput-color bg-yellow"></span>
                                    </label>
                                </div>
                                <div class="col-auto">
                                    <label class="form-colorinput">
                                        <input name="color" type="radio" value="bg-lime" class="form-colorinput-input" id="editColor11">
                                        <span class="form-colorinput-color bg-lime"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ពិពណ៌នា<span class="text-danger fw-bold mx-1">*</span></label>
                        <textarea class="form-control" name="description" id="edit-description" placeholder="ពិពណ៌នា"></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="attachment_required" id="edit-attachment_required">
                            <span class="form-check-label">ត្រូវការឯកសារភ្ជាប់</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn me-auto w-100" data-bs-dismiss="modal">បិទ</button>
                            </div>
                            <div class="col">
                                <button type="submit" name="update" class="btn btn-primary w-100">កែប្រែប្រភេទច្បាប់</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete Modal -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-status bg-danger"></div>
            <form id="deleteForm" action="/elms/create_leavetype" method="POST">
                <input type="hidden" id="deleteId" name="id">
                <div class="modal-body text-center py-4 mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M12 9v4"></path>
                        <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                    <h5 class="modal-title" id="deleteModalLabel">លុបប្រភេទច្បាប់</h5>
                    <p>តើអ្នកប្រាកដថាចង់លុបប្រភេទច្បាប់នេះមែនទេ?</p>
                </div>
                <div class="modal-footer border-top bg-light">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <a href="#" class="btn w-100" data-bs-dismiss="modal">
                                    បោះបង់
                                </a>
                            </div>
                            <div class="col">
                                <button type="submit" name="delete" class="btn btn-danger w-100">យល់ព្រម</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- edit and delete modal data  -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editBtns = document.querySelectorAll('.edit-btn');

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const duration = btn.getAttribute('data-duration');
                const color = btn.getAttribute('data-color');
                const description = btn.getAttribute('data-description');
                const attachment = btn.getAttribute('data-attachment');

                document.getElementById('edit-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-duration').value = duration;
                document.getElementById('edit-description').value = description;
                document.getElementById('edit-attachment_required').checked = (attachment === 'Yes');

                // Uncheck all color options
                const colorInputs = document.querySelectorAll('input[name="color"]');
                colorInputs.forEach(input => {
                    if (input.value === color) {
                        input.checked = true; // Check the radio input with the correct value
                        input.setAttribute('checked', 'checked'); // Add the checked attribute for compatibility
                    } else {
                        input.checked = false; // Uncheck other radio inputs
                        input.removeAttribute('checked'); // Remove the checked attribute
                    }
                });
            });
        });
    });


    $(document).on("click", ".delete-btn", function() {
        var id = $(this).data('id');
        $("#deleteId").val(id);
    });
</script>
<!-- end edit and delete modal data  -->
<?php include('src/common/footer.php'); ?>
<!-- pagination  -->
<script>
    $(document).ready(function() {
        var table = $('#leavetypeTable').DataTable({
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
<!-- end pagination  -->