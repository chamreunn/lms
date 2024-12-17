<?php
require_once 'src/models/hold/HoldModel.php';
require_once 'src/vendor/autoload.php';

use Mpdf\Mpdf;
use PhpOffice\PhpWord\PhpWord;

class HoldController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Ensure session is available
        }

        // Check if user session data is set
        if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
            // Get the current page and set the number of records per page
            $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $recordsPerPage = 5; // Set the desired number of records per page

            // Calculate the offset for the current page
            $offset = ($currentPage - 1) * $recordsPerPage;

            // Initialize the HoldModel with the database connection
            $holdModel = new HoldModel();

            // Fetch the holds for the current user based on the offset and records per page
            $getHolds = $holdModel->getHoldsByUserId($offset, $recordsPerPage);

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

            // Fetch the total number of records to calculate the total pages for pagination
            $totalRecords = $holdModel->getHoldsCountById();
            $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages

            // Load the view
            require 'src/views/hold/index.php';
        } else {
            // If the session data is missing, handle the error (e.g., redirect to login page)
            header('Location: /elms/login');
            exit();
        }
    }

    public function view($id)
    {
        $holdModel = new HoldModel();
        $getHoldById = $holdModel->getHoldById($id);
        require 'src/views/hold/view&edit.php';
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
            if (empty($_SESSION['user_id']) || empty($_POST['start_date']) || empty($_POST['end_date']) || empty($_POST['reason'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/hold");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];

            // Validate date order
            if (new DateTime($start_date) > new DateTime($end_date)) {
                $_SESSION['error'] = [
                    'title' => "កំហុសកាលបរិច្ឆេទ",
                    'message' => "កាលបរិច្ឆេទបញ្ចប់គួរត្រូវជាងកាលបរិច្ឆេទចាប់ផ្តើម។"
                ];
                header("Location: /elms/hold");
                exit();
            }

            $type = 'hold';
            $color = 'bg-primary';
            $title = "លិខិតព្យួរការងារ";

            // Calculate duration between start_date and end_date in months and years
            $startDate = new DateTime($start_date);
            $endDate = new DateTime($end_date);
            $interval = $startDate->diff($endDate);

            $years = $interval->y;
            $months = $interval->m;
            $days = $interval->d;

            // Count a partial month as a full month
            if ($days > 0) {
                $months += 1;
            }

            // Format the duration as X years, Y months
            $duration = "";
            if ($years > 0) {
                $duration .= $years . " ឆ្នាំ ";
            }
            if ($months > 0) {
                $duration .= $months . " ខែ ";
            }

            // Error handling: check if the duration meets the requirement (at least 6 months)
            if ($years === 0 && $months < 6) {
                $_SESSION['error'] = [
                    'title' => "កំហុសវ័យ",
                    'message' => "លិខិតព្យួរត្រូវចាប់ពី ៦ខែ ឡើង។"
                ];
                header("Location: /elms/hold");
                exit();
            }

            try {
                // Prepare data for the hold request
                $data = [
                    'user_id' => $user_id,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'reason' => $reason,
                    'duration' => $duration,
                    'type' => $type,
                    'color' => $color,
                    'approver_id' => $approver
                ];

                // Save the hold request using the HoldModel
                $holdRequestModel = new HoldModel();
                $hold_id = $holdRequestModel->createHoldRequest($data);

                // Recursive manager delegation
                $this->delegateManager($holdRequestModel, $userModel, $hold_id, $_SESSION['user_id'], $reason);

                // Process attachments
                if (!empty($_FILES['attachment']['name'][0])) {
                    foreach ($_FILES['attachment']['name'] as $key => $attachmentName) {
                        if (!empty($attachmentName)) {
                            $fileTmpPath = $_FILES['attachment']['tmp_name'][$key];
                            $fileName = basename($attachmentName);
                            $fileSize = $_FILES['attachment']['size'][$key];
                            $fileType = $_FILES['attachment']['type'][$key];
                            $fileError = $_FILES['attachment']['error'][$key];

                            // Perform validations (file size, type)
                            $allowedTypes = ['docx', 'pdf'];
                            $maxFileSize = 5097152; // 5MB

                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                            if ($fileError === 0 && in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
                                $destination = 'public/uploads/hold-attachments/' . $fileName;

                                // Move file to the destination
                                if (move_uploaded_file($fileTmpPath, $destination)) {
                                    // Insert each attachment into the hold_attachments table
                                    $holdRequestModel->saveHoldAttachment([
                                        'hold_id' => $hold_id,
                                        'file_name' => $fileName,
                                        'file_path' => $destination
                                    ]);
                                } else {
                                    $_SESSION['error'] = [
                                        'title' => "ឯកសារភ្ជាប់",
                                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                                    ];
                                    header("Location: /elms/hold");
                                    exit();
                                }
                            } else {
                                $_SESSION['error'] = [
                                    'title' => "ឯកសារភ្ជាប់",
                                    'message' => "ឯកសារមិនត្រឹមត្រូវទេ (ទំហំ ឬ ប្រភេទឯកសារ)។"
                                ];
                                header("Location: /elms/hold");
                                exit();
                            }
                        }
                    }
                }

                // Send notification after saving the request
                if ($hold_id) {

                    // Define notification details
                    $notificationMessage = $_SESSION['user_khmer_name'] . " បានស្នើលិខិតព្យួរការងារ";
                    $notificationProfile = $_SESSION['user_profile'];
                    $notificationLink = ($_SERVER['SERVER_NAME'] === '127.0.0.1') ? 'http://127.0.0.1/elms/pending' : 'https://leave.iauoffsa.us/elms/pending';

                    // Create the in-app notification
                    $notificationModel = new NotificationModel();
                    $notificationModel->createNotification($approver, $title, $notificationMessage, $notificationLink, $notificationProfile);


                    $userModel->sendHoldToTelegram($title, $approver, $start_date, $end_date, $duration, $reason);

                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                    ];
                    header("Location: /elms/hold");
                    exit();
                } else {
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចបញ្ជូនសំណើបានទេ។ សូមទំនាក់ទំនងទៅកាន់មន្ត្រីទទួលបន្ទុក។"
                    ];
                    header("Location: /elms/hold");
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/hold");
                exit();
            }
        }
    }

    /**
     * Recursively delegate manager to handle the approval if the current is unavailable.
     */
    private function delegateManager($holdRequestModel, $userModel, $hold_id, $user_id, $reason)
    {
        $levels = ['getEmailLeaderDOApi', 'getEmailLeaderHOApi', 'getEmailLeaderDDApi', 'getEmailLeaderHDApi'];
        $statuses = ['leave' => 'leave', 'mission' => 'mission'];

        foreach ($levels as $apiMethod) {
            $approver = $userModel->$apiMethod($user_id, $_SESSION['token']);
            if ($approver && !$userModel->isManagerOnLeaveToday($approver['ids']) && !$userModel->isManagerOnMission($approver['ids'])) {
                $holdRequestModel->insertManagerStatusAndUpdateApprover($hold_id, $approver['ids'], 'pending');
                return; // Stop when a valid approver is found
            }

            // Insert status if the manager is on leave or mission
            $status = $userModel->isManagerOnLeaveToday($approver['ids']) ? $statuses['leave'] : $statuses['mission'];
            $holdRequestModel->insertManagerStatusAndUpdateApprover($hold_id, $approver['ids'], $status);
        }
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
            $hold_id = $_POST['holdId'];
            // Validate required fields
            if (empty($_SESSION['user_id']) || empty($_POST['start_date']) || empty($_POST['end_date']) || empty($_POST['reason'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: view&edit-hold?holdId=" . $hold_id);
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];

            // Validate date order
            if (new DateTime($start_date) > new DateTime($end_date)) {
                $_SESSION['error'] = [
                    'title' => "កំហុសកាលបរិច្ឆេទ",
                    'message' => "កាលបរិច្ឆេទបញ្ចប់គួរត្រូវជាងកាលបរិច្ឆេទចាប់ផ្តើម។"
                ];
                header("Location: view&edit-hold?holdId=" . $hold_id);
                exit();
            }

            // Calculate duration between start_date and end_date in months and years
            $startDate = new DateTime($start_date);
            $endDate = new DateTime($end_date);
            $interval = $startDate->diff($endDate);

            $years = $interval->y;
            $months = $interval->m;
            $days = $interval->d;

            // Count a partial month as a full month
            if ($days > 0) {
                $months += 1;
            }

            // Format the duration as X years, Y months
            $duration = "";
            if ($years > 0) {
                $duration .= $years . " ឆ្នាំ ";
            }
            if ($months > 0) {
                $duration .= $months . " ខែ ";
            }

            // Error handling: check if the duration meets the requirement (at least 6 months)
            if ($years === 0 && $months < 6) {
                $_SESSION['error'] = [
                    'title' => "កំហុសវ័យ",
                    'message' => "លិខិតព្យួរត្រូវចាប់ពី ៦ខែ ឡើង។"
                ];
                header("Location: view&edit-hold?holdId=" . $hold_id);
                exit();
            }

            // Prepare data for updating
            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason, // Store the updated attachments
                'duration' => $duration,
                'approver_id' => $approver
            ];

            try {
                // Preserve existing attachment if no new file is uploaded
                $holdRequestModel = new HoldModel();
                // Update the hold request using the HoldModel
                $holdRequestModel->updateHoldRequest($hold_id, $data);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានកែប្រែដោយជោគជ័យ"
                ];
                header("Location: view&edit-hold?holdId=" . $hold_id);
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: view&edit-hold?holdId=" . $hold_id);
                exit();
            }
        }
    }

    public function handleFileUpload($file, $allowed_extensions, $max_size, $upload_path)
    {
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_error = $file['error'];
        $file_size = $file['size'];

        if ($file_error === UPLOAD_ERR_NO_FILE) {
            // No file was uploaded
            return null;
        }

        if ($file_error !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "An error occurred during the file upload."
            ];
            return false;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Invalid attachment file type."
            ];
            return false;
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Attachment file size exceeds the limit."
            ];
            return false;
        }

        // Make the file name unique by appending a timestamp
        $unique_file_name = pathinfo($file_name, PATHINFO_FILENAME) . '_' . time() . '.' . $file_ext;
        $destination = $upload_path . $unique_file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            return $unique_file_name;
        } else {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Failed to move the uploaded file."
            ];
            return false;
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];

            // Validate that the ID is provided
            if (!empty($id)) {
                // Call model to delete holiday
                $calendarModel = new HoldModel();
                $success = $calendarModel->deleteHold($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/hold");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/hold");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/hold");
                exit();
            }
        }
    }

    public function addMoreAttachment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ensure that the files were uploaded without errors
            if (isset($_FILES['moreAttachment']) && !empty($_FILES['moreAttachment']['name'][0])) {
                $holdMoldel = new HoldModel();
                // Array to hold the file data
                $uploadedFiles = $_FILES['moreAttachment'];

                // Loop through each file
                foreach ($uploadedFiles['name'] as $key => $filename) {
                    // Define a unique name for each file to prevent overwriting
                    $uniqueFileName = uniqid() . '_' . basename($filename);
                    $uploadPath = 'public/uploads/hold-attachments/' . $uniqueFileName;

                    // Move the file to the designated directory
                    if (move_uploaded_file($uploadedFiles['tmp_name'][$key], $uploadPath)) {
                        // Prepare data for insertion
                        $data = [
                            ':hold_id' => $_POST['id'],
                            ':file_name' => $uniqueFileName,
                            ':file_path' => $uploadPath
                        ];

                        // Call the createAttachment method to insert file info into the database
                        $holdMoldel->createAttachment($data);
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
                header('Location: /elms/view&edit-hold?holdId=' . $_POST['id']); // Change to your success page
                exit();
            } else {
                $_SESSION['error'] = [
                    'title' => "បន្តែមឯកសារភ្ចាប់",
                    'message' => "មិនអាចបន្ថែមឯកសារភ្ចាប់បានទេ សូមព្យាយាមម្តងទៀត។"
                ];
                echo "No files were selected for upload.";
            }
        }
    }

    public function removeAttachments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the filename (attachment) to be deleted and the associated transferout ID
            $attachment = $_POST['attachment'] ?? '';
            $holdId = $_POST['id'] ?? null;

            if ($attachment && $holdId) {
                // Initialize the TransferoutModel
                $hold = new HoldModel();

                // Delete the attachment record from the transferout_attachments table
                $hold->deleteAttachment($holdId, $attachment);

                // Construct the file path to delete the physical file
                $filePath = "public/uploads/hold-attachments/" . $attachment;
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
            header('Location:  /elms/view&edit-hold?holdId=' . $holdId); // Change to your success page
            exit();
        }
    }

    public function holdApproved()
    {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Ensure session is available
        }

        // Check if user session data is set
        if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
            // Get the current page and set the number of records per page
            $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $recordsPerPage = 5; // Set the desired number of records per page

            // Calculate the offset for the current page
            $offset = ($currentPage - 1) * $recordsPerPage;

            // Initialize the HoldModel with the database connection
            $holdModel = new HoldModel();

            // Fetch the holds for the current user based on the offset and records per page
            $getHolds = $holdModel->getApprovedHoldsByUserId($offset, $recordsPerPage);

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['position'], $_SESSION['departmentName']);

            // Fetch the total number of records to calculate the total pages for pagination
            $totalRecords = $holdModel->getHoldsCountById();
            $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages

            require 'src/views/hold/approved.php';
        }
    }

    public function holdRejected()
    {
        // Start the session if it's not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Ensure session is available
        }

        // Check if user session data is set
        if (isset($_SESSION['user_id']) && isset($_SESSION['token'])) {
            // Get the current page and set the number of records per page
            $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
            $recordsPerPage = 5; // Set the desired number of records per page

            // Calculate the offset for the current page
            $offset = ($currentPage - 1) * $recordsPerPage;

            // Initialize the HoldModel with the database connection
            $holdModel = new HoldModel();

            // Fetch the holds for the current user based on the offset and records per page
            $getHolds = $holdModel->getRejectHoldsByUserId($offset, $recordsPerPage);

            // Initialize the UserModel
            $userModel = new User();

            // Get approver based on role and department
            $approver = $userModel->getApproverByRole($userModel, $_SESSION['user_id'], $_SESSION['token'], $_SESSION['role'], $_SESSION['departmentName']);

            // Fetch the total number of records to calculate the total pages for pagination
            $totalRecords = $holdModel->getHoldsCountById();
            $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages


            require 'src/views/hold/rejected.php';
        }
    }

    private function convertToKhmerNumbers($number)
    {
        $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
        $number = (string) $number;
        $converted = '';
        for ($i = 0; $i < strlen($number); $i++) {
            $digit = $number[$i];
            $converted .= is_numeric($digit) ? $khmerNumbers[$digit] : $digit;
        }
        return $converted;
    }

    function convertDateToKhmer($date)
    {
        if (empty($date)) {
            return '';
        }

        // Convert Arabic numerals to Khmer numerals
        $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

        // Khmer month names
        $khmerMonths = [
            1 => 'មករា',
            2 => 'កុម្ភៈ',
            3 => 'មិនា',
            4 => 'មេសា',
            5 => 'ឧសភា',
            6 => 'មិថុនា',
            7 => 'កក្កដា',
            8 => 'សីហា',
            9 => 'កញ្ញា',
            10 => 'តុលា',
            11 => 'វិច្ឆិកា',
            12 => 'ធ្នូ'
        ];

        // Parse the date
        $timestamp = strtotime($date);
        if (!$timestamp) {
            return ''; // Return empty if the date is invalid
        }

        // Extract date components
        $day = date('j', $timestamp);
        $month = (int) date('n', $timestamp);
        $year = date('Y', $timestamp);

        // Convert day and year to Khmer numerals
        $khmerDay = '';
        $khmerYear = '';

        foreach (str_split($day) as $digit) {
            $khmerDay .= $khmerNumbers[$digit];
        }

        foreach (str_split($year) as $digit) {
            $khmerYear .= $khmerNumbers[$digit];
        }

        // Return formatted Khmer date
        return $khmerDay . ' ' . $khmerMonths[$month] . ' ' . $khmerYear;
    }

    public function export()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fileType = $_POST['fileType'];
            $holdId = $_POST['holdId'] ?? '';
            $duration = $_POST['duration'] ?? '';
            $startDate = $_POST['start_date'] ?? '';
            $endDate = $_POST['end_date'] ?? '';
            $reason = $_POST['reason'] ?? '';
            $position = $_SESSION['position'];
            $dob = $_SESSION['dob'];
            $department = $_SESSION['departmentName'];
            $filename = 'សំណើសុំស្ថិតនៅក្នុងភាពទំនេរ';

            $khmerNumber = $this->convertToKhmerNumbers($duration);
            $startDate = $this->convertDateToKhmer($startDate);
            $endDate = $this->convertDateToKhmer($endDate);
            $dob = $this->convertDateToKhmer($dob);

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
                            }

                            .content img {
                                width: 100px; /* Adjust size of the logo */
                            }
                            .content {
                                line-height: 25px;
                                font-size: 14px;
                            }

                            /* Table layout for footer */
                            .footer-table {
                                width: 100%;
                                border-collapse: collapse; /* Ensures borders collapse into single lines */
                                margin-top: 20px;
                            }

                            .footer-table td {
                                padding: 20px;
                                text-align: center;
                                font-size: 14px;
                                line-height: 25px;
                            }

                            .footer-table td strong {
                                font-weight: bold;
                            }

                            /* Right-align the bottom footer */
                            .footer-right {
                                text-align: right; /* Aligns the last footer to the right side */
                                vertical-align: top; /* Align to the top for better placement */
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

                        <div class="sub-header">
                            <h3>អាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</h3>
                            <h3 style="text-indent: 50px;">អង្គភាពសវនកម្មផ្ទៃក្នុង</h3>
                        </div>

                        <div class="header">
                            <h3>សូមគោរពជូន</h3>
                            <h3>ឯកឧត្តមប្រធានអង្គភាពសវនកម្មផ្ទៃក្នុងនៃអាជ្ញាធរសេវាហិរញ្ញវត្ថុមិនមែនធនាគារ</h3>
                        </div>

                         <table style="width: 100%; border-collapse: collapse; font-family: KhmerMEF1; font-size: 14px; line-height: 1.5;">
                            <tr>
                                <td style="font-weight: bold; width: 10%; vertical-align: top;">កម្មវត្ថុ</td>
                                <td style="width: 90%; vertical-align: top;">៖ ការស្នើសុំគោលការណ៍អនុញ្ញាតព្យួរការងារ រយៈពេល ' . $khmerNumber . ' ចាប់ពីថ្ងៃទី ' . $startDate . ' ដល់ថ្ងៃទី ' . $endDate . '។</td>
                            </tr>
                        </table>

                        <table style="width: 100%; border-collapse: collapse; font-family: KhmerMEF1; font-size: 14px; line-height: 1.5;">
                            <tr>
                                <td style="font-weight: bold; width: 10%; vertical-align: top;">មូលហេតុ</td>
                                <td style="width: 90%; vertical-align: top;">៖ ' . $reason . '។</td>
                            </tr>
                        </table>

                        <div class="content">
                            <p style="text-indent: 50px;">សេចក្តីដូចមានក្នុងកម្មវត្ថុ និងមូលហេតុខាងលើ សូមគោរពជម្រាបជូន <strong>ឯកឧត្តមប្រធាន</strong> មេត្តាជ្រាបដ៍ខ្ពង់ខ្ពស់ថា៖ ខ្ញុំបាទ/នាងខ្ញុំឈ្មោះ ' . $_SESSION['user_khmer_name'] . ' កើតថ្ងៃទី ' . $dob . ' ប្រភេទ' . $position . ' បច្ចុប្បន្នជា ' . $position . ' នៃ' . $department . ' សូមគោរពស្នើសុំការអនុញ្ញាតដ៍ខ្ពង់ខ្ពស់ពី <strong>ឯកឧត្តមប្រធាន</strong> ដើម្បីព្យួរការងារ (ការដាក់ឱ្យស្ថិតនៅក្នុងភាពទំនេរគ្មានបៀវត្ស) រយៈពេល ' . $khmerNumber . ' ចាប់ពីថ្ងៃទី ' . $startDate . ' ដល់ថ្ងៃទី ' . $endDate . ' ដោយក្តីអនុគ្រោះ។ </p>
                            <p style="text-indent: 50px;">សេចក្តីដូចបានគោរពជម្រាបជូនខាងលើ សូម <strong>ឯកឧត្តមប្រធាន</strong> មេត្តាពិនិត្យ និងសម្រេចដោយសេចក្តីអនុគ្រោះ។</p>
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
                                    <p>ថ្ងៃ...............ខែ.............ឆ្នាំ.............ព.ស. ២៥...</p>
                                    <p>..................ថ្ងៃទី...........ខែ............ឆ្នាំ២០..........</p>
                                    <strong>' . $department . '</strong>
                                    <p style="font-weight: bold;">ប្រធាន</p>
                                </td>
                                <td class="footer-top">
                                    <p>ថ្ងៃ..............ខែ.............ឆ្នាំ.............ព.ស. ២៥...</p>
                                    <p>..................ថ្ងៃទី...........ខែ............ឆ្នាំ២០..........</p>
                                    <strong>ហត្ថលេខាសមីខ្លួន</strong>
                                </td>
                            </tr>
                            <!-- Second row with the bottom footer aligned to the right -->
                            <tr>
                                <td colspan="2" class="footer-right">
                                    <p>បានឃើញ និងឯកភាព</p>
                                    <p>សូមជូន នាយកដ្ឋានកិច្ចការទូទៅ</p>
                                    <p>ដើម្បីមុខងារ</p>
                                    <p>ថ្ងៃ.........................ខែ.............ឆ្នាំ.............ព.ស. ២៥...</p>
                                    <p>..................ថ្ងៃទី...........ខែ............ឆ្នាំ២០..........</p>
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
                $section->addText("User ID: $holdId");
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
