<?php
require_once 'src/models/MissionModel.php';

class MissionController
{
    public function index()
    {
        $missionModel = new Mission();
        $missions = $missionModel->getMissionById($_SESSION['user_id']);

        require 'src/views/missions/index.php';
    }
}
