<?php
require_once 'src/models/Department.php';

class DepartmentController
{
    private $db;
    private $department;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
        $this->department = new Department($this->db);
    }

    public function index()
    {
        $stmt = $this->department->read();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $departments;
    }

    public function store()
    {
        $data = [
            'name' => $_POST['dname'],
            'description' => $_POST['description']
        ];

        // Check if department name already exists
        if ($this->department->exists($data['name'])) {
            $_SESSION['error'] = [
                'title' => "បង្កើតនាយកដ្ឋានថ្មី",
                'message' => "មាននាយកដ្ឋាននេះរួចរាល់ហើយ។"
            ];
            header("Location: department");
            exit();
        }

        $this->department->name = $data['name'];
        $this->department->description = $data['description'];
        $this->department->created_at = date('Y-m-d H:i:s');
        $this->department->updated_at = date('Y-m-d H:i:s');

        if ($this->department->create()) {
            $_SESSION['success'] = [
                'title' => "បង្កើតនាយកដ្ឋានថ្មី",
                'message' => "បង្កើតនាយកដ្ឋានបានជោគជ័យ។"
            ];
            header("Location: /elms/department");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "បង្កើតនាយកដ្ឋានថ្មី",
                'message' => "មានបញ្ហា មិនអាចបង្កើតនាយកដ្ឋានបានទេ។"
            ];
        }
    }

    public function update()
    {
        $data = [
            'id' => $_POST['id'],
            'name' => $_POST['dname'],
            'description' => $_POST['description']
        ];

        $this->department->id = $data['id'];
        $this->department->name = $data['name'];
        $this->department->description = $data['description'];
        $this->department->updated_at = date('Y-m-d H:i:s');

        if ($this->department->update()) {
            $_SESSION['success'] = [
                'title' => "កែប្រែនាយកដ្ឋាន",
                'message' => "កែប្រែនាយកដ្ឋានបានជោគជ័យ។"
            ];
            header("Location: /elms/department.php");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "កែប្រែនាយកដ្ឋាន",
                'message' => "Error updating department."
            ];
        }
    }

    public function delete($id)
    {
        $this->department->id = $id;

        if ($this->department->delete()) {
            $_SESSION['success'] = [
                'title' => "លុបនាយកដ្ឋាន",
                'message' => "លុបនាយកដ្ឋានបានជោគជ័យ។"
            ];
            header("Location: /elms/department.php");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "លុបនាយកដ្ឋាន",
                'message' => "Error deleting department."
            ];
        }
    }
}
