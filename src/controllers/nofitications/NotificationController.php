<?php
require_once 'src/models/notifications/NotificationModel.php';
require_once 'src/models/User.php';

class NotificationsController
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        $notification = new NotificationModel();

        // Default to page 1 if no page query parameter is present
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 10; // Notifications per page
        $offset = ($page - 1) * $limit;

        // Get paginated notifications
        $userNotifications = $notification->getAllNotifications($_SESSION['user_id'], $limit, $offset);

        // Count total notifications for pagination
        $totalNotifications = $notification->countUserNotifications($_SESSION['user_id']);
        $totalPages = ceil($totalNotifications / $limit);

        require 'src/views/notifications/index.php';
    }

    public function markNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['notification_id'];
            $isRead = $_POST['is_read'];

            $stmt = $this->pdo->prepare("UPDATE allnotifications SET is_read = ? WHERE id = ?");
            $stmt->execute([$isRead, $notificationId]);

            header("Location: /elms/allnotifications");
            exit();
        }
    }

    public function deleteNotification()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $notificationId = $_POST['notification_id'];

            $stmt = $this->pdo->prepare("DELETE FROM allnotifications WHERE id = ?");
            $stmt->execute([$notificationId]);

            if ($stmt) {
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "លុបសារជូនដំណឺងដោយជោគជ័យ។"
                ];
                header("Location: /elms/allnotifications");
                exit();
            }
        }
    }


    public function getUserNotifications($userId)
    {
        // Example query (you should replace this with your actual database logic)
        $query = "SELECT * FROM allnotifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$userId]);

        // Fetch notifications from the database
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Fetch user details from the API
        $userApiResponse = $userModel->getUserByIdApi($userId, $_SESSION['token']);

        // Check if the API response is valid
        if ($userApiResponse && isset($userApiResponse['success']) && $userApiResponse['success']) {
            $userDetails = $userApiResponse['data']; // Assuming the API returns user data under 'data'
            $userImage = 'https://hrms.iauoffsa.us/images/' . $userDetails['image'] ?? 'default_avatar.jpg'; // Use default image if no avatar

            // Attach the user image to each notification
            foreach ($notifications as &$notification) {
                $notification['avatar'] = $userImage;
            }
        } else {
            // Log or handle API error if needed
            error_log('Failed to fetch user details from API.');
        }

        // Return the notifications as a response
        return [
            'success' => true,
            'data' => $notifications,
        ];
    }

    public function markNotificationAsRead($notificationId)
    {
        // Example query to mark notification as read
        $query = "UPDATE allnotifications SET is_read = 1 WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$notificationId]);

        // Return success message
        return [
            'success' => true,
            'message' => 'Notification marked as read',
        ];
    }
}
