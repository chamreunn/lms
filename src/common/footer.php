</div>
</div>
</div>
</div>

<!-- Head Office Apply Leave -->
<div class="modal modal-blur fade" id="head-office-apply-leave" tabindex="-1" aria-labelledby="headOfficeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/hof-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="leave_type_hof" class="form-label fw-bold">ប្រភេទច្បាប់<span class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select ts-select" id="leave_type_hof" name="leave_type_id" required>
                                    <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                    <?php foreach ($leavetypes as $leavetype): ?>
                                        <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                            data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                            <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                            <?= $leavetype['name'] ?> <?= $leavetype['document_status'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-6">
                                <label for="start_date_hof" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker" id="start_date_hof" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="end_date_hof" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ដល់កាលបរិច្ឆេទ" class="form-control date-picker" id="end_date_hof" name="end_date" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ផ្ទេរសិទ្ធ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select select-people" id="transfer_id_hof" name="transferId" required>
                                    <option value="">ផ្ទេរសិទ្ធ</option>
                                    <?php foreach ($depoffice['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($depoffice['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($depoffice['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($depoffice['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-12">
                                <label for="remarks_hof" class="form-label fw-bold">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea rows="5" class="form-control" id="remarks_hof" name="remarks" placeholder="មូលហេតុ" required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 attachment-file-container">
                                <label class="form-label">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">បង្កើតសំណើ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Head Of Department Apply Leave -->
<div class="modal modal-blur fade" id="head-of-department" tabindex="-1" aria-labelledby="headOfDepartmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/hod-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type_hod" class="form-label fw-bold">ប្រភេទច្បាប់<span class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type_hod" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?> <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name_hod" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="start_date_hod" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['start_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker" id="start_date_hod" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="end_date_hod" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off" value="<?= htmlspecialchars($_POST['end_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="ដល់កាលបរិច្ឆេទ" class="form-control date-picker" id="end_date_hod" name="end_date" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ផ្ទេរសិទ្ធ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select select-people" id="transfer_id_hod" name="transferId" required>
                                    <option value="">ផ្ទេរសិទ្ធ</option>
                                    <?php foreach ($depdepart['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($depdepart['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($depdepart['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($depdepart['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hod" name="leave_type_name" value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-12">
                                <label for="remarks_hod" class="form-label fw-bold">មូលហេតុ<span class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea rows="5" class="form-control" id="remarks_hod" name="remarks" placeholder="មូលហេតុ" required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 attachment-file-container">
                                <label class="form-label">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">បង្កើតសំណើ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Apply Late In -->
<div class="modal modal-blur fade" id="apply-late-in" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>ចូលយឺត</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/apply_latein" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12 mb-3">
                            <label for="lateindate" class="form-label fw-bold">កាលបរិច្ឆេទចាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                        <line x1="16" y1="3" x2="16" y2="7"></line>
                                        <line x1="8" y1="3" x2="8" y2="7"></line>
                                        <line x1="4" y1="11" x2="20" y2="11"></line>
                                        <rect x="8" y="15" width="2" height="2"></rect>
                                    </svg>
                                </span>
                                <input type="text" autocomplete="off"
                                    value="<?= isset($_POST['date']) ? translateDateToKhmer($_POST['date'], 'j F, Y') : '' ?>"
                                    placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control date-picker" name="date">
                            </div>
                        </div>
                        <div class="col-lg-12 mb-3">
                            <label class="form-label">ម៉ោង<span class="text-danger mx-1 fw-bold">*</span></label>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                        <path d="M12 7v5l.5 .5" />
                                        <path
                                            d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                        <path d="M15 21v-6" />
                                    </svg>
                                </span>
                                <input type="text" autocomplete="off" value="09:00" placeholder="ម៉ោង"
                                    class="form-control time-picker" id="time" name="time">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">មូលហេតុ<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                                name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                        </div>
                        <div class="col-lg-6">
                            <label class="form-check cursor-pointer">
                                <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                                <span class="form-check-label">យល់ព្រមលើការបញ្ចូល<span
                                        class="text-danger fw-bold mx-1">*</span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">
                                    បញ្ចូន
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Late Out -->
<div class="modal modal-blur fade" id="apply-late-out" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply_lateout" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">ចេញយឺត</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">កាលបរិច្ឆេទ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទចាប់ពី"
                                class="form-control date-picker" name="date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">ម៉ោង<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" value="17:30" placeholder="ម៉ោង"
                                class="form-control time-picker" id="time" name="time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">មូលហេតុ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                            name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើការបញ្ចូល<span
                                    class="text-danger fw-bold mx-1">*</span></span>
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

<!-- Modal Apply Leave Early  -->
<div class="modal modal-blur fade" id="apply-leaveearly" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply_leaveearly" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">ចេញមុន</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="leftbefore" class="form-label">កាលបរិច្ឆេទ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទ"
                                class="form-control date-picker" id="leftbefore" name="date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ម៉ោង<span class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" value="16:00" placeholder="ម៉ោង"
                                class="form-control time-picker" id="time" name="time">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">មូលហេតុ<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <textarea autocomplete="off" placeholder="មូលហេតុ" class="form-control" id="reason"
                            name="reason"><?= htmlspecialchars($_POST['reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើការបញ្ចូល<span
                                    class="text-danger fw-bold mx-1">*</span></span>
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

<!-- Modal Apply Mission -->
<div class="modal modal-blur fade" id="mission" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/apply-mission" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">បេសកកម្ម</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ឈ្មោះបេសកកម្ម</label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="ឈ្មោះបេសកកម្ម" class="form-control"
                                id="mission_start" name="mission_name">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ជ្រើសរើសគណនី</label>
                        <select type="text" class="form-select select-people" name="user_id"
                            placeholder="Select a person">
                            <option value="NULL" selected disable>សូមជ្រើសរើសគណនី</option>
                            <?php foreach ($getAllUser['data'] as $user): ?>
                                <option value="<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($user['image'], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                    <?= htmlspecialchars($user['firstNameKh'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">កាលបរិច្ឆេទចាប់ពី<span
                                class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទ"
                                class="form-control date-picker" name="start_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ដល់កាលបរិចេ្ឆទ<span class="text-danger mx-1 fw-bold">*</span></label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-clock-12">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path d="M3 12a9 9 0 0 0 9 9m9 -9a9 9 0 1 0 -18 0" />
                                    <path d="M12 7v5l.5 .5" />
                                    <path
                                        d="M18 15h2a1 1 0 0 1 1 1v1a1 1 0 0 1 -1 1h-1a1 1 0 0 0 -1 1v1a1 1 0 0 0 1 1h2" />
                                    <path d="M15 21v-6" />
                                </svg>
                            </span>
                            <input type="text" autocomplete="off" placeholder="ដល់កាលបរិចេ្ឆទ"
                                class="form-control date-picker" name="end_date">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="createMissionDoc" class="form-label">ឯកសារភ្ជាប់</label>
                        <label id="createMissionName" for="createMissionDoc"
                            class="btn w-100 text-start p-3 flex-column text-muted bg-light">
                            <span class="p-1">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="icon icon-tabler icons-tabler-outline icon-tabler-signature mx-0">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M3 17c3.333 -3.333 5 -6 5 -8c0 -3 -1 -3 -2 -3s-2.032 1.085 -2 3c.034 2.048 1.658 4.877 2.5 6c1.5 2 2.5 2.5 3.5 1l2 -3c.333 2.667 1.333 4 3 4c.53 0 2.639 -2 3 -2c.517 0 1.517 .667 3 2" />
                                </svg>
                            </span>
                            <span>ឯកសារភ្ជាប់</span>
                        </label>
                        <input type="file" name="missionDoc" id="createMissionDoc" accept=".pdf, .docx, .xlsx" required
                            hidden onchange="displayFileName('createMissionDoc', 'createMissionName')" />
                    </div>
                    <div class="mb-3">
                        <label class="form-check cursor-pointer">
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?>>
                            <span class="form-check-label">យល់ព្រមលើការបញ្ចូល
                                <span class="text-danger fw-bold mx-1">*</span>
                            </span>
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

<!-- Modal Apply Hold -->
<div class="modal modal-blur fade" id="hold" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary mb-0"><?= $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/apply-hold" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី
                            <span class="text-danger mx-1 fw-bold">*</span>
                        </label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <!-- Retain the value of start_date -->
                            <input type="text" autocomplete="off" placeholder="កាលបរិច្ឆេទចាប់ពី"
                                class="form-control date-picker" name="start_date"
                                value="<?= isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ
                            <span class="text-danger mx-1 fw-bold">*</span>
                        </label>
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                    <line x1="16" y1="3" x2="16" y2="7"></line>
                                    <line x1="8" y1="3" x2="8" y2="7"></line>
                                    <line x1="4" y1="11" x2="20" y2="11"></line>
                                    <rect x="8" y="15" width="2" height="2"></rect>
                                </svg>
                            </span>
                            <!-- Retain the value of end_date -->
                            <input type="text" autocomplete="off" placeholder="ដល់កាលបរិច្ឆេទ"
                                class="form-control date-picker" name="end_date"
                                value="<?= isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : '' ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                        <div class="row g-2">
                            <div class="col">
                                <input type="file" name="attachment" accept=".pdf, .docx, .xlsx" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label fw-bold">មូលហេតុ
                            <span class="text-danger mx-1 fw-bold">*</span>
                        </label>
                        <div class="input-icon">
                            <!-- Retain the value of the reason textarea -->
                            <textarea type="text" rows="5" cols="5" autocomplete="off" placeholder="មូលហេតុ"
                                class="form-control"
                                name="reason"><?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '' ?></textarea>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-check">
                            <input class="form-check-input cursor-pointer" type="checkbox" name="agree" required>
                            <span class="form-check-label cursor-pointer">ខ្ញុំយល់ព្រម
                                និងទទួលស្គាល់លើទិន្នន័យដែលបានបញ្ចូល។<span
                                    class="text-danger mx-1 fw-bold">*</span></span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">បញ្ជូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Apply transferout -->
<div class="modal modal-blur fade" id="transferout" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary mb-0"><?= $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/apply-transferout" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-6 col-sm-12 col-md-12">
                            <label for="start_date" class="form-label fw-bold">ពីនាយកដ្ឋាន
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <select name="department" id="department" class="form-select ts-select">
                                    <option value="<?= $_SESSION['departmentId'] ?>"><?= $_SESSION['departmentName'] ?>
                                    </option>
                                    <?php if (!empty($departments['data'])): ?>
                                        <?php foreach ($departments['data'] as $department): ?>
                                            <option value="<?= htmlspecialchars($department['id']) ?>">
                                                <?= htmlspecialchars($department['departmentNameKh']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No departments available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-md-12">
                            <label for="end_date" class="form-label fw-bold">ទៅនាយកដ្ឋាន
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <select name="department" id="department" class="form-select ts-select">
                                    <option selected disabled>ជ្រើសរើសនាយកដ្ឋាន
                                    </option>
                                    <?php if (!empty($departments['data'])): ?>
                                        <?php foreach ($departments['data'] as $department): ?>
                                            <option value="<?= htmlspecialchars($department['id']) ?>">
                                                <?= htmlspecialchars($department['departmentNameKh']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No departments available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-md-12">
                            <label for="start_date" class="form-label fw-bold">ពីការិយាល័យ
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <select name="offices" id="office" class="form-select ts-select">
                                    <option value="<?= $_SESSION['officeId'] ?>"><?= $_SESSION['officeName'] ?>
                                    </option>
                                    <?php if (!empty($offices['data'])): ?>
                                        <?php foreach ($offices['data'] as $office): ?>
                                            <option value="<?= htmlspecialchars($office['id']) ?>">
                                                <?= htmlspecialchars($office['officeNameKh']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No Offices available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-6 col-sm-12 col-md-12">
                            <label for="end_date" class="form-label fw-bold">ទៅការិយាល័យ
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <select name="offices" id="offices" class="form-select ts-select">
                                    <option selected disabled>ជ្រើសរើសការិយាល័យ
                                    </option>
                                    <?php if (!empty($offices['data'])): ?>
                                        <?php foreach ($offices['data'] as $office): ?>
                                            <option value="<?= htmlspecialchars($office['id']) ?>">
                                                <?= htmlspecialchars($office['officeNameKh']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No offices available</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="file" name="attachment" accept=".pdf, .docx, .xlsx"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label for="reason" class="form-label fw-bold">មូលហេតុ
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <!-- Retain the value of the reason textarea -->
                                <textarea type="text" rows="5" cols="5" autocomplete="off" placeholder="មូលហេតុ"
                                    class="form-control" name="reason"
                                    required><?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '' ?></textarea>
                            </div>
                        </div>
                        <div>
                            <label class="form-check">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="agree" required>
                                <span class="form-check-label cursor-pointer">ខ្ញុំយល់ព្រម
                                    និងទទួលស្គាល់លើទិន្នន័យដែលបានបញ្ចូល។<span
                                        class="text-danger mx-1 fw-bold">*</span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">បញ្ជូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Apply resign -->
<div class="modal modal-blur fade" id="resign" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary mb-0"><?= $title ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/apply-resign" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div>
                            <label for="reason" class="form-label fw-bold">បទពិសោធន៍ការងារ
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <!-- Retain the value of the reason textarea -->
                                <textarea type="text" rows="5" cols="5" autocomplete="off" placeholder="បទពិសោធន៍..."
                                    class="form-control" name="reason"
                                    required><?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '' ?></textarea>
                            </div>
                        </div>
                        <div>
                            <label for="reason" class="form-label fw-bold">មូលហេតុ
                                <span class="text-danger mx-1 fw-bold">*</span>
                            </label>
                            <div class="input-icon">
                                <!-- Retain the value of the reason textarea -->
                                <textarea type="text" rows="5" cols="5" autocomplete="off" placeholder="មូលហេតុ..."
                                    class="form-control" name="reason"
                                    required><?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '' ?></textarea>
                            </div>
                        </div>
                        <div>
                            <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="file" name="attachment" accept=".pdf, .docx, .xlsx"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="form-check">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="agree" required>
                                <span class="form-check-label cursor-pointer">ខ្ញុំយល់ព្រម
                                    និងទទួលស្គាល់លើទិន្នន័យដែលបានបញ្ចូល។<span
                                        class="text-danger mx-1 fw-bold">*</span></span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="w-100">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">បោះបង់</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">បញ្ជូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabler Core -->
<script src="public/dist/js/tabler.min.js?1668287865" defer></script>
<script src="public/dist/js/demo.min.js?1668287865" defer></script>

<script src="public/dist/libs/apexcharts/dist/apexcharts.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/js/jsvectormap.min.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world.js?1668287865" defer></script>
<script src="public/dist/libs/jsvectormap/dist/maps/world-merc.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.js?1668287865" defer></script>

<script src="public/dist/libs/nouislider/dist/nouislider.min.js?1668287865" defer></script>
<script src="public/dist/libs/litepicker/dist/litepicker.js?1668287865" defer></script>
<script src="public/dist/libs/tom-select/dist/js/tom-select.base.min.js?1668287865" defer></script>
<!-- other link  -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<!-- datatables  -->
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<!-- Script to hide the loader with delay -->
<script>
    window.addEventListener('load', function () {
        // Add a delay before removing the loader
        setTimeout(function () {
            var loader = document.getElementById('loader-wrapper');
            document.body.classList.remove('loading');
            loader.style.display = 'none';
        }, 500); // Delay for 0.5 second (500 milliseconds)
    });
</script>
<!-- end  -->

<script>
    document.addEventListener("DOMContentLoaded", function () {
        window.Litepicker && new Litepicker({
            element: document.getElementById("datepicker-inline"),
            buttonText: {
                previousMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-left -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="15 6 9 12 15 18"/>
                </svg>`,
                nextMonth: `<!-- Download SVG icon from http://tabler-icons.io/i/chevron-right -->
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <polyline points="9 6 15 12 9 18"/>
                </svg>`,
            },
            inlineMode: true,
            startDate: new Date(), // Set the active date to today
            autoApply: true,
            singleMode: true,
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Check if element exists before initializing TomSelect
        const leaveTypeElement = document.getElementById("leave_type");
        if (leaveTypeElement) {
            new TomSelect(leaveTypeElement, {
                copyClassesToDropdown: false,
                dropdownClass: "dropdown-menu ts-dropdown",
                optionClass: "dropdown-item",
                controlInput: "<input>",
                render: {
                    item: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                    option: function (data, escape) {
                        return data.customProperties ?
                            `<div><span class="dropdown-item-indicator">${data.customProperties}</span>${escape(data.text)}</div>` :
                            `<div>${escape(data.text)}</div>`;
                    },
                },
            });

            leaveTypeElement.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const leaveTypeName = selectedOption.getAttribute('data-leave-name');
                document.getElementById('leave_type_name').value = leaveTypeName;
            });
        }

        // Check if elements with class 'date-picker' exist before initializing Litepicker
        const dateInputs = document.querySelectorAll('.date-picker');
        dateInputs.forEach(input => {
            new Litepicker({
                element: input,
                singleMode: true,
                format: "YYYY-MM-DD",
                lang: 'kh',
                buttonText: {
                    previousMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>`,
                    nextMonth: `<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="9 6 15 12 9 18" /></svg>`,
                }
            });
        });

        // Initialize Flatpickr for elements with the 'time-picker' class
        const timeInputs = document.querySelectorAll(".time-picker");
        timeInputs.forEach(timeInput => {
            flatpickr(timeInput, {
                enableTime: true,
                noCalendar: true,
                dateFormat: "H:i",
                time_24hr: false,
                defaultHour: 12,
                defaultMinute: 0,
                locale: 'km',
                allowInput: true, // Allow users to type directly into the input field
                minuteIncrement: 1 // Set minute intervals to 1 (instead of 5)
            });
        });

        // Initial setup to ensure signature file input is visible if checkbox is checked
        const signatureCheckbox = document.getElementById('signature');
        if (signatureCheckbox && signatureCheckbox.checked) {
            const signatureFileInput = document.getElementById('signatureFile');
            if (signatureFileInput) {
                signatureFileInput.style.display = 'block';
            }
        }
    });

    function toggleFileInput(checkbox, fileInputId) {
        const fileInput = document.getElementById(fileInputId);
        if (fileInput) {
            fileInput.style.display = checkbox.checked ? 'block' : 'none';
        }
    }

    function displayFileName(inputId, labelId) {
        const input = document.getElementById(inputId);
        const fileNameLabel = document.getElementById(labelId);
        if (input && fileNameLabel) {
            fileNameLabel.textContent = input.files[0] ? input.files[0].name : '';
        }
    }
</script>

<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var el;
        window.TomSelect && (new TomSelect(el = document.getElementById('select-status'), {
            copyClassesToDropdown: false,
            dropdownClass: 'dropdown-menu ts-dropdown',
            optionClass: 'dropdown-item',
            controlInput: '<input>',
            render: {
                item: function (data, escape) {
                    if (data.customProperties) {
                        return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                    }
                    return '<div>' + escape(data.text) + '</div>';
                },
                option: function (data, escape) {
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

<!-- ts select  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        var elements = document.querySelectorAll(".ts-select"); // Select all elements with the class ts-select
        elements.forEach(function (el) {
            window.TomSelect &&
                new TomSelect(el, {
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
        });
    });
    // @formatter:on
</script>

<!-- datatable sort  -->
<script>
    document.querySelectorAll('.table-sort').forEach(function (header) {
        header.addEventListener('click', function () {
            let table = header.closest('.sortable-table');
            let columnIndex = [...header.parentNode.children].indexOf(header);
            let ascending = !header.classList.contains('asc');
            sortTable(table, columnIndex, ascending);
            header.classList.toggle('asc', ascending);
        });
    });

    function sortTable(table, columnIndex, ascending) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.sort((a, b) => {
            const cellA = a.children[columnIndex].innerText.trim();
            const cellB = b.children[columnIndex].innerText.trim();
            return ascending ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
        });
        rows.forEach(row => tbody.appendChild(row));
    }
</script>

<!-- for select people dropdown  -->
<script>
    // @formatter:off
    document.addEventListener("DOMContentLoaded", function () {
        // Select all elements with the class 'select-people'
        const selectElements = document.querySelectorAll('.select-people');

        selectElements.forEach(el => {
            window.TomSelect && (new TomSelect(el, {
                copyClassesToDropdown: false,
                dropdownClass: 'dropdown-menu ts-dropdown',
                optionClass: 'dropdown-item',
                controlInput: '<input>',
                render: {
                    item: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function (data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data.customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    });
    // @formatter:on
</script>

</body>

</html>