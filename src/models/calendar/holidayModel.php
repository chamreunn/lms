<?php

class CalendarModel
{
    private $pdo;

    private $holiday = "holidays";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getHoliday()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->holiday ORDER BY id DESC");

        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getHolidayCDay()
    {
        $currentMonth = date('m');  // Get current month as a two-digit number (01-12)
        $currentYear = date('Y');   // Get the current year

        $stmt = $this->pdo->prepare("SELECT * FROM $this->holiday WHERE MONTH(holiday_date) = :month AND YEAR(holiday_date) = :year");

        $stmt->bindParam(':month', $currentMonth);
        $stmt->bindParam(':year', $currentYear);

        $stmt->execute();
        return $stmt->fetchAll();
    }


    public function createHoliday($holidayName, $holidayDate, $holidayType, $holidayDescription, $color)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("
            INSERT INTO $this->holiday (holiday_name, holiday_date, holiday_type, holiday_description, color, created_at) 
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $holidayName,
            $holidayDate,
            $holidayType,
            $holidayDescription,
            $color
        ]);

        // Return the ID of the newly created leave request
        return $this->pdo->lastInsertId();
    }

    public function updateHoliday($id, $holidayName, $holidayDate, $holidayType, $holidayDescription, $color)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("
        UPDATE $this->holiday 
        SET holiday_name = ?, 
            holiday_date = ?, 
            holiday_type = ?, 
            holiday_description = ?, 
            color = ?, 
            updated_at = NOW() 
        WHERE id = ?
    ");
        $stmt->execute([
            $holidayName,
            $holidayDate,
            $holidayType,
            $holidayDescription,
            $color,
            $id
        ]);

        // Return the number of affected rows (1 if successful, 0 if not)
        return $stmt->rowCount();
    }

    public function deleteHoliday($id)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("DELETE FROM $this->holiday WHERE id = ?");
        $stmt->execute([$id]);

        // Return the number of affected rows (1 if successful, 0 if not)
        return $stmt->rowCount();
    }

    public function getLeadersOnLeave()
    {
        $departmentName = $_SESSION['departmentName'];
        $today = date('Y-m-d');
        $leadersOnLeave = [];

        // Modify the query to include a JOIN with the leave_types table
        $stmt = $this->pdo->prepare("
            SELECT lr.*, lt.name 
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.department = ? 
            AND lr.start_date <= ? 
            AND lr.end_date >= ? 
            AND lr.status = 'Approved'
        ");

        // Execute the query with the necessary parameters
        $stmt->execute([$departmentName, $today, $today]);
        $leaveRequests = $stmt->fetchAll();

        // Loop through each leave request and fetch the leader data using the API
        foreach ($leaveRequests as $leaveRequest) {
            $userId = $leaveRequest['user_id'];
            $token = $_SESSION['token'];

            $userModel = new User();
            $leaderResponse = $userModel->getUserByIdApi($userId, $token);

            // Check if the API response is successful and contains the leader's data
            if ($leaderResponse['http_code'] === 200 && isset($leaderResponse['data'])) {
                // Include both the leader data and leave type in the response
                $leadersOnLeave[] = [
                    'leader' => $leaderResponse['data'], // Data of the leader from API
                    'leave_request' => $leaveRequest,    // Data of the leave request
                ];
            } else {
                // Handle any API errors or unexpected responses here, if needed
                error_log("Error fetching leader data for user ID: $userId. Error: " . $leaderResponse['error']);
            }
        }

        return $leadersOnLeave;
    }
}
