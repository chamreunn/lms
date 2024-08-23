<?php
require_once 'config/database.php';
class AdminModel
{
    private $pdo;
    private $table_name = "late_in_out";

    private $table = "missions";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllLatein()
    {
        $query = "
        SELECT lt.*, u.*, lt.status AS late_status, lt.id AS late_id
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            u.*, 
            m.email AS manager_email, 
            o.name AS office_name, 
            d.name AS department_name, 
            p.name AS position_name ,
            p.color AS pcolor
         FROM 
            users u
         LEFT JOIN 
            users m ON u.office_id = m.office_id 
            AND m.position_id = (SELECT doffice_id FROM offices WHERE id = u.office_id)
         LEFT JOIN 
            offices o ON u.office_id = o.id
         LEFT JOIN 
            departments d ON u.department_id = d.id
         LEFT JOIN 
            positions p ON u.position_id = p.id
         WHERE 
            u.id = ?'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function getUserLeaveRequests($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            lr.*, 
            lt.name AS leave_type_name, 
            lt.duration, 
            lt.color, 
            u.khmer_name AS user_name, 
            u.date_of_birth AS dob, 
            u.email AS user_email, 
            u.profile_picture AS user_profile,
            d.name AS department_name,
            p.name AS position_name
        FROM 
            leave_requests lr
        JOIN 
            leave_types lt ON lr.leave_type_id = lt.id
        JOIN 
            users u ON lr.user_id = u.id
        JOIN 
            departments d ON u.department_id = d.id
        JOIN 
            positions p ON u.position_id = p.id
        WHERE 
            lr.user_id = ? AND lr.status = "Approved" ORDER BY lr.id DESC'
        );
        $stmt->execute([$user_id]);

        // Fetch all results
        return $stmt->fetchAll(); // Return all leave requests for the user
    }

    public function countUserApprovedLeaveRequests($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            COUNT(*) AS leave_request_count
        FROM 
            leave_requests lr
        JOIN 
            leave_types lt ON lr.leave_type_id = lt.id
        JOIN 
            users u ON lr.user_id = u.id
        JOIN 
            departments d ON u.department_id = d.id
        JOIN 
            positions p ON u.position_id = p.id
        WHERE 
            lr.user_id = ? AND lr.status = "Approved"'
        );
        $stmt->execute([$user_id]);

        // Fetch the count result
        return $stmt->fetchColumn(); // Return the count of approved leave requests
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
            WHERE late_in_out.user_id = ? AND late_in_out.late_in IS NOT NULL AND late_in_out.status = "Approved" || late_in_out.status = "Rejected"
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getOvertimeInCount($user_id)
    {
        $stmt = $this->pdo->prepare('
        SELECT 
            COUNT(*) AS count
        FROM 
            late_in_out
        WHERE 
            user_id = ? 
            AND late_in IS NOT NULL 
            AND (status = "Approved" OR status = "Rejected")
    ');
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getMission()
    {
        $query = "
        SELECT m.*,u.* m.user_id AS uId
        FROM $this->table m
        JOIN users u ON m.user_id = u.id 
        ORDER BY updated_at DESC
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLateinCount()
    {
        $query = "
        SELECT COUNT(*) AS latein_count
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latein_count'] ?? 0;
    }

    public function getLateCountToday()
    {
        $query = "
        SELECT COUNT(*) AS latein_count
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
        AND DATE(lt.date) = CURDATE()
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latein_count'] ?? 0;
    }

    public function updateRequest($approver_id, $action, $request_id, $comment, $signature)
    {
        try {
            // Start a transaction to ensure atomicity
            $this->pdo->beginTransaction();

            // Insert the approval record with the signature
            $stmt = $this->pdo->prepare(
                'INSERT INTO late_approvals (acted_by, action, late_approval_id, comment, signature, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$approver_id, $action, $request_id, $comment, $signature]);

            // Update the action and updated_at timestamp in the existing late_approvals record
            $stmt = $this->pdo->prepare("UPDATE late_in_out SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$action, $request_id]);

            // Commit the transaction
            $this->pdo->commit();

            // Optionally, fetch the updated_at timestamp if needed
            $stmt = $this->pdo->prepare("SELECT updated_at FROM late_in_out WHERE id = ?");
            $stmt->execute([$request_id]);
            $updatedAt = $stmt->fetchColumn();

            if ($updatedAt === false) {
                throw new Exception("Unable to fetch updated_at timestamp for approval.");
            }

            return $updatedAt; // Return the updated_at timestamp if needed

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
