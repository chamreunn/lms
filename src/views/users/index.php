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

ob_start();
$title = "បង្កើតគណនី";
include('create_user_modal.php');
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
                        <input type="search" class="form-control d-inline-block w-9 me-3" value="Search user…" />
                        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-create-user">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <line x1="12" y1="5" x2="12" y2="19" />
                                <line x1="5" y1="12" x2="19" y2="12" />
                            </svg>
                            New user
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

<div class="row row-cards">
    <?php if (isset($users['data']) && is_array($users['data'])): ?>
        <?php foreach ($users['data'] as $key => $value): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card rounded-3 shadow-sm">
                    <div class="card-body p-4 text-center">
                        <span class="avatar avatar-xl mb-3"
                            style="background-image: url(https://hrms.iauoffsa.us/images/<?= $value['image'] ?? "default_image.svg" ?>); object-fit: cover;"></span>
                        <h3 class="m-0 mb-1">
                            <form action="<?= '/elms/view_detail' ?>" method="POST" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?= $value['id'] ?>">
                                <a href="<?= '/elms/view_detail?user_id=' . urlencode($value['id']) ?>">
                                    <?= htmlspecialchars($value['lastNameKh'] . " " . $value['firstNameKh'] ?? '') ?>
                                </a>
                            </form>
                        </h3>
                        <div class="mb-2">
                            <span
                                class="badge <?= $value['position_color'] ?? '' ?>"><?php echo htmlspecialchars($value['role']); ?></span>
                        </div>
                        <div class="text-muted mb-2">
                            <?= htmlspecialchars($value['office_name'] ?? $value['department_name'] ?? '') ?>
                        </div>
                        <div class="mb-0">
                            <?php if ($value['active'] === '1'): ?>
                                <span class="badge bg-success-lt"><?= "Active" ?></span>
                            <?php else: ?>
                                <span class="badge bg-secondary-lt"><?= "Inactive" ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="d-flex">
                        <a href="<?= '/elms/edit_user_detail?user_id=' . urlencode($value['id']) ?>" class="card-btn"
                            data-bs-toggle="tooltip" title="កែប្រែ">
                            <!-- Download SVG icon from http://tabler-icons.io/i/edit -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                <path d="M16 5l3 3" />
                            </svg>
                            កែប្រែ
                        </a>
                        <a href="#" class="card-btn text-danger" data-bs-toggle="modal"
                            data-bs-target="#modal-delete-user-<?= $value['id'] ?>">
                            <!-- Download SVG icon from http://tabler-icons.io/i/trash -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                <path stroke="none" d="M0 0h24H24H0z" fill="none" />
                                <path d="M4 7l16 0" />
                                <path d="M10 11l0 6" />
                                <path d="M14 11l0 6" />
                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                            </svg>
                            លុបគណនី
                        </a>
                    </div>
                </div>
            </div>

            <div class="modal modal-blur fade" id="modal-edit-user-<?= $value['id'] ?>" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="/elms/edit_user" method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Create New User</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row align-items-center justify-content-center">
                                    <div class="col-auto">
                                        <div class="avatar avatar-upload rounded">
                                            <img id="avatar-preview-<?= $value['id'] ?>"
                                                src="https://hrms.iauoffsa.us/images/<?= $value['image'] ?>" alt="Avatar"
                                                class="rounded" width="50" height="50">
                                            <input type="file" name="eavatar-upload" id="avatar-upload-<?= $value['id'] ?>"
                                                class="d-none" accept="image/*">
                                            <label for="avatar-upload-<?= $value['id'] ?>"
                                                class="avatar-upload-button d-flex flex-column align-items-center justify-content-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" class="icon">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                    <path d="M12 5l0 14"></path>
                                                    <path d="M5 12l14 0"></path>
                                                </svg>
                                                <span class="avatar-upload-text">Add</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Khmer Name Input Field -->
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">ឈ្មោះមន្ត្រី<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="7" r="4" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                            <input type="text" name="ekhmer_name" class="form-control"
                                                value="<?= $value['lastNameKh'] . " " . $value['firstNameKh'] ?>">
                                        </div>
                                    </div>

                                    <!-- English Name Input Field -->
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">English Name<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="7" r="4" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                            <input type="text" name="eenglish_name" class="form-control"
                                                value="<?= $value['engName'] ?>">
                                        </div>
                                    </div>

                                    <!-- Username Input Field -->
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">Username<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <circle cx="12" cy="7" r="4" />
                                                    <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
                                                </svg>
                                            </span>
                                            <input type="text" name="eusername" class="form-control"
                                                value="<?= $value['engName'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">អាសយដ្ឋានអ៊ីមែល<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                                    <path d="M3 7l9 6l9 -6" />
                                                </svg>
                                            </span>
                                            <input type="email" name="eemail" class="form-control"
                                                value="<?= $value['email'] ?>">
                                        </div>
                                    </div>

                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">ភេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                                        <select class="form-select" id="eselect-gender-<?= $value['id'] ?>" name="egender">
                                            <option selected value="<?= $value['gender'] ?>"><?= $value['gender'] ?></option>
                                            <option value="ប្រុស">ប្រុស</option>
                                            <option value="ស្រី">ស្រី</option>
                                        </select>
                                    </div>

                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">ស្ថានភាពសកម្ម<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <select class="form-select" id="eselect-status-<?= $value['id'] ?>" name="estatus">
                                            <option selected value="<?= $value['active'] ?>"><?= $value['active'] ?></option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-lg-4 col-sm-12 mb-3">
                                        <label class="form-label">លេខទូរស័ព្ទ<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span class="input-icon-addon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-phone">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path
                                                        d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
                                                </svg>
                                            </span>
                                            <input type="text" name="ephone" class="form-control"
                                                value="<?= $value['phoneNumber'] ?>">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-3 col-sm-12 mb-3">
                                        <label class="form-label">នាយកដ្ឋាន<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <select class="form-select" name="edepartment"
                                            id="eselect-department-<?= $value['id'] ?>" required>
                                            <option selected value="<?= $value['department_id'] ?>">
                                                <?= $value['department_name'] ?>
                                            </option>
                                            <?php foreach ($departments as $department): ?>
                                                <option value="<?= $department['id'] ?>"><?= $department['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-sm-12 mb-3">
                                        <label class="form-label">ការិយាល័យ</label>
                                        <select class="form-select" name="eoffice" id="eselect-office-<?= $value['id'] ?>">
                                            <option selected value="<?= $value['office_id'] ?>"><?= $value['office_name'] ?>
                                            </option>
                                            <?php foreach ($offices as $office): ?>
                                                <option value="<?= $office['id'] ?>"><?= $office['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-sm-12 mb-3">
                                        <label class="form-label">តួនាទី<span class="text-danger mx-1 fw-bold">*</span></label>
                                        <select class="form-select" name="eposition" id="eselect-position-<?= $value['id'] ?>"
                                            required>
                                            <option selected value="<?= $value['role'] ?>"
                                                data-custom-properties='&lt;span class="badge <?= $position['color'] ?>"&gt;'>
                                                <?= $value['role'] ?>
                                            </option>
                                            <?php foreach ($positions as $position): ?>
                                                <option value="<?= $position['id'] ?>"
                                                    data-custom-properties='&lt;span class="badge <?= $position['color'] ?>"&gt;'>
                                                    <?= $position['name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-sm-12 mb-3">
                                        <label class="form-label">Roles<span class="text-danger mx-1 fw-bold">*</span></label>
                                        <select class="form-select" name="erole" id="eselect-role-<?= $value['id'] ?>" required>
                                            <option selected value="<?= $value['role'] ?>"><?= $value['role'] ?></option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= $role['name'] ?>"><?= $role['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12 mb-3">
                                        <label class="form-label">អាសយដ្ឋាន<span
                                                class="text-danger mx-1 fw-bold">*</span></label>
                                        <div class="input-icon">
                                            <span
                                                class="input-icon-addon"><!-- Download SVG icon from http://tabler-icons.io/i/calendar -->
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"
                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-home-link">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M20.085 11.085l-8.085 -8.085l-9 9h2v7a2 2 0 0 0 2 2h4.5" />
                                                    <path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 1.807 1.143" />
                                                    <path d="M21 21m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M21 16m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M16 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                    <path d="M21 16l-5 3l5 2" />
                                                </svg>
                                            </span>
                                            <textarea class="form-control" name="eaddress"><?= $value['address'] ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-top">
                                <div class="w-100">
                                    <div class="row">
                                        <div class="col">
                                            <input type="hidden" name="user_id" value="<?= $value['id'] ?>">
                                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-primary ms-auto w-100">រក្សាទុក</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <!-- script for select  -->
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    new TomSelect("#eselect-gender-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    new TomSelect("#eselect-status-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    new TomSelect("#eselect-department-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    new TomSelect("#eselect-office-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    new TomSelect("#eselect-position-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    new TomSelect("#eselect-role-<?= $value['id'] ?>", {
                        copyClassesToDropdown: false,
                        dropdownClass: "dropdown-menu ts-dropdown",
                        optionClass: "dropdown-item",
                        controlInput: "<input>",
                        render: {
                            item: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                            option: function (data, escape) {
                                if (data.customProperties) {
                                    return '<div><span class="dropdown-item-indicator">' + data.customProperties + "</span>" + escape(data.text) + "</div>";
                                }
                                return "<div>" + escape(data.text) + "</div>";
                            },
                        },
                    });

                    // Initialize Litepicker for date of birth
                    var edobPicker = new Litepicker({
                        element: document.getElementById("edob-datepicker-<?= $value['id'] ?>"),
                        singleMode: true,
                        format: "YYYY-MM-DD",
                        buttonText: {
                            previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                            nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                        },
                    });
                    // change profile 
                    document.addEventListener('DOMContentLoaded', function () {
                        const avatarUploadInput = document.getElementById('avatar-upload-<?= $value['id'] ?>');
                        const avatarPreview = document.getElementById('avatar-preview-<?= $value['id'] ?>');

                        avatarUploadInput.addEventListener('change', function () {
                            const file = this.files[0];

                            if (file) {
                                const reader = new FileReader();

                                reader.onload = function (e) {
                                    avatarPreview.src = e.target.result;
                                };

                                reader.readAsDataURL(file);
                            }
                        });
                    });
                });
            </script>

            <!-- Delete User Modal -->
            <div class="modal modal-blur fade" id="modal-delete-user-<?= $value['id'] ?>" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-status bg-danger"></div>
                        <form action="/elms/delete_user" method="POST">
                            <div class="modal-body text-center py-4 mb-0">
                                <input type="hidden" name="user_id" value="<?= $value['id'] ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="icon mb-2 text-danger icon-lg">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M12 9v4"></path>
                                    <path
                                        d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z">
                                    </path>
                                    <path d="M12 16h.01"></path>
                                </svg>
                                <h5 class="modal-title">លុបគណនី</h5>
                                <p>តើអ្នកប្រាកដទេថានិងលុបគណនី <span
                                        class="text-danger fw-bold"><?= htmlspecialchars($value['firstNameKh']) ?></span> នេះ?
                                </p>
                            </div>
                            <div class="modal-footer bg-light border-top">
                                <div class="w-100 mt-3">
                                    <div class="row">
                                        <div class="col">
                                            <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                                        </div>
                                        <div class="col">
                                            <button type="submit" class="btn btn-danger ms-auto w-100">បាទ / ចា៎</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
