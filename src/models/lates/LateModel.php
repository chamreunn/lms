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
        // Fetch overtime data along with approver information from late_approvals
        $stmt = $this->pdo->prepare("
            SELECT late_in_out.*, 
                   late_approvals.acted_by AS approver_id, 
                   late_approvals.action AS approval_status, 
                   late_approvals.created_at AS approval_date
            FROM $this->table_name
            LEFT JOIN late_approvals ON late_in_out.id = late_approvals.lateId
            WHERE late_in_out.user_id = ? 
            AND late_in_out.late_in IS NOT NULL
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

            // Loop through each overtime record and add user and approval details from the API response
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = $user['firstNameKh'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';

                // Fetch approver details from API if needed (optional)
                if (isset($record['approver_id'])) {
                    $approverApiResponse = $userModel->getUserByIdApi($record['approver_id'], $token);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];
                        $record['approver_name'] = $approver['firstNameKh'] ?? 'Unknown';
                        $record['approver_email'] = $approver['email'] ?? 'Unknown';
                    } else {
                        $record['approver_name'] = 'Unknown';
                        $record['approver_email'] = 'Unknown';
                    }
                } else {
                    $record['approver_name'] = 'Unknown';
                    $record['approver_email'] = 'Unknown';
                }
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
                $record['approver_name'] = 'Unknown';
                $record['approver_email'] = 'Unknown';
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
        // Fetch overtime data along with approver information from late_approvals
        $stmt = $this->pdo->prepare("
            SELECT late_in_out.*, 
                   late_approvals.acted_by AS approver_id, 
                   late_approvals.action AS approval_status, 
                   late_approvals.created_at AS approval_date
            FROM $this->table_name
            LEFT JOIN late_approvals ON late_in_out.id = late_approvals.lateId
            WHERE late_in_out.user_id = ? 
            AND late_in_out.late_out IS NOT NULL
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

            // Loop through each overtime record and add user and approval details from the API response
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = $user['firstNameKh'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';

                // Fetch approver details from API if needed (optional)
                if (isset($record['approver_id'])) {
                    $approverApiResponse = $userModel->getUserByIdApi($record['approver_id'], $token);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];
                        $record['approver_name'] = $approver['firstNameKh'] ?? 'Unknown';
                        $record['approver_email'] = $approver['email'] ?? 'Unknown';
                    } else {
                        $record['approver_name'] = 'Unknown';
                        $record['approver_email'] = 'Unknown';
                    }
                } else {
                    $record['approver_name'] = 'Unknown';
                    $record['approver_email'] = 'Unknown';
                }
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
                $record['approver_name'] = 'Unknown';
                $record['approver_email'] = 'Unknown';
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

    public function getLeaveEarly($user_id, $token)
    {
        // Fetch overtime data along with approver information from late_approvals
        $stmt = $this->pdo->prepare("
            SELECT late_in_out.*, 
                   late_approvals.acted_by AS approver_id, 
                   late_approvals.action AS approval_status, 
                   late_approvals.created_at AS approval_date
            FROM $this->table_name
            LEFT JOIN late_approvals ON late_in_out.id = late_approvals.lateId
            WHERE late_in_out.user_id = ? 
            AND late_in_out.leave_early IS NOT NULL
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

            // Loop through each overtime record and add user and approval details from the API response
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = $user['firstNameKh'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';

                // Fetch approver details from API if needed (optional)
                if (isset($record['approver_id'])) {
                    $approverApiResponse = $userModel->getUserByIdApi($record['approver_id'], $token);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];
                        $record['approver_name'] = $approver['firstNameKh'] ?? 'Unknown';
                        $record['approver_email'] = $approver['email'] ?? 'Unknown';
                    } else {
                        $record['approver_name'] = 'Unknown';
                        $record['approver_email'] = 'Unknown';
                    }
                } else {
                    $record['approver_name'] = 'Unknown';
                    $record['approver_email'] = 'Unknown';
                }
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
                $record['approver_name'] = 'Unknown';
                $record['approver_email'] = 'Unknown';
            }

            // Debug: Log the API failure case
            error_log("API call failed for User ID " . $user_id . ". Setting default values.");
        }

        return $overtimeRecords;
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

    public function applyLateIn($userId, $type, $date, $time, $lateMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO $this->table_name (user_id, type, date, late_in, late, reasons) VALUES (:user_id, :type, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $userId, 'type' => $type, 'date' => $date, 'time' => $time, 'late' => $lateMinutes, 'reason' => $reason]);
    }

    public function applyLateOut($date, $type, $time24, $overtimeoutMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO late_in_out (user_id, type, date, late_out, late, reasons) VALUES (:user_id, :type, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'type' => $type, 'date' => $date, 'time' => $time24, 'late' => $overtimeoutMinutes, 'reason' => $reason]);
    }

    public function applyLeaveEarly($date, $type, $time24, $leaveEarlyMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO late_in_out (user_id, type, date, leave_early, late, reasons) VALUES (:user_id, :type, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'type' => $type, 'date' => $date, 'time' => $time24, 'late' => $leaveEarlyMinutes, 'reason' => $reason]);
    }

    // Update late-in request in the database
    public function updateLateIn($lateId, $userId, $date, $time, $lateMinutes, $reason)
    {
        $sql = "UPDATE $this->table_name 
                SET user_id = :userId, date = :date, late_in = :time, late = :lateMinutes, reasons = :reason, updated_at = NOW()
                WHERE id = :lateId";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':lateMinutes', $lateMinutes);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':lateId', $lateId);

        // Execute the update query
        return $stmt->execute();
    }

    public function updateLateOut($lateId, $userId, $date, $time, $lateMinutes, $reason)
    {
        $sql = "UPDATE $this->table_name 
                SET user_id = :userId, date = :date, late_out = :time, late = :lateMinutes, reasons = :reason, updated_at = NOW()
                WHERE id = :lateId";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':lateMinutes', $lateMinutes);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':lateId', $lateId);

        // Execute the update query
        return $stmt->execute();
    }

    public function updateLeaveEarly($lateId, $userId, $date, $time, $lateMinutes, $reason)
    {
        $sql = "UPDATE $this->table_name 
                SET user_id = :userId, date = :date, leave_early = :time, late = :lateMinutes, reasons = :reason, updated_at = NOW()
                WHERE id = :lateId";

        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':userId', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':time', $time);
        $stmt->bindParam(':lateMinutes', $lateMinutes);
        $stmt->bindParam(':reason', $reason);
        $stmt->bindParam(':lateId', $lateId);

        // Execute the update query
        return $stmt->execute();
    }

    // Optionally fetch the late-in request by ID for pre-filling the form
    public function getLateInById($lateId)
    {
        $sql = "SELECT * FROM $this->table_name  WHERE id = :lateId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':lateId', $lateId);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
