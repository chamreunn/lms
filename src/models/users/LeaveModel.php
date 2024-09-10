<?php

class LeaveModel
{
    private $pdo;

    private $table_name = "leave_requests";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function create($user_id, $user_email, $leave_type_id, $position, $office, $department, $leave_type_name, $start_date, $end_date, $remarks, $duration_days, $attachment, $signature)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("
            INSERT INTO $this->table_name (user_id, uemails, leave_type_id, position, office, department, leave_type, start_date, end_date, remarks, num_date, attachment, signature, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user_id,
            $user_email,
            $leave_type_id,
            $position,
            $office,
            $department,
            $leave_type_name,
            $start_date,
            $end_date,
            $remarks,
            $duration_days,
            $attachment,
            $signature,
            'Pending'
        ]);

        // Return the ID of the newly created leave request
        return $this->pdo->lastInsertId();
    }

    public function getRequestsByUserId($user_id)
    {
        // Prepare and execute the SQL query (remove JOINs with users, departments, and positions)
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, 
            lt.name as leave_type_name, 
            lt.duration, 
            lt.color
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.user_id = ?'
        );
        $stmt->execute([$user_id]);

        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Add user information and additional data to each result
        foreach ($results as &$result) {
            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $result['user_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $result['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $result['user_email'] = $user['email'] ?? 'Unknown';
                $result['department_name'] = $user['department']['name'] ?? 'Unknown';
                $result['position_name'] = $user['position']['name'] ?? 'Unknown';
                $result['user_profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $result['user_name'] = 'Unknown';
                $result['dob'] = 'Unknown';
                $result['user_email'] = 'Unknown';
                $result['department_name'] = 'Unknown';
                $result['position_name'] = 'Unknown';
                $result['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }

            // Fetch additional data using existing methods
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id'], $_SESSION['token']);
            $result['doffice'] = $this->getDOfficePositions($result['id']);
            $result['hoffice'] = $this->getHOfficePositions($result['id']);
            $result['ddepartment'] = $this->getDDepartmentPositions($result['id']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id']);
            $result['dunit'] = $this->getDUnitPositions($result['id']);
            $result['unit'] = $this->getUnitPositions($result['id']);
        }

        return $results;
    }

    // New method to get filtered requests
    public function getRequestsByFilters($user_id, $filters)
    {
        // Base SQL query (remove JOINs with users, departments, positions, and offices)
        $sql = 'SELECT lr.*, 
                   lt.name as leave_type_name, 
                   lt.duration, 
                   lt.color
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
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
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Add user information and additional data to each result
        foreach ($results as &$result) {
            // Fetch user data from API using the user_id from the result
            $userApiResponse = $userModel->getUserByIdApi($result['user_id'], $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $result['user_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $result['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $result['user_email'] = $user['email'] ?? 'Unknown';
                $result['department_name'] = $user['department']['name'] ?? 'Unknown';
                $result['position_name'] = $user['position']['name'] ?? 'Unknown';
                $result['office_name'] = $user['office']['name'] ?? 'Unknown';
                $result['user_profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $result['user_name'] = 'Unknown';
                $result['dob'] = 'Unknown';
                $result['user_email'] = 'Unknown';
                $result['department_name'] = 'Unknown';
                $result['position_name'] = 'Unknown';
                $result['office_name'] = 'Unknown';
                $result['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }

            // Fetch additional data using existing methods
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id'], $_SESSION['token']);
            $result['doffice'] = $this->getDOfficePositions($result['id']);
            $result['hoffice'] = $this->getHOfficePositions($result['id']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id']);
            $result['dunit'] = $this->getDUnitPositions($result['id']);
            $result['unit'] = $this->getUnitPositions($result['id']);
        }

        return $results;
    }

    public function pendingCount($user_id)
    {
        // Prepare the SQL statement
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as count FROM ' . $this->table_name . ' WHERE user_id = ?');

        // Execute the query with the parameters
        $stmt->execute([$user_id]);

        // Fetch the result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Log the count of pending leave requests
        error_log("Count of pending leave requests: " . print_r($result, true));

        // Return the count
        return $result['count'];
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

    public function getRequestById($leave_id, $token)
    {
        // Query to fetch the leave request and related data, including the attachment requirement
        $stmt = $this->pdo->prepare(
            'SELECT lr.*, 
                lt.name as leave_type_name, 
                lt.duration, 
                lt.color, 
                lt.attachment_required AS attRequired, 
                lr.department AS department_name, 
                lr.office AS office_name, 
                lr.position AS position_name
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.id = ?'
        );
        $stmt->execute([$leave_id]);
        $leaveRequest = $stmt->fetch();

        if ($leaveRequest) {
            // ពិនិត្យមើលថាតើការភ្ជាប់ឯកសារត្រូវការឬអត់ និងវាមាននៅក្នុងសំណើចាកចេញឬអត់
            if ($leaveRequest['attRequired'] === 'Yes') {
                if (empty($leaveRequest['attachment'])) {
                    // ដោះស្រាយករណីដែលត្រូវការភ្ជាប់ឯកសារ ប៉ុន្តែបាត់បង់
                    error_log("ការភ្ជាប់ឯកសារត្រូវការសម្រាប់សំណើចាកចេញ ID: $leave_id ប៉ុន្តែមិនមាន។");
                    $leaveRequest['attachment_error'] = "ត្រូវការភ្ជាប់ឯកសារសម្រាប់ប្រភេទច្បាប់នេះ។";
                } else {
                    $leaveRequest['attachment_error'] = null; // គ្មានបញ្ហាថាត្រូវការភ្ជាប់ឯកសារនេះ
                }
            } else {
                $leaveRequest['attachment_error'] = null; // មិនត្រូវការភ្ជាប់ឯកសារនេះទេ
            }
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($leaveRequest['user_id'], $token);

            if ($userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $userData = $userApiResponse['data'];
                // Add user information to the leave request array
                $leaveRequest['khmer_name'] = $userData['lastNameKh'] . " " . $userData['firstNameKh'] ?? null;
                $leaveRequest['phone_number'] = $userData['phoneNumber'] ?? null;
                $leaveRequest['email'] = $userData['email'] ?? null;
                $leaveRequest['dob'] = $userData['date_of_birth'] ?? null;
                $leaveRequest['deputy_head_name'] = $userData['deputy_head_name'] ?? null;
            } else {
                // Handle API error or missing data
                error_log("Failed to fetch user data for leave request ID: $leave_id");
                $leaveRequest['khmer_name'] = null;
                $leaveRequest['phone_number'] = null;
                $leaveRequest['dob'] = null;
                $leaveRequest['deputy_head_name'] = null;
            }

            // Optional: Add logic to fetch approvals, office positions, etc.
            $leaveRequest['approvals'] = $this->getApprovalsByLeaveRequestId($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['doffice'] = $this->getDOfficePositions($leaveRequest['id']);
            $leaveRequest['hoffice'] = $this->getHOfficePositions($leaveRequest['id'], $_SESSION['token']);
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

    public function getApprovalsByLeaveRequestId($leave_request_id, $token)
    {
        // Query to get approval details without fetching user and position data directly
        $stmt = $this->pdo->prepare(
            'SELECT a.*, 
                a.signature,  -- Include the signature column
                (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
         FROM leave_approvals a
         WHERE a.leave_request_id = ?'
        );

        // Execute the query with the leave request ID parameter
        $stmt->execute([$leave_request_id, $leave_request_id]);
        $approvals = $stmt->fetchAll();

        // Check if an attachment is required for the leave type
        $attachmentStmt = $this->pdo->prepare(
            'SELECT lt.attachment_required 
         FROM leave_requests lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.id = ?'
        );
        $attachmentStmt->execute([$leave_request_id]);
        $attachmentRequired = $attachmentStmt->fetchColumn();

        // If attachment is required, fetch attachment data
        if ($attachmentRequired === 'YES') {
            $attachmentData = $this->fetchAttachmentsByLeaveRequestId($leave_request_id);
            if (empty($attachmentData)) {
                // Log error or handle missing attachment
                error_log("Attachment is required but not found for leave request ID: $leave_request_id");
                // Optionally, you could return an error response or adjust the approvals data to reflect this issue.
            }
        }

        $userModel = new User(); // Assuming User class is responsible for API calls to fetch user data

        // Fetch approver information using API
        foreach ($approvals as &$approval) {
            $approverId = $approval['approver_id'];

            // Use the API to get the user details
            $userApiResponse = $userModel->getUserByIdApi($approverId, $token);

            if ($userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $userData = $userApiResponse['data'];
                $approval['approver_name'] = $userData['lastNameKh'] . " " . $userData['firstNameKh'] ?? null;
                $approval['profile'] = 'https://hrms.iauoffsa.us/images/' . $userData['image'] ?? null;
                $approval['position_name'] = $userData['position']['name'] ?? null; // Adjust based on your API response structure
                $approval['position_color'] = $userData['position']['color'] ?? null; // Adjust based on your API response structure
            } else {
                // Handle API error or missing data
                error_log("Failed to fetch user data for approver ID: $approverId");
                $approval['approver_name'] = null;
                $approval['profile'] = null;
                $approval['position_name'] = null;
                $approval['position_color'] = null;
            }
        }

        return $approvals;
    }

    public function leaveUserApproved($token)
    {
        $today = date('Y-m-d'); // Get today's date

        // Fetch office and department from the session
        $sessionOffice = $_SESSION['officeName'];
        $sessionDepartment = $_SESSION['departmentName'];

        // Query to get leave requests that are approved and include today's date
        $stmt = $this->pdo->prepare(
            'SELECT lr.id as leave_request_id, lr.user_id, lr.start_date, lr.end_date, lr.num_date,
                lr.office, lr.department, lr.status
         FROM leave_requests lr
         WHERE ? BETWEEN lr.start_date AND lr.end_date
           AND lr.status = ?
           AND lr.office = ?
           AND lr.department = ?'
        );
        $stmt->execute([$today, 'Approved', $sessionOffice, $sessionDepartment]);
        $leaveRequests = $stmt->fetchAll();

        if (empty($leaveRequests)) {
            return []; // Return an empty array if no approved leave requests are found
        }

        $userModel = new User(); // Assuming User class is responsible for API calls to fetch user data
        $approvals = [];

        foreach ($leaveRequests as $request) {
            $userId = $request['user_id'];
            $userApiResponse = $userModel->getUserByIdApi($userId, $token);

            if ($userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $userData = $userApiResponse['data'];
                $approvals[] = [
                    'leave_request_id' => $request['leave_request_id'],
                    'user_name' => $userData['lastNameKh'] . " " . $userData['firstNameKh'] ?? null,
                    'profile' => 'https://hrms.iauoffsa.us/images/' . $userData['image'] ?? null,
                    'position_name' => $userData['position']['name'] ?? null,
                    'position_color' => $userData['position']['color'] ?? null,
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'num_date' => $request['num_date'],
                ];
            } else {
                // Handle API error or missing data
                error_log("Failed to fetch user data for user ID: $userId");
                $approvals[] = [
                    'leave_request_id' => $request['leave_request_id'],
                    'user_name' => null,
                    'profile' => null,
                    'position_name' => null,
                    'position_color' => null,
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'num_date' => $request['num_date'],
                ];
            }
        }

        return $approvals;
    }

    // Function to fetch attachment data by leave request ID
    private function fetchAttachmentsByLeaveRequestId($leave_request_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leave_attachments WHERE leave_request_id = ?');
        $stmt->execute([$leave_request_id]);
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

    public function updateAttachment($leave_id, $attachmentUrl)
    {
        try {
            // Prepare the SQL statement
            $stmt = $this->pdo->prepare(
                'UPDATE leave_requests
                 SET attachment = :attachmentUrl
                 WHERE id = :leave_id'
            );

            // Bind the parameters
            $stmt->bindParam(':attachmentUrl', $attachmentUrl);
            $stmt->bindParam(':leave_id', $leave_id, PDO::PARAM_INT);

            // Execute the statement
            return $stmt->execute();
        } catch (PDOException $e) {
            // Log error message
            error_log("Database Error: " . $e->getMessage());
            return false;
        }
    }
}
