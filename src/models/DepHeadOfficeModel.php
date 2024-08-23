<?php

require_once 'config/database.php';

class DepHeadOfficeModel
{
    private $pdo;
    private $table_name = "leave_requests";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function create(
        $user_id,
        $leave_type_id,
        $leave_type_name,
        $start_date,
        $end_date,
        $remarks,
        $duration_days,
        $attachment,
        $signature
    ): int {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare('
            INSERT INTO leave_requests 
            (user_id, leave_type_id, leave_type, start_date, end_date, remarks, num_date, attachment, signature, status, dhead_office, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ');

        $stmt->execute([
            $user_id,
            $leave_type_id,
            $leave_type_name,
            $start_date,
            $end_date,
            $remarks,
            $duration_days,
            $attachment,
            $signature,
            'Pending',
            'Approved'
        ]);

        // Return the ID of the newly created leave request
        return $this->pdo->lastInsertId();
    }

    public function getRequestsByUserId($user_id)
    {
        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, 
                lt.name as leave_type_name, 
                lt.duration, 
                lt.color, 
                u.khmer_name as user_name, 
                u.date_of_birth as dob, 
                u.email as user_email, 
                u.office_id, 
                u.department_id, 
                u.position_id, 
                u.profile_picture as user_profile,
                d.name as department_name,
                p.name as position_name,
                o.name as office_name
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         JOIN users u ON lr.user_id = u.id
         JOIN departments d ON u.department_id = d.id
         JOIN positions p ON u.position_id = p.id
         JOIN offices o ON u.office_id = o.id
         WHERE lr.user_id = ?'
        );
        $stmt->execute([$user_id]);

        // Fetch all results
        $results = $stmt->fetchAll();

        // Add additional data to each result
        foreach ($results as &$result) {
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id']);
            $result['doffice'] = $this->getDOfficePositions($result['id']);
            $result['hoffice'] = $this->getHOfficePositions($result['id']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id']);
            $result['dunit'] = $this->getDUnitPositions($result['id']);
            $result['unit'] = $this->getUnitPositions($result['id']);
        }

        return $results;
    }

    // New method to get filtered requests
    public function getRequestsByFilters($user_id, $filters)
    {
        // Base SQL query
        $sql = 'SELECT lr.*, 
                   lt.name as leave_type_name, 
                   lt.duration, 
                   lt.color, 
                   u.khmer_name as user_name, 
                   u.date_of_birth as dob, 
                   u.email as user_email, 
                   u.office_id, 
                   u.department_id, 
                   u.position_id, 
                   u.profile_picture as user_profile,
                   d.name as department_name,
                   p.name as position_name,
                   o.name as office_name
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN users u ON lr.user_id = u.id
            JOIN departments d ON u.department_id = d.id
            JOIN positions p ON u.position_id = p.id
            JOIN offices o ON u.office_id = o.id
            WHERE lr.user_id = ?';

        $params = [$user_id];

        // Dynamically build the SQL query based on provided filters
        if (!empty($filters['start_date'])) {
            $sql .= ' AND lr.start_date >= ?';
            $params[] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $sql .= ' AND lr.end_date <= ?';
            $params[] = $filters['end_date'];
        }

        if (!empty($filters['status'])) {
            $sql .= ' AND lr.status = ?';
            $params[] = $filters['status'];
        }

        // Prepare and execute the SQL query
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        // Fetch all results
        $results = $stmt->fetchAll();

        // Add additional data to each result
        foreach ($results as &$result) {
            // Assuming these methods use the leave request ID to fetch additional details
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id']);
            $result['doffice'] = $this->getDOfficePositions($result['id']);
            $result['hoffice'] = $this->getHOfficePositions($result['id']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id']);
            $result['dunit'] = $this->getDUnitPositions($result['id']);
            $result['unit'] = $this->getUnitPositions($result['id']);
        }

        return $results;
    }

    public function getLeaveByUser($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, lt.name as leave_type_name, lt.duration, lt.color, 
                u.khmer_name,u.profile_picture AS profile
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         JOIN users u ON lr.user_id = u.id
         WHERE lr.user_id = ? AND lr.status != ? ORDER BY lr.id'
        );
        $stmt->execute([$user_id, 'Approved']);
        return $stmt->fetchAll();
    }

    public function getTodayLeaveById($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, lt.name as leave_type_name, lt.duration, lt.color 
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.user_id = ?
         AND lr.status = "Approved"
         AND CURRENT_DATE BETWEEN lr.start_date AND lr.end_date'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function countRequestsByUserId($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) as request_count 
         FROM leave_requests 
         WHERE user_id = ?'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetch()['request_count'];
    }

    public function getLeaveRequestById($request_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leave_requests WHERE id = ?');
        $stmt->execute([$request_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch a single row as an associative array
    }

    public function approve($request_id, $status, $remarks, $approver_id)
    {
        $stmt = $this->pdo->prepare('UPDATE leave_requests SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $request_id]);

        $stmt = $this->pdo->prepare('INSERT INTO leave_approvals (request_id, approver_id, status, remarks, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([$request_id, $approver_id, $status, $remarks]);
    }

    public function getAllLeaves()
    {
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, 
                u.khmer_name AS user_name, 
                u.profile_picture AS profile
         FROM leave_requests lr
         JOIN users u ON lr.user_id = u.id
         WHERE lr.status = ?'
        );
        $stmt->execute(['approved']);
        return $stmt->fetchAll();
    }

    public function getRequestById($leave_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, lt.name as leave_type_name, lt.duration, lt.color,
                deputy.khmer_name as deputy_head_name, user_request.phone_number, user_request.khmer_name, user_request.date_of_birth as dob,
                o.name as office_name, d.name as department_name,
                p.name as position_name
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         JOIN users user_request ON lr.user_id = user_request.id
         JOIN offices o ON user_request.office_id = o.id
         JOIN departments d ON o.department_id = d.id
         JOIN positions p ON user_request.position_id = p.id
         JOIN users deputy ON o.doffice_id = deputy.id
         WHERE lr.id = ?'
        );
        $stmt->execute([$leave_id]);
        $leaveRequest = $stmt->fetch();

        if ($leaveRequest) {
            $leaveRequest['approvals'] = $this->getApprovalsByLeaveRequestId($leaveRequest['id']);
            $leaveRequest['doffice'] = $this->getDOfficePositions($leaveRequest['id']);
            $leaveRequest['hoffice'] = $this->getHOfficePositions($leaveRequest['id']);
            $leaveRequest['hdepartment'] = $this->getHDepartmentPositions($leaveRequest['id']);
            $leaveRequest['dunit'] = $this->getDUnitPositions($leaveRequest['id']);
            $leaveRequest['unit'] = $this->getUnitPositions($leaveRequest['id']);
        }

        return $leaveRequest;
    }

    public function getLeaveCountById($user_id)
    {
        // Prepare the SQL query
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) as leave_count
         FROM leave_requests
         WHERE user_id = ? AND status = ?'
        );

        // Execute the query with the provided user ID and status "Approved"
        $stmt->execute([$user_id, 'Approved']);

        // Fetch the result
        $result = $stmt->fetch();

        // Return the leave count
        return $result['leave_count'];
    }

    public function getPaginatedRequestsByUserId($user_id, $limit, $offset)
    {
        // Prepare the SQL query for fetching paginated results
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, lt.name as leave_type, lt.color
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.user_id = ?
         LIMIT ? OFFSET ?'
        );

        // Execute the query with the provided user ID, limit, and offset
        $stmt->execute([$user_id, $limit, $offset]);

        // Fetch the results
        return $stmt->fetchAll();
    }

    public function getTotalRequestsByUserId($user_id)
    {
        // Prepare the SQL query for counting total leave requests
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) as total
         FROM leave_requests
         WHERE user_id = ?'
        );

