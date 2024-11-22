<?php
$title = "ពិនិត្យមើលឯកសារ";
require_once 'src/common/header.php';
?>

<!-- Page header -->
<div class="container-xl mt-3">
    <div class="row g-2 align-items-center mb-3">
        <div class="col">
            <h2 class="page-title">

            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
            <button type="button" class="btn btn-primary" onclick="javascript:window.print();">
                <!-- Download SVG icon from http://tabler-icons.io/i/printer -->
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2"></path>
                    <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4"></path>
                    <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z"></path>
                </svg>
                បោះពុម្ព
            </button>
        </div>
    </div>

    <div class="card card-lg">
        <div class="card-body p-0">

            <div class="row">
                <center class="invoice-number"
                    style="font-family: khmer mef2;color: #2F5496;font-size: 18px; margin-top: -10px;">
                    ព្រះរាជាណាចក្រកម្ពុជា<br>
                    ជាតិ សាសនា ព្រះមហាក្សត្រ
                </center>

                <div class="col-5 text-center text-primary">
                    <img src="public/img/icons/brands/logo2.png" style="width: 100px;" class="mb-3" />
                    <p class="h4">អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</p>
                    <p class="h4">អង្គភាពសវនកម្មផ្ទៃក្នុង</p>
                </div>
                <div class="col-12 text-center">
                    <h3>សូមគោរពជូន</h3>
                    <h3>ឯកឧត្តមអង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</h3>
                </div>

                <div class="col-12">
                    <p
                        style="font-family: khmer mef1; font-size: 16px; line-height: 30px; text-align:justify; text-indent: 50px;white-space: nowrap;">
                        <strong class="h3">កម្មវត្ថុ៖</strong> <span>ការស្នើសុំគោលការណ៍អនុញ្ញាតព្យួរការងារ (ការដាក់ឱ្យស្ថិតនៅក្នុងភាពទំនេរគ្មានបៀវត្ស) រយៈពេល......ឆ្នាំ ចាប់ពីថ្ងៃទី<?= $startDate ?>ដល់ថ្ងៃទី.....ខែ........ឆ្នាំ.............។
                            
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('src/common/footer.php'); ?>