<?php
require_once 'src/models/attendance/AttendanceModel.php';

class AttendanceController
{
    public function index()
    {
        require 'src/views/attendence/check.php';
    }
}