        // Execute the query with the provided user ID
        $stmt->execute([$user_id]);

        // Fetch the result
        $result = $stmt->fetch();

        // Return the total count
        return $result['total'];
    }

    public function getApprovalsByLeaveRequestId($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
                u.khmer_name AS approver_name, 
                u.profile_picture AS profile,
                p.name AS position_name,
                p.color AS position_color,
                a.signature,  -- Include the signature column
                (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
            FROM leave_approvals a
            JOIN users u ON a.approver_id = u.id
            JOIN positions p ON u.position_id = p.id
            WHERE a.leave_request_id = ?'
        );
        // Execute the query with the leave request ID parameter
        $stmt->execute([$leave_request_id, $leave_request_id]);
        // Return the fetched results
        return $stmt->fetchAll();
    }

    public function getDOfficePositions($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
            u.khmer_name AS approver_name, 
            u.profile_picture AS profile,
            p.name AS position_name,
            p.color AS position_color,
            a.signature,  -- Include the signature column
            (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
        FROM leave_approvals a
        JOIN users u ON a.approver_id = u.id
        JOIN positions p ON u.position_id = p.id
        WHERE a.leave_request_id = ?
        AND p.name = ?'  // Filter by the specific position name
        );
        // Execute the query with the leave request ID parameter and the position name
        $stmt->execute([$leave_request_id, $leave_request_id, 'អនុប្រធានការិយាល័យ']);
        // Return the fetched results
        return $stmt->fetchAll();
    }

    public function getHOfficePositions($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
            u.khmer_name AS approver_name, 
            u.profile_picture AS profile,
            p.name AS position_name,
            p.color AS position_color,
            a.signature,  -- Include the signature column
            (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
        FROM leave_approvals a
        JOIN users u ON a.approver_id = u.id
        JOIN positions p ON u.position_id = p.id
        WHERE a.leave_request_id = ?
        AND p.name = ?'  // Filter by the specific position name
        );
        // Execute the query with the leave request ID parameter and the position name
        $stmt->execute([$leave_request_id, $leave_request_id, 'ប្រធានការិយាល័យ']);
        // Return the fetched results
        return $stmt->fetchAll();
    }

    public function getHDepartmentPositions($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
            u.khmer_name AS approver_name, 
            u.profile_picture AS profile,
            p.name AS position_name,
            p.color AS position_color,
            a.signature,  -- Include the signature column
            (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
        FROM leave_approvals a
        JOIN users u ON a.approver_id = u.id
        JOIN positions p ON u.position_id = p.id
        WHERE a.leave_request_id = ?
        AND p.name = ?'  // Filter by the specific position name
        );
        // Execute the query with the leave request ID parameter and the position name
        $stmt->execute([$leave_request_id, $leave_request_id, 'ប្រធាននាយកដ្ឋាន']);
        // Return the fetched results
        return $stmt->fetchAll();
    }

    public function getDUnitPositions($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
            u.khmer_name AS approver_name, 
            u.profile_picture AS profile,
            p.name AS position_name,
            p.color AS position_color,
            a.signature,  -- Include the signature column
            (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
        FROM leave_approvals a
        JOIN users u ON a.approver_id = u.id
        JOIN positions p ON u.position_id = p.id
        WHERE a.leave_request_id = ?
        AND p.name = ?'  // Filter by the specific position name
        );
        // Execute the query with the leave request ID parameter and the position name
        $stmt->execute([$leave_request_id, $leave_request_id, 'អនុប្រធានអង្គភាព']);
        // Return the fetched results
        return $stmt->fetchAll();
    }
    
    public function getUnitPositions($leave_request_id)
    {
        // Query to get the approval details along with approver's information, position details, and signature
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
            u.khmer_name AS approver_name, 
            u.profile_picture AS profile,
            p.name AS position_name,
            p.color AS position_color,
            a.signature,  -- Include the signature column
            (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
        FROM leave_approvals a
        JOIN users u ON a.approver_id = u.id
        JOIN positions p ON u.position_id = p.id
        WHERE a.leave_request_id = ?
        AND p.name = ?'  // Filter by the specific position name
        );
        // Execute the query with the leave request ID parameter and the position name
        $stmt->execute([$leave_request_id, $leave_request_id, 'ប្រធានអង្គភាព']);
        // Return the fetched results
        return $stmt->fetchAll();
    }

    public function deleteLeaveRequest($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM leave_requests WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelLeaveRequest($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE leave_requests SET status = :status WHERE id = :id");
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
