<?php

class LateModel
{
    private $pdo;

    private $table_name = 'late_in_out';

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // Begin transaction
    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    // Commit transaction
    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    // Rollback transaction
    public function rollBackTransaction()
    {
        $this->pdo->rollBack();
    }

    public function getAllLatetype()
    {
        $stmt = $this->pdo->query("SELECT * FROM latetype");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lateInCount($user_id)
    {
        // Prepare the SQL statement to count only late entries
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM ' . $this->table_name . ' WHERE user_id = ? AND late_in IS NOT NULL');

        // Execute the query with the user_id parameter
        $stmt->execute([$user_id]);

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Log the count of late-in entries
        error_log("Count of late-in entries: " . print_r($result, true));

        // Return the count
        return $result['count'];
    }

    public function getLateModelByUserId($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM latetype WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getOvertimeIn($user_id, $token)
    {
        // Fetch overtime data without joining the users table
        $stmt = $this->pdo->prepare("SELECT late_in_out.*
        FROM $this->table_name
        WHERE late_in_out.user_id = ? AND late_in_out.late_in IS NOT NULL
        ORDER BY late_in_out.created_at DESC
    ");
        $stmt->execute([$user_id]);
        $overtimeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data from API
        $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

        // Check if the API response is successful
        if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Loop through each overtime record and add user details from the API response
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
            }
        } else {
            // Handle cases where the API call fails or returns no data
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['profile'] = 'default-profile.png';
                $record['office_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['email'] = 'Unknown';
            }

            // Debug: Log the API failure case
            error_log("API call failed for User ID " . $user_id . ". Setting default values.");
        }

        return $overtimeRecords;
    }

    public function getOvertimeInByFilters($user_id, $filters)
    {
        // Base SQL query
        $sql = '
            SELECT late_in_out.*, 
                   users.khmer_name, 
                   users.profile_picture AS profile,
                   users.email AS email,
                   departments.name AS department_name, 
                   offices.name AS office_name, 
                   positions.name AS position_name
            FROM late_in_out
            JOIN users ON late_in_out.user_id = users.id
            LEFT JOIN departments ON users.department_id = departments.id
            LEFT JOIN offices ON users.office_id = offices.id
            LEFT JOIN positions ON users.position_id = positions.id
            WHERE late_in_out.user_id = ? 
              AND late_in_out.late_in IS NOT NULL
        ';

        $params = [$user_id];

        // Dynamically build the SQL query based on provided filters
        if (!empty($filters['start_date'])) {
            $sql .= ' AND late_in_out.date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND late_in_out.status = ?';
            $params[] = $filters['status'];
        }

        $sql .= ' ORDER BY late_in_out.created_at DESC';

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results
        $results = $stmt->fetchAll();
        return $results;
    }

    public function getOvertimeOut($user_id, $token)
    {
        // Base SQL query without joining the users table
        $sql = '
        SELECT late_in_out.*
        FROM late_in_out
        WHERE late_in_out.user_id = ? 
          AND late_in_out.late_out IS NOT NULL
        ORDER BY late_in_out.created_at DESC
    ';

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$user_id]);

        // Fetch all overtime records
        $overtimeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data from API
        $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

