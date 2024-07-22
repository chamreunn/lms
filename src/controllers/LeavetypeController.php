<?php

require_once 'config/database.php'; // Adjust the path as per your project structure
require_once 'src/models/Leavetype.php'; // Adjust the path as per your project structure

class LeavetypeController
{
    private $leavetypeModel;

    public function __construct()
    {
        $this->leavetypeModel = new Leavetype();
    }

    public function index()
    {
        try {
            return $this->leavetypeModel->getAllLeavetypes();
        } catch (PDOException $e) {
            // Handle database errors
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public function store($data)
    {
        try {
            return $this->leavetypeModel->createLeavetype($data);
        } catch (PDOException $e) {
            // Handle database errors
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data)
    {
        try {
            return $this->leavetypeModel->updateLeavetype($id, $data);
        } catch (PDOException $e) {
            // Handle database errors
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id)
    {
        try {
            return $this->leavetypeModel->deleteLeavetype($id);
        } catch (PDOException $e) {
            // Handle database errors
            error_log('Database error: ' . $e->getMessage());
            return false;
        }
    }
}

// Example usage:
$controller = new LeavetypeController();
$leavetypes = $controller->index(); // Get all leave types
// $newLeaveType = $controller->store($data); // Create a new leave type
// $updatedLeaveType = $controller->update($id, $data); // Update an existing leave type
// $deleted = $controller->delete($id); // Delete a leave type
