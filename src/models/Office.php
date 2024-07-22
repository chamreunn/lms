<?php
require_once 'config/database.php';
class Office
{
    private $pdo;
    private $table_name = "offices";

    public $id;
    public $name;
    public $department_id;
    public $hoffice_id;
    public $doffice_id;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function read()
    {
        $query = "SELECT 
                o.*, 
                d.name as department_name, 
                u1.khmer_name as head_khmer_name, 
                u1.id as head_id, 
                u2.khmer_name as deputy_head_khmer_name, 
                u2.id as deputy_head_id 
              FROM " . $this->table_name . " o
              LEFT JOIN departments d ON o.department_id = d.id
              LEFT JOIN users u1 ON o.hoffice_id = u1.id
              LEFT JOIN users u2 ON o.doffice_id = u2.id
              ORDER BY o.created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (name, department_id, created_at, updated_at) VALUES (:name, :did, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->department_id = htmlspecialchars(strip_tags($this->department_id));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':did', $this->department_id);
        $stmt->bindParam(':created_at', $this->created_at);
        $stmt->bindParam(':updated_at', $this->updated_at);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function exists($name)
    {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE name = :name";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function update()
    {
        $query = "UPDATE " . $this->table_name . " 
              SET name=:name, department_id=:did, hoffice_id=:hoid, doffice_id=:dhoid, updated_at=:updated_at 
              WHERE id=:id";
        $stmt = $this->pdo->prepare($query);

        // Bind parameters with unique names
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":did", $this->department_id);
        $stmt->bindParam(":hoid", $this->hoffice_id);
        $stmt->bindParam(":dhoid", $this->doffice_id); // Use :dhoid instead of :did
        $stmt->bindParam(":updated_at", $this->updated_at);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":id", $this->id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
