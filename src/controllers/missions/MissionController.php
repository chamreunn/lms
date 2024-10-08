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

            $user_id = $_POST['user_id'];
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

            // Generate the URL for the uploaded file
            $attachment_url = 'https://leave.iauoffsa.us/elms/public/uploads/missions_attachments/' . $attachment_name;

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
                $createActivityResult = $userModel->logUserActivity($user_id, $activity);

                // Check if both operations were successful
                if ($createMissionResult) {
                    // Commit transaction
                    $this->pdo->commit();
                    $updateToApi = $userModel->updateMissionToApi($user_id, $start_date, $end_date, $mission, $_SESSION['token']);

                    // Step 1: Check if the manager has a Telegram account
                    $telegramUser = $userModel->getTelegramIdByUserId($user_id);

                    if ($telegramUser && !empty($telegramUser['telegram_id'])) {
                        // Step 2: Log the telegram_id for debugging
                        error_log("Found telegram_id: " . $telegramUser['telegram_id']);

                        // Step 3: Prepare the Telegram message
                        // Creating a list of notifications
                        $notifications = [
                            "🔔 *លិខិតបេសកកម្ម*",
                            "---------------------------------------------",
                            "👤 *អ្នកបង្កើត:* `{$_SESSION['user_khmer_name']}`",
                            "📅 *ចាប់ពី:* `{$start_date}`",
                            "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                            "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                            "📎 *ឯកសារ:* [ចុចដើម្បីទាញយកឯកសារ]({$attachment_url})", // Adding the clickable attachment link
                        ];

                        // Joining notifications into a single message with new lines
                        $telegramMessage = implode("\n", $notifications);

                        // Step 5: Attempt to send the Telegram notification with the "View the Request" button
                        $telegramModel = new TelegramModel($this->pdo);
                        $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage);

                        // Step 6: Check if the notification was successfully sent
                        if ($success) {
                            error_log("Telegram notification sent successfully to user with telegram_id: " . $telegramUser['telegram_id']);
                        } else {
                            error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramUser['telegram_id']);
                            $_SESSION['success'] = [
                                'title' => "ជោគជ័យ",
                                'message' => "បានបង្កើតបេសកកម្មរួចរាល់។"
                            ];
                        }
                    } else {
                        // Log the failure to find a valid telegram_id
                        error_log("No valid telegram_id found for managerId: ");
                        $_SESSION['success'] = [
                            'title' => "ជោគជ័យ",
                            'message' => "បានបង្កើតបេសកកម្មរួចរាល់។"
                        ];
                    }

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
            header("Location: /elms/adminmissions");
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