        // Check if the API response is successful
        if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Loop through each overtime record and add user details from the API response
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
            }
        } else {
            // Handle cases where the API call fails or returns no data
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = 'Unknown';
                $record['profile'] = 'default-profile.png';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['office_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
            }

            // Debug: Log the API failure case
            error_log("API call failed for User ID " . $user_id . ". Setting default values.");
        }

        return $overtimeRecords;
    }

    public function getOvertimeOutByFilters($user_id, $filters)
    {
        // Base SQL query
        $sql = '
        SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
               offices.name AS office_name, positions.name AS position_name, users.email AS email
        FROM late_in_out
        JOIN users ON late_in_out.user_id = users.id
        LEFT JOIN departments ON users.department_id = departments.id
        LEFT JOIN offices ON users.office_id = offices.id
        LEFT JOIN positions ON users.position_id = positions.id
        WHERE late_in_out.user_id = ? AND late_in_out.late_out IS NOT NULL
    ';

        $params = [$user_id];

        // Dynamically build the SQL query based on provided filters
        if (!empty($filters['start_date'])) {
            $sql .= ' AND late_in_out.date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND late_in_out.status = ?';
            $params[] = $filters['status'];
        }

        // Single ORDER BY clause
        $sql .= ' ORDER BY late_in_out.created_at DESC';

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results
        return $stmt->fetchAll();
    }

    public function getLeaveEarly($user_id)
    {
        // Fetch leave early data from late_in_out table
        $stmt = $this->pdo->prepare('
            SELECT late_in_out.*
            FROM late_in_out
            WHERE late_in_out.user_id = ? AND late_in_out.leave_early IS NOT NULL
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);
        $leaveEarlyRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data using the API and add it to each record
        foreach ($leaveEarlyRecords as &$record) {
            // Get user data from API
            $userApiResponse = $userModel->getUserByIdApi($record['user_id'], $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data'];

                // Add user information to the record
                $record['khmer_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Default profile picture if not available
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['office_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile'] = 'default-profile.png'; // Default profile picture if API fails
            }
        }

        return $leaveEarlyRecords;
    }

    public function getLeaveEarlyByFilters($user_id, $filters)
    {
        // Base SQL query
        $sql = '
        SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
               offices.name AS office_name, positions.name AS position_name, users.email AS email
        FROM late_in_out
        JOIN users ON late_in_out.user_id = users.id
        LEFT JOIN departments ON users.department_id = departments.id
        LEFT JOIN offices ON users.office_id = offices.id
        LEFT JOIN positions ON users.position_id = positions.id
        WHERE late_in_out.user_id = ? AND late_in_out.leave_early IS NOT NULL
    ';

        $params = [$user_id];

        // Dynamically build the SQL query based on provided filters
        if (!empty($filters['start_date'])) {
            $sql .= ' AND late_in_out.date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND late_in_out.status = ?';
            $params[] = $filters['status'];
        }

        // Single ORDER BY clause (already present in the base query)
        $sql .= ' ORDER BY late_in_out.created_at DESC';

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results
        return $stmt->fetchAll();
    }

    public function getOvertimeinCount($user_id)
    {
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) AS overtime_count
        FROM late_in_out 
        WHERE user_id = ? AND late_in IS NOT NULL
    ');
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['overtime_count'];
    }

    public function getOvertimeoutCount($user_id)
    {
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) AS overtime_count
        FROM late_in_out 
        WHERE user_id = ? AND late_out IS NOT NULL
    ');
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        return $result['overtime_count'];
    }

    public function createLate($name, $color)
    {
        $stmt = $this->pdo->prepare("INSERT INTO latetype (user_id ,name, color) VALUES (:user_id, :name, :color)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'name' => $name, 'color' => $color]);
    }

    public function updateLate($id, $name, $color)
    {
        $stmt = $this->pdo->prepare("UPDATE latetype SET user_id =:user_id, name = :name, color = :color WHERE id = :id");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'id' => $id, 'name' => $name, 'color' => $color]);
    }

    public function deleteLateIn($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM late_in_out WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteLateOut($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM late_in_out WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteLateEarly($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM late_in_out WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function applyLateIn($userId, $date, $time, $lateMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO $this->table_name (user_id, date, late_in, late, reasons) VALUES (:user_id, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $userId, 'date' => $date, 'time' => $time, 'late' => $lateMinutes, 'reason' => $reason]);
    }

    public function applyLateOut($date, $time24, $overtimeoutMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO late_in_out (user_id, date, late_out, late, reasons) VALUES (:user_id, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'date' => $date, 'time' => $time24, 'late' => $overtimeoutMinutes, 'reason' => $reason]);
    }

    public function applyLeaveEarly($date, $time24, $leaveEarlyMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO late_in_out (user_id, date, leave_early, late, reasons) VALUES (:user_id, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'date' => $date, 'time' => $time24, 'late' => $leaveEarlyMinutes, 'reason' => $reason]);
    }
}
