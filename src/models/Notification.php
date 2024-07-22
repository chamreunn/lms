<?php

require_once 'config/database.php';

class Notification
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getNotificationsByUserId($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function createNotification($user_id, $message)
    {
        $stmt = $this->pdo->prepare('INSERT INTO notifications (user_id, message, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$user_id, $message]);
    }
}
