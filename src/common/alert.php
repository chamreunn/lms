<?php
function showAlert($sessionType, $alertType, $iconPath, $bgColor)
{
    if (isset($_SESSION[$sessionType]) && is_array($_SESSION[$sessionType])): ?>
        <div id="customAlert"
            class="col-10 col-lg-3 col-md-10 col-sm-10 alert alert-<?php echo $alertType; ?> position-fixed top-0 start-50 translate-middle-x mt-3 shadow animate__animated"
            style="z-index: 999999;" role="alert">
            <div class="d-flex align-items-center">
                <div class="p-2 rounded-circle border border-<?php echo $alertType; ?>">
                    <!-- SVG icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon icon-tabler icons-tabler-outline icon-tabler-<?php echo $iconPath; ?>">
                        <?php echo $bgColor; ?>
                    </svg>
                </div>
                <div class="ms-2 w-100">
                    <h4 class="alert-title"><?php echo htmlentities($_SESSION[$sessionType]['title']); ?></h4>
                    <div class="text-secondary"><?php echo htmlentities($_SESSION[$sessionType]['message']); ?></div>
                </div>
            </div>
            <div class="progress mt-2" style="height: 2px; position: absolute; bottom: 0; left: 0; width: 100%;">
                <div class="progress-bar bg-<?php echo $alertType; ?>" id="<?php echo $sessionType; ?>Progress"
                    role="progressbar" style="width: 100%;"></div>
            </div>
        </div>
        <script>
            var alert = document.getElementById('customAlert');
            var progress = document.getElementById('<?php echo $sessionType; ?>Progress');
            var duration = 5000; // Duration in milliseconds
            var interval = setInterval(function () {
                duration -= 100;
                var progressWidth = (duration / 5000) * 100;
                progress.style.width = progressWidth + '%';

                if (duration <= 0) {
                    clearInterval(interval);
                    alert.classList.remove('animate__bounceInDown');
                    alert.classList.add('animate__bounceOutUp');
                    setTimeout(function () {
                        alert.remove();
                    }, 1000); // Allow animation to complete before removing element
                }
            }, 100);
        </script>
        <?php unset($_SESSION[$sessionType]);
    endif;
}
?>

<?php
// Display alerts based on priority
if (isset($_SESSION['error'])) {
    showAlert('error', 'danger', 'x', '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M18 6l-12 12" /><path d="M6 6l12 12" />');
} elseif (isset($_SESSION['success'])) {
    showAlert('success', 'success', 'check', '<path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M5 12l5 5l10 -10" />');
} elseif (isset($_SESSION['notification_success'])) {
    showAlert('notification_success', 'info', 'info-circle', '<circle cx="12" cy="12" r="10" /><path d="M12 16v-4" /><path d="M12 8h.01" />');
}
?>