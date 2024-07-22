<?php
require_once 'src/models/Position.php';

class PositionController
{
    public function index()
    {
        global $pdo;
        $positionModel = new Position($pdo);
        return $positionModel->getAllPositions();
    }

    public function store($name, $color)
    {
        global $pdo;
        $positionModel = new Position($pdo);
        $positionModel->createPosition($name, $color);
        $_SESSION['success'] = [
            'title' => "Create Position",
            'message' => "Position created successfully."
        ];
        header("Location: /elms/positions");
        exit();
    }

    public function edit($id)
    {
        global $pdo;
        $positionModel = new Position($pdo);
        return $positionModel->getPositionById($id);
    }

    public function update($id, $name, $color)
    {
        global $pdo;
        $positionModel = new Position($pdo);
        $positionModel->updatePosition($id, $name, $color);
        $_SESSION['success'] = [
            'title' => "Update Position",
            'message' => "Position updated successfully."
        ];
        header("Location: /elms/positions");
        exit();
    }

    public function delete($id)
    {
        global $pdo;
        $positionModel = new Position($pdo);
        if ($positionModel->deletePosition($id)) {
            $_SESSION['success'] = [
                'title' => "Delete Position",
                'message' => "Position deleted successfully."
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "Delete Position",
                'message' => "Error deleting Position."
            ];
        }
        header("Location: /elms/positions");
        exit();
    }
}
