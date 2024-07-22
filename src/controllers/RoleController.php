<?php
require_once 'src/models/Role.php';

class RoleController
{
    public function index()
    {
        global $pdo;
        $roleModel = new Role($pdo);
        return $roleModel->getAllRoles();
    }

    public function store($name, $description)
    {
        global $pdo;
        $roleModel = new Role($pdo);
        $roleModel->createRole($name, $description);
        $_SESSION['success'] = [
            'title' => "Create Role",
            'message' => "Role created successfully."
        ];
        header("Location: /elms/roles");
        exit();
    }

    public function edit($id)
    {
        global $pdo;
        $roleModel = new Role($pdo);
        return $roleModel->getRoleById($id);
    }

    public function update($id, $name, $description)
    {
        global $pdo;
        $roleModel = new Role($pdo);
        $roleModel->updateRole($id, $name, $description);
        $_SESSION['success'] = [
            'title' => "Update Role",
            'message' => "Role updated successfully."
        ];
        header("Location: /elms/roles");
        exit();
    }

    public function delete($id)
    {
        global $pdo;
        $roleModel = new Role($pdo);
        if ($roleModel->deleteRole($id)) {
            $_SESSION['success'] = [
                'title' => "Delete Role",
                'message' => "Role deleted successfully."
            ];
        } else {
            $_SESSION['error'] = [
                'title' => "Delete Role",
                'message' => "Error deleting role."
            ];
        }
        header("Location: /elms/roles");
        exit();
    }
}
