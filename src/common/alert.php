<?php if (isset($_SESSION['success']) && is_array($_SESSION['success'])) : ?>
    <div id="customAlert" class="col-10 col-lg-3 col-md-10 col-sm-10 alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3 shadow animate__animated animate__bounceInDown" style="z-index: 999999;" role="alert">
        <div class="d-flex align-items-center">
            <div class="p-2 rounded-circle border border-success">
                <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </div>
            <div class="ms-2 w-100">
                <h4 class="alert-title"><?php echo htmlentities($_SESSION['success']['title']); ?></h4>
                <div class="text-secondary"><?php echo htmlentities($_SESSION['success']['message']); ?></div>
            </div>
        </div>
        <div class="progress mt-2" style="height: 2px; position: absolute; bottom: 0; left: 0; width: 100%;">
            <div class="progress-bar bg-success" id="successProgress" role="progressbar" style="width: 100%;"></div>
        </div>
    </div>
    <script>
        var successAlert = document.getElementById('customAlert');
        var successProgress = document.getElementById('successProgress');
        var successDuration = 5000; // Duration in milliseconds
        var successInterval = setInterval(function() {
            successDuration -= 100;
            var progressWidth = (successDuration / 5000) * 100;
            successProgress.style.width = progressWidth + '%';

            if (successDuration <= 0) {
                clearInterval(successInterval);
                successAlert.classList.remove('animate__bounceInDown');
                successAlert.classList.add('animate__bounceOutUp');
                setTimeout(function() {
                    successAlert.remove();
                }, 1000); // Allow animation to complete before removing element
            }
        }, 100);
    </script>
<?php unset($_SESSION['success']);
endif; ?>

<?php if (isset($_SESSION['error']) && is_array($_SESSION['error'])) : ?>
    <div id="errorAlert" class="col-10 col-lg-3 col-md-10 col-sm-10 alert alert-danger position-fixed top-0 start-50 translate-middle-x mt-3 shadow animate__animated animate__bounceInDown" style="z-index: 999999;" role="alert">
        <div class="d-flex align-items-center">
            <div class="p-2 rounded-circle border border-danger">
                <!-- Download SVG icon from http://tabler-icons.io/i/check -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-x">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M18 6l-12 12" />
                    <path d="M6 6l12 12" />
                </svg>
            </div>
            <div class="ms-2 w-100">
                <h4 class="alert-title"><?php echo htmlentities($_SESSION['error']['title']); ?></h4>
                <div class="text-secondary"><?php echo htmlentities($_SESSION['error']['message']); ?></div>
            </div>
        </div>
        <div class="progress mt-2" style="height: 2px; position: absolute; bottom: 0; left: 0; width: 100%;">
            <div class="progress-bar bg-danger" id="errorProgress" role="progressbar" style="width: 100%;"></div>
        </div>
    </div>
    <script>
        var errorAlert = document.getElementById('errorAlert');
        var errorProgress = document.getElementById('errorProgress');
        var errorDuration = 5000; // Duration in milliseconds
        var errorInterval = setInterval(function() {
            errorDuration -= 100;
            var progressWidth = (errorDuration / 5000) * 100;
            errorProgress.style.width = progressWidth + '%';

            if (errorDuration <= 0) {
                clearInterval(errorInterval);
                errorAlert.classList.remove('animate__bounceInDown');
                errorAlert.classList.add('animate__bounceOutUp');
                setTimeout(function() {
                    errorAlert.remove();
                }, 1000); // Allow animation to complete before removing element
            }
        }, 100);
    </script>
<?php unset($_SESSION['error']);
endif; ?>
