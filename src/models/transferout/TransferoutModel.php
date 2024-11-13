<?php

class TransferoutModel
{
    protected $tbltransferout = 'transferout'; // The table where data is stored

    protected $transferout_approval = 'transferout_approval';

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

        $sql = "INSERT INTO $this->transferout_approval (transferout_id, approver_id, status)
                VALUES (:transfer_id, :approver_id, :status)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':transfer_id' => $hold_id,
            ':approver_id' => $approver_id,
            ':status' => $status,
        ]);
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
        // Prepare the SQL query to get the transfer request and approval tracking details
        $sql = "SELECT h.*, GROUP_CONCAT(a.filename) AS attachments, a.created_at, a.transferout_id,  ha.status AS approval_status, ha.updated_at AS approved_at, ha.approver_id, ha.comment AS comment
        FROM $this->transferout_approval ha
        JOIN $this->tbltransferout h ON ha.transferout_id = h.id
        LEFT JOIN transferout_attachments a ON h.id = a.transferout_id
        WHERE h.user_id = :userId AND h.id = :id ORDER BY ha.id DESC
    ";

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
            // Initialize UserModel to fetch department and office data
            $userModel = new User();
            $departments = $userModel->getAllDepartmentApi($_SESSION['token']);
            $offices = $userModel->getAllOfficeApi($_SESSION['token']);

            // Map departments and offices for easy lookup by ID
            $departmentMap = [];
            foreach ($departments['data'] as $department) {
                $departmentMap[$department['id']] = $department['departmentNameKh'];
            }

            $officeMap = [];
            foreach ($offices['data'] as $office) {
                $officeMap[$office['id']] = $office['officeNameKh'];
            }

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

            // Add department and office names for from/to fields
            foreach ($results as &$result) {
                // From/To department and office names based on their IDs
                $result['from_department_name'] = $departmentMap[$result['from_department']] ?? 'Unknown Department';
                $result['to_department_name'] = $departmentMap[$result['to_department']] ?? 'Unknown Department';
                $result['from_office_name'] = $officeMap[$result['from_office']] ?? 'Unknown Office';
                $result['to_office_name'] = $officeMap[$result['to_office']] ?? 'Unknown Office';

                // Fetch approver details if approver_id is set
                if ($result['approver_id']) {
                    $approverApiResponse = $userModel->getUserByIdApi($result['approver_id'], $_SESSION['token']);
                    if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                        $approver = $approverApiResponse['data'];
                        $result['approver_name'] = trim(($approver['lastNameKh'] ?? 'Unknown') . " " . ($approver['firstNameKh'] ?? ''));
                        $result['profile'] = !empty($approver['image']) ? 'https://hrms.iauoffsa.us/images/' . $approver['image'] : 'default-profile.png';
                        $result['position_name'] = $approver['position']['name'] ?? 'Unknown';
                        // Assign the attachments string directly
                        $result['attachments'] = !empty($result['attachments']) ? $result['attachments'] : '';
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
