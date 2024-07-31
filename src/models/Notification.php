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
        $stmt = $this->pdo->prepare('SELECT * FROM notifications WHERE receiver_id = ? ORDER BY created_at DESC LIMIT 5');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function createNotification($receiver_id, $user_id, $request_id, $message)
    {
        $stmt = $this->pdo->prepare('INSERT INTO notifications (receiver_id, user_id, request_id, message, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$receiver_id, $user_id, $request_id, $message]);
    }
}
