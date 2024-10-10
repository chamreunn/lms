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
            $role = $_SESSION['role'];
            $departments = $_SESSION['departmentName'];
            $title = "លិខិតព្យួរការងារ";
    
            // Calculate duration between start_date and end_date in months and years
            $startDate = new DateTime($start_date);
            $endDate = new DateTime($end_date);
            $interval = $startDate->diff($endDate);  // Get DateInterval object
    
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
    
            // Get manager's ID based on role and department
            $getalllevels = $this->getAllRole($user_id, $role, $departments);
            $managerId = $getalllevels['managerId'];
    
            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'attachment' => $attachment_name, // This will be null if no file was uploaded
                'duration' => $duration, // Store the calculated duration
                'type' => $type,
                'color' => $color,
                'approver_id' => $managerId
            ];
    
            // Call the model method to save the hold request
            $holdRequestModel = new HoldModel($this->pdo); // Assuming $this->pdo is your PDO instance
    
            if ($holdRequestModel->createHoldRequest($data)) {
                $userModel->sendDocks($title, $userModel, $managerId, $start_date, $end_date, $duration, $reason, $holdRequestModel, null);
    
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
    
    public function getAllRole($user_id, $role, $departments)
    {
        $userModel = new User();
        $roleToApiMap = [
            'Deputy Head Of Office' => 'getEmailLeaderDOApi',
            'Head Of Office' => 'getEmailLeaderHOApi',
            'Deputy Head Of Department' => 'getEmailLeaderDDApi',
            'Deputy Head Of Unit 1' => 'getEmailLeaderHUApi',
            'Deputy Head Of Unit 2' => 'getEmailLeaderHUApi',
            'Head Of Department' => 'getEmailLeaderDHU1Api',  // Default for Head Of Department
            'NULL' => 'getEmailLeaderDOApi' // Fallback for any unspecified role
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
            $Depoffice = $userModel->{$roleToApiMap[$role] ?? $roleToApiMap['NULL']}($user_id, $_SESSION['token']);
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
                $DepofficeFallback = $userModel->{$roleToApiMap[$fallbackRole] ?? $roleToApiMap['NULL']}($user_id, $_SESSION['token']);
    
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
    
        throw new Exception("Unable to find a suitable manager. Please contact support.");
    }
    
    private function handleFileUpload($file, $allowedExtensions, $maxSize, $uploadDir)
    {
        if (!empty($file['name'])) {
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedExtensions)) {
                return false; // Invalid file extension
            }

            if ($file['size'] > $maxSize) {
                return false; // File exceeds size limit
            }

            $fileName = uniqid() . '.' . $fileExtension;
            $uploadPath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $fileName;
            } else {
                return false; // Failed to upload file
            }
        }

        return null; // No file uploaded
    }

    private function getFallbackRole($role)
    {
        $fallbackRoles = [
            'Deputy Head Of Office' => 'Head Of Office',
            'Head Of Office' => 'Deputy Head Of Department',
            'Deputy Head Of Department' => 'Head Of Department',
            'Head Of Department' => 'Deputy Head Of Unit 1',
            'Deputy Head Of Unit 1' => 'Deputy Head Of Unit 2',
            'Deputy Head Of Unit 2' => 'NULL',
            'NULL' => 'Head Of Office'
        ];

        return $fallbackRoles[$role] ?? 'NULL';
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
