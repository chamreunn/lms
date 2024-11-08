<?php

class ResignModel
{
    protected $tblresign = 'resigned'; // The table where data is stored

    protected $resigned_approval = 'resigned_approval';

    protected $tblresigned_attachment = 'resigned_attachments';

    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function getResigns($offset, $limit)
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return []; // Return an empty array if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the SQL query using PDO
        $sql = "SELECT * FROM $this->tblresign WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset"; // Adjust the order as necessary

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

    // Get a resign request by ID
    public function getResignRequestById($resign_id)
    {
        $sql = "SELECT * FROM $this->tblresign WHERE id = :resign_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':resign_id', $resign_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC); // Fetch data as an associative array
    }

    // Update an existing hold request
    public function updateResignRequest($resign_id, $data)
    {
        $sql = "UPDATE $this->tblresign 
                SET workexperience = :workexperience,
                    reason = :reason, 
                    approver_id = :approver_id 
                WHERE id = :resign_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':resign_id', $resign_id);
        $stmt->bindParam(':workexperience', $data['workexperience']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':approver_id', $data['approver_id']);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update resign request.');
        }
    }

    public function getResignById($id)
    {
        // Prepare the SQL query to get the hold request and all approval tracking steps specific to that hold request
        $sql = "
            SELECT r.*,
            GROUP_CONCAT(DISTINCT a.file_name) AS attachments, 
            MAX(a.uploaded_at) AS latest_uploaded_at,
            ra.status AS approval_status, ra.approver_id, ra.comment AS comment
            FROM $this->resigned_approval ra
            JOIN $this->tblresign r ON ra.resign_id = r.id
            LEFT JOIN $this->tblresigned_attachment a ON r.id = a.resign_id
            WHERE r.user_id = :userId AND r.id =:id  
            GROUP BY r.id, ra.status, ra.updated_at, ra.approver_id, ra.comment
            ORDER BY ra.id DESC";
        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the user ID parameter
        $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            // Initialize UserModel
            $userModel = new User();

            // Fetch the submitter (user) data from the API
            $userApiResponse = $userModel->getUserByIdApi($results[0]['user_id'], $_SESSION['token']);
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $user = $userApiResponse['data'];

                // Add user info to the first result (submitter details)
                $results[0]['user_name'] = trim(($user['lastNameKh'] ?? 'Unknown') . " " . ($user['firstNameKh'] ?? ''));
                $results[0]['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $results[0]['user_email'] = $user['email'] ?? 'Unknown';
                $results[0]['department_name'] = $user['department']['name'] ?? 'Unknown';
                $results[0]['position_name'] = $user['position']['name'] ?? 'Unknown';
                $results[0]['office_name'] = $user['office']['name'] ?? 'Unknown';
                $results[0]['user_profile'] = !empty($user['image']) ? 'https://hrms.iauoffsa.us/images/' . $user['image'] : 'default-profile.png';
            }

            // For each approval, fetch approver details
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
                    } else {
                        $result['approver_name'] = 'Unknown';
                        $result['profile'] = 'default-profile.png'; // Default profile if no approver info is available
                    }
                } else {
                    $result['approver_name'] = 'No approver assigned';
                    $result['profile'] = 'default-profile.png'; // Default profile if no approver assigned
                }
            }

            return $results;
        }

        return [];
    }

    public function createResignRequest($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tblresign (user_id, approver_id, workexperience, reason, type, color, status, created_at) 
            VALUES (:user_id, :approver_id, :workexperience, :reason, :type, :color, :status, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':workexperience', $data['workexperience'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);
        $stmt->bindParam(':status', $data['status'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }

    public function saveResignAttachment($data)
    {
        $sql = "INSERT INTO $this->tblresigned_attachment (resign_id, file_name, file_path) VALUES (:resign_id, :file_name, :file_path)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':resign_id' => $data['resign_id'],
            ':file_name' => $data['file_name'],
            ':file_path' => $data['file_path']
        ]);
    }

    public function createAttachment($data)
    {
        // Prepare the SQL statement with the correct parameter bindings
        $stmt = $this->pdo->prepare("INSERT INTO $this->tblresigned_attachment (resign_id, file_name, file_path, uploaded_at) VALUES (:resign_id, :file_name, :file_path, NOW())");

        // Execute the statement with the provided data
        $stmt->execute($data);
    }

    public function deleteAttachment($resignId, $filename)
    {
        // Prepare the SQL statement to delete the attachment from the transferout_attachments table
        $stmt = $this->pdo->prepare("DELETE FROM $this->tblresigned_attachment WHERE resign_id = :resign_id AND file_name = :filename");
        $stmt->execute([
            ':resign_id' => $resignId,
            ':filename' => $filename
        ]);
    }

    public function deleteResignById($id)
    {
        // Start a transaction to ensure both deletions are successful or neither
        $this->pdo->beginTransaction();

        try {
            // Delete the hold from tblresign
            $stmtResign = $this->pdo->prepare("DELETE FROM {$this->tblresign} WHERE id = ?");
            $stmtResign->execute([$id]);

            $stmtResign = $this->pdo->prepare("DELETE FROM {$this->tblresigned_attachment} WHERE resign_id = ?");
            $stmtResign->execute([$id]);

            // Commit the transaction if both queries succeed
            $this->pdo->commit();

            // Return the number of affected rows from the tblholds delete
            return $stmtResign->rowCount();
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function insertManagerStatusToResignApprovals($resign_id, $approver_id, $status)
    {
        // Debugging
        if (is_array($resign_id)) {
            error_log('Error: $resign_id is an array, using the first element');
            $resign_id = $resign_id[0]; // Adjust as needed based on what $resign_id should be
        }
        if (is_array($approver_id)) {
            error_log('Error: $approver_id is an array, using the first element');
            $approver_id = $approver_id[0]; // Adjust as needed
        }
        if (is_array($status)) {
            error_log('Error: $status is an array, using the first element');
            $status = $status[0]; // Adjust as needed
        }

        $sql = "INSERT INTO resigned_approval (resign_id, approver_id, status)
                VALUES (:resign_id, :approver_id, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':resign_id' => $resign_id,
            ':approver_id' => $approver_id,
            ':status' => $status,
        ]);
    }

    public function getResignCountById()
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return 0; // Return 0 if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the query to count the records for this specific user
        $query = "SELECT COUNT(*) AS total FROM $this->tblresign WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch and return the total count
        return $stmt->fetchColumn();
    }

    public function getResignByuserId($userId)
    {
        // Prepare the SQL query to get hold requests specific to that hold request
        $sql = "SELECT h.*, 
            GROUP_CONCAT(DISTINCT a.file_name) AS attachments, 
            MAX(a.uploaded_at) AS latest_uploaded_at
            FROM $this->tblresign h
            LEFT JOIN $this->tblresigned_attachment a ON h.id = a.resign_id
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
                $request['profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png';
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

            // Ensure 'attachments' is always a string for consistent handling in the view
            $request['attachments'] = $request['attachments'] ?? '';
        }

        return $results;
    }
}
