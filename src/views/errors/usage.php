<?php
$title = "របៀបប្រើប្រាស់ប្រព័ន្ធ";
include('src/common/head.php');
?>
<!-- Include AOS CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
<style>
    /* Modern Gradient Background */
    body {
        background: linear-gradient(135deg, #ece9e6 0%, #ffffff 50%, #e6f2ff 100%);
        font-family: 'Khmer-mef1', sans-serif;
        position: relative;
        z-index: 1;
    }

    /* Subtle Texture Overlay */
    body::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.15;
        z-index: 0;
    }

    /* Enhanced Wave Effect */
    .wave-background {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
    }

    .wave-background svg {
        display: block;
    }

    /* Content Layering */
    .main {
        position: relative;
        z-index: 2;
    }

    .step {
        display: flex;
        align-items: center;
    }

    .step-number {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
        font-weight: bold;
    }

    .nav-link:hover {
        background-color: #f8f9fa;
        text-decoration: none;
        color: #0056b3;
    }
    
</style>

<div class="card">
    <header class="sticky-top">
        <a href="." class="btn btn-primary position-absolute mt-3 mx-3" data-bs-toggle="tooltip"
            data-bs-placement="right" data-bs-custom-class="custom-tooltip" data-bs-title="ត្រឡប់ទៅកាន់ទំព័រដើម">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="icon icon-tabler icons-tabler-outline icon-tabler-chevron-left me-0">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M15 6l-6 6l6 6" />
            </svg>
        </a>
    </header>
    <!-- Page Content -->
    <div class="container-fluid px-0">
        <!-- Hero Section -->
        <section
            class="vh-100 d-flex flex-column align-items-center justify-content-center text-center position-relative overflow-hidden"
            data-aos="fade-up">
            <div class="container position-relative z-2">
                <!-- Title -->
                <h2 class="display-4 mb-3 text-primary text-wrap" style="line-height: 100px;" data-aos="fade-up" data-aos-delay="100">
                    របៀបប្រើប្រាស់ប្រព័ន្ធសុំច្បាប់ឌីជីថល | ជំនាន់ទី ១.០
                </h2>

                <!-- Subtitle -->
                <p class="text-primary text-muted fs-5 fs-md-3" data-aos="fade-up" data-aos-delay="200">
                    របស់អង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ (អ.ស.ហ.)
                </p>
            </div>

            <!-- Wave Background -->
            <div class="wave-background position-absolute bottom-0 start-0 w-100">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" class="w-100" style="margin-top: -20px;">
                    <path fill="#e6f2ff" fill-opacity="1"
                        d="M0,288L48,282.7C96,277,192,267,288,256C384,245,480,235,576,208C672,181,768,139,864,128C960,117,1056,139,1152,160C1248,181,1344,203,1392,213.3L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z">
                    </path>
                </svg>
            </div>
        </section>

        <!-- Navigation Menu -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="card border-0 shadow-sm" data-aos="fade-right">
                    <div class="card-body">
                        <ul class="nav flex-column gap-3">
                            <li class="nav-item">
                                <a href="#step1"
                                    class="nav-link d-flex align-items-center text-primary px-3 py-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-login me-3" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 12h10"></path>
                                        <path d="M15 16l5 -4l-5 -4"></path>
                                        <path d="M4 4v16"></path>
                                    </svg>
                                    ១. ការចូលប្រព័ន្ធ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#step2"
                                    class="nav-link d-flex align-items-center text-primary px-3 py-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-file-text me-3" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                        <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h4l4 4v12a2 2 0 0 1 -2 2">
                                        </path>
                                        <path d="M9 9h1"></path>
                                        <path d="M9 13h6"></path>
                                        <path d="M9 17h6"></path>
                                    </svg>
                                    ២. ការដាក់សំណើផ្សេងៗ
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#step3"
                                    class="nav-link d-flex align-items-center text-primary px-3 py-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-lock me-3" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="5" y="11" width="14" height="10" rx="2"></rect>
                                        <circle cx="12" cy="16" r="1"></circle>
                                        <path d="M8 11v-4a4 4 0 1 1 8 0v4"></path>
                                    </svg>
                                    ៣. ការផ្លាស់ប្រូរពាក្យសម្ងាត់
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#step4"
                                    class="nav-link d-flex align-items-center text-primary px-3 py-2 rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="icon icon-tabler icon-tabler-qrcode me-3" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <rect x="4" y="4" width="6" height="6" rx="1"></rect>
                                        <rect x="4" y="14" width="6" height="6" rx="1"></rect>
                                        <rect x="14" y="4" width="6" height="6" rx="1"></rect>
                                        <path d="M16 16h2v2h-2z"></path>
                                        <path d="M20 14v4"></path>
                                        <path d="M14 20h4"></path>
                                    </svg>
                                    ៤. ការបង្កើត QR Code
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 1: Login -->
        <section id="step1" class="py-5" data-aos="fade-up">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6" data-aos="fade-right" data-aos-delay="100">
                        <h1 class="section-title fw-bold text-primary mb-3">១. ការចូលប្រព័ន្ធ</h1>
                        <p style="line-height: 25px; text-indent: 28px;">
                            ការចូលទៅកាន់ <span class="text-primary fw-bold">ប្រព័ន្ធសុំច្បាប់ឌីជីថល</span>
                            របស់អង្គភាពសវនកម្មផ្ទៃក្នុង ដោយចូលទៅកាន់តំណភ្ជាប់ <a
                                href="https://iauowpcoreweb.iauoffsa.us/"
                                target="_blank">https://iauowpcoreweb.iauoffsa.us/</a> រួចវាយបញ្ចូល
                        </p>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="bg-danger-lt text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-mail">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
                                            <path d="M3 7l9 6l9 -6" />
                                        </svg>
                                    </div>
                                    <div class="text-danger mx-2 fw-bold fs-large">អាសយដ្ឋានអ៊ីម៉ែល</div>
                                </div>
                            </li>
                            <li class="mb-2">
                                <div class="d-flex align-items-center justify-content-start">
                                    <div class="bg-danger-lt text-white avatar">
                                        <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-square-asterisk">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M3 3m0 2a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2z" />
                                            <path d="M12 8.5v7" />
                                            <path d="M9 10l6 4" />
                                            <path d="M9 14l6 -4" />
                                        </svg>
                                    </div>
                                    <div class="text-danger mx-2 fw-bold fs-large">ពាក្យសម្ងាត់</div>
                                </div>
                            </li>
                        </ul>
                        <p class="text-muted">
                            <small>ភ្លេចពាក្យសម្ងាត់? <a href="#" class="text-primary">សូមចុចទីនេះ</a>!</small>
                        </p>
                    </div>
                    <div class="col-md-6 text-center" data-aos-delay="300">
                        <img src="public/img/elements/loginPage.png"
                            class="img-fluid shadow-lg rounded-3 w-75 rounded-shadow" alt="Login Screenshot">
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 2: Submit Leave -->
        <section id="step2" class="py-5 bg-light" data-aos="fade-up">
            <div class="container">
                <h1 class="section-title  fw-bold text-primary mb-3">២. ការដាក់សំណើផ្សេងៗ</h1>
                <p>ជ្រើសរើសអាយខនក្នុងការដាក់សំណើផ្សេងៗដូចជា៖</p>
            </div>
            <div class="container-fluid text-dark">
                <!-- leave  -->
                <div class="container">
                    <div class="text-center mt-4">
                        <div class="text-center mt-4">
                            <div class="row g-3">
                                <div class="col-lg-3 col-sm-12" data-aos="fade-up" data-aos-delay="100">
                                    <a href="#leave">
                                        <img src="public/img/elements/leaveRequest.png"
                                            class="img-fluid shadow-sm hover-shadow-lg rounded-3 mb-3 h-100"
                                            alt="Request Screenshot">
                                    </a>
                                </div>
                                <div class="col-lg-3 col-sm-12" data-aos="fade-up" data-aos-delay="200">
                                    <a href="#latein">
                                        <img src="public/img/elements/lateinRequest.png"
                                            class="img-fluid shadow-sm hover-shadow-lg rounded-3 mb-3 h-100"
                                            alt="Request Screenshot">
                                    </a>
                                </div>
                                <div class="col-lg-3 col-sm-12" data-aos="fade-up" data-aos-delay="300">
                                    <a href="#lateout">
                                        <img src="public/img/elements/lateoutRequest.png"
                                            class="img-fluid shadow-sm hover-shadow-lg rounded-3 mb-3 h-100"
                                            alt="Request Screenshot">
                                    </a>
                                </div>
                                <div class="col-lg-3 col-sm-12" data-aos="fade-up" data-aos-delay="400">
                                    <a href="#leaveearly">
                                        <img src="public/img/elements/leaveearlyRequest.png"
                                            class="img-fluid shadow-sm hover-shadow-lg rounded-3 mb-3 h-100"
                                            alt="Request Screenshot">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 p-lg-5 mb-3" id="leave">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12 d-flex justify-content-center" data-aos="fade-left"
                                    data-aos-delay="100">
                                    <img src="public/img/elements/leave.png" class="img-fluid mb-3 me-3 h-100"
                                        alt="Request Screenshot">
                                </div>
                                <div class="col-lg-6 col-sm-12">
                                    <div data-aos="fade-left" data-aos-delay="0" class="aos-init aos-animate">
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="200">
                                            <div class="col-auto">
                                                <div class="bg-primary-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-square-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 12h6" />
                                                        <path d="M12 9v6" />
                                                        <path
                                                            d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បង្កើតសំណើច្បាប់ឈប់សម្រាក</h3>
                                                <p class="text-secondary m-0">ចុចលើ <span
                                                        class="text-primary">សំណើច្បាប់ឈប់សម្រាក</span>
                                                    រួចបំពេញតាមទម្រង់ដែលបានបង្ហាញ។</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-0" data-aos="fade-up" data-aos-delay="300">
                                            <div class="col-auto">
                                                <div class="bg-primary-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-pencil-bolt">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                        <path d="M13.5 6.5l4 4" />
                                                        <path d="M19 16l-2 3h4l-2 3" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>ជ្រើសរើសប្រភេទច្បាប់</h3>
                                                <ul class="d-flex flex-column">
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-blue badge-blink mb-3"></div>
                                                        <p class="mx-2">ច្បាប់ឈប់សម្រាករយៈពេលខ្លី</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-info badge-blink mb-3"></div>
                                                        <p class="mx-2">ច្បាប់ឈប់សម្រាករយៈពេលវែង</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-indigo badge-blink mb-3"></div>
                                                        <p class="mx-2">ច្បាប់ឈប់សម្រាកព្យាបាលជម្ងឺ</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-purple badge-blink mb-3"></div>
                                                        <p class="mx-2">ច្បាប់ឈប់សម្រាកប្រចាំឆ្នាំ</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-danger badge-blink mb-3"></div>
                                                        <p class="mx-2">ច្បាប់ឈប់សម្រាកដោយមានធុរៈផ្ទាល់ខ្លួន</p>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="badge bg-green badge-blink mb-3"></div>
                                                        <p class="mx-2">ផ្សេងៗ</p>
                                                    </div>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="400">
                                            <div class="col-auto">
                                                <div class="bg-primary-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M7 14h.013" />
                                                        <path d="M10.01 14h.005" />
                                                        <path d="M13.01 14h.005" />
                                                        <path d="M16.015 14h.005" />
                                                        <path d="M13.015 17h.005" />
                                                        <path d="M7.01 17h.005" />
                                                        <path d="M10.01 17h.005" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>ជ្រើសរើសកាលបរិចេ្ឆទ</h3>
                                                <p>ដោយជ្រើសរើសកាលបរិច្ឆេទចាប់ផ្តើម និងបញ្ចប់ប្រព័ន្ធនិងធ្វើការគណនា <span
                                                        class="text-danger">ថ្ងៃឈប់សម្រាក</span>
                                                    និងដកចេញដោយស្វ័យប្រវត្តិ។
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-0" data-aos="fade-up" data-aos-delay="500">
                                            <div class="col-auto">
                                                <div class="bg-primary-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-bubble-text">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 10h10" />
                                                        <path d="M9 14h5" />
                                                        <path
                                                            d="M12.4 3a5.34 5.34 0 0 1 4.906 3.239a5.333 5.333 0 0 1 -1.195 10.6a4.26 4.26 0 0 1 -5.28 1.863l-3.831 2.298v-3.134a2.668 2.668 0 0 1 -1.795 -3.773a4.8 4.8 0 0 1 2.908 -8.933a5.33 5.33 0 0 1 4.287 -2.16" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>មូលហេតុនៃការឈប់សម្រាក</h3>
                                                <p>ត្រូវបញ្ចូលនូវហេតុអោយបានត្រឹមត្រូវ។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- latein  -->
                <div class="hr" data-aos-delay="200"></div>
                <div class="py-5" id="latein">
                    <div class="container p-lg-5">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12" data-aos="fade-right" data-aos-delay="100">
                                    <div data-aos="fade-up" data-aos-delay="0" class="aos-init aos-animate">
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="200">
                                            <div class="col-auto">
                                                <div class="bg-green-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-square-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 12h6" />
                                                        <path d="M12 9v6" />
                                                        <path
                                                            d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បង្កើតសំណើចូលយឺត</h3>
                                                <p class="text-secondary m-0">ចុចលើ <span
                                                        class="text-green">សំណើចូលយឺត</span>
                                                    រួចបំពេញតាមទម្រង់ដែលបានបង្ហាញ។</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="300">
                                            <div class="col-auto">
                                                <div class="bg-green-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M7 14h.013" />
                                                        <path d="M10.01 14h.005" />
                                                        <path d="M13.01 14h.005" />
                                                        <path d="M16.015 14h.005" />
                                                        <path d="M13.015 17h.005" />
                                                        <path d="M7.01 17h.005" />
                                                        <path d="M10.01 17h.005" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>ជ្រើសរើសកាលបរិចេ្ឆទ</h3>
                                                <p>ជ្រើសរើសកាលបរិចេ្ឆទដែលបានចូលយឺត</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="400">
                                            <div class="col-auto">
                                                <div class="bg-green-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលម៉ោងចូលយឺត</h3>
                                                <p>បញ្ចូលម៉ោងចូលយឺតដែលបានចូលយឺត</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-0" data-aos="fade-up" data-aos-delay="500">
                                            <div class="col-auto">
                                                <div class="bg-green-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-bubble-text">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 10h10" />
                                                        <path d="M9 14h5" />
                                                        <path
                                                            d="M12.4 3a5.34 5.34 0 0 1 4.906 3.239a5.333 5.333 0 0 1 -1.195 10.6a4.26 4.26 0 0 1 -5.28 1.863l-3.831 2.298v-3.134a2.668 2.668 0 0 1 -1.795 -3.773a4.8 4.8 0 0 1 2.908 -8.933a5.33 5.33 0 0 1 4.287 -2.16" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលមូលហេតុ</h3>
                                                <p>ត្រូវបញ្ចូលនូវហេតុអោយបានត្រឹមត្រូវ។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 d-flex justify-content-center" data-aos="fade-left"
                                    data-aos-delay="600">
                                    <img src="public/img/elements/lateIn.png" class="img-fluid mb-3 h-100"
                                        alt="Request Screenshot">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- lateout  -->
                <div class="hr" data-aos-delay="200"></div>
                <div class="container" id="lateout">
                    <div class="p-lg-5">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12 d-flex justify-content-center" data-aos="fade-left"
                                    data-aos-delay="100">
                                    <img src="public/img/elements/lateOut.png" class="img-fluid mb-3 h-100"
                                        alt="Request Screenshot">
                                </div>
                                <div class="col-lg-6 col-sm-12" data-aos="fade-up" data-aos-delay="200">
                                    <div data-aos="fade-up" data-aos-delay="0" class="aos-init aos-animate">
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="300">
                                            <div class="col-auto">
                                                <div class="bg-orange-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-square-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 12h6" />
                                                        <path d="M12 9v6" />
                                                        <path
                                                            d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បង្កើតសំណើចេញយឺត</h3>
                                                <p class="text-secondary m-0">ចុចលើ <span
                                                        class="text-orange">សំណើចេញយឺត</span>
                                                    រួចបំពេញតាមទម្រង់ដែលបានបង្ហាញ។</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="400">
                                            <div class="col-auto">
                                                <div class="bg-orange-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M7 14h.013" />
                                                        <path d="M10.01 14h.005" />
                                                        <path d="M13.01 14h.005" />
                                                        <path d="M16.015 14h.005" />
                                                        <path d="M13.015 17h.005" />
                                                        <path d="M7.01 17h.005" />
                                                        <path d="M10.01 17h.005" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>ជ្រើសរើសកាលបរិចេ្ឆទ</h3>
                                                <p>ជ្រើសរើសកាលបរិចេ្ឆទដែលបានចេញយឺត</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="500">
                                            <div class="col-auto">
                                                <div class="bg-orange-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលម៉ោងចេញយឺត</h3>
                                                <p>បញ្ចូលម៉ោងចូលយឺតដែលបានចេញយឺត</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-0" data-aos="fade-up" data-aos-delay="600">
                                            <div class="col-auto">
                                                <div class="bg-orange-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-bubble-text">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 10h10" />
                                                        <path d="M9 14h5" />
                                                        <path
                                                            d="M12.4 3a5.34 5.34 0 0 1 4.906 3.239a5.333 5.333 0 0 1 -1.195 10.6a4.26 4.26 0 0 1 -5.28 1.863l-3.831 2.298v-3.134a2.668 2.668 0 0 1 -1.795 -3.773a4.8 4.8 0 0 1 2.908 -8.933a5.33 5.33 0 0 1 4.287 -2.16" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលមូលហេតុ</h3>
                                                <p>ត្រូវបញ្ចូលនូវហេតុអោយបានត្រឹមត្រូវ។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- leaveEarly  -->
                <div class="hr" data-aos-delay="200"></div>
                <div class="container" id="leaveearly">
                    <div class="p-lg-5">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-lg-6 col-sm-12" data-aos="fade-right" data-aos-delay="100">
                                    <div data-aos="fade-up" data-aos-delay="0" class="aos-init aos-animate">
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="200">
                                            <div class="col-auto">
                                                <div class="bg-danger-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-square-plus">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M9 12h6" />
                                                        <path d="M12 9v6" />
                                                        <path
                                                            d="M3 5a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-14z" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បង្កើតសំណើចេញមុន</h3>
                                                <p class="text-secondary m-0">ចុចលើ <span
                                                        class="text-danger">សំណើចេញមុន</span>
                                                    រួចបំពេញតាមទម្រង់ដែលបានបង្ហាញ។</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="300">
                                            <div class="col-auto">
                                                <div class="bg-danger-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-calendar-month">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
                                                        <path d="M16 3v4" />
                                                        <path d="M8 3v4" />
                                                        <path d="M4 11h16" />
                                                        <path d="M7 14h.013" />
                                                        <path d="M10.01 14h.005" />
                                                        <path d="M13.01 14h.005" />
                                                        <path d="M16.015 14h.005" />
                                                        <path d="M13.015 17h.005" />
                                                        <path d="M7.01 17h.005" />
                                                        <path d="M10.01 17h.005" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>ជ្រើសរើសកាលបរិចេ្ឆទ</h3>
                                                <p>ជ្រើសរើសកាលបរិចេ្ឆទដែលបានចេញមុន</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3" data-aos="fade-up" data-aos-delay="400">
                                            <div class="col-auto">
                                                <div class="bg-danger-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-clock">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
                                                        <path d="M12 7v5l3 3" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលម៉ោងចេញមុន</h3>
                                                <p>បញ្ចូលម៉ោងចេញមុនដែលបានចេញមុន</p>
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-0" data-aos="fade-up" data-aos-delay="500">
                                            <div class="col-auto">
                                                <div class="bg-danger-lt avatar">
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/currency-dollar -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-bubble-text">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M7 10h10" />
                                                        <path d="M9 14h5" />
                                                        <path
                                                            d="M12.4 3a5.34 5.34 0 0 1 4.906 3.239a5.333 5.333 0 0 1 -1.195 10.6a4.26 4.26 0 0 1 -5.28 1.863l-3.831 2.298v-3.134a2.668 2.668 0 0 1 -1.795 -3.773a4.8 4.8 0 0 1 2.908 -8.933a5.33 5.33 0 0 1 4.287 -2.16" />
                                                    </svg>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <h3>បញ្ចូលមូលហេតុ</h3>
                                                <p>ត្រូវបញ្ចូលនូវហេតុអោយបានត្រឹមត្រូវ។</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 d-flex justify-content-center" data-aos="fade-left"
                                    data-aos-delay="600">
                                    <img src="public/img/elements/leaveEarly.png" class="img-fluid mb-3 h-100"
                                        alt="Request Screenshot">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Step 3: Change Password -->
        <section id="step3" class="py-5" data-aos="fade-up">
            <div class="container text-dark">
                <div class="row g-3">
                    <div class="col">
                        <h1 class="section-title fw-bold text-primary">៣. ការផ្លាស់ប្រូរពាក្យសម្ងាត់</h1>
                        <p>សូមអនុវត្តតាមជំហានខាងក្រោមដើម្បីផ្លាស់ប្តូរពាក្យសម្ងាត់របស់អ្នក:</p>
                        <ol class="text-left">
                            <li>ចូលទៅកាន់គណនីរបស់អ្នក រួចចុចលើរូបប្រូហ្វាល់</span>។</li>
                            <li>បន្ទាប់មកជ្រើសរើស <span class="text-primary">ផ្លាស់ប្តូរពាក្យសម្ងាត់។</li>
                            <li>បញ្ចូលពាក្យសម្ងាត់ថ្មី និងបញ្ជាក់ពាក្យសម្ងាត់ថ្មី។</li>
                            <li>ចុចលើប៊ូតុង "ផ្លាស់ប្តូរ" ដើម្បីបញ្ចប់។</li>
                        </ol>
                        <div class="text-danger">
                            <p class="mt-3">សូមតាមដានការបញ្ចូលពាក្យសម្ងាត់ថ្មីដែលមានលក្ខណៈប្រសើរ ដូចជា:</p>
                            <ul>
                                <li>មានយ៉ាងហោចណាស់ ៨ តួអក្សរ។</li>
                                <li>រួមមានតួអក្សរធំ និងតួអក្សរតូច។</li>
                                <li>រួមមានលេខនិងសញ្ញាពិសេសដូចជា @, #, $, %។</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-12 d-flex justify-content-center" data-aos="fade-up" data-aos-delay="100">
                        <img src="public/img/elements/changePassword.png" class="img-fluid rounded-3 mb-3 h-100"
                            alt="Request Screenshot">
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQs -->
        <section id="step4" class="py-5 bg-light" data-aos="fade-up">
            <div class="container">
                <h1 class="section-title fw-bold text-primary">៤. ការបង្កើត QR Code</h1>
                <p>ការបង្កើត <span class="text-primary">QR Code</span> សម្រាប់ស្កេនដើម្បីកត់ត្រាវត្តមានប្រចាំថ្ងៃ។</p>
            </div>
            <div class="container d-flex align-items-center justify-content-center">
                <div class="me-5" data-aos="fade-right" data-aos-delay="100">
                    <div class="step mb-4">
                        <span class="step-number bg-primary text-white rounded-circle me-3">1</span>
                        <span class="step-text">ចូលទៅកាន់ប្រព័ន្ធ</span>
                    </div>
                    <div class="step mb-4">
                        <span class="step-number bg-primary text-white rounded-circle me-3">2</span>
                        <span class="step-text">ចុចទៅលើរូប <span class="text-primary fw-bolder">QR Code</span>
                            ដែលមានបង្ហាញនៅក្បែរប្រូហ្វាល់របស់អ្នកប្រើប្រាស់</span>
                    </div>
                    <div class="step">
                        <span class="step-number bg-primary text-white rounded-circle me-3">3</span>
                        <span class="step-text">ចុចប៊ូតុង <span class="text-primary fw-bolder">"បង្កើត QR
                                Code"</span>។</span>
                    </div>
                </div>
                <div data-aos="fade-left" data-aos-delay="200">
                    <img src="public/img/elements/qrCode.png" class="img-fluid" alt="QR Code Illustration">
                </div>
            </div>
        </section>
    </div>
    <!-- Footer -->
    <footer class="footer text-white py-4 bg-dark" data-aos="fade-in">
        <div class="container">
            <div class="row align-items-center justify-content-between">
                <!-- Logo and About -->
                <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                    <img src="public/img/icons/brands/logo2.png" alt="Logo" class="mb-2" style="max-width: 70px;">
                </div>
                <!-- Quick Links -->
                <div class="col-md-4 text-center mb-3 mb-md-0">
                    <h5 class="text-uppercase text-primary mb-3">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#step1" class="text-white text-decoration-none">ចូលប្រើប្រាស់ប្រព័ន្ធ</a></li>
                        <li><a href="#step2" class="text-white text-decoration-none">បង្កើតសំណើផ្សេងៗ</a></li>
                        <li><a href="#step3" class="text-white text-decoration-none">ផ្លាស់ប្តូរពាក្យសម្ងាត់។</a></li>
                        <li><a href="#step4" class="text-white text-decoration-none">បង្កើត QR Code</a></li>
                    </ul>
                </div>
                <!-- Contact Info -->
                <div class="col-md-4 text-center text-md-end">
                    <h5 class="text-uppercase text-primary mb-3">Get in Touch</h5>
                    <p class="small mb-2"><i class="bi bi-envelope-fill me-2"></i><a href="mailto:support@example.com"
                            class="text-white text-decoration-none">support@example.com</a></p>
                    <p class="small mb-2"><i class="bi bi-telephone-fill me-2"></i>+1 800 123 4567</p>
                    <p class="small"><i class="bi bi-geo-alt-fill me-2"></i>1234 Your Street, Your City, Your Country
                    </p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <p class="small mb-0"
                        style="font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif">
                        © 2024 Internal Audit Unit (IAU). All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
</div>

<!-- AOS JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
<script>
    // Initialize AOS
    AOS.init({
        duration: 800,
        once: false,
    });
</script>
<?php include('src/common/footer.php'); ?>