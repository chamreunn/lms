<?php
require_once 'config/database.php';
class Position
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllPositions()
    {
        $stmt = $this->pdo->query("SELECT * FROM positions");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createPosition($name, $color)
    {
        $stmt = $this->pdo->prepare("INSERT INTO positions (name, color) VALUES (:name, :color)");
        $stmt->execute(['name' => $name, 'color' => $color]);
    }

    public function getPositionById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM positions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePosition($id, $name, $color)
    {
        $stmt = $this->pdo->prepare("UPDATE positions SET name = :name, color = :color WHERE id = :id");
        $stmt->execute(['id' => $id, 'name' => $name, 'color' => $color]);
    }

    public function deletePosition($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM positions WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
