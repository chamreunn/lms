<?php
require_once 'config/database.php';
class LeaveApproval
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function submitApproval($leave_request_id, $approver_id, $status, $remarks, $signaturePath)
    {
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
            'UPDATE leave_requests SET dhead_office = ? WHERE id = ?'
        );
        $stmt->execute([$newStatus, $leave_request_id]);
    }

    public function getPending($approver_id)
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
            SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color, lt.name AS leavetype
            FROM leave_requests lr 
            JOIN users u ON lr.user_id = u.id 
            JOIN positions p ON u.position_id = p.id 
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.dhead_office = ? 
            AND u.office_id = ? 
            AND u.department_id = ?
            AND u.role = ? 
            AND p.name IN (?, ?) 
            AND lr.user_id != ?
        ');
            $stmt->execute(['Pending', $office_id, $department_id, 'User', 'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', $approver_id]);
            return $stmt->fetchAll();
        } else {
            return [];
        }
    }

    public function getAllLeaveRequests()
    {
        // Fetch all leave requests from the database
        $stmt = $this->pdo->prepare('SELECT * FROM leave_requests 
        WHERE  dhead_office = ?  
        AND position IN (?, ?)
        AND office = ?
        AND department = ?
        AND user_id != ?
        ');
        $stmt->execute(['Pending', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', $_SESSION['officeName'], $_SESSION['departmentName'], $_SESSION['user_id']]);
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data for each leave request using the API
        foreach ($leaveRequests as &$request) {
            // Get user data from API
            $userApiResponse = $userModel->getUserByIdApi($request['user_id'], $_SESSION['token']);

            // Debug: Log the API response for each user
            error_log("API Response for User ID " . $request['user_id'] . ": " . print_r($userApiResponse, true));

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $request['user_name'] = $user['lastNameKh'] . " " .$user['firstNameKh'] ?? 'Unknown';
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png'; // Use a default profile image if API fails

                // Debug: Log the API failure case
                error_log("API call failed for User ID " . $request['user_id'] . ". Setting default values.");
            }
        }

        // Debug: Log the final leave requests array
        error_log("Final leave requests data: " . print_r($leaveRequests, true));

        return $leaveRequests; // Return the modified leave requests
    }

    public function countPendingRequestsForApprover()
    {
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) as count
        FROM leave_requests 
        WHERE dhead_office = ?  
        AND position IN (?, ?)
        AND office = ?
        AND department = ?
        AND user_id != ?
    ');

        // Execute the query with the parameters
        $stmt->execute([
            'Pending',
            'មន្រ្តីលក្ខន្តិកៈ',
            'ភ្នាក់ងាររដ្ឋបាល',
            $_SESSION['officeName'],
            $_SESSION['departmentName'],
            $_SESSION['user_id']
        ]);
        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log the count of approved leave requests
        error_log("Count of approved leave requests: " . print_r($result, true));

        // Return the count
        return $result['count'];
    }

    public function approvedCount()
    {
        // Prepare the SQL query to count the approved leave requests
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) as count
        FROM leave_requests 
        WHERE status = ?  
        AND position IN (?, ?)
        AND office = ?
        AND department = ?
        AND user_id != ?
    ');

        // Execute the query with the parameters
        $stmt->execute([
            'Approved',
            'មន្រ្តីលក្ខន្តិកៈ',
            'ភ្នាក់ងាររដ្ឋបាល',
            $_SESSION['officeName'],
            $_SESSION['departmentName'],
            $_SESSION['user_id']
        ]);

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log the count of approved leave requests
        error_log("Count of approved leave requests: " . print_r($result, true));

        // Return the count
        return $result['count'];
    }

    public function rejectedCount()
    {
        $stmt = $this->pdo->prepare('
        SELECT COUNT(*) as count
        FROM leave_requests 
        WHERE dhead_office = ?  
        AND position IN (?, ?)
        AND office = ?
        AND department = ?
        AND user_id != ?
    ');

        // Execute the query with the parameters
        $stmt->execute([
            'Rejected',
            'មន្រ្តីលក្ខន្តិកៈ',
            'ភ្នាក់ងាររដ្ឋបាល',
            $_SESSION['officeName'],
            $_SESSION['departmentName'],
            $_SESSION['user_id']
        ]);
        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Debug: Log the count of approved leave requests
        error_log("Count of approved leave requests: " . print_r($result, true));

        // Return the count
        return $result['count'];
    }

    public function allCount($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch();

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];

            // Query to count pending requests for users in the same office or department as the approver
            // and who have the specified positions, including additional user details
            $stmt = $this->pdo->prepare('
            SELECT COUNT(*) as approved_count
            FROM leave_requests lr 
            JOIN users u ON lr.user_id = u.id 
            JOIN positions p ON u.position_id = p.id 
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.dhead_office IN (?, ?, ?)
            AND u.office_id = ? 
            AND u.department_id = ?
            AND u.role = ? 
            AND p.name IN (?, ?) 
            AND lr.user_id != ?
        ');
            $stmt->execute(['Rejected', 'Approved', 'Pending', $office_id, $department_id, 'User', 'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', $approver_id]);
            $result = $stmt->fetch();
            return $result['approved_count'];
        } else {
            return 0;
        }
    }

    public function getdhapproved($approver_id)
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
                SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color, la.approver_id, la.updated_at AS approved_at, au.email AS approver_email, au.khmer_name AS approver_name
                FROM leave_requests lr
                JOIN users u ON lr.user_id = u.id
                JOIN positions p ON u.position_id = p.id
                JOIN leave_types lt ON lr.leave_type_id = lt.id
                JOIN leave_approvals la ON lr.id = la.leave_request_id
                JOIN users au ON la.approver_id = au.id
                WHERE la.approver_id = ?
                AND lr.dhead_office = ?
                AND (u.office_id = ? OR u.department_id = ?)
                AND u.role = ?
                AND p.name IN (?, ?)
                AND lr.user_id != ?
            ');

            // Execute the query with appropriate parameters
            $stmt->execute([
                $approver_id,       // Approver ID
                'Approved',         // dhead_department status
                $office_id,         // Office ID
                $department_id,     // Department ID
                'User',             // Role
                'មន្ត្រីលក្ខខន្តិកៈ',
                'ភ្នាក់ងាររដ្ឋបាល', // Position names
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
            SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color AS leave_type_color
            FROM leave_requests lr 
            JOIN users u ON lr.user_id = u.id 
            JOIN positions p ON u.position_id = p.id 
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = ? 
            AND u.office_id = ? 
            AND u.department_id = ?
            AND u.role = ? 
            AND p.name IN (?, ?) 
            AND lr.user_id != ?
        ');
            $stmt->execute(['Approved', $office_id, $department_id, 'User', 'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', $approver_id]);
            return $stmt->fetchAll();
        } else {
            return [];
        }
    }
}
