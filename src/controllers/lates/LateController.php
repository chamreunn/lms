<?php
require_once 'src/models/lates/LateModel.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LateController
{
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
        $getleaveearly = $lateModel->getLeaveEarly($_SESSION['user_id']);

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

        // Validate date format
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "ទ្រង់ទ្រាយកាលបរិច្ឆេទមិនត្រឹមត្រូវ។ ប្រើ Y-m-d."
            ];
            header("Location: /elms/overtimein");
            exit();
        }

        // Validate time format
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
            $time .= ":00";
        }

        // Calculate late minutes
        $workStartTime = DateTime::createFromFormat('H:i', '09:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time);

        $lateMinutes = 0;
        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i;
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

            if (!$adminEmails || $adminEmails['http_code'] !== 200 || empty($adminEmails['emails'])) {
                throw new Exception("Unable to fetch admin emails.");
            }

            // Use the first admin email or handle multiple emails as needed
            $adminEmail = $adminEmails['emails'][0];

            // Send email notification
            if (!$this->sendEmailNotification($adminEmail, $date, $time, $lateMinutes, $reason)) {
                throw new Exception("Notification email could not be sent.");
            }

            // Apply the late-in request to the database
            $lateModel->applyLateIn($date, $time, $lateMinutes, $reason);

            // Commit transaction after success
            $lateModel->commitTransaction();

            // Optionally display admin email in success message
            $_SESSION['success'] = [
                'title' => "សំណើចូលយឺត",
                'message' => "អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . ($adminEmail ?? "") . ". សូមអរគុណ។"
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

    public function createLateOut($date, $time, $reason)
    {
        // Validate and sanitize inputs
        $date = trim($date);
        $time = trim($time);
        $reason = trim($reason);

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

        // ស្ទួនពិនិត្យម៉ោងត្រឹមត្រូវ (ឧ. ទ្រង់ទ្រាយ HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ទ្រង់ទ្រាយម៉ោងមិនត្រឹមត្រូវ។ ប្រើ HH:MM."
            ];
            header("Location: /elms/overtimeout");
            exit();
        }

        // បន្ថែមវិនាទីទៅម៉ោងប្រសិនបើចាំបាច់
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ":00";
        }

        // Convert 12-hour time format to 24-hour format
        $time = date("H:i:s", strtotime($time));

        // Calculate late minutes
        $workStartTime = DateTime::createFromFormat('H:i', '05:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time);

        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i;
        } else {
            $lateMinutes = 0;
        }

        // Usage in your code
        $userModel = new User();
        // Fetch the admin emails
        $adminEmails = $userModel->getAdminEmails($_SESSION['token']);

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

        // Calculate overtime
        $workEndTime = DateTime::createFromFormat('H:i', '17:30');
        $submittedTimeForOvertime = DateTime::createFromFormat('H:i:s', $time);

        if ($submittedTimeForOvertime > $workEndTime) {
            $intervalOvertime = $workEndTime->diff($submittedTimeForOvertime);
            $lateMinutes = $intervalOvertime->h * 60 + $intervalOvertime->i;
        } else {
            $lateMinutes = 0;
        }

        // Sanitize reason
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {

            if (!$this->sendEmailNotification($adminEmail, $date, $time, $lateMinutes, $reason)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again.$adminEmail"
                ];
            }

            $lateModel = new LateModel();
            $lateModel->applyLateOut($date, $time, $lateMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "លិខិតចេញយឺត",
                'message' => "អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . $adminEmail . " សូមមេត្តារង់ចាំ។ សូមអរគុណ។"
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
        // Sanitize reason
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {

            if (!$this->sendEmailNotification($adminEmail, $date, $time, $lateMinutes, $reason)) {
                $_SESSION['error'] = [
                    'title' => "Email Error",
                    'message' => "Notification email could not be sent. Please try again.$adminEmail"
                ];
            }

            $lateModel = new LateModel();
            $lateModel->applyLeaveEarly($date, $time, $lateMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "លិខិតចេញយឺត",
                'message' => "អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។ សំណើបានបញ្ជូនទៅកាន់ " . $adminEmail . " សូមមេត្តារង់ចាំ។ សូមអរគុណ។"
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

    private function sendEmailNotification($adminEmail, $date, $time, $lateMinutes, $reason)
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
            <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
            <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
            <style>
                .profile-img {
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    padding: 20px;
                    border: 1px solid #e2e2e2;
                    border-radius: 10px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                }
                .header {
                    background-color: #007bff;
                    color: white;
                    padding: 10px;
                    border-radius: 10px 10px 0 0;
                }
                .icon {
                    vertical-align: middle;
                    margin-right: 10px;
                }
                .content {
                    padding: 20px;
                    background-color: #f9f9f9;
                }
                .btn {
                    display: inline-block;
                    padding: 10px 20px;
                    margin-top: 10px;
                    color: white;
                    background-color: #007bff;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .footer {
                    padding: 10px;
                    text-align: center;
                    background-color: #f1f1f1;
                    border-radius: 0 0 10px 10px;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h4><img src='http://localhost/elms/public/img/icons/brands/logo2.png' class='icon' alt='Leave Request' /> Leave Request Notification</h4>
                </div>
                <div class='content'>
                    <p>$reason</p>
                    <p><strong>រយៈពេល :</strong> $lateMinutes </p>
                    <p><strong>ចាប់ពីថ្ងៃ :</strong> $date</p>
                    <p><strong>មូលហេតុ :</strong> $reason</p>
                    <a href='http://localhost/elms/view-leave-detail?leave_id={}' class='btn'>ចុចទីនេះ</a>
                </div>
                <div class='footer'>
                    <p>&copy; " . date("Y") . " Leave Management System. All rights reserved.</p>
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
