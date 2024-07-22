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
}
