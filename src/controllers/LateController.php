<?php
require_once 'src/models/LateModel.php';

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
        $getovertimein = $lateModel->getOvertimein($_SESSION['user_id']);

        require 'src/views/documentlate/overtimein.php';
    }

    public function overtimeout()
    {
        $lateModel = new LateModel();
        $getovertimeout = $lateModel->getOvertimeOut($_SESSION['user_id']);

        require 'src/views/documentlate/overtimeout.php';
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
    public function delete($id)
    {
        global $pdo;
        $lateModel = new LateModel($pdo);
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
        global $pdo;

        // Validate and sanitize inputs
        $date = trim($date);
        $time = trim($time);
        $reason = trim($reason);

        // ស្ទួនសារបញ្ចូលចូល
        if (empty($date)) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "ចន្លោះកាលបរិច្ឆេទត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        if (empty($time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ចន្លោះម៉ោងត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        if (empty($reason)) {
            $_SESSION['error'] = [
                'title' => "Reason Error",
                'message' => "ចន្លោះមូលហេតុត្រូវបានទាមទារ។"
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        // ស្ទួនពិនិត្យកាលបរិច្ឆេទត្រឹមត្រូវ
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "ទ្រង់ទ្រាយកាលបរិច្ឆេទមិនត្រឹមត្រូវ។ ប្រើ Y-m-d."
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        // ស្ទួនពិនិត្យម៉ោងត្រឹមត្រូវ (ឧ. ទ្រង់ទ្រាយ HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ទ្រង់ទ្រាយម៉ោងមិនត្រឹមត្រូវ។ ប្រើ HH:MM."
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        // បន្ថែមវិនាទីទៅម៉ោងប្រសិនបើចាំបាច់
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ":00";
        }

        // គណនាម៉ោងយឺត
        $workStartTime = DateTime::createFromFormat('H:i', '09:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time);

        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i;
        } else {
            $lateMinutes = 0;
        }

        // បញ្ចូលមូលហេតុ
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {
            $lateModel = new LateModel($pdo);
            $lateModel->applyLateIn($date, $time, $lateMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "សំណើចូលយឺត",
                'message' => "សំណើចូលយឺតបានបង្កើតដោយជោគជ័យ។ អ្នកបានយឺតចំនួន {$lateMinutes} នាទី។"
            ];
        } catch (Exception $e) {
            // គ្រប់គ្រងកំហុសដោយសុវត្ថិភាព
            $_SESSION['error'] = [
                'title' => "Database Error",
                'message' => "មានកំហុសមួយបានកើតឡើងក្នុងការបង្កើតសំណើចូលយឺត៖ " . $e->getMessage()
            ];
        }

        header("Location: /elms/overtimein");
        exit();
    }

    public function createLateOut($date, $time, $reason)
    {
        global $pdo;

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
            header("Location: /elms/late_out_request");
            exit();
        }

        // Check if time field is empty
        if (empty($time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "Time field is required."
            ];
            header("Location: /elms/late_out_request");
            exit();
        }

        // Check if reason field is empty
        if (empty($reason)) {
            $_SESSION['error'] = [
                'title' => "Reason Error",
                'message' => "Reason field is required."
            ];
            header("Location: /elms/late_out_request");
            exit();
        }

        // Check if date is valid
        $dateObj = DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $_SESSION['error'] = [
                'title' => "Date Error",
                'message' => "Invalid date format. Use YYYY-MM-DD."
            ];
            header("Location: /elms/late_out_request");
            exit();
        }

        // ស្ទួនពិនិត្យម៉ោងត្រឹមត្រូវ (ឧ. ទ្រង់ទ្រាយ HH:MM)
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
            $_SESSION['error'] = [
                'title' => "Time Error",
                'message' => "ទ្រង់ទ្រាយម៉ោងមិនត្រឹមត្រូវ។ ប្រើ HH:MM."
            ];
            header("Location: /elms/late_in_request");
            exit();
        }

        // បន្ថែមវិនាទីទៅម៉ោងប្រសិនបើចាំបាច់
        if (preg_match('/^\d{2}:\d{2}$/', $time)) {
            $time .= ":00";
        }

        // Convert 12-hour time format to 24-hour format
        $time24 = date("H:i:s", strtotime($time));

        // Calculate late minutes
        $workStartTime = DateTime::createFromFormat('H:i', '05:00');
        $submittedTime = DateTime::createFromFormat('H:i:s', $time24);

        if ($submittedTime > $workStartTime) {
            $interval = $workStartTime->diff($submittedTime);
            $lateMinutes = $interval->h * 60 + $interval->i;
        } else {
            $lateMinutes = 0;
        }

        // Calculate overtime
        $workEndTime = DateTime::createFromFormat('H:i', '17:30');
        $submittedTimeForOvertime = DateTime::createFromFormat('H:i:s', $time24);

        if ($submittedTimeForOvertime > $workEndTime) {
            $intervalOvertime = $workEndTime->diff($submittedTimeForOvertime);
            $overtimeoutMinutes = $intervalOvertime->h * 60 + $intervalOvertime->i;
        } else {
            $overtimeoutMinutes = 0;
        }

        // Sanitize reason
        $reason = htmlspecialchars($reason, ENT_QUOTES, 'UTF-8');

        try {
            $lateModel = new LateModel($pdo);
            $lateModel->applyLateOut($date, $time24, $overtimeoutMinutes, $reason);

            $_SESSION['success'] = [
                'title' => "លិខិតចេញយឺត",
                'message' => "បង្កើតសំណើបានជោគជ័យ។ អ្នកបានយឺតចំនួន $overtimeoutMinutes នាទី។"
            ];
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
}
