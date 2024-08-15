<?php
require_once 'src/models/Notification.php';

class NotificationController
{
    public function index()
    {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getNotificationsByUserId($_SESSION['user_id']);

        require 'src/views/notifications/index.php';
    }

    public function viewAllNotify()
    {
        $notificationModel = new Notification();
        $notifications = $notificationModel->getNotificationsByUserId($_SESSION['user_id']);

        require 'src/views/notifications/index.php';
    }

    public function viewDetail($id)
    {
        $notificationModel = new Notification();
        $notification = $notificationModel->getAllNotification($id);

        if ($notification) {
            require "src/views/notifications/notificationDetail.php";
        } else {
            echo "Notification not found.";
        }
    }

    public function markasread()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];

            $notificationModel = new Notification();
            $marksAllAsReads = $notificationModel->markasread($status, $user_id);
        }
    }
}
