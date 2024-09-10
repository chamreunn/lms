<?php
require_once 'config/database.php';

class MissionModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getMissionById($user_id, $token)
    {
        // Fetch missions from the database
        $stmt = $this->pdo->prepare('
        SELECT *
        FROM missions
        WHERE user_id = ?
        ORDER BY created_at DESC
    ');
        $stmt->execute([$user_id]);
        $missions = $stmt->fetchAll();

        if (empty($missions)) {
            return []; // Return an empty array if no missions are found
        }

        // Fetch user data from the API
        $userModel = new User(); // Assuming you have a User model for API calls
        $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

        if ($userApiResponse['http_code'] !== 200 || !isset($userApiResponse['data'])) {
            // Handle API error or missing data
            error_log("Failed to fetch user data for user ID: $user_id");
            return $missions; // Return missions without user data if API fails
        }

        $userData = $userApiResponse['data'];

        // Add user information to each mission record
        foreach ($missions as &$mission) {
            $mission['user_name'] = $userData['lastNameKh'] . " " . $userData['firstNameKh'] ?? null;
            $mission['phone_number'] = $userData['phoneNumber'] ?? null;
            $mission['email'] = $userData['email'] ?? null;
            // Add other user data fields as needed
        }

        return $missions;
    }

    public function missionCount($user_id)
    {
        // Prepare the SQL statement to count missions for the given user ID
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) AS mission_count
        FROM missions
        WHERE user_id = ?
    ');

        // Execute the statement with the user ID as a parameter
        $stmt->execute([$user_id]);

        // Fetch the mission count
        $missionCount = $stmt->fetchColumn();

        // Return the mission count
        return $missionCount;
    }

    public function missionsToday($user_id, $token)
    {
        // Fetch today's missions from the database
        $stmt = $this->pdo->prepare('
        SELECT *, 
               DATE_FORMAT(start_date, "%Y-%m-%d") AS start_date, 
               DATE_FORMAT(end_date, "%Y-%m-%d") AS end_date
        FROM missions
        WHERE user_id = ? 
          AND DATE(start_date) = CURDATE()
        ORDER BY created_at DESC
    ');
        $stmt->execute([$user_id]);
        $missions = $stmt->fetchAll();

        if (empty($missions)) {
            return []; // Return an empty array if no missions are found
        }

        // Fetch user data from the API
        $userModel = new User(); // Assuming you have a User model for API calls
        $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

        if ($userApiResponse['http_code'] !== 200 || !isset($userApiResponse['data'])) {
            // Handle API error or missing data
            error_log("Failed to fetch user data for user ID: $user_id");
            return $missions; // Return missions without user data if API fails
        }

        $userData = $userApiResponse['data'];

        // Add user information to each mission record
        foreach ($missions as &$mission) {
            $mission['user_name'] = $userData['lastNameKh'] . " " . $userData['firstNameKh'] ?? null;
            $mission['phone_number'] = $userData['phoneNumber'] ?? null;
            $mission['email'] = $userData['email'] ?? null;
            // Add other user data fields as needed
        }

        return $missions;
    }

    public function create($user_id, $missionName, $start_date, $end_date, $attachment_name, $duration_days)
    {
        $stmt = $this->pdo->prepare("INSERT INTO missions (user_id, missionName, start_date, end_date, attachment, num_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$user_id, $missionName, $start_date, $end_date, $attachment_name, $duration_days]);
        return true;
    }

    public function update($mission_id, $user_id, $missionName, $start_date, $end_date, $attachment_name, $duration_days)
    {
        $stmt = $this->pdo->prepare("UPDATE missions SET user_id = ?, missionName = ?, start_date = ?, end_date = ?, attachment = ?, num_date = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$user_id, $missionName, $start_date, $end_date, $attachment_name, $duration_days, $mission_id]);
        return true;
    }

    public function getMissionByFilters($user_id, $filters)
    {
        // Base SQL query
        $sql = 'SELECT users.*, missions.*
            FROM missions
            JOIN users ON missions.user_id = users.id
            WHERE missions.user_id = ?';

        $params = [$user_id];

        // Dynamically build the SQL query based on provided filters
        if (!empty($filters['start_date'])) {
            $sql .= ' AND missions.start_date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= ' AND missions.end_date <= ?';
            $params[] = $filters['end_date'];
        }

        // Add the ORDER BY clause at the end
        $sql .= ' ORDER BY missions.created_at DESC';

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results
        $results = $stmt->fetchAll();
        return $results;
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM missions WHERE id = ?");
        $stmt->execute([$id]);
        return true;
    }
}
