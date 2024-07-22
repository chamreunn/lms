<div class="modal modal-blur fade" id="modal-edit-role" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="/elms/update_role" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label class="form-label">Name<span class="text-danger mx-1 fw-bold">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description<span class="text-danger mx-1 fw-bold">*</span></label>
                        <input type="text" class="form-control" name="description" required>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top">
                    <div class="w-100 mt-3">
                        <div class="row">
                            <div class="col">
                                <button type="button" class="btn w-100" data-bs-dismiss="modal">Cancel</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn w-100 btn-primary ms-auto">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>