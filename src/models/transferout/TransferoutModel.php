<?php

class TransferoutModel
{
    protected $tblholds = 'holds'; // The table where data is stored

    protected $tblholds_approvals = 'holds_approvals';

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function createTransferout($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tblholds (user_id, approver_id, start_date, end_date, reason, attachment, duration, type, color, created_at) 
            VALUES (:user_id, :approver_id, :start_date, :end_date, :reason, :attachment, :duration, :type, :color, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt->bindParam(':attachment', $data['attachment'], PDO::PARAM_STR);
        $stmt->bindParam(':duration', $data['duration'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }
}
