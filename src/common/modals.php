<!-- User Apply Leave -->
<div class="modal modal-blur fade" id="user-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" name="end_date"
                                        required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <!-- <span class="input-icon-addon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-message">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                <path d="M8 9h8" />
                                                <path d="M8 13h6" />
                                                <path d="M18 4a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-5l-5 3v-3h-2a3 3 0 0 1 -3 -3v-8a3 3 0 0 1 3 -3h12z" />
                                            </svg>
                                        </span> -->
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">
                        <span>បង្កើតសំណើ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- deputy head of office Apply Leave -->
<div class="modal modal-blur fade" id="do-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/dof-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">បោះបង់</button>
                    <button type="submit" class="btn btn-primary">
                        <span>បង្កើតសំណើ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Head Office Apply Leave -->
<div class="modal modal-blur fade" id="head-office-apply-leave" tabindex="-1" aria-labelledby="headOfficeModalLabel"
    aria-hidden="true">
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
                                <label for="leave_type_hof" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select ts-select" id="leave_type_hof" name="leave_type_id" required>
                                    <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                    <?php foreach ($leavetypes as $leavetype): ?>
                                        <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                            data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                            <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                            <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-6">
                                <label for="start_date_hof" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="start_date_hof" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="end_date_hof" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="end_date_hof"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ផ្ទេរសិទ្ធ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select select-people" id="transfer_id_hof" name="transferId"
                                    required>
                                    <option value="">ផ្ទេរសិទ្ធ</option>
                                    <?php foreach ($depoffice['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($depoffice['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($depoffice['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($depoffice['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-12">
                                <label for="remarks_hof" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea rows="5" class="form-control" id="remarks_hof" name="remarks"
                                        placeholder="មូលហេតុ"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
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

<!-- deputy head of department apply leave -->
<div class="modal modal-blur fade" id="dd-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/ddep-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
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
                    <button type="submit" class="btn btn-primary">
                        <span>បង្កើតសំណើ</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Head Of Department Apply Leave -->
<div class="modal modal-blur fade" id="head-of-department" tabindex="-1" aria-labelledby="headOfDepartmentModalLabel"
    aria-hidden="true">
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
                            <label for="leave_type_hod" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type_hod" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name_hod" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label for="start_date_hof" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="start_date_hof" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="end_date_hof" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <span class="input-icon-addon">
                                        <!-- SVG icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                            viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="4" y="5" width="16" height="16" rx="2"></rect>
                                            <line x1="16" y1="3" x2="16" y2="7"></line>
                                            <line x1="8" y1="3" x2="8" y2="7"></line>
                                            <line x1="4" y1="11" x2="20" y2="11"></line>
                                            <rect x="8" y="15" width="2" height="2"></rect>
                                        </svg>
                                    </span>
                                    <input type="text" autocomplete="off"
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="end_date_hof"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">ផ្ទេរសិទ្ធ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <select class="form-select select-people" id="transfer_id_hod" name="transferId"
                                    required>
                                    <option value="">ផ្ទេរសិទ្ធ</option>
                                    <?php foreach ($depdepart['ids'] as $index => $id): ?>
                                        <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                            data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($depdepart['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                            <?= htmlspecialchars($depdepart['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($depdepart['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="leave_type_name_hod" name="leave_type_name"
                                    value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                            </div>
                            <div class="col-lg-12">
                                <label for="remarks_hod" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea rows="5" class="form-control" id="remarks_hod" name="remarks"
                                        placeholder="មូលហេតុ"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>
                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
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

<!-- Deputy unit 1 Apply Leave -->
<div class="modal modal-blur fade" id="du1-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/du1-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
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
                                <button type="submit" class="btn btn-primary w-100">បង្កើតសំណើ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- deputy of unit 2 Apply Leave -->
<div class="modal modal-blur fade" id="du2-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/du2-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
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
                                <button type="submit" class="btn btn-primary w-100">បង្កើតសំណើ</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Head Unit Apply Leave -->
<div class="modal modal-blur fade" id="unit-apply" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><strong>បង្កើតសំណើ</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="/elms/hunit-apply-leave" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label fw-bold">ប្រភេទច្បាប់<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select ts-select" id="leave_type" name="leave_type_id" required>
                                <option value="">ជ្រើសរើសប្រភេទច្បាប់</option>
                                <?php foreach ($leavetypes as $leavetype): ?>
                                    <option value="<?= $leavetype['id'] ?>" data-leave-name="<?= $leavetype['name'] ?>"
                                        data-custom-properties='<span class="badge <?= $leavetype['color'] ?>"></span>'
                                        <?= (isset($_POST['leave_type_id']) && $_POST['leave_type_id'] == $leavetype['id']) ? 'selected' : '' ?>>
                                        <?= $leavetype['name'] ?>     <?= $leavetype['document_status'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-6 mb-3">
                                <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទចាប់ពី<span
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
                                        value="<?= htmlspecialchars($_POST['start_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="កាលបរិច្ឆេទចាប់ពី" class="form-control leave-picker"
                                        id="lstart_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label for="end_date" class="form-label fw-bold">ដល់កាលបរិច្ឆេទ<span
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
                                        value="<?= htmlspecialchars($_POST['end_date'] ?? date('Y-m-d'), ENT_QUOTES, 'UTF-8'); ?>"
                                        placeholder="ដល់កាលបរិច្ឆេទ" class="form-control leave-picker" id="lend_date"
                                        name="end_date" required>
                                </div>
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label for="reason" class="form-label fw-bold">មូលហេតុ<span
                                        class="text-danger mx-1 fw-bold">*</span></label>
                                <div class="input-icon">
                                    <textarea type="text" autocomplete="off" placeholder="មូលហេតុ" rows="5"
                                        class="form-control" id="remarks" name="remarks"
                                        required><?= htmlspecialchars($_POST['remarks'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 attachment-file-container">
                                <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                                <input type="file" name="attachment" class="attachment-file form-control" />
                            </div>
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
                                <button type="submit" class="btn btn-primary w-100">បង្កើតសំណើ</button>
                            </div>
                        </div>
                    </div>
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
                            <input class="form-check-input" type="checkbox" name="agree" <?= isset($_POST['agree']) ? 'checked' : ''; ?> required>
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
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title mb-0">លិខិតពួ្ររការងារ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/apply-hold" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
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
                        <div class="col-12">
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
                        <div class="col-12">
                            <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="file" class="form-control" id="attachment" name="attachment[]"
                                        accept=".pdf,.docx" multiple>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">អ្នកអនុម័ត<span
                                    class="text-danger mx-1 fw-bold">*</span></label>
                            <select class="form-select select-people" id="transfer_id_hof" name="approverId" required>
                                <?php if (isset($approver['ids'][0])): ?>
                                    <option value="<?= htmlspecialchars($approver['ids'][0], ENT_QUOTES, 'UTF-8') ?>"
                                        data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][0], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                        <?= htmlspecialchars($approver['lastNameKh'][0], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][0], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endif; ?>

                                <?php foreach ($approver['ids'] as $index => $id): ?>
                                    <option value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>"
                                        data-custom-properties="&lt;span class=&quot;avatar avatar-xs&quot; style=&quot;background-image: url('https://hrms.iauoffsa.us/images/<?= htmlspecialchars($approver['image'][$index], ENT_QUOTES, 'UTF-8') ?>')&quot;&gt;&lt;/span&gt;">
                                        <?= htmlspecialchars($approver['lastNameKh'][$index], ENT_QUOTES, 'UTF-8') . " " . htmlspecialchars($approver['firstNameKh'][$index], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="hidden" id="leave_type_name_hof" name="leave_type_name"
                                value="<?= htmlspecialchars($_POST['leave_type_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <div class="col-12">
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
                        <div class="col-12">
                            <label class="form-check">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="agree" required>
                                <span class="form-check-label cursor-pointer">ខ្ញុំយល់ព្រម
                                    និងទទួលស្គាល់លើទិន្នន័យដែលបានបញ្ចូល។<span
                                        class="text-danger mx-1 fw-bold">*</span></span>
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
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title mb-0">លិខិតផ្ទេរចេញ</h5>
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
                                <select name="fromDepartment" id="department" class="form-select ts-select">
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
                                <select name="toDepartment" id="department" class="form-select ts-select">
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
                                <select name="fromOffice" id="office" class="form-select ts-select">
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
                                <select name="toOffice" id="offices" class="form-select ts-select">
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
                                    class="form-control" name="reason" required></textarea>
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
                <div class="modal-footer bg-light">
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
            <div class="modal-header bg-primary text-light">
                <h5 class="modal-title mb-0">លិខិតលារឈប់</h5>
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
                <div class="modal-footer bg-light">
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
<div class="modal modal-blur fade" id="backwork" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-light mb-0">លិខិតបន្តការងារ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/elms/apply-backwork" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="start_date" class="form-label fw-bold">កាលបរិច្ឆេទ
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
                        <div class="col-12">
                            <label class="form-label fw-bold">ឯកសារភ្ជាប់</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="file" class="form-control" id="attachment" name="attachment[]"
                                        accept=".pdf,.docx" multiple>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
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
                        <div class="col-12">
                            <label class="form-check">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="agree" required>
                                <span class="form-check-label cursor-pointer">ខ្ញុំយល់ព្រម
                                    និងទទួលស្គាល់លើទិន្នន័យដែលបានបញ្ចូល។<span
                                        class="text-danger mx-1 fw-bold">*</span></span>
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
                                <button type="submit" class="btn btn-primary w-100">បញ្ជូន</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>