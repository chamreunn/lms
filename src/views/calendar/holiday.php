<?php
$title = "ថ្ងៃឈប់សម្រាក";
include('src/common/header.php');
?>

<!-- header of page  -->
<div class="page-header d-print-none mt-0 mb-3">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <div class="page-pretitle">
                    គ្រប់គ្រង
                </div>
                <h2 class="page-title"> <?= $title ?> </h2>
            </div>
            <!-- Page title actions -->
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
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
                </div>
            </div>
        </div>
    </div>
</div>

<!-- body of page  -->

<div class="container-xl">
    <div class="card rounded-3">
        <div class="card-header">
            <h3 class="card-title"><?= $title ?></h3>
        </div>
        <div class="d-none d-md-block">
            <!-- Regular table for larger screens -->
            <table id="leavetypeTable" class="table card-table table-vcenter text-nowrap datatable">
                <thead>
                    <tr>
                        <th>ឈ្មោះប្រភេទច្បាប់</th>
                        <th>ពណ៌</th>
                        <th>រយៈពេល</th>
                        <th width="20">ពិពណ៌នា</th>
                        <th>សកម្មភាព</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($getHolidays)): ?>
                        <tr>
                            <td colspan="7" class="text-center">
                                <img src="public/img/icons/svgs/empty.svg" alt="">
                                <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team"
                                    class="btn btn-primary mb-3">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <line x1="12" y1="5" x2="12" y2="19" />
                                        <line x1="5" y1="12" x2="19" y2="12" />
                                    </svg>
                                    បន្ថែមថ្ងៃឈប់សម្រាក
                                </a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($getHolidays as $holiday): ?>
                            <tr>
                                <td><?= $holiday['holiday_name'] ?></td>
                                <td>
                                    <span class="badge bg-<?= $holiday['color'] ?>"><?= $holiday['holiday_name'] ?></span>
                                </td>
                                <td><?= $holiday['holiday_date'] ?></td>
                                <td><?= $holiday['holiday_description'] ?></td>
                                <td>
                                    <a href="#" class="icon me-2 edit-btn" data-bs-toggle="modal"
                                        data-bs-target="#editHoliday<?= $holiday['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round" class="icon icon-tabler icon-tabler-edit">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                            <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                            <path d="M16 5l3 3" />
                                        </svg>
                                    </a>
                                    <a href="#" class="icon delete-btn text-danger" data-bs-toggle="modal"
                                        data-bs-target="#deleteHoliday<?= $holiday['id'] ?>">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
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

                            <!-- edit  -->
                            <div class="modal modal-blur fade" id="editHoliday<?= $holiday['id'] ?>" tabindex="-1"
                                aria-modal="true" role="dialog">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">កែប្រែ <?= $title ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="/elms/updateHoliday" method="POST">
                                            <input type="hidden" name="id" value="<?= $holiday['id'] ?>">
                                            <div class="modal-body">
                                                <div class="row g-3 mb-3 align-items-end">
                                                    <div class="col-12">
                                                        <label class="form-label">ឈ្មោះថ្ងៃឈប់សម្រាក<span
                                                                class="text-danger mx-1 fw-bold">*</span>
                                                        </label>
                                                        <input type="text" name="holidayName"
                                                            value="<?= $holiday['holiday_name'] ?>" class="form-control"
                                                            required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">កាលបរិច្ឆេទ<span
                                                                class="text-danger mx-1 fw-bold">*</span>
                                                        </label>
                                                        <input type="text" name="holidayDate"
                                                            value="<?= $holiday['holiday_date'] ?>"
                                                            class="form-control date-picker" required>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">ប្រភេទថ្ងៃឈប់សម្រាក<span
                                                                class="text-danger mx-1 fw-bold">*</span></label>
                                                        <select class="form-select ts-select" name="holidayType" required>
                                                            <option selected value="<?= $holiday['holiday_type'] ?>">
                                                                <?= $holiday['holiday_type'] ?>
                                                            </option>
                                                            <option value="National">National</option>
                                                            <option value="Regional">Regional</option>
                                                            <option value="Religious">Religious</option>
                                                            <option value="Public">Public</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">ពណ៌នា</label>
                                                    <textarea class="form-control"
                                                        name="holidayDescription"><?= $holiday['holiday_description'] ?></textarea>
                                                </div>
                                                <div>
                                                    <label class="form-label">ជ្រើសរើសពណ៌</label>
                                                    <div class="row g-2">
                                                        <?php
                                                        $colors = ['dark', 'white', 'blue', 'azure', 'indigo', 'purple', 'pink', 'red', 'orange', 'yellow', 'lime'];
                                                        foreach ($colors as $color):
                                                            $checked = ($color === $holiday['color']) ? 'checked' : '';
                                                            ?>
                                                            <div class="col-auto">
                                                                <label class="form-colorinput">
                                                                    <input name="color" type="radio" value="<?= $color ?>"
                                                                        class="form-colorinput-input" <?= $checked ?>>
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
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- delete  -->
                            <div class="modal modal-blur fade" id="deleteHoliday<?= $holiday['id'] ?>" tabindex="-1"
                                role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-status bg-danger"></div>
                                        <form action="/elms/deleteHoliday" method="POST">
                                            <div class="modal-body text-center py-4 mb-0">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon mb-2 text-danger icon-lg">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 9v4"></path>
                                                    <path
                                                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                                    </path>
                                                    <path d="M12 16h.01"></path>
                                                </svg>
                                                <h5 class="modal-title">លុបថ្ងៃឈប់សម្រាក</h5>
                                                <p>តើអ្នកប្រាកដទេថានិងលុប <span
                                                        class="text-danger fw-bold"><?= htmlspecialchars($holiday['holiday_name']) ?></span>
                                                    នេះ?
                                                </p>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <div class="w-100">
                                                    <div class="row">
                                                        <div class="col">
                                                            <button type="button" class="btn w-100"
                                                                data-bs-dismiss="modal">បោះបង់</button>
                                                        </div>
                                                        <input type="hidden" name="id" value="<?= $holiday['id'] ?>">
                                                        <div class="col">
                                                            <button type="submit" class="btn btn-red w-100">យល់ព្រម</button>
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

        <!-- Collapsible view for small screens -->
        <div class="accordion d-md-none" id="leaveTypeAccordion">
            <?php if (empty($holiday)): ?>
                <div class="text-center">
                    <img src="public/img/icons/svgs/empty.svg" alt="">
                    <p>មិនទាន់មានប្រភេទច្បាប់ថ្មីនៅឡើយ។ សូមបង្កើតដោយចុចប៊ូតុងខាងក្រោយ ឬស្តាំដៃខាងលើ</p>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#modal-team" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <line x1="12" y1="5" x2="12" y2="19" />
                            <line x1="5" y1="12" x2="19" y2="12" />
                        </svg>
                        បន្ថែមថ្ងៃឈប់សម្រាក
                    </a>
                </div>
            <?php else: ?>
                <?php foreach ($getHolidays as $key => $holiday): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $key ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapse<?= $key ?>" aria-expanded="false" aria-controls="collapse<?= $key ?>">
                                <?= $holiday['holiday_name'] ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $key ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $key ?>"
                            data-bs-parent="#leaveTypeAccordion">
                            <div class="accordion-body">
                                <p><strong>ពណ៌:</strong> <span
                                        class="badge bg-<?= $holiday['color'] ?>"><?= $holiday['color'] ?></span></p>
                                <p><strong>កាលបរិច្ឆេទ:</strong> <?= $holiday['holiday_date'] ?></p>
                                <p><strong>ពិពណ៌នា:</strong> <?= $holiday['holiday_description'] ?></p>
                                <div class="d-flex">

                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
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
                            <input type="text" name="holidayName" placeholder="ឈ្មោះថ្ងៃឈប់សម្រាក" autocomplete="off" class="form-control"
                                required>
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