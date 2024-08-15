<?php
class Department
{
    private $pdo;
    private $table_name = "departments";

    public $id;
    public $name;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    public function read()
    {
        $query = "
            SELECT 
                d.*, 
                d.name AS department_name, 
                u1.khmer_name AS head_khmer_name, 
                u1.id AS head_id, 
                u2.khmer_name AS deputy_head_khmer_name, 
                u2.id AS deputy_head_id 
            FROM departments d
            LEFT JOIN users u1 ON d.hdepartment_id = u1.id
            LEFT JOIN users u2 ON d.ddepartment_id = u2.id
            ORDER BY d.created_at DESC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->table_name . " (name, description, created_at, updated_at) VALUES (:name, :description, :created_at, :updated_at)";
        $stmt = $this->pdo->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->created_at = htmlspecialchars(strip_tags($this->created_at));
        $this->updated_at = htmlspecialchars(strip_tags($this->updated_at));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':description', $this->description);
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
        $query = "UPDATE " . $this->table_name . " SET name=:name, updated_at=:updated_at WHERE id=:id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(":name", $this->name);
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
