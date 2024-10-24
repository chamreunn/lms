<?php

class HoldModel
{
    protected $tblholds = 'holds'; // The table where data is stored

    protected $tblholds_approvals = 'holds_approvals';

    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function createHoldRequest($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tblholds (user_id, approver_id, start_date, end_date, reason, attachment, duration, type, color, created_at) 
            VALUES (:user_id, :approver_id, :start_date, :end_date, :reason, :attachment, :duration, :type, :color, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);
        $stmt->bindParam(':attachment', $data['attachment'], PDO::PARAM_STR);
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
                    attachment = :attachment, 
                    duration = :duration, 
                    approver_id = :approver_id 
                WHERE id = :hold_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':hold_id', $hold_id);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':attachment', $data['attachment']);
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
        // Prepare the SQL query to get the hold request and all approval tracking steps specific to that hold request
        $sql = "
            SELECT h.*, ha.status AS approval_status, ha.approved_at, ha.approver_id, ha.comments AS comment
            FROM $this->tblholds_approvals ha
            JOIN $this->tblholds h ON ha.hold_id = h.id
            WHERE h.user_id = :userId AND h.id =:id ORDER BY ha.id DESC
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
}
