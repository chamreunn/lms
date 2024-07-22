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

// Include necessary files
require_once 'src/controllers/PositionController.php';
require_once 'src/models/Position.php';

$title = "តួនាទី";
$controller = new PositionController();
$positions = $controller->index(); // Fetch all positions

include('src/common/header.php');
include('add_position_modal.php');
include('edit_position_modal.php');
include('delete_position_modal.php');
?>
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<!-- Page header -->
<div class="page-header d-print-none mt-0">
    <div class="col-12">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle mx-1">
                    ទំព័រដើម
                </div>
                <h2 class="page-title">
                    <?php echo htmlspecialchars($title ?? ""); ?>
                </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    <div class="d-flex">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-position" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            បង្កើតតួនាទីថ្មី
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- positions Table -->
<div class="col-12">
    <div class="card mt-3 rounded-3">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
        </div>
        <div class="table-responsive">
            <table id="positionTable" class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>ឈ្មោះតួនាទី</th>
                        <th>ពណ៌</th>
                        <th>ថ្ងៃបង្កើត</th>
                        <th>ថ្ងៃកែប្រែ</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($positions)) : ?>
                        <tr>
                            <td colspan="4" class="text-center">
                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                <p>មិនទាន់មានតួនាទីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-position" class="btn btn-primary mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    បង្កើតតួនាទីថ្មី
                                </a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($positions as $position) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($position['name']); ?></td>
                                <td>
                                    <div class="badge <?= htmlspecialchars($position['color']) ?>"><?php echo htmlspecialchars($position['name']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($position['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($position['updated_at']); ?></td>
                                <td>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-edit-position" data-position-id="<?php echo htmlspecialchars($position['id']); ?>" data-position-color="<?= htmlspecialchars($position['color']) ?>" data-position-name="<?php echo htmlspecialchars($position['name']); ?>" class="icon me-2 edit-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-delete-position" data-position-id="<?php echo htmlspecialchars($position['id']); ?>" class="icon delete-btn text-danger">
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
<?php
include('src/common/footer.php'); ?>
<script>
    // Pass data to edit modal
    document.addEventListener('DOMContentLoaded', function() {
        var editPositionModal = document.getElementById('modal-edit-position');
        editPositionModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var positionId = button.getAttribute('data-position-id');
            var positionName = button.getAttribute('data-position-name');
            var positionColor = button.getAttribute('data-position-color');

            var modalTitle = editPositionModal.querySelector('.modal-title');
            var modalBodyInputName = editPositionModal.querySelector('input[name="name"]');
            var modalBodyInputId = editPositionModal.querySelector('input[name="id"]');

            modalTitle.textContent = 'Edit position: ' + positionName;
            modalBodyInputName.value = positionName;
            modalBodyInputId.value = positionId;

            // Uncheck all color options and check the correct one
            var colorInputs = editPositionModal.querySelectorAll('input[name="color"]');
            colorInputs.forEach(function(input) {
                if (input.value === positionColor) {
                    input.checked = true; // Check the radio input with the correct value
                    input.setAttribute('checked', 'checked'); // Add the checked attribute for compatibility
                } else {
                    input.checked = false; // Uncheck other radio inputs
                    input.removeAttribute('checked'); // Remove the checked attribute
                }
            });
        });

        var deleteModal = document.getElementById('modal-delete-position');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var positionId = button.getAttribute('data-position-id'); // Extract info from data-* attributes
            var input = deleteModal.querySelector('#delete-position-id'); // Find the hidden input field
            input.value = positionId; // Set the value of the hidden input field
        });
    });
</script>
<style>
    /* Hide the default pagination controls */
    #positionTable_paginate {
        display: none;
    }
</style>
<!-- pagination footer  -->
<script>
    $(document).ready(function() {
        var table = $('#positionTable').DataTable({
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