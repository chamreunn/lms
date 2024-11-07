<?php

class BackworkModel
{
    protected $tblbackwork = 'backwork'; // The table where backwork data is stored
    protected $tblapproval = 'backwork_approval'; // Table for approval data
    protected $tblattachment = 'backwork_attachment'; // Table for attachment data

    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo; // Inject the PDO object into the class
    }

    public function getBackwork($offset, $limit)
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return []; // Return an empty array if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the SQL query using PDO
        $sql = "SELECT * FROM $this->tblbackwork WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit OFFSET :offset"; // Adjust the order as necessary

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

    public function getBackworkCountById()
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return 0; // Return 0 if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the query to count the records for this specific user
        $query = "SELECT COUNT(*) AS total FROM $this->tblbackwork WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch and return the total count
        return $stmt->fetchColumn();
    }

    public function getBackworkForEdit($id)
    {
        // Prepare the SQL query to get the hold request, approval tracking steps, and attachments
        $sql = "SELECT b.*, 
        GROUP_CONCAT(DISTINCT a.file_name) AS attachments, 
        MAX(a.uploaded_at) AS latest_uploaded_at, -- Get the latest uploaded date
        ba.status AS approval_status, 
        ba.updated_at AS approved_at, 
        ba.approver_id, 
        ba.comment AS comment
        FROM $this->tblbackwork b
        JOIN $this->tblapproval ba ON ba.back_id = b.id
        LEFT JOIN $this->tblattachment a ON b.id = a.back_id
        WHERE b.user_id = :user_id AND b.id = :id 
        GROUP BY b.id, ba.status, ba.updated_at, ba.approver_id, ba.comment
        ORDER BY ba.id DESC"; // Still order by the latest approval

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

    public function deleteBackworkById($id)
    {
        // Start a transaction to ensure both deletions are successful or neither
        $this->pdo->beginTransaction();

        try {
            // Delete the hold from tblholds
            $stmtHolds = $this->pdo->prepare("DELETE FROM {$this->tblbackwork} WHERE id = ?");
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

    public function Request($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tblbackwork (user_id, approver_id, date, reason, created_at) 
            VALUES (:user_id, :approver_id, :date, :reason, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':date', $data['date'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }

    public function saveAttachment($data)
    {
        $sql = "INSERT INTO $this->tblattachment (back_id, file_name, file_path) VALUES (:back_id, :file_name, :file_path)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':back_id' => $data['back_id'],
            ':file_name' => $data['file_name'],
            ':file_path' => $data['file_path']
        ]);
    }

    public function createAttachment($data)
    {
        // Prepare the SQL statement with the correct parameter bindings
        $stmt = $this->pdo->prepare("INSERT INTO $this->tblattachment (back_id, file_name, file_path, uploaded_at) VALUES (:back_id, :file_name, :file_path, NOW())");

        // Execute the statement with the provided data
        $stmt->execute($data);
    }

    public function deleteAttachment($backId, $filename)
    {
        // Prepare the SQL statement to delete the attachment from the transferout_attachments table
        $stmt = $this->pdo->prepare("DELETE FROM $this->tblattachment WHERE back_id = :back_id AND file_name = :filename");
        $stmt->execute([
            ':back_id' => $backId,
            ':filename' => $filename
        ]);
    }

    public function delegateManager($backwork, $userModel, $back_id, $user_id, $reason)
    {
        $levels = ['getEmailLeaderDOApi', 'getEmailLeaderHOApi', 'getEmailLeaderDDApi', 'getEmailLeaderHDApi'];
        $statuses = ['leave' => 'leave', 'mission' => 'mission'];

        foreach ($levels as $apiMethod) {
            $approver = $userModel->$apiMethod($user_id, $_SESSION['token']);
            if ($approver && !$userModel->isManagerOnLeaveToday($approver['ids']) && !$userModel->isManagerOnMission($approver['ids'])) {
                $backwork->managerStatus($back_id, $approver['ids'], 'pending');
                return; // Stop when a valid approver is found
            }

            // Insert status if the manager is on leave or mission
            $status = $userModel->isManagerOnLeaveToday($approver['ids']) ? $statuses['leave'] : $statuses['mission'];
            $backwork->managerStatus($back_id, $approver['ids'], $status);
        }
    }

    public function managerStatus($back_id, $approver_id, $status)
    {
        // Debugging
        if (is_array($back_id)) {
            error_log('Error: $back_id is an array, using the first element');
            $back_id = $back_id[0]; // Adjust as needed based on what $back_id should be
        }
        if (is_array($approver_id)) {
            error_log('Error: $approver_id is an array, using the first element');
            $approver_id = $approver_id[0]; // Adjust as needed
        }
        if (is_array($status)) {
            error_log('Error: $status is an array, using the first element');
            $status = $status[0]; // Adjust as needed
        }

        $sql = "INSERT INTO $this->tblapproval (back_id, approver_id, status)
                VALUES (:back_id, :approver_id, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':back_id' => $back_id,
            ':approver_id' => $approver_id,
            ':status' => $status,
        ]);
    }

    // Update an existing hold request
    public function updateBackWorkRequest($back_id, $data)
    {
        $sql = "UPDATE $this->tblbackwork 
                SET date = :date, 
                    reason = :reason, 
                    approver_id = :approver_id 
                WHERE id = :back_id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':back_id', $back_id);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->bindParam(':approver_id', $data['approver_id']);

        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception('Failed to update Back work request.');
        }
    }
}
