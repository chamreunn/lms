<?php
require_once 'src/models/SettingModel.php';

class SettingController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $myaccounts = $settingModel->getUserById($_SESSION['user_id']);

        require 'src/views/settings/myaccount.php';
    }

    public function activity()
    {
        $settingModel = new SettingModel();
        $activities = $settingModel->getUserActivity($_SESSION['user_id']);

        // Format timestamps using the timeAgo function
        foreach ($activities as &$activity) {
            $activity['created_at'] = $settingModel->timeAgo($activity['created_at']);
        }

        require 'src/views/settings/activity.php';
    }

    public function updateProfilePicture()
    {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
            $userModel = new User();
            $userId = $_SESSION['user_id'];

            // Handle file upload
            $file = $_FILES['profile_picture'];
            $fileName = basename($file['name']);
            $uploadDir = 'public/uploads/profiles/';
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                // Update profile picture path in database
                $userModel->updateProfilePicture($userId, $uploadFile);
                $_SESSION['success'] = [
                    'title' => "ជោគជ័យ",
                    'message' => "កែប្រែរូបភាពបានជោគជ័យ។"
                ];
            } else {
                $_SESSION['error'] = [
                    'title' => "បរាជ័យ",
                    'message' => "មិនអាចបញ្ចូលរូបភាពបានទេ"
                ];
            }
        } else {
            $_SESSION['error'] = [
                'title' => "បរាជ័យ",
                'message' => "មិនអាចបញ្ចូលរូបភាពបានទេ"
            ];
        }
        sleep(1);
        header('Location: /elms/my-account');
        exit();
    }

    public function resetProfilePicture()
    {
        $userModel = new User();
        $userId = $_SESSION['user_id'];

        // Remove profile picture from the server
        $user = $userModel->getUserById($userId);
        if ($user && $user['profile_picture']) {
            unlink($user['profile_picture']);
        }

        // Reset profile picture path in database
        $userModel->updateProfilePicture($userId, 'public/uploads/profiles/default_image.svg');

        $_SESSION['success'] = [
            'title' => "ជោគជ័យ",
            'message' => "លុបរូបភាពបានជោគជ័យ។"
        ];
        sleep(1);
        header('Location: /elms/my-account');
        exit();
    }
}
