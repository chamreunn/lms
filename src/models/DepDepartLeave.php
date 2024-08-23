<?php
require_once 'config/database.php';
class DepDepartLeave
{
    private $pdo;

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
            (user_id, leave_type_id, leave_type, start_date, end_date, remarks, num_date, attachment, signature, status, dhead_department, created_at) 
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
                u.department_id, 
                u.position_id, 
                u.profile_picture as user_profile,
                d.name as department_name,
                p.name as position_name,
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         JOIN users u ON lr.user_id = u.id
         JOIN departments d ON u.department_id = d.id
         JOIN positions p ON u.position_id = p.id
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
            $result['ddepartment'] = $this->getDDepartmentPositions($result['id']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id']);
            $result['dunit'] = $this->getDUnitPositions($result['id']);
            $result['unit'] = $this->getUnitPositions($result['id']);
        }

        return $results;
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

    public function getDDepartmentPositions($leave_request_id)
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
        $stmt->execute([$leave_request_id, $leave_request_id, 'អនុប្រធាននាយកដ្ឋាន']);
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

    public function getPendingRequestsForApprover($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to get pending requests for users in the same office or department as the approver
            $stmt = $this->pdo->prepare('
                SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color, lt.name AS leavetype, lt.color AS color
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id
                JOIN positions p ON u.position_id = p.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                WHERE lr.head_office IN (?, ?) 
                AND lr.dhead_department = ?
                AND (u.office_id = ? OR u.department_id = ?)
                AND u.role IN (?, ?, ?)
                AND p.name IN (?, ?, ?, ?)
                AND lr.user_id != ?
            ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                'Approved',
                'Rejected',
                'Pending',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',
                'Deputy Head Of Office',
                'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល',
                'អនុប្រធានការិយាល័យ',
                'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function countPendingRequestsForApprover($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to count pending requests for users in the same office or department as the approver
            $stmt = $this->pdo->prepare('
            SELECT COUNT(lr.id) AS pending_count
            FROM leave_requests lr
            JOIN users u ON lr.user_id = u.id
            JOIN positions p ON u.position_id = p.id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.head_office IN (?, ?) 
            AND lr.dhead_department = ?
            AND (u.office_id = ? OR u.department_id = ?)
            AND u.role IN (?, ?, ?)
            AND p.name IN (?, ?, ?, ?)
            AND lr.user_id != ?
        ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                'Approved',
                'Rejected',
                'Pending',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',
                'Deputy Head Of Office',
                'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល',
                'អនុប្រធានការិយាល័យ',
                'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            // Fetch the count of pending requests
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['pending_count'] ?? 0;
        } else {
            return 0;
        }
    }

    public function countApprovedRequestsForApprover($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to count pending requests for users in the same office or department as the approver
            $stmt = $this->pdo->prepare('
            SELECT COUNT(lr.id) AS pending_count
            FROM leave_requests lr
            JOIN users u ON lr.user_id = u.id
            JOIN positions p ON u.position_id = p.id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.head_office IN (?, ?) 
            AND lr.dhead_department = ?
            AND (u.office_id = ? OR u.department_id = ?)
            AND u.role IN (?, ?, ?)
            AND p.name IN (?, ?, ?, ?)
            AND lr.user_id != ?
        ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                'Approved',
                'Rejected',
                'Approved',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',
                'Deputy Head Of Office',
                'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល',
                'អនុប្រធានការិយាល័យ',
                'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            // Fetch the count of pending requests
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['pending_count'] ?? 0;
        } else {
            return 0;
        }
    }

    public function countRejectedRequestsForApprover($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to count pending requests for users in the same office or department as the approver
            $stmt = $this->pdo->prepare('
            SELECT COUNT(lr.id) AS pending_count
            FROM leave_requests lr
            JOIN users u ON lr.user_id = u.id
            JOIN positions p ON u.position_id = p.id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.head_office IN (?, ?) 
            AND lr.dhead_department = ?
            AND (u.office_id = ? OR u.department_id = ?)
            AND u.role IN (?, ?, ?)
            AND p.name IN (?, ?, ?, ?)
            AND lr.user_id != ?
        ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                'Approved',
                'Rejected',
                'Rejected',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',
                'Deputy Head Of Office',
                'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល',
                'អនុប្រធានការិយាល័យ',
                'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            // Fetch the count of pending requests
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['pending_count'] ?? 0;
        } else {
            return 0;
        }
    }

    public function submitApproval($leave_request_id, $approver_id, $status, $remarks, $signaturePath)
    {
        // Fetch the role of the approver
        $stmt = $this->pdo->prepare(
            'SELECT role FROM users WHERE id = ?'
        );
        $stmt->execute([$approver_id]);
        $approverRole = $stmt->fetchColumn();

        if ($approverRole === false) {
            throw new Exception("Invalid approver ID: $approver_id");
        }

        // Insert the approval record with the signature
        $stmt = $this->pdo->prepare(
            'INSERT INTO leave_approvals (leave_request_id, approver_id, status, remarks, signature, updated_at)
        VALUES (?, ?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$leave_request_id, $approver_id, $status, $remarks, $signaturePath]);

        // Get the updated_at timestamp
        $stmt = $this->pdo->prepare(
            'SELECT updated_at FROM leave_approvals WHERE leave_request_id = ? AND approver_id = ? ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute([$leave_request_id, $approver_id]);
        $updatedAt = $stmt->fetchColumn();

        if ($updatedAt === false) {
            throw new Exception("Unable to fetch updated_at timestamp for approval.");
        }

        // Update leave request status based on the approval chain
        $this->updateLeaveRequestStatus($leave_request_id, $status);

        return $updatedAt; // Return the updated_at timestamp
    }

    private function updateLeaveRequestStatus($leave_request_id, $latestStatus)
    {
        // Fetch the current status of the leave request
        $stmt = $this->pdo->prepare(
            'SELECT dhead_office, num_date FROM leave_requests WHERE id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $leaveRequest = $stmt->fetch();

        if (!$leaveRequest) {
            throw new Exception("Invalid leave request ID: $leave_request_id");
        }

        $currentStatus = $leaveRequest['dhead_office'];
        $duration = $leaveRequest['num_date'];

        // If the current status is already 'Rejected', no further updates are needed
        if ($currentStatus == 'Rejected') {
            return;
        }

        // Determine the number of required approvals based on the duration of the leave request
        $requiredApprovals = $duration < 3 ? 4 : 6;

        // Determine the new status based on the latest approval status
        $newStatus = ($latestStatus == 'Rejected') ? 'Rejected' : 'Approved';

        // Update the leave request status
        $stmt = $this->pdo->prepare(
            'UPDATE leave_requests SET dhead_department = ? WHERE id = ?'
        );
        $stmt->execute([$newStatus, $leave_request_id]);
    }

    public function gethapproved($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to get approved requests for users in the same office or department as the approver
            // and who have the specified positions, including additional user details
            $stmt = $this->pdo->prepare('
            SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color, la.approver_id,la.updated_at AS approved_at, au.email AS approver_email, au.khmer_name AS approver_name
            FROM leave_requests lr
            JOIN users u ON lr.user_id = u.id
            JOIN positions p ON u.position_id = p.id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN leave_approvals la ON lr.id = la.leave_request_id
            JOIN users au ON la.approver_id = au.id
            WHERE la.approver_id = ?
            AND lr.dhead_department = ?
            AND (u.office_id = ? OR u.department_id = ?)
            AND u.role IN (?, ?, ?)
            AND p.name IN (?, ?, ?, ?)
            AND lr.user_id != ?
        ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                $approver_id,       // Approver ID
                'Approved',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',
                'Deputy Head Of Office',
                'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល',
                'អនុប្រធានការិយាល័យ',
                'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }

    public function getUserApproveByTeam($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch();

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];
            // Query to get pending requests for users in the same office or department as the approver
            // and who have the specified positions, including additional user details
            $stmt = $this->pdo->prepare('
            SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color AS leavetype_color
            FROM leave_requests lr 
            JOIN users u ON lr.user_id = u.id 
            JOIN positions p ON u.position_id = p.id 
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = ? 
            AND u.office_id = ? 
            AND u.department_id = ?
            AND u.role = ? 
            AND p.name IN (?, ?, ?, ?) 
            AND lr.user_id != ?
        ');
            $stmt->execute(['Approved', $office_id, $department_id, 'User', 'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', $approver_id]);
            return $stmt->fetchAll();
        } else {
            return [];
        }
    }
}
