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
        $stmt = $this->pdo->prepare(
            'SELECT n.*, u.*, n.status as status
             FROM notifications n
             JOIN users u ON n.user_id = u.id
             WHERE n.receiver_id = ?
             ORDER BY n.created_at DESC
             LIMIT 5'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getAllNotification($userId)
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC'
        );
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function createNotification($receiver_ids, $user_id, $request_id, $message)
    {
        // Check if receiver_ids is an array and has valid data
        if (!is_array($receiver_ids) || empty($receiver_ids)) {
            throw new Exception('Invalid receiver ID(s) provided.');
        }

        // Prepare the SQL statement for inserting notifications
        $stmt = $this->pdo->prepare('INSERT INTO notifications (receiver_id, user_id, request_id, message, created_at) VALUES (?, ?, ?, ?, NOW())');

        // Iterate through receiver IDs and create a notification for each one
        foreach ($receiver_ids as $receiver_id) {
            // Execute the query with provided parameters
            $stmt->execute([$receiver_id, $user_id, $request_id, $message]);
        }
    }

    public function markasread($status, $user_id)
    {
        $stmt = $this->pdo->prepare('UPDATE notifications SET status = ?, updated_at = NOW() WHERE receiver_id = ?');
        return $stmt->execute([$status, $user_id]);
    }
}
