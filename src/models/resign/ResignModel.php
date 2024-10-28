<?php

class ResignModel
{
    protected $tblresign = 'resigned'; // The table where data is stored

    protected $resigned_approval = 'resigned_approval';

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function getResignByUserId($offset, $limit)
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
                    attachment = :attachment, 
                    approver_id = :approver_id 
                WHERE id = :resign_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':resign_id', $resign_id);
        $stmt->bindParam(':workexperience', $data['workexperience']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':attachment', $data['attachment']);
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
            SELECT r.*, ra.status AS approval_status, ra.approver_id, ra.comment AS comment
            FROM $this->resigned_approval ra
            JOIN $this->tblresign r ON ra.resign_id = r.id
            WHERE r.user_id = :userId AND r.id =:id ORDER BY ra.id DESC
        ";

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
        $sql = "INSERT INTO $this->tblresign (user_id, approver_id, workexperience, reason, attachment, created_at) 
            VALUES (:user_id, :approver_id, :workexperience, :reason, :attachment, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':workexperience', $data['workexperience'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt->bindParam(':attachment', $data['attachment'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }

    public function deleteResignById($id)
    {
        // Start a transaction to ensure both deletions are successful or neither
        $this->pdo->beginTransaction();

        try {
            // Delete the hold from tblholds
            $stmtResign = $this->pdo->prepare("DELETE FROM {$this->tblresign} WHERE id = ?");
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
}
