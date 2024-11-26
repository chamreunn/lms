<?php
require_once 'src/models/backwork/BackworkModel.php';
require_once 'src/vendor/autoload.php';

use Mpdf\Mpdf;
use PhpOffice\PhpWord\PhpWord;

class BackworkController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Initialize the HoldModel with the database connection
        $backworkModel = new BackworkModel($this->pdo);

        // Fetch the holds for the current user based on the offset and records per page
        $backworks = $backworkModel->getBackwork($offset, $recordsPerPage);

        // Initialize the UserModel
        $userModel = new User();

        // Fetch the department or office leader's email using session user_id and token
        $approver = $userModel->getEmailLeaderDOApi($_SESSION['user_id'], $_SESSION['token']);

        // Check if the current leader is on leave or on a mission
        if ($userModel->isManagerOnLeaveToday($approver['ids']) || $userModel->isManagerOnMission($approver['ids'])) {
            // If on leave or on mission, try to get the higher-level leader
            $approver = $userModel->getEmailLeaderHOApi($_SESSION['user_id'], $_SESSION['token']);

            // Check if the higher-level leader is also on leave or on mission
            if ($userModel->isManagerOnLeaveToday($approver['ids']) || $userModel->isManagerOnMission($approver['ids'])) {
                // If still on leave or on mission, get the Deputy Director leader
                $approver = $userModel->getEmailLeaderDDApi($_SESSION['user_id'], $_SESSION['token']);

                if ($userModel->isManagerOnLeaveToday($approver['ids']) || $userModel->isManagerOnMission($approver['ids'])) {
                    // If still on leave or on mission, get the Deputy Director leader
                    $approver = $userModel->getEmailLeaderHDApi($_SESSION['user_id'], $_SESSION['token']);
                }
            }
        }

        // Fetch the total number of records to calculate the total pages for pagination
        $totalRecords = $backworkModel->getBackworkCountById();
        $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages

        require 'src/views/backwork/index.php';
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];

            // Validate that the ID is provided
            if (!empty($id)) {
                // Call model to delete holiday
                $backworkModel = new BackworkModel($this->pdo);
                $success = $backworkModel->deleteBackworkById($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/backwork");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/backwork");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/backwork");
                exit();
            }
        }
    }

    public function create()
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();

            // Validate required fields
            if (empty($_SESSION['user_id']) || empty($_POST['date']) || empty($_POST['reason'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/backwork");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $startDate = $_POST['date'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];

            $type = "back";
            $color = "bg-lime";

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'date' => $startDate,
                'reason' => $reason,
                'approver_id' => $approver,
                'type' => $type,
                'color' => $color
            ];

            try {

                $userModel = new User();
                $RequestModel = new BackworkModel();
                $backId = $RequestModel->Request($data);

                // Process attachments
                if (!empty($_FILES['attachment']['name'][0])) {
                    foreach ($_FILES['attachment']['name'] as $key => $attachmentName) {
                        if (!empty($attachmentName)) {
                            $fileTmpPath = $_FILES['attachment']['tmp_name'][$key];
                            $fileName = uniqid() . '_' . basename($attachmentName);
                            $fileSize = $_FILES['attachment']['size'][$key];
                            $fileType = $_FILES['attachment']['type'][$key];
                            $fileError = $_FILES['attachment']['error'][$key];

                            // Perform validations (file size, type)
                            $allowedTypes = ['docx', 'pdf'];
                            $maxFileSize = 5097152; // 5MB

                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                            if ($fileError === 0 && in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
                                $destination = 'public/uploads/backwork-attachments/' . $fileName;

                                // Move file to the destination
                                if (move_uploaded_file($fileTmpPath, $destination)) {
                                    // Insert each attachment into the hold_attachments table
                                    $RequestModel->saveAttachment([
                                        'back_id' => $backId,
                                        'file_name' => $fileName,
                                        'file_path' => $destination
                                    ]);
                                } else {
                                    $_SESSION['error'] = [
                                        'title' => "ឯកសារភ្ជាប់",
                                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                                    ];
                                    header("Location: /elms/backwork");
                                    exit();
                                }
                            } else {
                                $_SESSION['error'] = [
                                    'title' => "ឯកសារភ្ជាប់",
                                    'message' => "ឯកសារមិនត្រឹមត្រូវទេ (ទំហំ ឬ ប្រភេទឯកសារ)។"
                                ];
                                header("Location: /elms/backwork");
                                exit();
                            }
                        }
                    }
                }

                // Recursive manager delegation
                $RequestModel->delegateManager($RequestModel, $userModel, $backId, $_SESSION['user_id'], $reason);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                ];
                header("Location: /elms/backwork");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/backwork");
                exit();
            }
        }
    }

    public function view($id)
    {
        $backworkModel = new BackworkModel();
        $getbackworkById = $backworkModel->getBackworkForEdit($id);
        require 'src/views/backwork/view&edit.php';
    }

    public function update()
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $userModel = new User();
            $back_id = $_POST['backworkId'];
            // Validate required fields
            if (empty($_SESSION['user_id']) || empty($_POST['date']) || empty($_POST['reason'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: view&edit-hold?holdId=" . $back_id);
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $date = $_POST['date'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];



            // Prepare data for updating
            $data = [
                'user_id' => $user_id,
                'date' => $date,
                'reason' => $reason,
                'approver_id' => $approver
            ];

            try {
                $backworkRequestModel = new BackworkModel($this->pdo);
                $backworkRequestModel->updateBackWorkRequest($back_id, $data);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានកែប្រែដោយជោគជ័យ"
                ];
                header("Location: view&edit-backwork?backworkId=" . $back_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: view&edit-backwork?backworkId=" . $back_id);
                exit();
            }
        }
    }

    public function addMoreAttachment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_FILES['moreAttachment']) && !empty($_FILES['moreAttachment']['name'][0])) {
                $backworkModel = new BackworkModel($this->pdo);
                $uploadedFiles = $_FILES['moreAttachment'];

                // Loop through each file
                foreach ($uploadedFiles['name'] as $key => $filename) {
                    // Define a unique name for each file to prevent overwriting
                    $uniqueFileName = uniqid() . '_' . basename($filename);
                    $uploadPath = 'public/uploads/backwork-attachments/' . $uniqueFileName;

                    // Move the file to the designated directory
                    if (move_uploaded_file($uploadedFiles['tmp_name'][$key], $uploadPath)) {
                        // Prepare data for insertion
                        $data = [
                            ':back_id' => $_POST['id'],
                            ':file_name' => $uniqueFileName,
                            ':file_path' => $uploadPath
                        ];

                        // Call the createAttachment method to insert file info into the database
                        $backworkModel->createAttachment($data);
                    } else {
                        // Handle the error if file upload fails
                        echo "Failed to upload file: " . $filename;
                    }
                }

                // Optionally, redirect or display a success message
                $_SESSION['success'] = [
                    'title' => "បន្តែមឯកសារភ្ចាប់",
                    'message' => "បន្ថែមឯកសារភ្ចាប់បានជោគជ័យ។"
                ];
                header('Location: /elms/view&edit-backwork?backworkId=' . $_POST['id']); // Change to your success page
                exit();
            } else {
                $_SESSION['error'] = [
                    'title' => "បន្តែមឯកសារភ្ចាប់",
                    'message' => "មិនអាចបន្ថែមឯកសារភ្ចាប់បានទេ សូមព្យាយាមម្តងទៀត។"
                ];
                header('Location: /elms/view&edit-backwork?backworkId=' . $_POST['id']); // Change to your success page
                exit();
            }
        }
    }

    public function removeAttachments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the filename (attachment) to be deleted and the associated transferout ID
            $attachment = $_POST['attachment'] ?? '';
            $backworkId = $_POST['id'] ?? null;

            if ($attachment && $backworkId) {
                // Initialize the TransferoutModel
                $backwork = new BackworkModel($this->pdo);

                // Delete the attachment record from the transferout_attachments table
                $backwork->deleteAttachment($backworkId, $attachment);

                // Construct the file path to delete the physical file
                $filePath = "public/uploads/backwork-attachments/" . $attachment;
                if (file_exists($filePath)) {
                    unlink($filePath); // Delete the file from the server
                }

                // Set a success message
                $_SESSION['success'] = [
                    'title' => "លុបឯកសារភ្ជាប់",
                    'message' => "លុបបានជោគជ័យ។"
                ];
            } else {
                // Handle missing parameters
                $_SESSION['error'] = [
                    'title' => "លុបឯកសារភ្ជាប់",
                    'message' => "មិនអាចលុបឯកសារភ្ជាប់បានទេ។"
                ];
            }

            // Redirect or return response
            header('Location:  /elms/view&edit-backwork?backworkId=' . $backworkId); // Change to your success page
            exit();
        }
    }

    public function export()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $fileType = $_POST['fileType'];
            $backworkId = $_POST['backworkId'] ?? '';
            $date = $_POST['date'];
            $created_at = $_POST['created_at'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $filename = $_POST['fileName'] ?? 'Unknown Name File';

            // session 
            $position = $_SESSION['position'];
            $dob = $_SESSION['dob'];
            $department = $_SESSION['departmentName'];

            $convert = new User();
            $created_at = $convert->convertDateToKhmer($created_at);
            $dob = $convert->convertDateToKhmer($dob);
            $date = $convert->convertDateToKhmer($date);

            if ($fileType === 'PDF') {
                // Configure mPDF
                $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
                $fontDirs = $defaultConfig['fontDir'];

                $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
                $fontData = $defaultFontConfig['fontdata'];

                $mpdf = new Mpdf([
                    'fontDir' => array_merge($defaultConfig['fontDir'], ['public/dist/fonts']),
                    'fontdata' => $fontData + [
                        'khmermef1' => [
                            'R' => 'Khmer MEF1 Regular.ttf',  // Regular font
                            'B' => 'Khmer MEF2 Regular.ttf',    // Bold font
                        ],
                    ],
                    'default_font' => 'khmermef1',   // Set default font to khmermef1
                    'percentSubset' => 0, // Embed full font
                    'allow_charset_conversion' => true,
                    'useAdobeCJK' => false,
                    'useOTL' => 0xFF, // required for Khmer
                    'useKashida' => 75, // required for Khmer
                ]);

                // HTML content
                $html = '
                    <!DOCTYPE html>
                    <html lang="km">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                        <title>សំណើសុំស្ថិតនៅក្នុងភាពទំនេរ</title>
                        <style>
                            body {
                                font-family: "khmermef1";
                                font-size: 13px;
                                line-height: 1.5;
                                margin: 0;
                                padding: 0;
                            }
                            .header {
                                text-align: center;
                                line-height: 0.5;
                            }

                            .content img {
                                width: 100px; /* Adjust size of the logo */
                            }

                            .content {
                                line-height: 1.7;
                                font-size: 15px;
                                text-align: justify;
                            }

                            /* Table layout for footer */
                            .footer-table {
                                width: 100%;
                                border-collapse: collapse; /* Ensures borders collapse into single lines */
                                margin-top: -30px;
                            }

                            .footer-table td {
                                padding: 20px;
                                text-align: center;
                                font-size: 15px;
                                line-height: 1.7;
                            }

                            .footer-table td strong {
                                font-weight: bold;
                            }

                            /* Right-align the bottom footer */
                            .footer-right {
                                text-align: right; /* Aligns the last footer to the right side */
                                vertical-align: top; /* Align to the top for better placement */
                                justify-content: right;
                            }

                            /* Styling for the first row */
                            .footer-table .footer-top {
                                vertical-align: top;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="header">
                            <h2>ព្រះរាជាណាចក្រកម្ពុជា</h2>
                            <h3>ជាតិ សាសនា ព្រះមហាក្សត្រ</h3>
                        </div>

                        <div class="sub-header" style="line-height: 0.5; color: #2F5496;">
                            <img src="' . $_SESSION['user_profile'] . '" alt="no image found" />
                            <h3>អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</h3>
                            <h3 style="text-indent: 50px;">អង្គភាពសវនកម្មផ្ទៃក្នុង</h3>
                        </div>

                        <div class="header">
                            <h3>សូមគោរពជូន</h3>
                            <h3>ឯកឧត្តមប្រធានអង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</h3>
                        </div>

                         <table style="width: 100%; border-collapse: collapse; font-family: KhmerMEF1; font-size: 15px; line-height: 1.7; text-align: justify;">
                            <tr>
                                <td style="font-weight: bold; width: 10%; vertical-align: top;">កម្មវត្ថុ</td>
                                <td style="width: 90%; vertical-align: top;">៖ ការស្នើសុំគោលការណ៍អនុញ្ញាតចូលម្រើការងារវិញបន្ទាប់ពីការព្យួរការងារចាប់ពីថ្ងៃទី ' . $date . ' ។</td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; font-family: KhmerMEF1; font-size: 15px; line-height: 1.7; text-align: justify;">
                            <tr>
                                <td style="font-weight: bold; width: 10%; vertical-align: top;">យោង</td>
                                <td style="width: 90%; vertical-align: top;">៖ ' . $reason . '។</td>
                            </tr>
                        </table>

                        <div class="content">
                            <p style="text-indent: 50px;">សេចក្តីដូចមានក្នុងកម្មវត្ថុ និងយោងខាងលើ សូមគោរពជម្រាបជូន <strong>ឯកឧត្តមប្រធាន</strong> មេត្តាជ្រាបដ៍ខ្ពង់ខ្ពស់ថា៖ ខ្ញុំបាទ/នាងខ្ញុំឈ្មោះ ' . $_SESSION['user_khmer_name'] . ' កើតថ្ងៃទី ' . $dob . ' ប្រភេទ' . $position . ' បច្ចុប្បន្នជា ' . $position . ' នៃ' . $department . ' ដើម្បីចូលម្រើការងារវិញបន្ទាប់ពីការព្យួរការងារចាប់ពីថ្ងៃទី ' . $date . ' ដោយក្តីអនុគ្រោះ។ </p>
                            <p style="text-indent: 50px;">សេចក្តីដូចបានគោរពជម្រាបជូនខាងលើ សូម <strong>ឯកឧត្តមប្រធាន</strong> មេត្តាពិនិត្យ និងសម្រេចដោយសេចក្តីអនុគ្រោះបំផុត។</p>
                            <p style="text-indent: 50px;">សូម <strong>ឯកឧត្តមប្រធាន</strong> មេត្តាទទួលនូវការគោរពដ៏ខ្ពង់ខ្ពស់ពីខ្ញុំ</p>
                        </div>

                        <!-- Footer using Table Layout -->
                        <table class="footer-table">
                            <tr>
                                <!-- First row with two columns for top footers -->
                                <td class="footer-top">
                                    <p>បានឃើញ និងសូមគោរពជូន</p>
                                    <strong>ឯកឧត្តមប្រធានអង្គភាព</strong>
                                    <p>ពិនិត្យនិងសម្រេច</p>
                                    <p>ថ្ងៃ.......ខែ..........ឆ្នាំ..........ព.ស. ២៥.........</p>
                                    <p>.........ថ្ងៃទី........ខែ.........ឆ្នាំ២០..........</p>
                                    <strong>' . $department . '</strong>
                                    <p style="font-weight: bold;">ប្រធាន</p>
                                </td>
                                <td class="footer-top">
                                    <p>ថ្ងៃ..............ខែ.............ឆ្នាំ.............ព.ស. ២៥...</p>
                                    <p>.............ថ្ងៃទី...........ខែ............ឆ្នាំ២០..........</p>
                                    <strong>ហត្ថលេខាសមីខ្លួន</strong>
                                </td>
                            </tr>
                            <!-- Second row with the bottom footer aligned to the right -->
                            <tr>
                                <td colspan="3" class="footer-right">
                                    <p>បានឃើញ និងឯកភាព</p>
                                    <p>សូមជូន នាយកដ្ឋានកិច្ចការទូទៅ</p>
                                    <p>ដើម្បីមុខងារ</p>
                                    <p>ថ្ងៃ..............ខែ..........ឆ្នាំ.............ព.ស. ២៥...</p>
                                    <p>..........ថ្ងៃទី.........ខែ.......ឆ្នាំ២០..........</p>
                                    <strong>អង្គភាពសវនកម្មផ្ទៃក្នុង</strong>
                                    <p style="font-weight: bold;">ប្រធាន</p>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>
                ';

                // Write the HTML to PDF
                $mpdf->WriteHTML($html);

                $mpdf->Output("$filename.pdf", 'D'); // Force download
            } elseif ($fileType === 'DOCX') {
                // Generate DOCX using PHPWord
                $phpWord = new PhpWord();
                $section = $phpWord->addSection();
                $section->addText('សំណើសុំស្ថិតនៅក្នុងភាពទំនេរ');
                $section->addText("User ID: $backworkId");
                $section->addText("នេះជាឯកសារដែលបានបង្កើតជា DOCX");

                // Save the DOCX file
                $tempFile = tempnam(sys_get_temp_dir(), 'docx');
                $phpWord->save($tempFile, 'Word2007');

                // Deliver the file
                header('Content-Description: File Transfer');
                header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                header('Content-Disposition: attachment; filename="' . "$filename.docx" . '"');
                header('Content-Transfer-Encoding: binary');
                header('Expires: 0');
                header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                header('Pragma: public');
                readfile($tempFile);
                unlink($tempFile); // Delete temp file
                exit;
            } else {
                echo "Invalid file type selected.";
            }
        } else {
            echo "Invalid request method.";
        }
    }
}
