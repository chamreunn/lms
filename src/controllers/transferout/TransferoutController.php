<?php
require_once 'src/models/transferout/TransferoutModel.php';

class TransferoutController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $userModel = new User();
        $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
        $offices = $userModel->getAllOfficeApi($_SESSION['token']);

        // Check for errors in the departments API response
        if ($departments['http_code'] !== 200) {
            $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
            $departments['data'] = []; // Fallback to empty data if there's an error
        }

        // Check for errors in the offices API response
        if ($offices['http_code'] !== 200) {
            $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
            $offices['data'] = []; // Fallback to empty data if there's an error
        }

        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        // Initialize the HoldModel with the database connection
        $transferModel = new TransferoutModel($this->pdo);

        // Fetch the holds for the current user based on the offset and records per page
        $gettransferouts = $transferModel->getTransferoutWithDetails($offset, $recordsPerPage);

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
        $totalRecords = $transferModel->getTransferoutCountById();
        $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages

        // Load the view
        require 'src/views/transferout/index.php';
    }

    public function create()
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if the request method is POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate required fields
            if (
                empty($_SESSION['user_id']) || empty($_POST['fromdepartment']) || empty($_POST['todepartment']) || empty($_POST['fromoffice'])
                || empty($_POST['tooffice']) || empty($_POST['reason'])
            ) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/transferout");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $fromdepartment = htmlspecialchars($_POST['fromdepartment']);
            $todepartment = htmlspecialchars($_POST['todepartment']);
            $fromoffice = htmlspecialchars($_POST['fromoffice']);
            $tooffice = htmlspecialchars($_POST['tooffice']);
            $reason = htmlspecialchars($_POST['reason']);
            $approver = htmlspecialchars($_POST['approverId']);

            // Static assignment for notification parameters
            $type = 'transferout';
            $color = 'bg-info';
            $title = "លិខិតផ្ទេការងារ";

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'from_department' => $fromdepartment,
                'to_department' => $todepartment,
                'from_office' => $fromoffice,
                'to_office' => $tooffice,
                'attachment' => null,  // No attachments in this table
                'type' => $type,
                'color' => $color,
                'reason' => $reason,
                'approver_id' => $approver
            ];

            try {
                // Save the main transferout record
                $transferout = new TransferoutModel($this->pdo);
                $transferout_id = $transferout->createTransferout($data);

                // Handle attachments separately
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
                                $destination = 'public/uploads/transferout-attachments/' . $fileName;

                                // Move file to the destination
                                if (move_uploaded_file($fileTmpPath, $destination)) {
                                    // Insert the attachment record in transferout_attachments table
                                    $attachmentData = [
                                        'transferout_id' => $transferout_id,
                                        'filename' => $fileName
                                    ];
                                    $transferout->createAttachment($attachmentData);
                                } else {
                                    $_SESSION['error'] = [
                                        'title' => "ឯកសារភ្ជាប់",
                                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                                    ];
                                    header("Location: /elms/transferout");
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

                // Recursive manager delegation
                $this->delegateManager($transferout, new User(), $transferout_id, $_SESSION['user_id'], $reason);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                ];
                header("Location: /elms/transferout");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបញ្ចូលទិន្នន័យ: " . $e->getMessage()
                ];
                header("Location: /elms/transferout");
                exit();
            }
        }
    }

    private function delegateManager($transferout, $userModel, $transferout_id, $user_id, $reason)
    {
        $levels = ['getEmailLeaderDOApi', 'getEmailLeaderHOApi', 'getEmailLeaderDDApi', 'getEmailLeaderHDApi'];
        $statuses = ['leave' => 'leave', 'mission' => 'mission'];

        foreach ($levels as $apiMethod) {
            $approver = $userModel->$apiMethod($user_id, $_SESSION['token']);
            if ($approver && !$userModel->isManagerOnLeaveToday($approver['ids']) && !$userModel->isManagerOnMission($approver['ids'])) {
                $transferout->insertManagerStatusToHoldsApprovals($transferout_id, $approver['ids'], 'pending');
                return; // Stop when a valid approver is found
            }

            // Insert status if the manager is on leave or mission
            $status = $userModel->isManagerOnLeaveToday($approver['ids']) ? $statuses['leave'] : $statuses['mission'];
            $transferout->insertManagerStatusToHoldsApprovals($transferout_id, $approver['ids'], $status);
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

    public function view($id)
    {
        $transferoutModel = new TransferoutModel($this->pdo);
        $getTransferouts = $transferoutModel->getTransferoutById($id);
        require 'src/views/transferout/view&edit.php';
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];

            // Validate that the ID is provided
            if (!empty($id)) {
                // Call model to delete holiday
                $transferoutModel = new TransferoutModel($this->pdo);
                $success = $transferoutModel->deleteTransferout($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/transferout");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/transferout");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/transferout");
                exit();
            }
        }
    }

    public function addMoreAttachment()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Ensure that the files were uploaded without errors
            if (isset($_FILES['moreAttachment']) && !empty($_FILES['moreAttachment']['name'][0])) {
                $transferout = new TransferoutModel($this->pdo);
                // Array to hold the file data
                $uploadedFiles = $_FILES['moreAttachment'];

                // Loop through each file
                foreach ($uploadedFiles['name'] as $key => $filename) {
                    // Define a unique name for each file to prevent overwriting
                    $uniqueFileName = uniqid() . '_' . basename($filename);
                    $uploadPath = 'public/uploads/transferout-attachments/' . $uniqueFileName;

                    // Move the file to the designated directory
                    if (move_uploaded_file($uploadedFiles['tmp_name'][$key], $uploadPath)) {
                        // Prepare data for insertion
                        $data = [
                            ':transferout_id' => $_POST['id'],
                            ':filename' => $uniqueFileName
                        ];

                        // Call the createAttachment method to insert file info into the database
                        $transferout->createAttachment($data);
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
                header('Location: /elms/view&edit-transferout?transferId=' . $_POST['id']); // Change to your success page
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

    // Handle the form submission for updating attachments
    public function removeAttachments()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Get the filename (attachment) to be deleted and the associated transferout ID
            $attachment = $_POST['attachment'] ?? '';
            $transferoutId = $_POST['id'] ?? null;

            if ($attachment && $transferoutId) {
                // Initialize the TransferoutModel
                $transferout = new TransferoutModel($this->pdo);

                // Delete the attachment record from the transferout_attachments table
                $transferout->deleteAttachment($transferoutId, $attachment);

                // Construct the file path to delete the physical file
                $filePath = "public/uploads/transferout-attachments/" . $attachment;
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
            header('Location: /elms/view&edit-transferout?transferId=' . $transferoutId); // Change to your success page
            exit();
        }
    }
}