no data
    <?php endif; ?>
    <div class="col-md-6 col-lg-3">
        <a href="#" class="card h-100 d-flex align-items-center justify-content-center d-block" data-bs-toggle="modal"
            data-bs-target="#modal-create-user">
            <div class="card-body d-flex justify-content-center align-items-center fs-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                    <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                    <path d="M12 5l0 14" />
                    <path d="M5 12l14 0" />
                </svg>
            </div>
        </a>
    </div>
</div>

<!-- pagination  -->
<div class="d-flex mt-4">
    <ul class="pagination ms-auto">
        <li class="page-item">
            <a class="page-link" href="#" tabindex="-1" aria="true">
                <!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                    <polyline points="15 6 9 12 15 18" />
                </svg>
                prev
            </a>
        </li>
        <li class="page-item"><a class="page-link" href="#">1</a></li>
        <li class="page-item active"><a class="page-link" href="#">2</a></li>
        <li class="page-item"><a class="page-link" href="#">3</a></li>
        <li class="page-item"><a class="page-link" href="#">4</a></li>
        <li class="page-item"><a class="page-link" href="#">5</a></li>
        <li class="page-item">
            <a class="page-link" href="#">
                next <!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24h24H0z" fill="none" />
                    <polyline points="9 6 15 12 9 18" />
                </svg>
            </a>
        </li>
    </ul>
</div>
<?php include('src/common/footer.php'); ?>

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