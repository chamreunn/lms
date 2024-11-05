<?php
require_once 'src/models/hold/HoldModel.php';

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
            $holdModel = new HoldModel($this->pdo);

            // Fetch the holds for the current user based on the offset and records per page
            $getHolds = $holdModel->getHoldsByUserId($offset, $recordsPerPage);

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
        $holdModel = new HoldModel($this->pdo);
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
                $holdRequestModel = new HoldModel($this->pdo);
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

                $link = "https://leave.iauoffsa.us/elms/pending";

                // Send notification after saving the request
                $sendToTelegram = $userModel->sendHolds($title, $approver, $start_date, $end_date, $duration, $reason, $link);

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
                $holdRequestModel = new HoldModel($this->pdo);
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
                $calendarModel = new HoldModel($this->pdo);
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
                $holdMoldel = new HoldModel($this->pdo);
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
                $hold = new HoldModel($this->pdo);

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
}
