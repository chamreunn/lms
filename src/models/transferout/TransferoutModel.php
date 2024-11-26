<?php

class TransferoutModel
{
    protected $tbltransferout = 'transferout'; // The table where data is stored

    protected $transferout_approval = 'transferout_approval';

    protected $transferout_attachment = 'transferout_attachments';

    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo; // Inject the global PDO object
    }

    public function createTransferout($data)
    {
        // Prepare the SQL query using PDO
        $sql = "INSERT INTO $this->tbltransferout (user_id, approver_id, from_department, to_department, from_office, to_office,  type, color, reason, created_at) 
            VALUES (:user_id, :approver_id, :from_department, :to_department, :from_office, :to_office, :type, :color, :reason, NOW())";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the parameters to the prepared statement
        $stmt->bindParam(':user_id', $data['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':approver_id', $data['approver_id'], PDO::PARAM_INT);
        $stmt->bindParam(':from_department', $data['from_department'], PDO::PARAM_STR);
        $stmt->bindParam(':to_department', $data['to_department'], PDO::PARAM_STR);
        $stmt->bindParam(':from_office', $data['from_office'], PDO::PARAM_STR);
        $stmt->bindParam(':to_office', $data['to_office'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindParam(':color', $data['color'], PDO::PARAM_STR);
        $stmt->bindParam(':reason', $data['reason'], PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Return the last inserted hold_id
        return $this->pdo->lastInsertId();
    }

    public function createAttachment($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO transferout_attachments (transferout_id, filename, created_at) VALUES (:transferout_id, :filename, NOW())");
        $stmt->execute($data);
    }

    public function insertManagerStatusAndUpdateApprover($transferout_id, $approver_id, $status)
    {
        // Debugging checks
        if (is_array($transferout_id)) {
            error_log('Error: $transferout_id is an array, using the first element');
            $transferout_id = $transferout_id[0]; // Adjust as needed
        }
        if (is_array($approver_id)) {
            error_log('Error: $approver_id is an array, using the first element');
            $approver_id = $approver_id[0]; // Adjust as needed
        }
        if (is_array($status)) {
            error_log('Error: $status is an array, using the first element');
            $status = $status[0]; // Adjust as needed
        }

        try {
            // Begin transaction
            $this->pdo->beginTransaction();

            // Insert into holds_approvals
            $insertSql = "INSERT INTO transferout_approval (transferout_id, approver_id, status)
                      VALUES (:transferout_id, :approver_id, :status)";
            $insertStmt = $this->pdo->prepare($insertSql);
            $insertStmt->execute([
                ':transferout_id' => $transferout_id,
                ':approver_id' => $approver_id,
                ':status' => $status,
            ]);

            // Update approver_id in holds table
            $updateSql = "UPDATE transferout SET approver_id = :approver_id WHERE id = :transferout_id";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([
                ':approver_id' => $approver_id,
                ':transferout_id' => $transferout_id,
            ]);

            // Commit transaction
            $this->pdo->commit();
        } catch (Exception $e) {
            // Rollback on error
            $this->pdo->rollBack();
            error_log("Error in insertManagerStatusAndUpdateApprover: " . $e->getMessage());
            throw $e; // Re-throw exception to handle it upstream
        }
    }

    public function getTransferoutWithDetails($offset, $limit)
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return []; // Return an empty array if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Fetch department and office data via APIs
        $userModel = new User();
        $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
        $offices = $userModel->getAllOfficeApi($_SESSION['token']);

        // Create associative arrays for departments and offices for easier lookup
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

        // Prepare the SQL query to fetch transfer out data along with attachments
        $sql = "SELECT t.*, 
                   GROUP_CONCAT(a.filename) AS attachments 
            FROM $this->tbltransferout t
            LEFT JOIN transferout_attachments a ON t.id = a.transferout_id
            WHERE t.user_id = :user_id
            GROUP BY t.id
            ORDER BY t.created_at DESC
            LIMIT :limit OFFSET :offset";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        // Fetch the transfer out records
        $transferOutData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Map department and office names to transfer out records
        foreach ($transferOutData as &$record) {
            // Map department names
            $record['from_department_name'] = isset($departmentsById[$record['from_department']])
                ? $departmentsById[$record['from_department']]
                : 'Unknown Department';
            $record['to_department_name'] = isset($departmentsById[$record['to_department']])
                ? $departmentsById[$record['to_department']]
                : 'Unknown Department';

            // Map office names
            $record['from_office_name'] = isset($officesById[$record['from_office']])
                ? $officesById[$record['from_office']]
                : 'Unknown Office';
            $record['to_office_name'] = isset($officesById[$record['to_office']])
                ? $officesById[$record['to_office']]
                : 'Unknown Office';

            // Assign the attachments string directly
            $record['attachments'] = !empty($record['attachments']) ? $record['attachments'] : '';
        }

        return $transferOutData;
    }

    public function getTransferoutCountById()
    {
        // Check if the user ID is set in the session
        if (empty($_SESSION['user_id'])) {
            return 0; // Return 0 if no user ID is in the session
        }

        // Get user ID from session
        $user_id = $_SESSION['user_id'];

        // Prepare the query to count the records for this specific user
        $query = "SELECT COUNT(*) AS total FROM $this->tbltransferout WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);

        // Bind the user_id parameter
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch and return the total count
        return $stmt->fetchColumn();
    }

    public function deleteTransferout($id)
    {
        // Start a transaction to ensure both deletions are successful or neither
        $this->pdo->beginTransaction();

        try {
            // Delete the hold from tblholds
            $stmtHolds = $this->pdo->prepare("DELETE FROM {$this->tbltransferout} WHERE id = ?");
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

    public function getTransferoutById($id)
    {
        // SQL query to get the transfer request and approvals as separate rows
        $sql = "SELECT h.*, 
                    GROUP_CONCAT(DISTINCT a.filename) AS attachments, 
                    MAX(a.created_at) AS latest_uploaded_at, 
                    ha.status AS approval_status, 
                    ha.updated_at AS approved_at, 
                    ha.approver_id, 
                    ha.comment AS comment
                FROM $this->tbltransferout h
                JOIN $this->transferout_approval ha ON ha.transferout_id = h.id
                LEFT JOIN transferout_attachments a ON h.id = a.transferout_id
                WHERE h.user_id = :userId AND h.id = :id
                GROUP BY ha.id -- Group by approval ID to ensure distinct approval records
                ORDER BY ha.id DESC";

        // Prepare the statement
        $stmt = $this->pdo->prepare($sql);

        // Bind parameters
        $stmt->bindParam(':userId', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all results
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results) {
            // Initialize UserModel and fetch department/office data once
            $userModel = new User();
            $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
            $offices = $userModel->getAllOfficeApi($_SESSION['token']);

            // Create lookup maps for departments and offices
            $departmentMap = [];
            foreach ($departments['data'] as $department) {
                $departmentMap[$department['id']] = $department['departmentNameKh'];
            }

            $officeMap = [];
            foreach ($offices['data'] as $office) {
                $officeMap[$office['id']] = $office['officeNameKh'];
            }

            // Fetch submitter details for the first result
            $submitterApiResponse = $userModel->getUserByIdApi($results[0]['user_id'], $_SESSION['token']);
            if ($submitterApiResponse && $submitterApiResponse['http_code'] === 200 && isset($submitterApiResponse['data'])) {
                $submitter = $submitterApiResponse['data'];

                // Add submitter details to the first result
                $results[0]['user_name'] = trim(($submitter['lastNameKh'] ?? 'Unknown') . " " . ($submitter['firstNameKh'] ?? ''));
                $results[0]['dob'] = $submitter['dateOfBirth'] ?? 'Unknown';
                $results[0]['user_email'] = $submitter['email'] ?? 'Unknown';
                $results[0]['department_name'] = $submitter['department']['name'] ?? 'Unknown';
                $results[0]['position_name'] = $submitter['position']['name'] ?? 'Unknown';
                $results[0]['office_name'] = $submitter['office']['name'] ?? 'Unknown';
                $results[0]['user_profile'] = !empty($submitter['image']) ? 'https://hrms.iauoffsa.us/images/' . $submitter['image'] : 'default-profile.png';
            }

            // Iterate over each approval step
            foreach ($results as &$result) {
                // Populate department and office names for from/to fields
                $result['from_department_name'] = $departmentMap[$result['from_department']] ?? 'Unknown Department';
                $result['to_department_name'] = $departmentMap[$result['to_department']] ?? 'Unknown Department';
                $result['from_office_name'] = $officeMap[$result['from_office']] ?? 'Unknown Office';
                $result['to_office_name'] = $officeMap[$result['to_office']] ?? 'Unknown Office';

                // Fetch approver details if approver_id is set
                if ($result['approver_id']) {
                    $approverApiResponse = $userModel->getUserByIdApi($result['approver_id'], $_SESSION['token']);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];

                        // Add approver details to the result
                        $result['approver_name'] = trim(($approver['lastNameKh'] ?? 'Unknown') . " " . ($approver['firstNameKh'] ?? ''));
                        $result['profile'] = !empty($approver['image']) ? 'https://hrms.iauoffsa.us/images/' . $approver['image'] : 'default-profile.png';
                        $result['approver_department_name'] = $approver['department']['name'] ?? 'Unknown';
                        $result['approver_office_name'] = $approver['office']['name'] ?? 'Unknown';
                    } else {
                        $result['approver_name'] = 'Unknown';
                        $result['profile'] = 'default-profile.png';
                        $result['approver_department_name'] = 'Unknown Department';
                        $result['approver_office_name'] = 'Unknown Office';
                    }
                } else {
                    // Default values if no approver is assigned
                    $result['approver_name'] = 'No approver assigned';
                    $result['profile'] = 'default-profile.png';
                    $result['approver_department_name'] = 'Unknown Department';
                    $result['approver_office_name'] = 'Unknown Office';
                }
            }

            return $results;
        }

        return []; // Return an empty array if no results are found
    }

    public function getTransferoutByUserId($userId)
    {
        // SQL query to fetch transfer-out data and associated attachments
        $sql = "SELECT h.*, 
                GROUP_CONCAT(DISTINCT a.filename) AS attachments, 
                MAX(a.created_at) AS latest_uploaded_at
            FROM $this->tbltransferout h
            LEFT JOIN $this->transferout_attachment a ON h.id = a.transferout_id
            WHERE h.approver_id = :user_id AND h.status = 'pending'
            GROUP BY h.id
            ORDER BY h.id DESC";

        // Prepare and execute the query
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cache for user, department, and office data
        $userCache = [];
        $departmentMap = [];
        $officeMap = [];

        // Fetch all departments and offices once to avoid redundant API calls
        $userModel = new User();
        $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
        $offices = $userModel->getAllOfficeApi($_SESSION['token']);

        // Map departments and offices by their IDs for easy lookup
        if (!empty($departments['data'])) {
            foreach ($departments['data'] as $department) {
                $departmentMap[$department['id']] = $department['departmentNameKh'];
            }
        }

        if (!empty($offices['data'])) {
            foreach ($offices['data'] as $office) {
                $officeMap[$office['id']] = $office['officeNameKh'];
            }
        }

        foreach ($results as &$request) {
            $requestUserId = $request['user_id'];

            // Check the cache for user data
            if (!isset($userCache[$requestUserId])) {
                $retryCount = 3;

                while ($retryCount > 0) {
                    $userApiResponse = $userModel->getUserByIdApi($requestUserId, $_SESSION['token']);

                    if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                        $userCache[$requestUserId] = $userApiResponse['data'];
                        break;
                    }

                    $retryCount--;
                    usleep(200000); // Wait 200ms before retrying
                }
            }

            // Retrieve user data from cache or default to null
            $user = $userCache[$requestUserId] ?? null;

            // Populate department and office names
            $request['from_department_name'] = $departmentMap[$request['from_department']] ?? 'Unknown Department';
            $request['to_department_name'] = $departmentMap[$request['to_department']] ?? 'Unknown Department';
            $request['from_office_name'] = $officeMap[$request['from_office']] ?? 'Unknown Office';
            $request['to_office_name'] = $officeMap[$request['to_office']] ?? 'Unknown Office';

            if ($user) {
                // Populate user details if available
                $request['user_name'] = trim(($user['lastNameKh'] ?? '') . " " . ($user['firstNameKh'] ?? 'Unknown'));
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
            } else {
                // Default values if user data is not available
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png';
                error_log("Failed to fetch user data for User ID $requestUserId after retries.");
            }

            // Ensure 'attachments' is always a string for consistent handling
            $request['attachments'] = $request['attachments'] ?? '';
        }

        return $results;
    }
    
    public function deleteAttachment($transferoutId, $filename)
    {
        // Prepare the SQL statement to delete the attachment from the transferout_attachments table
        $stmt = $this->pdo->prepare("DELETE FROM transferout_attachments WHERE transferout_id = :transferout_id AND filename = :filename");
        $stmt->execute([
            ':transferout_id' => $transferoutId,
            ':filename' => $filename
        ]);
    }

}
