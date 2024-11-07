<?php
require_once 'src/models/backwork/BackworkModel.php';

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

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'date' => $startDate,
                'reason' => $reason,
                'approver_id' => $approver
            ];

            try {

                $userModel = new User();
                $RequestModel = new BackworkModel($this->pdo);
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
        $backworkModel = new BackworkModel($this->pdo);
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
}
