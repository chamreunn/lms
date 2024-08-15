<?php

class SettingModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getUserById($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            u.*, 
            m.email AS manager_email, 
            o.name AS office_name, 
            d.name AS department_name 
         FROM users u
         LEFT JOIN users m ON u.office_id = m.office_id AND m.position_id = (SELECT doffice_id FROM offices WHERE id = u.office_id)
         LEFT JOIN offices o ON u.office_id = o.id
         LEFT JOIN departments d ON u.department_id = d.id
         WHERE u.id = ?'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function getUserActivity($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT ual.*, u.khmer_name, u.email, u.office_id, u.department_id, u.profile_picture
         FROM user_activity_log ual
         JOIN users u ON ual.user_id = u.id
         WHERE ual.user_id = ?
         ORDER BY ual.created_at DESC'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function timeAgo($time)
    {
        $time = strtotime($time);
        $timeDifference = time() - $time;

        $units = [
            'year' => 365 * 24 * 60 * 60,
            'month' => 30 * 24 * 60 * 60,
            'week' => 7 * 24 * 60 * 60,
            'day' => 24 * 60 * 60,
            'hour' => 60 * 60,
            'minute' => 60,
        ];

        foreach ($units as $unit => $value) {
            if ($timeDifference >= $value) {
                $count = floor($timeDifference / $value);
                return "$count {$unit}" . ($count > 1 ? 's' : '') . " ago";
            }
        }

        return "Just now";
    }
}
