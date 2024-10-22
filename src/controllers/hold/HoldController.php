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
        // Get the current page and set the number of records per page
        $currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $recordsPerPage = 5; // Set the desired number of records per page

        // Calculate the offset for the current page
        $offset = ($currentPage - 1) * $recordsPerPage;

        $holdModel = new HoldModel($this->pdo);
        $getHolds = $holdModel->getHoldsByUserId($offset, $recordsPerPage);
         // Fetch total records for pagination calculation
        $totalRecords = $holdModel->getHoldsCountById();
        // Calculate total pages
        $totalPages = ceil($totalRecords / $recordsPerPage);
        require 'src/views/hold/index.php';
    }

    public function view($id)
    {
        $holdModel = new HoldModel($this->pdo);
        $getHoldById = $holdModel->getHoldById($_SESSION['user_id']);
        require 'src/views/hold/view&edit.php';
    }

    public function create()
    {
        // Ensure the session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

            // Validate date order: start_date should not be greater than end_date
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

            // Handle file attachment upload (optional field)
            if (!empty($_FILES['attachment']['name'])) {
                $attachment_name = $this->handleFileUpload($_FILES['attachment'], ['docx', 'pdf'], 5097152, 'public/uploads/hold-attachments/');
                if ($attachment_name === false) {
                    $_SESSION['error'] = [
                        'title' => "ឯកសារភ្ជាប់",
                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                    ];
                    header("Location: /elms/hold");
                    exit();
                }
            } else {
                $attachment_name = null; // No file uploaded
            }

            // Get manager's ID based on role and department
            try {
                $getalllevels = $this->getAllRole($user_id, $role, $departments);
                $managerId = $getalllevels['managerId'];
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => $e->getMessage()
                ];
                header("Location: /elms/hold");
                exit();
            }

            // Check if the manager is on leave or mission
            $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
            $isManagerOnMission = $userModel->isManagerOnMission($managerId);

            // Fallback logic: If manager is on leave or mission, try to escalate to the next available manager
            if ($isManagerOnLeave || $isManagerOnMission) {
                $fallbackRole = $this->getFallbackRole($role);
                try {
                    $fallbackManagerDetails = $this->getAllRole($user_id, $fallbackRole, $departments);
                    $managerId = $fallbackManagerDetails['managerId'];
                } catch (Exception $e) {
                    $_SESSION['error'] = [
                        'title' => "កំហុស",
                        'message' => $e->getMessage()
                    ];
                    header("Location: /elms/hold");
                    exit();
                }

                // Record the manager's leave or mission status
                $status = $isManagerOnLeave ? 'leave' : 'mission';
                $comment = $isManagerOnLeave ? 'Manager is on leave today' : 'Manager is on mission today';
            } else {
                // If the manager is available, status and comment are empty
                $status = '';
                $comment = '';
            }

            // Prepare data for saving
            $data = [
                'user_id' => $user_id,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'reason' => $reason,
                'attachment' => $attachment_name,
                'duration' => $duration,
                'type' => $type,
                'color' => $color,
                'approver_id' => $managerId
            ];

            try {
                // Save the hold request using the HoldModel
                $holdRequestModel = new HoldModel($this->pdo); // Assuming $this->pdo is your PDO instance
                $hold_id = $holdRequestModel->createHoldRequest($data);
                $holdRequestModel->insertManagerStatusToHoldsApprovals($hold_id, $managerId, $status, $comment);
                // If a fallback manager was used, save their status
                if (!empty($status)) {
                    $holdRequestModel->insertManagerStatusToHoldsApprovals($hold_id, $managerId, $status, $comment);
                }

                // Send notification after saving the request
                $userModel->sendDocks($title, $managerId, $start_date, $end_date, $duration, $reason, $holdRequestModel);

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

    public function getAllRole($user_id, $role, $departments)
    {
        $userModel = new User();
        $roleToApiMap = [
            'Deputy Head Of Office' => 'getEmailLeaderDOApi',
            'Head Of Office' => 'getEmailLeaderHOApi',
            'Deputy Head Of Department' => 'getEmailLeaderDDApi',
            'Deputy Head Of Unit 1' => 'getEmailLeaderHUApi',
            'Deputy Head Of Unit 2' => 'getEmailLeaderHUApi',
            'Head Of Department' => 'getEmailLeaderDHU1Api'
        ];

        $Depoffice = null;

        // Special handling for 'Head Of Department' role based on department
        if ($role == 'Head Of Department') {
            $Depoffice = ($departments == 'នាយកដ្ឋានកិច្ចការទូទៅ' || $departments == 'នាយកដ្ឋានសនវកម្មទី២')
                ? $userModel->getEmailLeaderDHU1Api($user_id, $_SESSION['token'])
                : $userModel->getEmailLeaderDHU2Api($user_id, $_SESSION['token']);
        } elseif (isset($roleToApiMap[$role])) {
            // Fetch the API method based on the role
            $apiMethod = $roleToApiMap[$role];
            $Depoffice = $userModel->{$apiMethod}($user_id, $_SESSION['token']);
        } else {
            // If role is 'NULL', escalate to fallback role directly
            return $this->escalateToFallbackRole($user_id, $role, $departments);
        }

        // Validate the API response
        if (!$Depoffice || $Depoffice['http_code'] !== 200 || empty($Depoffice['ids'])) {
            return $this->escalateToFallbackRole($user_id, $role, $departments);
        }

        // Loop through the list of potential managers and check availability
        foreach ($Depoffice['ids'] as $index => $managerId) {
            $managerEmail = $Depoffice['emails'][$index] ?? null;
            $managerName = (!empty($Depoffice['lastNameKh'][$index]) && !empty($Depoffice['firstNameKh'][$index]))
                ? $Depoffice['lastNameKh'][$index] . ' ' . $Depoffice['firstNameKh'][$index]
                : null;

            $isManagerOnLeave = $userModel->isManagerOnLeaveToday($managerId);
            $isManagerOnMission = $userModel->isManagerOnMission($managerId);

            // Return the manager's details if they are available (not on leave or mission)
            if (!$isManagerOnLeave && !$isManagerOnMission) {
                return [
                    'managerId' => $managerId,
                    'managerEmail' => $managerEmail,
                    'managerName' => $managerName,
                ];
            }
        }

        // If no available manager is found, escalate to the fallback role
        return $this->escalateToFallbackRole($user_id, $role, $departments);
    }

    private function escalateToFallbackRole($user_id, $role, $departments)
    {
        $fallbackRole = $this->getFallbackRole($role, $departments);

        // Log the fallback role for debugging purposes
        error_log("Escalating to fallback role: $fallbackRole");

        if ($fallbackRole) {
            // Recursively call getAllRole with the fallback role
            return $this->getAllRole($user_id, $fallbackRole, $departments);
        }

        // Log failure to find a suitable manager
        error_log("Unable to find a suitable manager for role: $role");

        // If no fallback is available, throw an exception
        throw new Exception("Unable to find a suitable manager. Please contact support.");
    }

    private function getFallbackRole($role, $department = null)
    {
        $fallbackRoles = [
            'NULL' => 'Deputy Head Of Office', // For a 'NULL' role, escalate to Deputy Head Of Office
            'Deputy Head Of Office' => 'Head Of Office',
            'Head Of Office' => 'Deputy Head Of Department',
            'Deputy Head Of Department' => 'Head Of Department',
            'Head Of Department' => $department === 'នាយកដ្ឋានសនវកម្មទី២' ? 'Deputy Head Of Unit 2' : 'Deputy Head Of Unit 1',
            'Deputy Head Of Unit 1' => 'Head Of Unit',
            'Deputy Head Of Unit 2' => 'Head Of Unit',
        ];

        return $fallbackRoles[$role] ?? 'Deputy Head Of Office';
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
