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
            $userModel = new User();
            $user_ids = $_POST['user_id']; // Array of selected user IDs
            $mission_name = $_POST['mission_name'] ?? "NULL";
            $start_date = $_POST['start_date'];
            $end_date = $_POST['end_date'];
            $activity = "បង្កើតសំណើបេសកកម្ម";
            $mission = "1" ?? "NULL";
            $attachment_name = null;
            $attachment_url = null;

            try {
                $this->pdo->beginTransaction();

                // Handle file upload (if any)
                if (isset($_FILES['missionDoc']) && $_FILES['missionDoc']['error'] == UPLOAD_ERR_OK) {
                    $file = $_FILES['missionDoc'];
                    $uploadDir = 'public/uploads/missions_attachments/';
                    $attachment_name = $this->handleFileUpload($file, ['pdf'], 20971520, $uploadDir);

                    if ($attachment_name) {
                        $attachment_url = 'https://leave.iauoffsa.us/elms/public/uploads/missions_attachments/' . $attachment_name;
                        error_log("File uploaded successfully. Filename: $attachment_name");
                    } else {
                        throw new Exception("Failed to upload or move the file.");
                    }
                }

                // Calculate mission duration
                $datetime_start = new DateTime($start_date);
                $datetime_end = new DateTime($end_date);
                $duration_days = $this->calculateTotalDays($datetime_start, $datetime_end);

                // Insert mission records for each user_id
                $missionModel = new MissionModel();
                foreach ($user_ids as $user_id) {
                    $createMissionResult = $missionModel->create($user_id, $mission_name, $start_date, $end_date, $attachment_name, $duration_days);

                    // Log user activity for each selected user
                    $userModel->logUserActivity($user_id, $activity);

                    // Update mission in the API for each user
                    $userModel->updateMissionToApi($user_id, $start_date, $end_date, $mission, $_SESSION['token']);

                    // Send Telegram notifications for each user with a Telegram ID
                    $telegramUser = $userModel->getTelegramIdByUserId($user_id);
                    if ($telegramUser && !empty($telegramUser['telegram_id'])) {
                        $notifications = [
                            "🔔 *លិខិតបេសកកម្ម*",
                            "---------------------------------------------",
                            "👤 *អ្នកបង្កើត:* `{$_SESSION['user_khmer_name']}`",
                            "📅 *ចាប់ពី:* `{$start_date}`",
                            "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                            "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                            "📎 *ឯកសារ:* [ចុចដើម្បីទាញយកឯកសារ]({$attachment_url})",
                        ];
                        $telegramMessage = implode("\n", $notifications);

                        $telegramModel = new TelegramModel($this->pdo);
                        $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage);
                    }
                }

                $this->pdo->commit();
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "បង្កើតបេសកកម្មបានជោគជ័យ។"
                ];
            } catch (Exception $e) {
                $this->pdo->rollBack();
                error_log("Error during mission creation: " . $e->getMessage());
                $_SESSION['error'] = [
                    'title' => "កំហុស",
                    'message' => "មានបញ្ហាក្នុងការបង្កើតសំណើបេសកកម្ម: " . $e->getMessage()
                ];
            }

            header("Location: /elms/adminmissions");
            exit();
        }
    }

    private function handleFileUpload($file, $allowed_extensions, $max_size, $upload_path)
    {
        $file_name = basename($file['name']);
        $file_tmp_name = $file['tmp_name'];
        $file_error = $file['error'];
        $file_size = $file['size'];

        // Log initial file upload info
        error_log("File Upload Info: Name: $file_name, Size: $file_size, Error: $file_error");

        if ($file_error === UPLOAD_ERR_NO_FILE) {
            error_log("No file was uploaded.");
            return null; // No file uploaded
        }

        if ($file_error !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "An error occurred during the file upload."
            ];
            error_log("Upload Error: " . $file_error);
            return false;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        error_log("File Extension: $file_ext");

        // Log allowed extensions
        error_log("Allowed Extensions: " . implode(", ", $allowed_extensions));

        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Invalid attachment file type."
            ];
            error_log("Invalid file type attempted: $file_ext");
            return false;
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Attachment file size exceeds the limit."
            ];
            error_log("File size exceeds limit: $file_size > $max_size");
            return false;
        }

        // Create a unique file name
        $unique_file_name = uniqid('', true) . '.' . $file_ext;
        $destination = $upload_path . $unique_file_name;

        error_log("Moving file to: $destination");

        if (!move_uploaded_file($file_tmp_name, $destination)) {
            $error = error_get_last();
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Failed to move the uploaded file. Error: " . $error['message']
            ];
            error_log("Failed to move file. Error: " . $error['message']);
            return false;
        }

        error_log("File uploaded successfully: $unique_file_name");
        return $unique_file_name; // Success
    }

    public function update($mission_id)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $user_id = $_POST['userId'];
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
                    header("Location: /elms/adminmissions");
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

            header("Location: /elms/adminmissions");
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
        header("Location: /elms/adminmissions");
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

    private function calculateTotalDays(DateTime $start_date, DateTime $end_date)
    {
        // Fetch holidays from the database
        $holidayModel = new CalendarModel();
        $holidays = $holidayModel->getHoliday(); // Assume this returns an array of holiday dates

        // Convert holidays to DateTime objects for comparison
        $holidayDates = array_map(function ($holiday) {
            return new DateTime($holiday['holiday_date']);
        }, $holidays);

        // Proceed to calculate the number of business days between the start and end date
        $business_days = 0;
        $current_date = clone $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = $current_date->format('N');

            // Check if the current date is a weekday and not a holiday
            if ($day_of_week < 6 && !in_array($current_date, $holidayDates)) {
                $business_days++;
            }

            $current_date->modify('+1 day');
        }

        return $business_days;
    }
}
