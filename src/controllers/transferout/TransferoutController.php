<?php
require_once 'src/models/transferout/TransferoutModel.php';

class TransferoutController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $userModel = new User();
        $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
        $offices = $userModel->getAllOfficeApi($_SESSION['token']);

        // Check for errors in the departments API response
        if ($departments['http_code'] !== 200) {
            $_SESSION['error'] = $departments['error'] ?? 'Unable to fetch departments. Please try again later.';
            $departments['data'] = []; // Fallback to empty data if there's an error
        }

        // Check for errors in the offices API response
        if ($offices['http_code'] !== 200) {
            $_SESSION['error'] = $offices['error'] ?? 'Unable to fetch offices. Please try again later.';
            $offices['data'] = []; // Fallback to empty data if there's an error
        }

        require 'src/views/transferout/index.php';
    }
}
