<?php
require_once 'src/models/backwork/BackworkModel.php';

class BackworkController
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        require 'src/views/backwork/index.php';
    }
}
