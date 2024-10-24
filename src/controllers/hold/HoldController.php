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
                        $maxFileSize = 5097152; // 5MB

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
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'attachment' => $attachment_names,
                'duration' => $duration,
                'type' => $type,
                'color' => $color,
                'approver_id' => $approver
            ];

            try {
                // Save the hold request using the HoldModel
                $userModel = new User();
                $holdRequestModel = new HoldModel($this->pdo);
                $hold_id = $holdRequestModel->createHoldRequest($data);

                // Recursive manager delegation
                $this->delegateManager($holdRequestModel, $userModel, $hold_id, $_SESSION['user_id'], $reason);

                // Send notification after saving the request
                $userModel->sendDocks($title, $approver, $start_date, $end_date, $duration, $reason, $holdRequestModel);

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

            // Handle multiple file attachments
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
                        $maxFileSize = 5097152; // 5MB

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
            } else {
                // If no new files are uploaded, keep the existing ones
                $holdRequestModel = new HoldModel($this->pdo);
                $existingHold = $holdRequestModel->getHoldRequestById($hold_id);
                $attachments = explode(',', $existingHold['attachment']); // Assuming it's stored as a comma-separated string
            }

            // Convert the array of attachments to a comma-separated string to store in the database
            $attachment_names = implode(',', $attachments);

            // Prepare data for updating
            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'attachment' => $attachment_names, // Store the updated attachments
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
}
