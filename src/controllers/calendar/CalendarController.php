<?php
require_once 'src/models/calendar/holidayModel.php';

class CalendarController
{
    public function index()
    {
        $calendarModel = new CalendarModel();
        $getHolidays = $calendarModel->getHoliday();

        require 'src/views/calendar/holiday.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $holidayName = $_POST['holidayName'] ?? '';
            $holidayDate = $_POST['holidayDate'] ?? '';
            $holidayType = $_POST['holidayType'] ?? '';
            $holidayDescription = $_POST['holidayDescription'] ?? '';
            $color = $_POST['color'] ?? 'white';

            // Validate required fields
            if (!empty($holidayName) && !empty($holidayDate) && !empty($holidayType)) {
                // Call model to create holiday
                $calendarModel = new CalendarModel();
                $success = $calendarModel->createHoliday($holidayName, $holidayDate, $holidayType, $holidayDescription, $color);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "បង្កើតបានជោគជ័យ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "បង្កើតបានជោគជ័យ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមវាយបញ្ចូលទិន្នន័យ។"
                ];
                header("Location: /elms/holidays");
                exit();
            }
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];
            $holidayName = $_POST['holidayName'] ?? '';
            $holidayDate = $_POST['holidayDate'] ?? '';
            $holidayType = $_POST['holidayType'] ?? '';
            $holidayDescription = $_POST['holidayDescription'] ?? '';
            $color = $_POST['color'] ?? 'white';

            // Validate required fields
            if (!empty($holidayName) && !empty($holidayDate) && !empty($holidayType)) {
                // Call model to create holiday
                $calendarModel = new CalendarModel();
                $success = $calendarModel->updateHoliday($id, $holidayName, $holidayDate, $holidayType, $holidayDescription, $color);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "បង្កើតបានជោគជ័យ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "បង្កើតបានជោគជ័យ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមវាយបញ្ចូលទិន្នន័យ។"
                ];
                header("Location: /elms/holidays");
                exit();
            }
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Collect form data
            $id = $_POST['id'];

            // Validate that the ID is provided
            if (!empty($id)) {
                // Call model to delete holiday
                $calendarModel = new CalendarModel();
                $success = $calendarModel->deleteHoliday($id);

                if ($success) {
                    // Redirect or show success message
                    $_SESSION['success'] = [
                        'title' => "ជោគជ័យ",
                        'message' => "លុបបានជោគជ័យ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                } else {
                    // Handle the error case
                    $_SESSION['error'] = [
                        'title' => "បរាជ័យ",
                        'message' => "មិនអាចលុបបានទេ។"
                    ];
                    header("Location: /elms/holidays");
                    exit();
                }
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "សូមផ្តល់ ID ថ្ងៃឈប់សម្រាក។"
                ];
                header("Location: /elms/holidays");
                exit();
            }
        }
    }

}
