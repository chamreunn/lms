<?php
require_once 'src/models/lates/LateModel.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LateController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $lateModel = new LateModel();
        $getlates = $lateModel->getLateModelByUserId($_SESSION['user_id']);

        require 'src/views/documentlate/index.php';
    }

    public function overtimein()
    {
        $lateModel = new LateModel();
        $getovertimein = $lateModel->getOvertimein($_SESSION['user_id'], $_SESSION['token']);

        require 'src/views/documentlate/overtimein.php';
    }

    public function overtimeout()
    {
        $lateModel = new LateModel();
        $getovertimeout = $lateModel->getOvertimeOut($_SESSION['user_id'], $_SESSION['token']);

        require 'src/views/documentlate/overtimeout.php';
    }

    public function leaveearly()
    {
        $lateModel = new LateModel();
        $getleaveearly = $lateModel->getLeaveEarly($_SESSION['user_id'], $_SESSION['token']);

        require 'src/views/documentlate/leaveearly.php';
    }

    public function viewOvertimeInWithFilters()
    {
        $filterOvertimeIn = new LateModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $getovertimein = $filterOvertimeIn->getOvertimeInByFilters($user_id, $filters);

        require 'src/views/documentlate/overtimein.php';
    }

    public function viewOvertimeOutWithFilters()
    {
        $filterOvertimeIn = new LateModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $getovertimeout = $filterOvertimeIn->getOvertimeOutByFilters($user_id, $filters);

        require 'src/views/documentlate/overtimeout.php';
    }

    public function viewLeaveEarlyWithFilters()
    {
        $filterOvertimeIn = new LateModel();
        $user_id = $_SESSION['user_id'];

        $filters = [
            'start_date' => $_POST['start_date'] ?? null,
            'status' => $_POST['status'] ?? null,
        ];

        $getleaveearly = $filterOvertimeIn->getLeaveEarlyByFilters($user_id, $filters);

        require 'src/views/documentlate/leaveearly.php';
    }

    public function store($name, $color)
    {
        global $pdo;
        $lateModel = new LateModel();
        $lateModel->createLate($name, $color);

        $_SESSION['success'] = [
            'title' => "បង្កើតលិខិតថ្មី",
            'message' => "បង្កើតលិខិតថ្មីបានជោគជ័យ។"
        ];

        header("Location: /elms/documents");
        exit();
    }

    public function update($id, $name, $color)
    {
        global $pdo;
        $lateModel = new LateModel($pdo);
        $lateModel->updateLate($id, $name, $color);

        $_SESSION['success'] = [
            'title' => "កែប្រែលិខិត",
            'message' => "បានកែប្រែរួចរាល់។"
        ];

        header("Location: /elms/documents");
        exit();
    }

    public function deleteLateIn($id)
    {
        $lateModel = new LateModel();
        if ($lateModel->deleteLateIn($id)) {
            $_SESSION['success'] = [
                'title' => "លុបលិខិត",
                'message' => "លុបបានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបលិខិត",
                'message' => "មិនអាចលុបលិខិតបានទេ។"
            ];
        }
        header("Location: /elms/overtimein");
        exit();
    }

    public function deleteLateOut($id)
    {
        $lateModel = new LateModel();
        if ($lateModel->deleteLateOut($id)) {
            $_SESSION['success'] = [
                'title' => "លុបលិខិត",
                'message' => "លុបបានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបលិខិត",
                'message' => "មិនអាចលុបលិខិតបានទេ។"
            ];
        }
        header("Location: /elms/overtimeout");
        exit();
    }

    public function deleteLeaveEarly($id)
    {
        $lateModel = new LateModel();
        if ($lateModel->deleteLateEarly($id)) {
            $_SESSION['success'] = [
                'title' => "លុបលិខិត",
                'message' => "លុបបានជោគជ័យ។"
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "លុបលិខិត",
                'message' => "មិនអាចលុបលិខិតបានទេ។"
            ];
        }
        header("Location: /elms/leaveearly");
        exit();
    }

    public function requestLateIn()
    {
        $lateModel = new LateModel();
        $getlates = $lateModel->getAllLatetype();

        require 'src/views/documentlate/late_in.php';
    }

    public function requestLateOut()
    {
        $lateModel = new LateModel();
        $getlates = $lateModel->getAllLatetype();

        require 'src/views/documentlate/late_out.php';
    }

    public function createLateIn($date, $time, $reason)
    {
        // Validate and sanitize inputs
        $date = trim($date);
        $time = trim($time);
        $reason = trim($reason);
        $userId = $_SESSION['user_id'];
        $title = "សំណើចូលយឺត";
        $type = "latein";

        // Basic validation
        if (empty($date)) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "ចន្លោះកាលបរិច្ឆេទត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        if (empty($time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ចន្លោះម៉ោងត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        if (empty($reason)) {
            $_SESSION['error'] = [
                'title' => "Reason Error",
                'message' => "ចន្លោះមូលហេតុត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        // Validate date format (Y-m-d)
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "ទ្រង់ទ្រាយកាលបរិច្ឆេទមិនត្រឹមត្រូវ។ ប្រើ Y-m-d."
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        // Validate time format (HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ទ្រង់ទ្រាយម៉ោងមិនត្រឹមត្រូវ។ ប្រើ HH:MM."
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        // Add seconds to time if necessary
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ":00"; // Converts HH:MM to HH:MM:SS format
        }

        // Calculate late minutes
        $workStartTime = DateTime::createFromFormat('H:i', '09:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time);

        $lateMinutes = 0;
        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i; // Total late minutes
        }

        // Sanitize reason input
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {
            // Begin transaction
            $lateModel = new LateModel();
            $lateModel->beginTransaction(); // Assuming you have a method for beginning a transaction

            // Fetch the admin emails via API
            $userModel = new User();
            $adminEmails = $userModel->getAdminEmails($_SESSION['token']);

            // Fetch the user's name in Khmer using the user ID and session token
            $userInfo = $userModel->getUserByIdApi($userId, $_SESSION['token']);
            if (!$userInfo || empty($userInfo['data'])) {
                throw new Exception("Unable to fetch user information.");
            }
            $userNameKh = $userInfo['data']['lastNameKh'] . ' ' . $userInfo['data']['firstNameKh'];

            // Check if fetching the admin emails was successful
            if (!$adminEmails || $adminEmails['http_code'] !== 200 || empty($adminEmails['emails'])) {
                throw new Exception("Unable to fetch admin emails.");
            }

            // Apply the late-in request to the database
            $lateModel->applyLateIn($userId, $type, $date, $time, $lateMinutes, $reason);

            // Use the first admin email or handle multiple emails as needed
            $adminEmail = $adminEmails['emails'][0];
            $adminName = $adminEmails['lastNameKh'][0] . ' ' . $adminEmails['firstNameKh'][0];
            $adminId = $adminEmails['ids'][0];

            // Get the admin's Telegram ID
            $telegramUser = $userModel->getTelegramIdByUserId($adminId);
            if ($telegramUser && !empty($telegramUser['telegram_id'])) {
                // Log the Telegram ID for debugging
                error_log("Found telegram_id: " . $telegramUser['telegram_id']);

                $notifications = [
                    "🔔 *សំណើចូលយឺត*",
                    "---------------------------------------------",
                    "👤 *អ្នកស្នើ: *`{$userNameKh}`",
                    "⏰ *ចូលយឺត:  *`{$lateMinutes} នាទី`",
                    "🗓️ *កាលបរិច្ឆេទ:   *`{$date}`",
                    "🕒 *ម៉ោង:    *`{$time}`",
                    "💬 *មូលហេតុ:  *`{$reason}`",
                ];

                // Joining notifications into a single message with new lines
                $telegramMessage = implode("\n", $notifications);

                // Step 4: Create the inline keyboard with a single "View the Request" button
                $keyboard = [
                    'inline_keyboard' => [
                        [
                            ['text' => 'ពិនិត្យមើលសំណើ', 'url' => 'https://leave.iauoffsa.us/elms/overtimein/'] // Using URL to open the request
                        ]
                    ]
                ];

                // Send the Telegram notification
                $telegramModel = new TelegramModel($this->pdo);
                $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage, $keyboard);

                // Check if the notification was successfully sent
                if (!$success) {
                    error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramUser['telegram_id']);
                    $_SESSION['error'] = [
                        'title' => "Telegram Notification Error",
                        'message' => "Could not send Telegram notification. Please check your settings or contact support."
                    ];
                }
            } else {
                // Log the failure to find a valid telegram_id
                error_log("No valid telegram_id found for adminId: " . $adminId);
                $_SESSION['error'] = [
                    'title' => "Telegram Notification Error",
                    'message' => "No Telegram ID associated with the admin. Notification could not be sent."
                ];
            }

            // Send email notification to the admin, including the user's Khmer name
            if (!$this->sendLateInEmail($userNameKh, $adminEmail, $date, $time, $lateMinutes, $reason, $title)) {
                throw new Exception("Notification email could not be sent.");
            }

            // Commit transaction after success
            $lateModel->commitTransaction();

            // Optionally display admin email in success message
            $_SESSION['success'] = [
                'title' => "សំណើចូលយឺត",
                'message' => "អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . ($adminName ?? "") . ". សូមអរគុណ។"
            ];
            header("Location: /elms/overtimein");
            exit();

        } catch (Exception $e) {
            // Rollback transaction in case of any error
            $lateModel->rollBackTransaction(); // Assuming you have a method for rollback

            // Log the error for debugging purposes
            error_log("Error: " . $e->getMessage());

            // Show error message to the user
            $_SESSION['error'] = [
                'title' => "សំណើចូលយឺត",
                'message' => "មានកំហុសមួយបានកើតឡើង: " . $e->getMessage()
            ];

            header("Location: /elms/overtimein");
            exit();
        }
    }

    public function editLateIn()
    {
        // Get POST data (from form submission)
        $lateId = $_POST['lateId'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $reason = $_POST['reason'];
        $userId = $_SESSION['user_id'];

        // Calculate late minutes (assuming work starts at 9:00 AM)
        $workStartTime = DateTime::createFromFormat('H:i', '09:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time . ":00");
        $lateMinutes = 0;

        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i; // Total late minutes
        }

        try {
            // Initialize LateModel and begin transaction
            $lateModel = new LateModel();
            $lateModel->beginTransaction();

            // Update the late-in request
            $updateSuccess = $lateModel->updateLateIn($lateId, $userId, $date, $time, $lateMinutes, $reason);

            if ($updateSuccess) {
                // Commit transaction
                $lateModel->commitTransaction();

                // Send notifications or emails here (if needed)

                // Success message
                $_SESSION['success'] = [
                    'title' => "សំណើចូលយឺត",
                    'message' => "សំណើបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ។"
                ];
            } else {
                throw new Exception("Failed to update the late-in request.");
            }

            // Redirect back to the list or summary page
            header("Location: /elms/overtimein");
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $lateModel->rollBackTransaction();

            // Log error for debugging
            error_log("Error updating late-in: " . $e->getMessage());

            // Error message
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "មានបញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាពសំណើ: " . $e->getMessage()
            ];

            // Redirect back to the form
            header("Location: /elms/overtimein");
            exit();
        }
    }

    public function editLateOut()
    {
        // Get POST data (from form submission)
        $lateId = $_POST['lateId'] ?? null;
        $date = $_POST['date'] ?? null;
        $time = $_POST['time'] ?? null;
        $reason = $_POST['reason'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Validate input data
        if (is_null($lateId) || is_null($date) || is_null($time) || is_null($reason) || is_null($userId)) {
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "ទិន្នន័យទាំងអស់ត្រូវបានចាំបាច់។"
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        $time = date("H:i:s", strtotime($time));

        $workEndTime = DateTime::createFromFormat('H:i', '17:30');
        $submittedTimeForOvertime = DateTime::createFromFormat('H:i:s', $time);

        if ($submittedTimeForOvertime > $workEndTime) {
            $intervalOvertime = $workEndTime->diff($submittedTimeForOvertime);
            $lateMinutes = $intervalOvertime->h * 60 + $intervalOvertime->i;
        } else {
            $lateMinutes = 0;
        }

        try {
            // Initialize LateModel and begin transaction
            $lateModel = new LateModel();
            $lateModel->beginTransaction();

            // Update the late-out request
            $updateSuccess = $lateModel->updateLateOut($lateId, $userId, $date, $time, $lateMinutes, $reason);

            if ($updateSuccess) {
                // Commit transaction
                $lateModel->commitTransaction();

                // Success message
                $_SESSION['success'] = [
                    'title' => "សំណើចូលយឺត",
                    'message' => "សំណើបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ។"
                ];
            } else {
                throw new Exception("Failed to update the late-in request.");
            }

            // Redirect back to the list or summary page
            header("Location: /elms/overtimeout");
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $lateModel->rollBackTransaction();

            // Log error for debugging
            error_log("Error updating late-in: " . $e->getMessage());

            // Error message
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "មានបញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាពសំណើ: " . $e->getMessage()
            ];

            // Redirect back to the form
            header("Location: /elms/overtimeout");
            exit();
        }
    }

    public function editLeaveEarly()
    {
        // Get POST data (from form submission)
        $lateId = $_POST['lateId'] ?? null;
        $date = $_POST['date'] ?? null;
        $time = $_POST['time'] ?? null;
        $reason = $_POST['reason'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Validate input data
        if (is_null($lateId) || is_null($date) || is_null($time) || is_null($reason) || is_null($userId)) {
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "ទិន្នន័យទាំងអស់ត្រូវបានចាំបាច់។"
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        $time24 = date("H:i:s", strtotime($time));

        // Define the reference time (16:00 or 4:00 PM) in 24-hour format
        $referenceTime24 = '16:00:00';

        // Create DateTime objects for comparison
        $referenceDateTime = DateTime::createFromFormat('H:i:s', $referenceTime24);
        $actualLeaveDateTime = DateTime::createFromFormat('H:i:s', $time24);

        // Calculate how many minutes before the reference time the employee left
        if ($actualLeaveDateTime < $referenceDateTime) {
            $interval = $referenceDateTime->diff($actualLeaveDateTime);
            $lateMinutes = ($interval->h * 60) + $interval->i;
        } else {
            $lateMinutes = 0;
        }

        try {
            // Initialize LateModel and begin transaction
            $lateModel = new LateModel();
            $lateModel->beginTransaction();

            // Update the late-out request
            $updateSuccess = $lateModel->updateLeaveEarly($lateId, $userId, $date, $time, $lateMinutes, $reason);

            if ($updateSuccess) {
                // Commit transaction
                $lateModel->commitTransaction();

                // Success message
                $_SESSION['success'] = [
                    'title' => "សំណើចូលយឺត",
                    'message' => "សំណើបានធ្វើបច្ចុប្បន្នភាពដោយជោគជ័យ។"
                ];
            } else {
                throw new Exception("Failed to update the late-in request.");
            }

            // Redirect back to the list or summary page
            header("Location: /elms/leaveearly");
            exit();

        } catch (Exception $e) {
            // Rollback transaction on error
            $lateModel->rollBackTransaction();

            // Log error for debugging
            error_log("Error updating late-in: " . $e->getMessage());

            // Error message
            $_SESSION['error'] = [
                'title' => "កំហុស",
                'message' => "មានបញ្ហាក្នុងការធ្វើបច្ចុប្បន្នភាពសំណើ: " . $e->getMessage()
            ];

            // Redirect back to the form
            header("Location: /elms/leaveearly");
            exit();
        }
    }

    public function createLateOut($date, $time, $reason)
    {
        // Validate and sanitize inputs
        $date = trim($date);
        $time = trim($time);
        $reason = trim($reason);
        $userId = $_SESSION['user_id'];
        $title = "សំណើចេញយឺត";
        $type = "lateout";

        // Check if date field is empty
        if (empty($date)) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "Date field is required."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // Check if time field is empty
        if (empty($time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "Time field is required."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // Check if reason field is empty
        if (empty($reason)) {
            $_SESSION['error'] = [
                'title' => "Reason Error",
                'message' => "Reason field is required."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // Check if date is valid
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "Invalid date format. Use YYYY-MM-DD."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // Validate time format (HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "Invalid time format. Use HH:MM."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // Append seconds to time if necessary
        $time .= ":00";

        // Convert to 24-hour format
        $time = date("H:i:s", strtotime($time));

        // Calculate late minutes
        $workStartTime = DateTime::createFromFormat('H:i', '17:30');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time);
        $lateMinutes = ($submittedTime > $workStartTime) ? $workStartTime->diff($submittedTime)->h * 60 + $workStartTime->diff($submittedTime)->i : 0;

        // Usage in your code
        $userModel = new User();
        // Fetch the admin emails
        $adminEmails = $userModel->getAdminEmails($_SESSION['token']);
        // Fetch the user's name using the user ID and session token
        $userInfo = $userModel->getUserByIdApi($userId, $_SESSION['token']);
        $userNameKh = $userInfo['data']['lastNameKh'] . ' ' . $userInfo['data']['firstNameKh'];

        if (!$adminEmails || $adminEmails['http_code'] !== 200 || empty($adminEmails['emails'])) {
            error_log("API Response: " . print_r($adminEmails, true));
            $_SESSION['error'] = [
                'title' => "Office Error",
                'message' => "Unable to find office details. Please contact support."
            ];
            header("Location: /elms/apply-leave");
            exit();
        }

        // Extract the first admin email (or handle multiple emails as needed)
        $adminEmail = $adminEmails['emails'][0];
        $adminName = $adminEmails['lastNameKh'][0] . ' ' . $adminEmails['firstNameKh'][0];
        $adminId = $adminEmails['ids'][0];

        // Attempt to send Telegram notification
        if (!$userModel->sendTelegramNotificationToAdmin($adminId, $userNameKh, $lateMinutes, $date, $time, $reason)) {
            $_SESSION['error'] = [
                'title' => "Telegram Notification Error",
                'message' => "Could not send Telegram notification. Please check your settings or contact support."
            ];
        }

        // Calculate overtime (if applicable)
        $workEndTime = DateTime::createFromFormat('H:i', '17:30');
        $submittedTimeForOvertime = DateTime::createFromFormat('H:i:s', $time);
        $lateMinutes = ($submittedTimeForOvertime > $workEndTime) ? $workEndTime->diff($submittedTimeForOvertime)->h * 60 + $workEndTime->diff($submittedTimeForOvertime)->i : 0;

        // Sanitize reason
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {
            // Send notification email
            if (!$this->sendLateOutEmail($userNameKh, $adminEmail, $date, $time, $lateMinutes, $reason, $title)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again."
                ];
            }

            $lateModel = new LateModel();
            $lateModel->applyLateOut($date, $type, $time, $lateMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "លិខិតចេញយឺត",
                'message' => "អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . $adminName . " សូមមេត្តារង់ចាំ។ សូមអរគុណ។"
            ];
            header("Location: /elms/overtimeout");
            exit();
        } catch (Exception $e) {
            // Handle errors gracefully
            $_SESSION['error'] = [
                'title' => "Database Error",
                'message' => "An error occurred while creating the late out request: " . $e->getMessage()
            ];
        }

        header("Location: /elms/overtimeout");
        exit();
    }

    public function createLeaveEarly($date, $time, $reason)
    {
        // Validate and sanitize inputs
        $date = trim($date);
        $time = trim($time);
        $reason = trim($reason);
        $userId = $_SESSION['user_id'];
        $title = "សំណើចេញមុន";
        $type = "leaveearly";

        // Check if date field is empty
        if (empty($date)) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "Date field is required."
            ];
            header("Location: /elms/leaveearly");
            exit();
        }

        // Check if time field is empty
        if (empty($time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "Time field is required."
            ];
            header("Location: /elms/leaveearly");
            exit();
        }

        // Check if reason field is empty
        if (empty($reason)) {
            $_SESSION['error'] = [
                'title' => "Reason Error",
                'message' => "Reason field is required."
            ];
            header("Location: /elms/leaveearly");
            exit();
        }

        // Check if date is valid
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "Invalid date format. Use YYYY-MM-DD."
            ];
            header("Location: /elms/leaveearly");
            exit();
        }

        // ស្ទួនពិនិត្យម៉ោងត្រឹមត្រូវ (ឧ. ទ្រង់ទ្រាយ HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ទ្រង់ទ្រាយម៉ោងមិនត្រឹមត្រូវ។ ប្រើ HH:MM."
            ];
            header("Location: /elms/leaveearly");
            exit();
        }

        // Add seconds to the time if not present
        if (strlen($time) === 5) {
            $time .= ":00";
        }

        // Convert the provided time to 24-hour format
        $time24 = date("H:i:s", strtotime($time));

        // Define the reference time (16:00 or 4:00 PM) in 24-hour format
        $referenceTime24 = '16:00:00';

        // Create DateTime objects for comparison
        $referenceDateTime = DateTime::createFromFormat('H:i:s', $referenceTime24);
        $actualLeaveDateTime = DateTime::createFromFormat('H:i:s', $time24);

        // Calculate how many minutes before the reference time the employee left
        if ($actualLeaveDateTime < $referenceDateTime) {
            $interval = $referenceDateTime->diff($actualLeaveDateTime);
            $lateMinutes = ($interval->h * 60) + $interval->i;
        } else {
            $lateMinutes = 0;
        }

        // Usage in your code
        $userModel = new User();
        // Fetch the admin emails
        $adminEmails = $userModel->getAdminEmails($_SESSION['token']);

        $userInfo = $userModel->getUserByIdApi($userId, $_SESSION['token']);
        $userNameKh = $userInfo['data']['lastNameKh'] . ' ' . $userInfo['data']['firstNameKh'];


        if (!$adminEmails || $adminEmails['http_code'] !== 200 || empty($adminEmails['emails'])) {
            error_log("API Response: " . print_r($adminEmails, true));
            $_SESSION['error'] = [
                'title' => "Office Error",
                'message' => "Unable to find office details. Please contact support."
            ];
            header("Location: /elms/apply-leave");
            exit();
        }

        // Extract the first admin email (or handle multiple emails as needed)
        $adminEmail = $adminEmails['emails'][0]; // Assuming you want the first email
        $adminName = $adminEmails['lastNameKh'][0] . ' ' . $adminEmails['firstNameKh'][0];
        $adminId = $adminEmails['ids'][0];

        // Get the admin's Telegram ID
        $telegramUser = $userModel->getTelegramIdByUserId($adminId);
        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            // Log the Telegram ID for debugging
            error_log("Found telegram_id: " . $telegramUser['telegram_id']);

            $notifications = [
                "🔔 *សំណើចេញមុន*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ: *`{$userNameKh}`",
                "⏰ *ចេញមុន:  *`{$lateMinutes} នាទី`",
                "🗓️ *កាលបរិច្ឆេទ:   *`{$date}`",
                "🕒 *ម៉ោង:    *`{$time}`",
                "💬 *មូលហេតុ:  *`{$reason}`",
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Step 4: Create the inline keyboard with a single "View the Request" button
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ពិនិត្យមើលសំណើ', 'url' => 'https://leave.iauoffsa.us/elms/leaveearly/'] // Using URL to open the request
                    ]
                ]
            ];

            // Send the Telegram notification
            $telegramModel = new TelegramModel($this->pdo);
            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage, $keyboard);

            // Check if the notification was successfully sent
            if (!$success) {
                error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramUser['telegram_id']);
                $_SESSION['error'] = [
                    'title' => "Telegram Notification Error",
                    'message' => "Could not send Telegram notification. Please check your settings or contact support."
                ];
            }
        } else {
            // Log the failure to find a valid telegram_id
            error_log("No valid telegram_id found for adminId: " . $adminId);
            $_SESSION['error'] = [
                'title' => "Telegram Notification Error",
                'message' => "No Telegram ID associated with the admin. Notification could not be sent."
            ];
        }
        // Sanitize reason
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {

            if (!$this->sendLateEarlyEmail($userNameKh, $adminEmail, $date, $time, $lateMinutes, $reason, $title)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again.$adminEmail"
                ];
            }

            $lateModel = new LateModel();
            $lateModel->applyLeaveEarly($date, $type, $time, $lateMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "លិខិតចេញយឺត",
                'message' => "អ្នកបានចេញមុនចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . $adminName . " សូមមេត្តារង់ចាំ។ សូមអរគុណ។"
            ];
            header("Location: /elms/leaveearly");
            exit();
        } catch (Exception $e) {
            // Handle errors gracefully
            $_SESSION['error'] = [
                'title' => "Database Error",
                'message' => "An error occurred while creating the late out request: " . $e->getMessage()
            ];
        }

        header("Location: /elms/leaveearly");
        exit();
    }

    private function sendLateInEmail($userName, $adminEmail, $date, $time, $lateMinutes, $reason, $title)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');

            // Ensure $adminEmail is a string, not an array
            if (is_array($adminEmail)) {
                $adminEmail = implode(', ', $adminEmail); // Convert array to comma-separated string
            }

            $mail->addAddress($adminEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
        <html>
        <head>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
            <style>
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .header {
                    background-color: #17a2b8;
                    color: white;
                    padding: 15px;
                    border-radius: 15px 15px 0 0;
                    text-align: center;
                }
                .header img {
                    max-height: 50px;
                    margin-bottom: 10px;
                }
                .header h4 {
                    margin: 0;
                    font-size: 1.5rem;
                    font-family: khmer mef2;
                }
                .content {
                    padding: 20px;
                    background-color: #f4f7f6;
                    color: #333;
                }
                .content p {
                    font-size: 1rem;
                    margin-bottom: 10px;
                }
                .content .details {
                    background-color: #e9ecef;
                    padding: 15px;
                    border-radius: 10px;
                    margin-bottom: 15px;
                }
                .details p {
                    margin: 0;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 30px;
                    color: #ffffff;
                    background-color: #28a745;
                    border-radius: 30px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
                .btn:hover {
                    background-color: #218838;
                }
                .footer {
                    padding: 10px;
                    background-color: #17a2b8;
                    color: white;
                    border-radius: 0 0 15px 15px;
                    text-align: center;
                }
                .footer p {
                    margin: 0;
                    font-size: 0.9rem;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://leave.iauoffsa.us/elms/public/img/icons/brands/logo2.png' alt='Logo'>
                    <h4>$title</h4>
                </div>
                <div class='content'>
                    <p>សូមគោរពជូនមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</p>
                    <div class='details'>
                        <p><strong>ឈ្មោះ:</strong> $userName</p>
                        <p><strong>កាលបរិចេ្ឆទ:</strong> $date</p>
                        <p><strong>ម៉ោងចូល:</strong> $time នាទី</p>
                        <p><strong>រយៈពេលយឺត:</strong> $lateMinutes នាទី</p>
                        <p><strong>មូលហេតុ:</strong> $reason</p>
                    </div>
                    <a href='https://leave.iauoffsa.us/elms/adminpending?action=latein' class='btn text-white'>ចុចទីនេះដើម្បីអនុម័ត</a>
                </div>
                <div class='footer'>
                    <p>&copy; <?= date('Y') ?> រក្សាសិទ្ធគ្រប់យ៉ាងដោយអង្គភាពសវនកម្មផ្ទៃក្នុង</p>
                </div>
            </div>
        </body>
        </html>
    ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $adminEmail");
                return true;
            } else {
                error_log("Email failed to send to $adminEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function sendLateOutEmail($userName, $adminEmail, $date, $time, $lateMinutes, $reason, $title)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');

            // Ensure $adminEmail is a string, not an array
            if (is_array($adminEmail)) {
                $adminEmail = implode(', ', $adminEmail); // Convert array to comma-separated string
            }

            $mail->addAddress($adminEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
        <html>
        <head>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
            <style>
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .header {
                    background-color: #17a2b8;
                    color: white;
                    padding: 15px;
                    border-radius: 15px 15px 0 0;
                    text-align: center;
                }
                .header img {
                    max-height: 50px;
                    margin-bottom: 10px;
                }
                .header h4 {
                    margin: 0;
                    font-size: 1.5rem;
                }
                .content {
                    padding: 20px;
                    background-color: #f4f7f6;
                    color: #333;
                }
                .content p {
                    font-size: 1rem;
                    margin-bottom: 10px;
                }
                .content .details {
                    background-color: #e9ecef;
                    padding: 15px;
                    border-radius: 10px;
                    margin-bottom: 15px;
                }
                .details p {
                    margin: 0;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 30px;
                    color: #ffffff;
                    background-color: #28a745;
                    border-radius: 30px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
                .btn:hover {
                    background-color: #218838;
                }
                .footer {
                    padding: 10px;
                    background-color: #17a2b8;
                    color: white;
                    border-radius: 0 0 15px 15px;
                    text-align: center;
                }
                .footer p {
                    margin: 0;
                    font-size: 0.9rem;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://leave.iauoffsa.us/elms/public/img/icons/brands/logo2.png' alt='Logo'>
                    <h4>$title</h4>
                </div>
                <div class='content'>
                    <p>សូមគោរពជូនមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</p>
                    <div class='details'>
                        <p><strong>ឈ្មោះ:</strong> $userName</p>
                        <p><strong>កាលបរិចេ្ឆទ:</strong> $date</p>
                        <p><strong>ម៉ោងចូល:</strong> $time នាទី</p>
                        <p><strong>រយៈពេលយឺត:</strong> $lateMinutes នាទី</p>
                        <p><strong>មូលហេតុ:</strong> $reason</p>
                    </div>
                    <a href='https://leave.iauoffsa.us/elms/adminpending?action=lateout' class='btn text-white'>ចុចទីនេះដើម្បីអនុម័ត</a>
                </div>
                <div class='footer'>
                    <p>&copy; <?= date('Y') ?> រក្សាសិទ្ធគ្រប់យ៉ាងដោយអង្គភាពសវនកម្មផ្ទៃក្នុង</p>
                </div>
            </div>
        </body>
        </html>
    ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $adminEmail");
                return true;
            } else {
                error_log("Email failed to send to $adminEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function sendLateEarlyEmail($userName, $adminEmail, $date, $time, $lateMinutes, $reason, $title)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');

            // Ensure $adminEmail is a string, not an array
            if (is_array($adminEmail)) {
                $adminEmail = implode(', ', $adminEmail); // Convert array to comma-separated string
            }

            $mail->addAddress($adminEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
        <html>
        <head>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
            <style>
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .header {
                    background-color: #17a2b8;
                    color: white;
                    padding: 15px;
                    border-radius: 15px 15px 0 0;
                    text-align: center;
                }
                .header img {
                    max-height: 50px;
                    margin-bottom: 10px;
                }
                .header h4 {
                    margin: 0;
                    font-size: 1.5rem;
                }
                .content {
                    padding: 20px;
                    background-color: #f4f7f6;
                    color: #333;
                }
                .content p {
                    font-size: 1rem;
                    margin-bottom: 10px;
                }
                .content .details {
                    background-color: #e9ecef;
                    padding: 15px;
                    border-radius: 10px;
                    margin-bottom: 15px;
                }
                .details p {
                    margin: 0;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 30px;
                    color: #ffffff;
                    background-color: #28a745;
                    border-radius: 10px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
                .btn:hover {
                    background-color: #218838;
                }
                .footer {
                    padding: 10px;
                    background-color: #17a2b8;
                    color: white;
                    border-radius: 0 0 15px 15px;
                    text-align: center;
                }
                .footer p {
                    margin: 0;
                    font-size: 0.9rem;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://leave.iauoffsa.us/elms/public/img/icons/brands/logo2.png' alt='Logo'>
                    <h4>$title</h4>
                </div>
                <div class='content'>
                    <p>សូមគោរពជូនមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</p>
                    <div class='details'>
                        <p><strong>ឈ្មោះ:</strong> $userName</p>
                        <p><strong>កាលបរិចេ្ឆទ:</strong> $date</p>
                        <p><strong>ម៉ោងចូល:</strong> $time នាទី</p>
                        <p><strong>រយៈពេលយឺត:</strong> $lateMinutes នាទី</p>
                        <p><strong>មូលហេតុ:</strong> $reason</p>
                    </div>
                    <a href='https://leave.iauoffsa.us/elms/adminpending?action=lateearly' class='btn text-white'>ចុចទីនេះដើម្បីអនុម័ត</a>
                </div>
                <div class='footer'>
                    <p>&copy; <?= date('Y') ?> រក្សាសិទ្ធគ្រប់យ៉ាងដោយអង្គភាពសវនកម្មផ្ទៃក្នុង</p>
                </div>
            </div>
        </body>
        </html>
    ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $adminEmail");
                return true;
            } else {
                error_log("Email failed to send to $adminEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function sendEmailNotification($userName, $adminEmail, $date, $time, $lateMinutes, $reason)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');

            // Ensure $adminEmail is a string, not an array
            if (is_array($adminEmail)) {
                $adminEmail = implode(', ', $adminEmail); // Convert array to comma-separated string
            }

            $mail->addAddress($adminEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
        <html>
        <head>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
            <style>
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: none;
                    border-radius: 15px;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
                    background-color: #ffffff;
                }
                .header {
                    background-color: #17a2b8;
                    color: white;
                    padding: 15px;
                    border-radius: 15px 15px 0 0;
                    text-align: center;
                }
                .header img {
                    max-height: 50px;
                    margin-bottom: 10px;
                }
                .header h4 {
                    margin: 0;
                    font-size: 1.5rem;
                }
                .content {
                    padding: 20px;
                    background-color: #f4f7f6;
                    color: #333;
                }
                .content p {
                    font-size: 1rem;
                    margin-bottom: 10px;
                }
                .content .details {
                    background-color: #e9ecef;
                    padding: 15px;
                    border-radius: 10px;
                    margin-bottom: 15px;
                }
                .details p {
                    margin: 0;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 30px;
                    color: #ffffff;
                    background-color: #28a745;
                    border-radius: 30px;
                    text-decoration: none;
                    font-weight: bold;
                    margin-top: 20px;
                    transition: background-color 0.3s ease;
                }
                .btn:hover {
                    background-color: #218838;
                }
                .footer {
                    padding: 10px;
                    background-color: #17a2b8;
                    color: white;
                    border-radius: 0 0 15px 15px;
                    text-align: center;
                }
                .footer p {
                    margin: 0;
                    font-size: 0.9rem;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <img src='https://leave.iauoffsa.us/elms/public/img/icons/brands/logo2.png' alt='Logo'>
                    <h4>សំណើចូលយឺត</h4>
                </div>
                <div class='content'>
                    <p>សូមគោរពជូនមន្ត្រីទទួលបន្ទុកគ្រប់គ្រងវត្តមាន</p>
                    <div class='details'>
                        <p><strong>ឈ្មោះ:</strong> $userName</p>
                        <p><strong>កាលបរិចេ្ឆទ:</strong> $date</p>
                        <p><strong>ម៉ោងចូល:</strong> $time នាទី</p>
                        <p><strong>រយៈពេលយឺត:</strong> $lateMinutes នាទី</p>
                        <p><strong>មូលហេតុ:</strong> $reason</p>
                    </div>
                    <a href='https://leave.iauoffsa.us/elms/view-leave-detail?leave_id={}' class='btn'>ចុចទីនេះដើម្បីអនុម័ត</a>
                </div>
                <div class='footer'>
                    <p>&copy; <?= date('Y') ?> Leave Management System. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
    ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $adminEmail");
                return true;
            } else {
                error_log("Email failed to send to $adminEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
