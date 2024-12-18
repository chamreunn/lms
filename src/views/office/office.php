<?php
require_once('src/controllers/OfficeController.php');
require_once('src/controllers/DepartmentController.php');
require_once('src/controllers/UserController.php');

$userController = new UserController();
$users = $userController->index();
$officecontroller = new OfficeController();
$offices = $officecontroller->index();
$controller = new DepartmentController();
$departments = $controller->index();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create'])) {
        $data = [
            'name' => $_POST['dname'],
            'did' => $_POST['did'],
            'hoid' => $_POST['hoid'],
            'dhoid' => $_POST['dhoid'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $officecontroller->store($data); // Pass $data to store method
        header('Location: /elms/office');
        exit();
    } elseif (isset($_POST['update'])) {
        $data = [
            'id' => $_POST['id'],
            'name' => $_POST['dname'],
            'did' => $_POST['did'],
            'hoid' => $_POST['hoid'],
            'dhoid' => $_POST['dhoid'],
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $officecontroller->update($data);
        header('Location: /elms/office');
        exit();
    } elseif (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $officecontroller->delete($id);
        header('Location: /elms/office');
        exit();
    }
}

$title = "ការិយាល័យ";
ob_start();
?>
<!-- <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css"> -->
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            បង្កើតការិយាល័យថ្មី
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
<!-- display office  -->
<div class="col-12">
    <div class="card rounded-3">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
        </div>
        <div class="table-responsive">
            <table id="officeTable" class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th></th>
                        <th>ឈ្មោះការិយាល័យ</th>
                        <th>នាយកដ្ឋាន</th>
                        <th>ប្រធានការិយាល័យ</th>
                        <th>អនុប្រធានការិយាល័យ</th>
                        <th>ថ្ងៃបង្កើត</th>
                        <th>ថ្ងៃកែប្រែ</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($offices)) : ?>
                        <tr>
                            <td colspan="8" class="text-center">
                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                <p>មិនទាន់មានការិយាល័យនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary mb-3">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    បង្កើតការិយាល័យថ្មី
                                </a>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($offices as $office) : ?>
                            <tr>
                                <td><?= $office['id'] ?></td>
                                <td><?= $office['name'] ?></td>
                                <td><?= $office['department_name'] ?></td>
                                <td><?= $office['head_khmer_name'] ?></td>
                                <td><?= $office['deputy_head_khmer_name'] ?></td>
                                <td><?= $office['created_at'] ?></td>
                                <td><?= $office['updated_at'] ?></td>
                                <td>
                                    <a href="#" class="icon me-2 edit-btn" data-id="<?= $office['id'] ?>" data-name="<?= $office['name'] ?>" data-did="<?= $office['department_id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                    <a href="#" class="icon delete-btn text-danger" data-id="<?= $office['id'] ?>" data-name="<?= $office['name'] ?>" data-bs-toggle="modal" data-bs-target="#deleteModal">
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
        <div class="card-footer d-flex align-items-center">
            <p class="m-0 text-secondary">Showing <span id="showing-start">1</span> to <span id="showing-end">8</span> of <span id="total-entries">16</span> entries</p>
            <ul id="custom-pagination" class="pagination m-0 ms-auto"></ul>
        </div>
    </div>
