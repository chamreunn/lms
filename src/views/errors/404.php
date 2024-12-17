<?php
$title = "គណនីត្រូវបានបិទបណ្តោះអាសន្ន";
require 'src/common/head.php';

// Check if user clicked on the link to unset session variables
if (isset($_GET['unset_session'])) {
    unset($_SESSION['blocked_user']);
    unset($_SESSION['user_profile']);
    unset($_SESSION['user_khmer_name']);
    // Redirect or perform any other action after unsetting sessions if needed
    header('Location: /elms/dashboard'); // Redirect to dashboard or another page
    exit;
}
?>

<div class="page page-center">
    <div class="container-tight py-5">
        <div class="empty">
            <!-- <div class="img">
                <img src="public/img/icons/svgs/notfound.svg" width="600" height="300"
                    alt="គណនីត្រូវបានបិទបណ្តោះអាសន្ន">
            </div> -->

            <dotlottie-player src="https://lottie.host/81c2a352-748a-487c-ac59-907ec9214ef3/RRDjwEWss2.lottie"
                background="transparent" speed="1" style="width: 800px; height: 400px;" loop autoplay>
            </dotlottie-player>

            <!-- <p class="empty-title">សូមអភ័យទោស... មិនមានទំព័រនេះទេ</p> -->
            <p class="empty-subtitle text-secondary">
                យើងសោកស្ដាយ ប៉ុន្តែទំព័រដែលអ្នកកំពុងស្វែងរក មិនមានទេ។
            </p>
            <div class="empty-action">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <!-- Download SVG icon from http://tabler-icons.io/i/arrow-left -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l14 0"></path>
                        <path d="M5 12l6 6"></path>
                        <path d="M5 12l6 -6"></path>
                    </svg>
                    ត្រឡប់ទៅទំព័រមុន
                </a>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php') ?>