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
}
