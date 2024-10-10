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
        $holdModel = new HoldModel($this->pdo);
        $getHolds = $holdModel->getHoldsByUserId();
        require 'src/views/hold/index.php';
    }

    public function view($id)
    {
        $holdModel = new HoldModel($this->pdo);
        $getHoldById = $holdModel->getHoldById($id);
        require 'src/views/hold/view&edit.php';
    }

    public function create()
    {
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

            $type = 'hold';
            $color = 'bg-primary';
            $role = $_SESSION['role'];
            $departments = $_SESSION['departmentName'];
            $title = "លិខិតព្យួរការងារ";

            // Calculate duration between start_date and end_date in months and years
            $startDate = new DateTime($start_date);
            $endDate = new DateTime($end_date);
            $interval = $startDate->diff($endDate);

            // Format the duration as X years, Y months
            $years = $interval->y;
            $months = $interval->m;
            $duration = "";
            if ($years > 0) {
                $duration .= $years . " ឆ្នាំ" . " ";
            }
            if ($months > 0) {
                $duration .= $months . " ខែ" . " ";
            }

            // Error handling: check if the duration meets the requirement (at least 1 month)
            if ($years === 0 && $months < 1) {
                $_SESSION['error'] = [
                    'title' => "កំហុសវ័យ",
                    'message' => "ប្រការ: ចំនួនសម្រាប់អារក្សគឺត្រូវតែយ៉ាងហោចណាស់ 1 ខែ។"
                ];
                header("Location: /elms/hold");
                exit();
            }

            // Initialize attachment as null (optional field)
            $attachment_name = $this->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 5097152, 'public/uploads/hold-attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/hold");
                exit();
            }

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'attachment' => $attachment_name, // This will be null if no file was uploaded
                'duration' => $duration, // Store the calculated duration
                'type' => $type,
                'color' => $color
            ];

            $getalllevels = $this->getAllrole($user_id, $role, $departments);

            // Call the model method to save the hold request
            $holdRequestModel = new HoldModel($this->pdo); // Assuming $this->pdo is your PDO instance

            if ($holdRequestModel->createHoldRequest($data)) {

                $userModel->sendDocks($title, $userModel, $getalllevels['managerId'], $start_date, $end_date, $duration, $reason, $holdRequestModel, 'null');
                
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "សំណើរបានបញ្ចូលដោយជោគជ័យ"
                ];
                header("Location: /elms/hold");
            } else {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មិនអាចបញ្ចូលសំណើបានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/hold");
            }
        }
    }

    public function getAllrole($user_id, $role, $departments)
    {
        $userModel = new User();
        $Depoffice = null;

        // Define a mapping of roles to corresponding API methods
        $roleToApiMap = [
            'Deputy Head Of Office' => 'getEmailLeaderHOApi',
            'Head Of Office' => 'getEmailLeaderDDApi',
            'Deputy Head Of Department' => 'getEmailLeaderHDApi',
            'Deputy Head Of Unit 1' => 'getEmailLeaderHUApi',
            'Deputy Head Of Unit 2' => 'getEmailLeaderHUApi',
            'Head Of Department' => 'getEmailLeaderDHU1Api',  // Default for Head Of Department
            'Default' => 'getEmailLeaderDOApi' // Fallback for any unspecified role
        ];

        // Special case for 'Head Of Department' with department-specific APIs
        if ($role == 'Head Of Department') {
            if ($departments == 'នាយកដ្ឋានកិច្ចការទូទៅ' || $departments == 'នាយកដ្ឋានសនវកម្មទី២') {
                $Depoffice = $userModel->getEmailLeaderDHU1Api($user_id, $_SESSION['token']);
            } else {
                $Depoffice = $userModel->getEmailLeaderDHU2Api($user_id, $_SESSION['token']);
            }
        } else {
            // General case for other roles based on mapping
            $Depoffice = $userModel->{$roleToApiMap[$role] ?? $roleToApiMap['Default']}($user_id, $_SESSION['token']);
        }

        // Check if the API response is valid
        if (!$Depoffice || $Depoffice['http_code'] !== 200 || empty($Depoffice['ids'])) {
            throw new Exception("Unable to find office details. Please contact support.");
        }

        // Iterate through the list of managers
        foreach ($Depoffice['ids'] as $index => $managerId) {
            $managerEmail = $Depoffice['emails'][$index] ?? null;
            $managerName = (!empty($Depoffice['lastNameKh'][$index]) && !empty($Depoffice['firstNameKh'][$index]))
                ? $Depoffice['lastNameKh'][$index] . ' ' . $Depoffice['firstNameKh'][$index]
                : null;

            // Check if the manager is on leave or mission
            $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
            $isManagerOnMission = $userModel->isManagerOnMission($managerId);

            if (!$isManagerOnLeave && !$isManagerOnMission) {
                // If the manager is available, return the manager's details
                return [
                    'managerId' => $managerId,
                    'managerEmail' => $managerEmail,
                    'managerName' => $managerName,
                ];
            } else {
                // Escalate to the next manager if the current one is unavailable
                $fallbackRole = $this->getFallbackRole($role);
                $DepofficeFallback = $userModel->{$roleToApiMap[$fallbackRole] ?? $roleToApiMap['Default']}($user_id, $_SESSION['token']);

                // Check if fallback manager is available
                if ($DepofficeFallback && $DepofficeFallback['http_code'] === 200 && !empty($DepofficeFallback['ids'])) {
                    // Return the fallback manager's details
                    $fallbackManagerId = $DepofficeFallback['ids'][0] ?? null;
                    $fallbackManagerEmail = $DepofficeFallback['emails'][0] ?? null;
                    $fallbackManagerName = (!empty($DepofficeFallback['lastNameKh'][0]) && !empty($DepofficeFallback['firstNameKh'][0]))
                        ? $DepofficeFallback['lastNameKh'][0] . ' ' . $DepofficeFallback['firstNameKh'][0]
                        : null;

                    return [
                        'managerId' => $fallbackManagerId,
                        'managerEmail' => $fallbackManagerEmail,
                        'managerName' => $fallbackManagerName,
                    ];
                }
            }
        }

        // If no manager is available, throw an exception or handle accordingly
        throw new Exception("No available manager found. All managers are on leave or on a mission.");
    }

    // Helper function to determine fallback role
    private function getFallbackRole($role, $department = null)
    {
        // Define the default fallback roles
        $fallbackRoles = [
            'Deputy Head Of Office' => 'Head Of Office',
            'Head Of Office' => 'Deputy Head Of Department',
            'Deputy Head Of Department' => 'Head Of Department',
            'Head Of Department' => 'Deputy Head Of Unit 1', // General case
            'Deputy Head Of Unit 1' => 'Deputy Head Of Unit 2',
            'Deputy Head Of Unit 2' => 'Default', // Fallback to default (lowest level)
            'Default' => 'Head Of Office', // If no specific role, escalate to Head Of Office
        ];

        // Special logic based on departments for Head Of Department fallback
        if ($role == 'Head Of Department') {
            if ($department == 'នាយកដ្ឋានកិច្ចការទូទៅ' || $department == 'នាយកដ្ឋានសនវកម្មទី២') {
                return 'Deputy Head Of Unit 1'; // Specific to Unit 1 based on department
            } else {
                return 'Deputy Head Of Unit 2'; // Specific to Unit 2 based on other departments
            }
        }

        // Return the fallback role based on the role provided, or 'Default' if not found
        return $fallbackRoles[$role] ?? 'Default';
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

        // Ensure upload directory exists
        if (!is_dir($upload_path)) {
            if (!mkdir($upload_path, 0755, true)) {
                $_SESSION['error'] = [
                    'title' => "File Error",
                    'message' => "Failed to create upload directory."
                ];
                return false;
            }
        }

        // Preserve the original file name with a unique suffix to avoid overwriting
        $unique_file_name = pathinfo($file_name, PATHINFO_FILENAME) . '_' . uniqid('', true) . '.' . $file_ext;
        $destination = $upload_path . DIRECTORY_SEPARATOR . $unique_file_name;

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
