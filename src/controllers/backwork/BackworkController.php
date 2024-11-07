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
        require 'src/views/backwork/index.php';
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
}
