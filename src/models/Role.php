<?php
require_once 'config/database.php';
class Role
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllRoles()
    {
        $stmt = $this->pdo->query("SELECT * FROM roles");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createRole($name, $description)
    {
        $stmt = $this->pdo->prepare("INSERT INTO roles (name, description) VALUES (:name, :description)");
        $stmt->execute(['name' => $name, 'description' => $description]);
    }

    public function getRoleById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM roles WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateRole($id, $name, $description)
    {
        $stmt = $this->pdo->prepare("UPDATE roles SET name = :name, description = :description WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'description' => $description]);
    }

    public function deleteRole($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM roles WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
