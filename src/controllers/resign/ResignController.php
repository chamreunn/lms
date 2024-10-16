<?php
require_once 'src/models/resign/ResignModel.php';

class ResignController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        require 'src/views/resign/index.php';
    }
}
