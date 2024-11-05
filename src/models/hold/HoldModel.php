<?php

class HoldModel
{
    protected $tblholds = 'holds'; // The table where data is stored

    protected $tblholds_approvals = 'holds_approvals';

    protected $tblholds_attachment = 'hold_attachments';

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function createHoldRequest($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tblholds (user_id, approver_id, start_date, end_date, reason, duration, type, color, created_at) 
            VALUES (:user_id, :approver_id, :start_date, :end_date, :reason, :duration, :type, :color, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt->bindParam(':duration', $data['duration'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }

    public function getHoldsByUserId($offset, $limit)
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return []; // Return an empty array if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the SQL query using PDO
        $sql = "SELECT * FROM $this->tblholds WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset"; // Adjust the order as necessary

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch all the results
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHoldsCountById()
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return 0; // Return 0 if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the query to count the records for this specific user
        $query = "SELECT COUNT(*) AS total FROM $this->tblholds WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch and return the total count
        return $stmt->fetchColumn();
    }

    // Update an existing hold request
    public function updateHoldRequest($hold_id, $data)
    {
        $sql = "UPDATE $this->tblholds 
                SET start_date = :start_date, 
                    end_date = :end_date, 
                    reason = :reason, 
                    duration = :duration, 
                    approver_id = :approver_id 
                WHERE id = :hold_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':hold_id', $hold_id);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':approver_id', $data['approver_id']);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update hold request.');
        }
    }

    // Get a hold request by ID
    public function getHoldRequestById($hold_id)
    {
        $sql = "SELECT * FROM $this->tblholds WHERE id = :hold_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':hold_id', $hold_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch data as an associative array
    }

    public function getHoldCounts()
    {
        $query = "SELECT COUNT(*) FROM $this->tblholds ORDER BY id DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getHoldById($id)
    {
        // Prepare the SQL query to get the hold request, approval tracking steps, and attachments
        $sql = "SELECT h.*, 
        GROUP_CONCAT(DISTINCT a.file_name) AS attachments, 
        MAX(a.uploaded_at) AS latest_uploaded_at, -- Get the latest uploaded date
        ha.status AS approval_status, 
        ha.updated_at AS approved_at, 
        ha.approver_id, 
        ha.comments AS comment
 FROM $this->tblholds h
 JOIN $this->tblholds_approvals ha ON ha.hold_id = h.id
 LEFT JOIN $this->tblholds_attachment a ON h.id = a.hold_id
 WHERE h.user_id = :user_id AND h.id = :id 
 GROUP BY h.id, ha.status, ha.updated_at, ha.approver_id, ha.comments
 ORDER BY ha.id DESC"; // Still order by the latest approval

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the user ID and hold ID parameters
        $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            // Initialize UserModel and fetch department and office data
            $userModel = new User();
            $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
            $offices = $userModel->getAllOfficeApi($_SESSION['token']);

            // Create lookup tables for department and office names
            $departmentsById = [];
            if (!empty($departments['data'])) {
                foreach ($departments['data'] as $department) {
                    $departmentsById[$department['id']] = $department['departmentNameKh'];
                }
            }

            $officesById = [];
            if (!empty($offices['data'])) {
                foreach ($offices['data'] as $office) {
                    $officesById[$office['id']] = $office['officeNameKh'];
                }
            }

            // Fetch the submitter (user) data from the API for the first result
            $userApiResponse = $userModel->getUserByIdApi($results[0]['user_id'], $_SESSION['token']);
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $user = $userApiResponse['data'];

                // Add user (submitter) info to the first result (submitter details)
                $results[0]['user_name'] = trim(($user['lastNameKh'] ?? 'Unknown') . " " . ($user['firstNameKh'] ?? ''));
                $results[0]['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $results[0]['user_email'] = $user['email'] ?? 'Unknown';
                $results[0]['department_name'] = $departmentsById[$user['departmentId']] ?? 'Unknown Department';
                $results[0]['position_name'] = $user['position']['name'] ?? 'Unknown';
                $results[0]['office_name'] = $officesById[$user['officeId']] ?? 'Unknown Office';
                $results[0]['user_profile'] = !empty($user['image']) ? 'https://hrms.iauoffsa.us/images/' . $user['image'] : 'default-profile.png';
            }

            // For each approval, fetch approver details and department/office names
            foreach ($results as &$result) {
                if ($result['approver_id']) {
                    // Fetch the approver's data via API
                    $approverApiResponse = $userModel->getUserByIdApi($result['approver_id'], $_SESSION['token']);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];

                        // Add approver details to the result
                        $result['approver_name'] = trim(($approver['lastNameKh'] ?? 'Unknown') . " " . ($approver['firstNameKh'] ?? ''));
                        $result['profile'] = !empty($approver['image']) ? 'https://hrms.iauoffsa.us/images/' . $approver['image'] : 'default-profile.png';
                        $result['position_name'] = $approver['position']['name'] ?? 'Unknown';
                        $result['approver_department_name'] = $departmentsById[$approver['departmentId']] ?? 'Unknown Department';
                        $result['approver_office_name'] = $officesById[$approver['officeId']] ?? 'Unknown Office';
                    } else {
                        // If no approver info is available, add placeholders
                        $result['approver_name'] = 'Unknown';
                        $result['profile'] = 'default-profile.png';
                        $result['approver_department_name'] = 'Unknown Department';
                        $result['approver_office_name'] = 'Unknown Office';
                    }
                } else {
                    // If no approver is assigned
                    $result['approver_name'] = 'No approver assigned';
                    $result['profile'] = 'default-profile.png';
                    $result['approver_department_name'] = 'Unknown Department';
                    $result['approver_office_name'] = 'Unknown Office';
                }
            }

            return $results; // Return the populated results
        }

        return []; // Return an empty array if no results are found
    }

    public function getHoldByuserId($userId)
    {
        // Prepare the SQL query to get hold requests specific to that hold request
        $sql = "SELECT h.*, 
            GROUP_CONCAT(DISTINCT a.file_name) AS attachments, 
            MAX(a.uploaded_at) AS latest_uploaded_at
            FROM $this->tblholds h
            LEFT JOIN $this->tblholds_attachment a ON h.id = a.hold_id
            WHERE h.approver_id = :user_id 
            GROUP BY h.id
            ORDER BY h.id DESC"; // Changed to h.id for consistency

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cache array to store user data temporarily
        $userCache = [];

        foreach ($results as &$request) {
            $userModel = new User();
            $requestUserId = $request['user_id']; // Use a different variable to avoid conflict

            // Check cache first to avoid multiple API calls for the same user
            if (!isset($userCache[$requestUserId])) {
                // Retry mechanism to attempt API calls more than once
                $retryCount = 3;
                while ($retryCount > 0) {
                    $userApiResponse = $userModel->getUserByIdApi($requestUserId, $_SESSION['token']);

                    if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                        $userCache[$requestUserId] = $userApiResponse['data'];
                        break; // Success, exit retry loop
                    }

                    $retryCount--;
                    usleep(200000); // Wait 200ms between retries to reduce API load
                }
            }

            // Retrieve user data from cache if available
            $user = $userCache[$requestUserId] ?? null;

            if ($user) {
                $request['user_name'] = ($user['lastNameKh'] ?? '') . " " . ($user['firstNameKh'] ?? 'Unknown');
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = $user['image'] ?? 'default-profile.png';
            } else {
                // Default values if API call ultimately fails
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png';
                error_log("Failed to fetch user data for User ID $requestUserId after retries.");
            }
        }

        return $results;
    }

    public function insertManagerStatusToHoldsApprovals($hold_id, $approver_id, $status)
    {
        // Debugging
        if (is_array($hold_id)) {
            error_log('Error: $hold_id is an array, using the first element');
            $hold_id = $hold_id[0]; // Adjust as needed based on what $hold_id should be
        }
        if (is_array($approver_id)) {
            error_log('Error: $approver_id is an array, using the first element');
            $approver_id = $approver_id[0]; // Adjust as needed
        }
        if (is_array($status)) {
            error_log('Error: $status is an array, using the first element');
            $status = $status[0]; // Adjust as needed
        }

        $sql = "INSERT INTO holds_approvals (hold_id, approver_id, status)
                VALUES (:hold_id, :approver_id, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':hold_id' => $hold_id,
            ':approver_id' => $approver_id,
            ':status' => $status,
        ]);
    }

    public function deleteHold($id)
    {
        // Start a transaction to ensure both deletions are successful or neither
        $this->pdo->beginTransaction();

        try {
            // Delete the hold from tblholds
            $stmtHolds = $this->pdo->prepare("DELETE FROM {$this->tblholds} WHERE id = ?");
            $stmtHolds->execute([$id]);

            // Commit the transaction if both queries succeed
            $this->pdo->commit();

            // Return the number of affected rows from the tblholds delete
            return $stmtHolds->rowCount();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function saveHoldAttachment($data)
    {
        $sql = "INSERT INTO $this->tblholds_attachment (hold_id, file_name, file_path) VALUES (:hold_id, :file_name, :file_path)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':hold_id' => $data['hold_id'],
            ':file_name' => $data['file_name'],
            ':file_path' => $data['file_path']
        ]);
    }

    public function createAttachment($data)
    {
        // Prepare the SQL statement with the correct parameter bindings
        $stmt = $this->pdo->prepare("INSERT INTO $this->tblholds_attachment (hold_id, file_name, file_path, uploaded_at) VALUES (:hold_id, :file_name, :file_path, NOW())");

        // Execute the statement with the provided data
        $stmt->execute($data);
    }

    public function deleteAttachment($holdId, $filename)
    {
        // Prepare the SQL statement to delete the attachment from the transferout_attachments table
        $stmt = $this->pdo->prepare("DELETE FROM $this->tblholds_attachment WHERE hold_id = :holdId AND file_name = :filename");
        $stmt->execute([
            ':holdId' => $holdId,
            ':filename' => $filename
        ]);
    }
}
