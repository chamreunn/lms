<?php
require_once 'config/database.php';
class Leavetype
{
    private $pdo;
    private $table_name = "leave_types";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllLeavetypes()
    {
        // Query to fetch leave types
        $query = "SELECT id, name, color, duration, description, attachment_required, created_at, updated_at 
              FROM $this->table_name";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $leaveTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Process each leave type and add a message if the document is required
        foreach ($leaveTypes as &$leaveType) {
            if (strtolower($leaveType['attachment_required']) === 'yes') {
                $leaveType['document_status'] = '(ត្រូវមានឯកសារភ្ជាប់​​ )';
            } else {
                $leaveType['document_status'] = '';
            }
        }

        return $leaveTypes;
    }


    public function createLeavetype($data)
    {
        $query = "INSERT INTO $this->table_name (name, color, duration, description, attachment_required) VALUES (:name, :color, :duration, :description, :attachment_required)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':attachment_required', $data['attachment_required']);
        return $stmt->execute();
    }

    public function updateLeavetype($id, $data)
    {
        $query = "UPDATE $this->table_name SET name = :name, color = :color, duration = :duration, description = :description, attachment_required = :attachment_required WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':color', $data['color']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':attachment_required', $data['attachment_required']);
        return $stmt->execute();
    }

    public function getLeaveTypeById($leave_type_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leave_types WHERE id = ?');
        $stmt->execute([$leave_type_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteLeavetype($id)
    {
        $query = "DELETE FROM $this->table_name WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
