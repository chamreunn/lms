<?php
require_once 'src/models/resign/ResignModel.php';

class ResignController
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
            $resignModel = new ResignModel($this->pdo);

            // Fetch the holds for the current user based on the offset and records per page
            $getResigns = $resignModel->getResignByUserId($offset, $recordsPerPage);

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
            $totalRecords = $resignModel->getResignCountById();
            $totalPages = ceil($totalRecords / $recordsPerPage); // Calculate total pages

            // Load the view
            require 'src/views/resign/index.php';
        } else {
            // If the session data is missing, handle the error (e.g., redirect to login page)
            header('Location: /elms/resign');
            exit();
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
            if (empty($_SESSION['user_id']) || empty($_POST['workexperience']) || empty($_POST['reason']) || empty($_FILES['attachment']['name'][0])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/resign");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $workexperience = $_POST['workexperience'];
            $reason = $_POST['reason'];
            $approver = $_POST['approverId'];

            $title = "លិខិតលារឈប់";

            $attachments = [];
            if (!empty($_FILES['attachment']['name'][0])) {
                // Loop through each file
                foreach ($_FILES['attachment']['name'] as $key => $attachmentName) {
                    if (!empty($attachmentName)) {
                        // Get the file info
                        $fileTmpPath = $_FILES['attachment']['tmp_name'][$key];
                        $fileName = basename($this->randomString(8) . $attachmentName);
                        $fileSize = $_FILES['attachment']['size'][$key];
                        $fileType = $_FILES['attachment']['type'][$key];
                        $fileError = $_FILES['attachment']['error'][$key];

                        // Perform validations (file size, type)
                        $allowedTypes = ['docx', 'pdf'];
                        $maxFileSize = 5097152; // 5MB

                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if ($fileError === 0 && in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
                            $destination = 'public/uploads/resign-attachments/' . $fileName;

                            // Move file to the destination
                            if (move_uploaded_file($fileTmpPath, $destination)) {
                                $attachments[] = $fileName; // Store uploaded file names
                            } else {
                                $_SESSION['error'] = [
                                    'title' => "ឯកសារភ្ជាប់",
                                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                                ];
                                header("Location: /elms/resign");
                                exit();
                            }
                        } else {
                            $_SESSION['error'] = [
                                'title' => "ឯកសារភ្ជាប់",
                                'message' => "ឯកសារមិនត្រឹមត្រូវទេ (ទំហំ ឬ ប្រភេទឯកសារ)។"
                            ];
                            header("Location: /elms/resign");
                            exit();
                        }
                    }
                }
            }

            // Convert the array of attachments to a comma-separated string to store in the database
            $attachment_names = implode(', ', $attachments);

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'workexperience' => $workexperience,
                'reason' => $reason,
                'attachment' => $attachment_names,
                'approver_id' => $approver
            ];

            try {
                // Save the hold request using the HoldModel
                $userModel = new User();
                $resignRequestModel = new ResignModel($this->pdo);
                $resign_id = $resignRequestModel->createResignRequest($data);

                // Recursive manager delegation
                $this->delegateManager($resignRequestModel, $userModel, $resign_id, $_SESSION['user_id'], $reason);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                ];
                header("Location: /elms/resign");
                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/resign");
                exit();
            }
        }
    }

    public function view($id)
    {
        $resignModel = new ResignModel($this->pdo);
        $getResignById = $resignModel->getResignById($id);
        require 'src/views/resign/view&edit.php';
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
            $resign_id = $_POST['resignId'];
            // Validate required fields
            if (empty($_SESSION['user_id']) || empty($_POST['workexperience']) || empty($_POST['reason']) || empty($_FILES['attachment'])) {
                $_SESSION['error'] = [
                    'title' => "បញ្ចូលទិន្នន័យមិនគ្រប់គ្រាន់",
                    'message' => "សូមបំពេញព័ត៌មានទាំងអស់។"
                ];
                header("Location: /elms/resign");
                exit();
            }

            // Sanitize and assign input values
            $user_id = $_SESSION['user_id'];
            $workexperience = $_POST['workexperience'];
            $reason = $_POST['reason'];

            // Handle multiple file attachments
            $attachments = [];
            if (!empty($_FILES['attachment']['name'][0])) {
                // Loop through each file
                foreach ($_FILES['attachment']['name'] as $key => $attachmentName) {
                    if (!empty($attachmentName)) {
                        // Get the file info
                        $fileTmpPath = $_FILES['attachment']['tmp_name'][$key];
                        $fileName = basename($this->randomString() . $attachmentName);
                        $fileSize = $_FILES['attachment']['size'][$key];
                        $fileType = $_FILES['attachment']['type'][$key];
                        $fileError = $_FILES['attachment']['error'][$key];

                        // Perform validations (file size, type)
                        $allowedTypes = ['docx', 'pdf'];
                        $maxFileSize = 5097152; // 5MB

                        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

                        if ($fileError === 0 && in_array($fileExtension, $allowedTypes) && $fileSize <= $maxFileSize) {
                            $destination = 'public/uploads/resign-attachments/' . $fileName;

                            // Move file to the destination
                            if (move_uploaded_file($fileTmpPath, $destination)) {
                                $attachments[] = $fileName; // Store uploaded file names
                            } else {
                                $_SESSION['error'] = [
                                    'title' => "ឯកសារភ្ជាប់",
                                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។ សូមព្យាយាមម្តងទៀត។"
                                ];
                                header("Location: /elms/resign");
                                exit();
                            }
                        } else {
                            $_SESSION['error'] = [
                                'title' => "ឯកសារភ្ជាប់",
                                'message' => "ឯកសារមិនត្រឹមត្រូវទេ (ទំហំ ឬ ប្រភេទឯកសារ)។"
                            ];
                            header("Location: /elms/resign");
                            exit();
                        }
                    }
                }
            } else {
                // If no new files are uploaded, keep the existing ones
                $resignRequestModel = new ResignModel($this->pdo);
                $existingResign = $resignRequestModel->getResignRequestById($resign_id);
                $attachments = explode(', ', $existingResign['attachment']); // Assuming it's stored as a comma-separated string
            }

            // Convert the array of attachments to a comma-separated string to store in the database
            $attachment_names = implode(', ', $attachments);

            // Prepare data for updating
            $data = [
                'user_id' => $user_id,
                'workexperience' => $workexperience,
                'reason' => $reason,
                'attachment' => $attachment_names,
            ];

            try {
                // Preserve existing attachment if no new file is uploaded
                $resignRequestModel = new ResignModel($this->pdo);
                // Update the hold request using the HoldModel
                $resignRequestModel->updateResignRequest($resign_id, $data);

                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានកែប្រែដោយជោគជ័យ"
                ];
                header("Location: /elms/view&edit-resign?resignId=$resign_id");
                // $this->view($_POST['resignId']);

                exit();
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/resign");
                exit();
            }
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
                $resignModel = new ResignModel($this->pdo);
                $success = $resignModel->deleteResignById($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/resign");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/resign");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/resign");
                exit();
            }
        }
    }

    private function delegateManager($resignRequestModel, $userModel, $resign_id, $user_id, $reason)
    {
        $levels = ['getEmailLeaderDOApi', 'getEmailLeaderHOApi', 'getEmailLeaderDDApi', 'getEmailLeaderHDApi'];
        $statuses = ['leave' => 'leave', 'mission' => 'mission'];

        foreach ($levels as $apiMethod) {
            $approver = $userModel->$apiMethod($user_id, $_SESSION['token']);
            if ($approver && !$userModel->isManagerOnLeaveToday($approver['ids']) && !$userModel->isManagerOnMission($approver['ids'])) {
                $resignRequestModel->insertManagerStatusToResignApprovals($resign_id, $approver['ids'], 'pending');
                return; // Stop when a valid approver is found
            }

            // Insert status if the manager is on leave or mission
            $status = $userModel->isManagerOnLeaveToday($approver['ids']) ? $statuses['leave'] : $statuses['mission'];
            $resignRequestModel->insertManagerStatusToResignApprovals($resign_id, $approver['ids'], $status);
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

    function randomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