</div>
<!-- Create office Modal -->
<div class="modal modal-blur fade" id="modal-team" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">បង្កើតការិយាល័យថ្មី</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/create_office" method="post">
                <div class="modal-body">
                    <div class="col mb-3">
                        <label class="form-label">ឈ្មោះការិយាល័យ<span class="text-danger fw-bold mx-1">*</span></label>
                        <input type="text" name="dname" class="form-control" placeholder="ឈ្មោះការិយាល័យ" required>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">ឈ្មោះនាយកដ្ឋាន</label>
                        <select type="text" name="did" class="form-select" placeholder="Select a date" id="select-users" value="">
                            <option selected disabled>ជ្រើសរើសនាយកដ្ឋាន</option>
                            <?php foreach ($departments as $department) : ?>
                                <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">ប្រធានការិយាល័យ</label>
                        <select type="text" name="hdid" class="form-select" id="select-office" value="">
                            <option selected disabled>ជ្រើសរើសនាយកដ្ឋាន</option>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id'] ?>"><?= $user['khmer_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">អនុប្រធានការិយាល័យ</label>
                        <select type="text" name="dhdid" class="form-select" id="select-department" value="">
                            <option selected disabled>ជ្រើសរើសនាយកដ្ឋាន</option>
                            <?php foreach ($users as $user) : ?>
                                <option value="<?= $user['id'] ?>"><?= $user['khmer_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">ពិពណ៌នា</label>
                        <textarea type="text" name="description" class="form-control" placeholder="ពិពណ៌នា"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn me-auto w-100" data-bs-dismiss="modal">បិទ</button>
                            </div>
                            <div class="col">
                                <button type="submit" name="create" class="btn btn-primary w-100">រក្សាទុក</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Edit office Modal -->
<div class="modal modal-blur fade" id="editModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">កែប្រែ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/office" method="post">
                <div class="modal-body">
                    <div class="row mb-3 align-items-end">
                        <div class="col-12 mb-3">
                            <label class="form-label">ឈ្មោះនាយកដ្ឋាន</label>
                            <input type="hidden" name="id" id="edit-id">
                            <input type="text" name="dname" id="edit-name" class="form-control" placeholder="ឈ្មោះនាយកដ្ឋាន" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">នាយកដ្ឋាន</label>
                            <select name="did" id="edit-did" class="form-select" required>
                                <option value="" disabled>Select a department</option>
                                <?php foreach ($departments as $department) : ?>
                                    <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">ប្រធានការិយាល័យ</label>
                            <select name="hoid" id="edit-hoid" class="form-select" required>
                                <option value="" disabled>Select a head of office</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['khmer_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">អនុប្រធានការិយាល័យ</label>
                            <select name="dhoid" id="edit-dhoid" class="form-select" required>
                                <option value="" disabled>Select a deputy head of office</option>
                                <?php foreach ($users as $user) : ?>
                                    <option value="<?= $user['id'] ?>"><?= $user['khmer_name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn me-auto w-100" data-bs-dismiss="modal">បិទ</button>
                            </div>
                            <div class="col">
                                <button type="submit" name="update" class="btn btn-primary w-100">រក្សាទុក</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Delete office Modal -->
<div class="modal modal-blur fade" id="deleteModal" tabindex="-1" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body text-center py-4 mb-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon mb-2 text-danger icon-lg">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M12 9v4"></path>
                    <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z"></path>
                    <path d="M12 16h.01"></path>
                </svg>
                <div class="modal-title fw-bold">តើអ្នកប្រាកដទេ?</div>
                <div>ប្រសិនបើអ្នក <span class="text-danger fw-bold">យល់ព្រម</span> អ្នកនឹងបាត់បង់ទិន្នន័យនេះជាអចិន្រ្តៃ។</div>
            </div>
            <div class="modal-status bg-danger"></div>
            <form action="/elms/office" method="post">
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <input type="hidden" name="id" id="delete-id">
                                <button type="button" class="btn btn-outline me-auto w-100" data-bs-dismiss="modal">បោះបង់</button>
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

<?php include('src/common/footer.php'); ?>
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
<!-- tomselect -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-users'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));

        window.TomSelect && (new TomSelect(el = document.getElementById('select-office'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        }));

        window.TomSelect && (new TomSelect(el = document.getElementById('select-department'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
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
<!-- edit  -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var selectEditDid = new TomSelect('#edit-did', {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        });

        var selectEditHoid = new TomSelect('#edit-hoid', {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        });

        var selectEditDhoid = new TomSelect('#edit-dhoid', {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function(data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
            },
        });

        // Function to update edit modal select on edit button click
        $(document).on("click", ".edit-btn", function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var did = $(this).data('did'); // Department ID
            var hoid = $(this).data('hoid'); // Head of office ID
            var dhoid = $(this).data('dhoid'); // Deputy head of office ID

            $("#edit-id").val(id);
            $("#edit-name").val(name);

            // Clear existing options and add new ones for each select element
            selectEditDid.clearOptions();
            <?php foreach ($departments as $department) : ?>
                selectEditDid.addOption({
                    value: <?= $department['id'] ?>,
                    text: '<?= addslashes($department['name']) ?>'
                });
            <?php endforeach; ?>

            selectEditHoid.clearOptions();
            <?php foreach ($users as $user) : ?>
                selectEditHoid.addOption({
                    value: <?= $user['id'] ?>,
                    text: '<?= addslashes($user['khmer_name']) ?>'
                });
            <?php endforeach; ?>

            selectEditDhoid.clearOptions();
            <?php foreach ($users as $user) : ?>
                selectEditDhoid.addOption({
                    value: <?= $user['id'] ?>,
                    text: '<?= addslashes($user['khmer_name']) ?>'
                });
            <?php endforeach; ?>

            // Set the selected values in the dropdowns
            selectEditDid.setValue(did);
            selectEditHoid.setValue(hoid);
            selectEditDhoid.setValue(dhoid);

            // Show the edit modal
            $('#editModal').modal('show');
        });
    });

    $(document).on("click", ".delete-btn", function() {
        var id = $(this).data('id');
        $("#delete-id").val(id);
    });
</script>