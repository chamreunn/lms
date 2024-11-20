<?php

class AttendanceModel
{
    public $pdo;

    protected $attendance = "attendances";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo; // Store the PDO instance
    }

    // Method to save the QR code to the database
    public function recordAttendance($userId, $date, $check, $isCheckIn = true)
    {
        // Check if an attendance record exists for the user on the given date
        $sql = "SELECT * FROM {$this->attendance} WHERE userId = :user_id AND date = :date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // Update the appropriate field based on the time of the action
            if ($isCheckIn) {
                $sql = "UPDATE {$this->attendance} SET checkIn = :checkIn WHERE userId = :user_id AND date = :date";
            } else {
                $sql = "UPDATE {$this->attendance} SET checkOut = :checkOut WHERE userId = :user_id AND date = :date";
            }
        } else {
            // Insert a new record
            $sql = "INSERT INTO {$this->attendance} (userId, date, checkIn, checkOut) 
                VALUES (:user_id, :date, :checkIn, :checkOut)";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':date', $date);

        if ($existingRecord) {
            // Update query parameters
            if ($isCheckIn) {
                $stmt->bindParam(':checkIn', $check);
            } else {
                $stmt->bindParam(':checkOut', $check);
            }
        } else {
            // Insert query parameters
            if ($isCheckIn) {
                $stmt->bindParam(':checkIn', $check);
                $stmt->bindValue(':checkOut', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':checkIn', null, PDO::PARAM_NULL);
                $stmt->bindParam(':checkOut', $check);
            }
        }

        return $stmt->execute();
    }
}