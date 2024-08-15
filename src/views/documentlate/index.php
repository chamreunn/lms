<?php
$title = "ប្រភេទលិខិតយឺ";
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
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary mb-3">
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
                        <th>ថ្ងៃបង្កើត</th>
                        <th>ថ្ងៃកែប្រែ</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($getlates)) : ?>
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
                        <?php foreach ($getlates as $getlate) : ?>
                            <tr>
                                <td><?= $getlate['id'] ?></td>
                                <td><?= $getlate['name'] ?></td>
                                <td><span class="badge <?= $getlate['color'] ?>"><?= $getlate['name'] ?></span></td>
                                <td><?= $getlate['created_at'] ?></td>
                                <td><?= $getlate['updated_at'] ?></td>
                                <td>
                                    <a href="#" class="icon me-2 edit-btn" data-bs-toggle="modal" data-bs-target="#edit<?= $getlate['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                    <a href="#" class="icon delete-btn text-danger" data-bs-toggle="modal" data-bs-target="#delete<?= $getlate['id'] ?>">
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

                            <!-- Create office Modal -->
                            <div class="modal modal-blur fade" id="edit<?= $getlate['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="/elms/update_late" method="POST">
                                            <div class="modal-header">
                                                <h5 class="modal-title">កែប្រែលិខិត</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" value="<?= $getlate['id'] ?>">
                                                <div class="mb-3">
                                                    <label class="form-label">ឈ្មោះលិខិត<span class="text-danger mx-1 fw-bold">*</span></label>
                                                    <input type="text" class="form-control" value="<?= htmlspecialchars($getlate['name']) ?>" name="name" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">ជ្រើសរើសព័ណ៌<span class="text-danger fw-bold mx-1">*</span></label>
                                                    <div class="row g-2">
                                                        <?php
                                                        $colors = ['bg-dark', 'bg-white', 'bg-blue', 'bg-azure', 'bg-indigo', 'bg-purple', 'bg-pink', 'bg-red', 'bg-orange', 'bg-yellow', 'bg-lime'];
                                                        foreach ($colors as $color) : ?>
                                                            <div class="col-auto">
                                                                <label class="form-colorinput">
                                                                    <input name="color" type="radio" value="<?= $color ?>" class="form-colorinput-input" <?= ($getlate['color'] == $color) ? 'checked' : '' ?>>
                                                                    <span class="form-colorinput-color <?= $color ?>"></span>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light border-top">
                                                <div class="w-100">
                                                    <div class="row">
                                                        <div class="col">
                                                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                                        </div>
                                                        <div class="col">
                                                            <button type="submit" class="btn w-100 btn-primary ms-auto">រក្សាទុក</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Delete office Modal -->
                            <div class="modal modal-blur fade" id="delete<?= $getlate['id'] ?>" tabindex="-1" style="display: none;" aria-hidden="true">
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
                                            <div>ប្រសិនបើអ្នកលុប <span class="text-danger fw-bold"><?= $getlate['name'] ?></span> អ្នកនឹងបាត់បង់ទិន្នន័យនេះជាអចិន្រ្តៃ។</div>
                                        </div>
                                        <div class="modal-status bg-danger"></div>
                                        <form action="/elms/delete_late" method="post">
                                            <div class="modal-footer bg-light border-top">
                                                <div class="w-100">
                                                    <div class="row">
                                                        <div class="col">
                                                            <input type="hidden" name="id" value="<?= $getlate['id'] ?>">
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
            <form action="/elms/create_late" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">បង្កើតលិខិតថ្មី</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះលិខិត<span class="text-danger mx-1 fw-bold">*</span></label>
                        <input type="text" class="form-control" name="name" required>
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
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">Submit</button>
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