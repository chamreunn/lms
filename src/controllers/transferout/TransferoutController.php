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

            $userModel = new User();

            // Validate required fields
            if (
                empty($_SESSION['user_id']) || empty($_POST['fromdepartment']) || empty($_POST['todepartment']) || empty($_POST['fromoffice'])
                || empty($_POST['tooffice']) || empty($_POST['reason'])
            ) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/hold");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $fromdepartment = $_POST['fromdepartment'];
            $todepartment = $_POST['todepartment'];
            $fromoffice = $_POST['fromoffice'];
            $tooffice = $_POST['tooffice'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];

            $type = 'transfer';
            $color = 'bg-info';
            $title = "លិខិតផ្ទេការងារ";

            $attachments = [];
            if (!empty($_FILES['attachment']['name'][0])) {
                // Loop through each file
                foreach ($_FILES['attachment']['name'] as $key => $attachmentName) {
                    if (!empty($attachmentName)) {
                        // Get the file info
                        $fileTmpPath = $_FILES['attachment']['tmp_name'][$key];
                        $fileName = basename($attachmentName);
                        $fileSize = $_FILES['attachment']['size'][$key];
                        $fileType = $_FILES['attachment']['type'][$key];
                        $fileError = $_FILES['attachment']['error'][$key];

                        // Perform validations (file size, type)
                        $allowedTypes = ['docx', 'pdf'];
                        $maxFileSize = 50097152; // 50MB

                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if ($fileError === 0 && in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
                            $destination = 'public/uploads/hold-attachments/' . $fileName;

                            // Move file to the destination
                            if (move_uploaded_file($fileTmpPath, $destination)) {
                                $attachments[] = $fileName; // Store uploaded file names
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

            // Convert the array of attachments to a comma-separated string to store in the database
            $attachment_names = implode(',', $attachments);

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'fromdepartment' => $fromdepartment,
                'todepartment' => $todepartment,
                'fromoffice' => $fromoffice,
                'attachment' => $attachment_names,
                'tooffice' => $tooffice,
                'type' => $type,
                'color' => $color,
                'approver_id' => $approver
            ];

            try {
                // Save the hold request using the HoldModel
                $userModel = new User();
                $transferout = new transferoutModel($this->pdo);
                $transferout_id = $transferout->createTransferout($data);

                // Recursive manager delegation
                $this->delegateManager($transferout, $userModel, $transferout_id, $_SESSION['user_id'], $reason);

                $link = "https://leave.iauoffsa.us/elms/pending";

                // Send notification after saving the request
                // $sendToTelegram = $userModel->tranferout($title, $approver, $start_date, $end_date, $duration, $reason, $link);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                ];
                header("Location: /elms/hold");
                exit();
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
                $holdRequestModel->insertManagerStatusToHoldsApprovals($hold_id, $approver['ids'], 'pending');
                return; // Stop when a valid approver is found
            }

            // Insert status if the manager is on leave or mission
            $status = $userModel->isManagerOnLeaveToday($approver['ids']) ? $statuses['leave'] : $statuses['mission'];
            $holdRequestModel->insertManagerStatusToHoldsApprovals($hold_id, $approver['ids'], $status);
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
}
