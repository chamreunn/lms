<?php
class NotificationModel
{
    protected $pdo;

    protected $notifications = "allnotifications";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function createNotification($user_id, $title, $message, $url = null, $profile = null)
    {
        $sql = "INSERT INTO $this->notifications (user_id, title, message, url, profile) VALUES (:user_id, :title, :message, :url, :profile)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':message' => $message,
            ':url' => $url,
            ':profile' => $profile
        ]);
    }

    public function getUserNotifications($user_id)
    {
        // Example query (you should replace this with your actual database logic)
        $query = "SELECT * FROM $this->notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getAllNotifications($userId, $limit, $offset)
    {
        $query = "SELECT * FROM $this->notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId, $limit, $offset]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUserNotifications($userId)
    {
        $query = "SELECT COUNT(*) as total FROM $this->notifications WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);

        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getUnsentNotifications($user_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT * 
            FROM $this->notifications 
            WHERE user_id = :user_id AND sent = 0
        ");
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markAsSent($notification_id)
    {
        $stmt = $this->pdo->prepare("
            UPDATE $this->notifications 
            SET sent = 1 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $notification_id]);
    }

    public function markAsRead($notification_id)
    {
        $sql = "UPDATE $this->notifications SET is_read = 1 WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':id' => $notification_id]);
    }
}

