<div class="modal modal-blur fade" id="modal-add-position" tabindex="-1" position="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" position="document">
        <div class="modal-content">
            <form action="/elms/create_position" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Position</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name<span class="text-danger mx-1 fw-bold">*</span></label>
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
                    <div class="w-100 mt-3">
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