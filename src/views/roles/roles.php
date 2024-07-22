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
require_once 'src/controllers/RoleController.php';
require_once 'src/models/Role.php';

$title = "Roles";
$controller = new RoleController();
$roles = $controller->index(); // Fetch all roles

include('src/common/header.php');
include('add_role_modal.php');
include('edit_role_modal.php');
include('delete_role_modal.php');
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-add-role" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            Add New Role
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Roles Table -->
<div class="col-12">
    <div class="card mt-3 rounded-3">
        <div class="card-header">
            <h3 class="card-title">Roles</h3>
        </div>
        <div class="table-responsive">
            <table id="roleTable" class="table table-vcenter card-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($roles as $role) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($role['name']); ?></td>
                            <td><?php echo htmlspecialchars($role['description']); ?></td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-edit-role" data-role-id="<?php echo $role['id']; ?>" data-role-name="<?php echo htmlspecialchars($role['name']); ?>" data-role-description="<?php echo htmlspecialchars($role['description']); ?>" class="icon me-2 edit-btn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                        <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                        <path d="M16 5l3 3" />
                                    </svg>
                                </a>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-delete-role" data-role-id="<?php echo $role['id']; ?>" class="icon delete-btn text-danger">
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
        var editRoleModal = document.getElementById('modal-edit-role');
        editRoleModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var roleId = button.getAttribute('data-role-id');
            var roleName = button.getAttribute('data-role-name');
            var roleDescription = button.getAttribute('data-role-description');

            var modalTitle = editRoleModal.querySelector('.modal-title');
            var modalBodyInputName = editRoleModal.querySelector('input[name="name"]');
            var modalBodyInputDescription = editRoleModal.querySelector('input[name="description"]');
            var modalBodyInputId = editRoleModal.querySelector('input[name="id"]');

            modalTitle.textContent = 'Edit Role: ' + roleName;
            modalBodyInputName.value = roleName;
            modalBodyInputDescription.value = roleDescription;
            modalBodyInputId.value = roleId;
        });

        var deleteModal = document.getElementById('modal-delete-role');
        deleteModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var roleId = button.getAttribute('data-role-id'); // Extract info from data-* attributes
            var input = deleteModal.querySelector('#delete-role-id'); // Find the hidden input field
            input.value = roleId; // Set the value of the hidden input field
        });
    });
</script>
<style>
    /* Hide the default pagination controls */
    #roleTable_paginate {
        display: none;
    }
</style>
<!-- pagination footer  -->
<script>
    $(document).ready(function() {
        var table = $('#roleTable').DataTable({
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