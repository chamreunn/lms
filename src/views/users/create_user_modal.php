<?php
require_once('src/controllers/DepartmentController.php');
require_once('src/controllers/OfficeController.php');
require_once('src/controllers/PositionController.php');
require_once('src/controllers/RoleController.php');

$rolecontroller = new RoleController();
$roles = $rolecontroller->index(); // Fetch all roles

$positioncontroller = new PositionController();
$positions = $positioncontroller->index(); // Fetch all positions

$officecontroller = new OfficeController();
$offices = $officecontroller->index();

$controller = new DepartmentController();
$departments = $controller->index();
?>
<!-- Create User Modal -->
<div class="modal modal-blur fade" id="modal-create-user" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/elms/create_user" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3 align-items-center justify-content-center">
                        <div class="col-auto">
                            <div class="avatar avatar-upload rounded">
                                <!-- Image preview -->
                                <img id="avatar-preview" src="default-avatar.png" alt="Avatar" class="rounded" width="50" height="50">
                                <!-- Hidden file input -->
                                <input type="file" name="avatar-upload" id="avatar-upload" class="d-none" accept="image/*">
                                <!-- Upload button -->
                                <label for="avatar-upload" class="avatar-upload-button d-flex flex-column align-items-center justify-content-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M12 5l0 14"></path>
                                        <path d="M5 12l14 0"></path>
                                    </svg>
                                    <span class="avatar-upload-text">Add</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-end mb-3">
                        <!-- Khmer Name Input Field -->
                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">ឈ្មោះមន្ត្រី<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <input type="text" name="khmer_name" class="form-control" placeholder="Enter Khmer Name">
                            </div>
                        </div>

                        <!-- English Name Input Field -->
                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">English Name<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <input type="text" name="english_name" class="form-control" placeholder="Enter English Name">
                            </div>
                        </div>

                        <!-- Username Input Field -->
                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">Username<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <circle cx="12" cy="7" r="4" />
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                    </svg>
                                </span>
                                <input type="text" name="username" class="form-control" placeholder="Enter Username">
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">អាសយដ្ឋានអ៊ីមែល<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                        <path d="M3 7l9 6l9 -6" />
                                    </svg>
                                </span>
                                <input type="email" name="email" class="form-control" placeholder="អាសយដ្ឋានអ៊ីមែល">
                            </div>
                        </div>

                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">ពាក្យសម្ងាត់<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-password-user">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 17v4" />
                                        <path d="M10 20l4 -2" />
                                        <path d="M10 18l4 2" />
                                        <path d="M5 17v4" />
                                        <path d="M3 20l4 -2" />
                                        <path d="M3 18l4 2" />
                                        <path d="M19 17v4" />
                                        <path d="M17 20l4 -2" />
                                        <path d="M17 18l4 2" />
                                        <path d="M9 6a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
                                        <path d="M7 14a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2" />
                                    </svg>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="ពាក្យសម្ងាត់">
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-12">
                            <label class="form-label">ភេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select type="text" class="form-select" placeholder="Select a date" id="select-gender" name="gender">
                                <option selected disabled>ជ្រើសរើសភេទ</option>
                                <option value="ប្រុស">ប្រុស</option>
                                <option value="ស្រី">ស្រី</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-6 col-sm-12">
                            <label class="form-label">លេខទូរស័ព្ទ<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler-icons.io/i/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                    </svg>
                                </span>
                                <input type="text" name="phonenumber" class="form-control" data-mask="(+855) 000-000-000-0" data-mask-visible="true" placeholder="(+855) 000-000-000-0" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 mb-3">
                            <label class="form-label">ថ្ងៃខែឆ្នាំកំណើត<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <rect x="4" y="5" width="16" height="16" rx="2" />
                                        <line x1="16" y1="3" x2="16" y2="7" />
                                        <line x1="8" y1="3" x2="8" y2="7" />
                                        <line x1="4" y1="11" x2="20" y2="11" />
                                        <line x1="11" y1="15" x2="12" y2="15" />
                                        <line x1="12" y1="15" x2="12" y2="18" />
                                    </svg>
                                </span>
                                <input class="form-control" name="dob" placeholder="ជ្រើសរើសថ្ងៃខែឆ្នាំកំណើត" id="datepicker-icon-prepend" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-3 col-sm-12 mb-3">
                            <label class="form-label">នាយកដ្ឋាន<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" name="department" id="select_department" required>
                                <option selected disabled>ជ្រើសរើសនាយកដ្ឋាន</option>
                                <?php foreach ($departments as $department) : ?>
                                    <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-12 mb-3">
                            <label class="form-label">ការិយាល័យ</label>
                            <select class="form-select" name="office" id="select_office">
                                <option selected disabled>ជ្រើសរើសការិយាល័យ</option>
                                <?php foreach ($offices as $office) : ?>
                                    <option value="<?= $office['id'] ?>"><?= $office['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-12 mb-3">
                            <label class="form-label">តួនាទី<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" name="position" id="select_position" required>
                                <option selected disabled>ជ្រើសរើសតួនាទី</option>
                                <?php foreach ($positions as $position) : ?>
                                    <option value="<?= $position['id'] ?>" data-custom-properties='&lt;span class="badge <?= $position['color'] ?>"&gt;'>
                                        <div class="mx-3"><?= $position['name'] ?></div>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-3 col-sm-12 mb-3">
                            <label class="form-label">Roles<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" name="role" id="select_role" required>
                                <option selected disabled>Choose Roles</option>
                                <?php foreach ($roles as $role) : ?>
                                    <option value="<?= $role['name'] ?>"><?= $role['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-lg-12">
                            <label class="form-label">អាសយដ្ឋាន<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-home-link">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M20.085 11.085l-8.085 -8.085l-9 9h2v7a2 2 0 0 0 2 2h4.5" />
                                        <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 1.807 1.143" />
                                        <path d="M21 21m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M21 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M16 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                        <path d="M21 16l-5 3l5 2" />
                                    </svg>
                                </span>
                                <textarea class="form-control" name="address" placeholder="អាសយដ្ឋាន"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col mb-2">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">បង្កើតគណនី</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- change profile  -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const avatarUploadInput = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');

        avatarUploadInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    });
</script>

<style>
    .avatar-upload {
        position: relative;
        display: inline-block;
        cursor: pointer;
        width: 120px;
        height: 120px;
    }

    .avatar-upload img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .avatar-upload-button {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        color: white;
        opacity: 0;
        transition: opacity 0.3s;
        border-radius: 10px;
    }

    .avatar-upload:hover .avatar-upload-button {
        opacity: 1;
        cursor: pointer;
    }
</style>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        window.Litepicker &&
            new Litepicker({
                element: document.getElementById("datepicker-icon-prepend"),
                buttonText: {
                    previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                },
            });
    });
    // @formatter:on
</script>
<!-- select gender  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select-gender")), {
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
    });
    // @formatter:on
</script>
<!-- select department  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select_department")), {
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
    });
    // @formatter:on
</script>
<!-- select offices  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select_office")), {
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
    });
    // @formatter:on
</script>
<!-- select position  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select_position")), {
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
    });
    // @formatter:on
</script>
<!-- select role  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function() {
        var el;
        window.TomSelect &&
            new TomSelect((el = document.getElementById("select_role")), {
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
    });
    // @formatter:on
</script>