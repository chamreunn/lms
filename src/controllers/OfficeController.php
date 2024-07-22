<?php
require_once 'config/database.php';
require_once 'src/models/Office.php';

class OfficeController
{
    private $db;
    private $office;

    public function __construct()
    {
        global $pdo;
        $this->db = $pdo;
        $this->office = new Office($this->db);
    }

    public function index()
    {
        $stmt = $this->office->read();
        $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $offices;
    }

    public function store()
    {
        $data = [
            'name' => $_POST['dname'],
            'did' => $_POST['did']
        ];

        // Check if department name already exists
        if ($this->office->exists($data['name'])) {
            $_SESSION['error'] = [
                'title' => "បង្កើតនាយកដ្ឋានថ្មី",
                'message' => "មាននាយកដ្ឋាននេះរួចរាល់ហើយ។"
            ];
            header("Location: /elms/office");
            exit();
        }

        $this->office->name = $data['name'];
        $this->office->department_id = $data['did'];
        $this->office->created_at = date('Y-m-d H:i:s');
        $this->office->updated_at = date('Y-m-d H:i:s');

        if ($this->office->create()) {
            $_SESSION['success'] = [
                'title' => "បង្កើតការិយាល័យថ្មី",
                'message' => "បង្កើតការិយាល័យបានជោគជ័យ។"
            ];
            header("Location: /elms/office");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "បង្កើតការិយាល័យថ្មី",
                'message' => "មានបញ្ហា មិនអាចបង្កើតការិយាល័យបានទេ។"
            ];
        }
    }

    public function update()
    {
        $data = [
            'id' => $_POST['id'],
            'name' => $_POST['dname'],
            'hoid' => $_POST['hoid'],
            'dhoid' => $_POST['dhoid'],
            'did' => $_POST['did']
        ];

        $this->office->id = $data['id'];
        $this->office->name = $data['name'];
        $this->office->hoffice_id = $data['hoid'];
        $this->office->doffice_id = $data['dhoid'];
        $this->office->department_id = $data['did'];
        $this->office->updated_at = date('Y-m-d H:i:s');

        if ($this->office->update()) {
            $_SESSION['success'] = [
                'title' => "កែប្រែការិយាល័យ",
                'message' => "កែប្រែការិយាល័យបានជោគជ័យ។"
            ];
            header("Location: /elms/office");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "កែប្រែការិយាល័យ",
                'message' => "Error updating office."
            ];
        }
    }

    public function delete($id)
    {
        $this->office->id = $id;

        if ($this->office->delete()) {
            $_SESSION['success'] = [
                'title' => "លុបការិយាល័យ",
                'message' => "លុបការិយាល័យបានជោគជ័យ។"
            ];
            header("Location: /elms/office");
            exit();
        } else {
            $_SESSION['error'] = [
                'title' => "លុបការិយាល័យ",
                'message' => "Error deleting office."
            ];
        }
    }
}
