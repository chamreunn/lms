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
                SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id
                JOIN positions p ON u.position_id = p.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                WHERE lr.head_office IN (?, ? ,?) 
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
                'Pending',
                'Pending',         // Status to filter
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User', 'Deputy Head Of Office', 'Head Of Office', // Roles
                'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', // Position names
                $approver_id        // Exclude the approver's own requests
            ]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return [];
        }
    }
    public function submitApproval($leave_request_id, $approver_id, $status, $remarks)
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

        // Insert the approval record
        $stmt = $this->pdo->prepare(
            'INSERT INTO leave_approvals (leave_request_id, approver_id, status, remarks, updated_at)
            VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$leave_request_id, $approver_id, $status, $remarks]);

        // Update leave request status based on the approval chain
        $this->updateLeaveRequestStatus($leave_request_id, $status);
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
                'User', 'Deputy Head Of Office', 'Head Of Office',// Roles
                'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', // Position names
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
