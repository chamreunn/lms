<?php

class HoldModel
{
    protected $tblholds = 'holds'; // The table where data is stored
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }

    /**
     * Insert a new hold request into the database
     */
    public function createHoldRequest($data)
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

        // Execute the statement and return the result
        return $stmt->execute();
    }

    public function getHoldsByUserId()
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return []; // Return an empty array if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the SQL query using PDO
        $sql = "SELECT * FROM $this->tblholds WHERE user_id = :user_id ORDER BY created_at DESC"; // Adjust the order as necessary

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch all the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHoldById($id)
    {
        // Prepare the SQL query to get a single hold by ID
        $sql = "SELECT * FROM $this->tblholds WHERE id = :id LIMIT 1"; // We limit the result to 1 row

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch a single result
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteHold($id)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("DELETE FROM $this->tblholds WHERE id = ?");
        $stmt->execute([$id]);

        // Return the number of affected rows (1 if successful, 0 if not)
        return $stmt->rowCount();
    }
}
