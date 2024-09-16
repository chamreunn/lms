<?php
require_once 'src/models/missions/MissionModel.php';
require_once 'src/models/User.php';

class MissionController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $missionModel = new MissionModel();
        $missions = $missionModel->getMissionById($_SESSION['user_id'], $_SESSION['token']);
        require 'src/views/missions/index.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $user_id = $_SESSION['user_id'];
            $mission_name = $_POST['mission_name'] ?? "NULL";
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $activity = "បង្កើតសំណើបេសកកម្ម";
            $mission = "1" ?? "NULL";

            // Handle file upload
            $attachment_name = $this->handleFileUpload($_FILES['missionDoc'], ['docx', 'pdf'], 5097152, 'public/uploads/missions_attachments/');
            if ($attachment_name === false) {
                $_SESSION['error'] = [
                    'title' => "ឯកសារភ្ជាប់",
                    'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                ];
                header("Location: /elms/mission");
                exit();
            }

            try {
                // Start transaction
                $this->pdo->beginTransaction();

                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $this->calculateTotalDays($datetime_start, $datetime_end);

                // Create mission request
                $missionModel = new MissionModel();
                $createMissionResult = $missionModel->create($user_id, $mission_name, $start_date, $end_date, $attachment_name, $duration_days);

                // Log user activity
                $userModel = new User();
                $createActivityResult = $userModel->logUserActivity($user_id, $activity);

                // Check if both operations were successful
                if ($createMissionResult) {
                    // Commit transaction
                    $this->pdo->commit();
                    $updateToApi = $userModel->updateMissionToApi($user_id, $start_date, $end_date, $mission, $_SESSION['token']);

                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "បង្កើតបេសកកម្មបានជោគជ័យ។"
                    ];
                } else {
                    // If either operation fails, roll back
                    $this->pdo->rollBack();
                    throw new Exception("Failed to create mission or log activity.");
                }
            } catch (Exception $e) {
                // Rollback transaction on error
                $this->pdo->rollBack();
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបង្កើតសំណើបេសកកម្ម: " . $e->getMessage()
                ];
            }

            // Redirect to mission page
            header("Location: /elms/mission");
            exit();
        }
    }

    public function update($mission_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $user_id = $_SESSION['user_id'];
            $mission_name = $_POST['mission_name'] ?? "NULL";
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $activity = "កែសម្រួលសំណើបេសកកម្ម";

            // Handle file upload (optional)
            $attachment_name = null;
            if (!empty($_FILES['missionDoc']['name'])) {
                $attachment_name = $this->handleFileUpload($_FILES['missionDoc'], ['docx', 'pdf'], 2097152, 'public/uploads/missions_attachments/');
                if ($attachment_name === false) {
                    $_SESSION['error'] = [
                        'title' => "ឯកសារភ្ជាប់",
                        'message' => "មិនអាចបញ្ចូលឯកសារភ្ជាប់បានទេ។​ សូមព្យាយាមម្តងទៀត"
                    ];
                    header("Location: /elms/mission");
                    exit();
                }
            }

            try {
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $this->calculateTotalDays($datetime_start, $datetime_end);

                // Update mission
                $missionModel = new MissionModel();
                $updateMissionResult = $missionModel->update($mission_id, $user_id, $mission_name, $start_date, $end_date, $attachment_name, $duration_days);

                // Log user activity
                $userModel = new User();
                $updateActivityResult = $userModel->logUserActivity($user_id, $activity);

                if ($updateMissionResult && $updateActivityResult) {
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "បេសកកម្មត្រូវបានកែប្រែដោយជោគជ័យ។"
                    ];
                } else {
                    throw new Exception("Failed to update mission or log activity.");
                }
            } catch (Exception $e) {
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការកែប្រែសំណើបេសកកម្ម: " . $e->getMessage()
                ];
            }

            header("Location: /elms/mission");
            exit();
        }
    }

    public function delete($id)
    {
        $missionModel = new MissionModel();
        if ($missionModel->delete($id)) {
            $_SESSION['success'] = [
                'title' => "លុបបេសកកម្ម",
                'message' => "លុបបានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបបេសកកម្ម",
                'message' => "មានបញ្ហា មិនអាចលុបបេសកកម្មបានទេ។"
            ];
        }
        header("Location: /elms/mission");
        exit();
    }

    public function viewMissionWithFilters()
    {
        $leaveMissiontModel = new MissionModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'end_date' => $_POST['end_date'] ?? null,
        ];

        $missions = $leaveMissiontModel->getMissionByFilters($user_id, $filters);

        require 'src/views/missions/index.php';
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


    private function calculateTotalDays(DateTime $start_date, DateTime $end_date)
    {
        $interval = $start_date->diff($end_date);
        return $interval->days + 1; // +1 to include both start and end date
    }
}
