<?php

require_once 'config/database.php';

class LateModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllLatetype()
    {
        $stmt = $this->pdo->query("SELECT * FROM latetype");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLateModelByUserId($user_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM latetype WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getOvertimeIn($user_id)
    {
        $stmt = $this->pdo->prepare('
            SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
                   offices.name AS office_name, positions.name AS position_name, users.email AS email
            FROM late_in_out
            JOIN users ON late_in_out.user_id = users.id
            LEFT JOIN departments ON users.department_id = departments.id
            LEFT JOIN offices ON users.office_id = offices.id
            LEFT JOIN positions ON users.position_id = positions.id
            WHERE late_in_out.user_id = ? AND late_in_out.late_in IS NOT NULL
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
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

    public function getOvertimeOut($user_id)
    {
        $stmt = $this->pdo->prepare('
            SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
                   offices.name AS office_name, positions.name AS position_name, users.email AS email
            FROM late_in_out
            JOIN users ON late_in_out.user_id = users.id
            LEFT JOIN departments ON users.department_id = departments.id
            LEFT JOIN offices ON users.office_id = offices.id
            LEFT JOIN positions ON users.position_id = positions.id
            WHERE late_in_out.user_id = ? AND late_in_out.late_out IS NOT NULL
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
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
        $stmt = $this->pdo->prepare('
        SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
               offices.name AS office_name, positions.name AS position_name, users.email AS email
        FROM late_in_out
        JOIN users ON late_in_out.user_id = users.id
        LEFT JOIN departments ON users.department_id = departments.id
        LEFT JOIN offices ON users.office_id = offices.id
        LEFT JOIN positions ON users.position_id = positions.id
        WHERE late_in_out.user_id = ? AND late_in_out.leave_early IS NOT NULL
        ORDER BY late_in_out.created_at DESC
    ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
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

    public function applyLateIn($date, $time, $lateMinutes, $reason)
    {
        $stmt = $this->pdo->prepare("INSERT INTO late_in_out (user_id, date, late_in, late, reasons) VALUES (:user_id, :date, :time, :late, :reason)");
        $stmt->execute(['user_id' => $_SESSION['user_id'], 'date' => $date, 'time' => $time, 'late' => $lateMinutes, 'reason' => $reason]);
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
