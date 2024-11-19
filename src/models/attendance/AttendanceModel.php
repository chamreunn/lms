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
    public function recordAttendance($userId, $date, $check)
    {
        $sql = "INSERT INTO {$this->attendance} (userId, date, checkIn) VALUES (:user_id, :date, :checkIn)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':checkIn', $check);
        return $stmt->execute();
    }
}