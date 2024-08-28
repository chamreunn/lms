<?php
require_once 'config/database.php';
class AdminModel
{
    private $pdo;
    private $table_name = "late_in_out";

    private $table = "missions";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getAllLatein()
    {
        $query = "
        SELECT lt.*, u.*, lt.status AS late_status, lt.id AS late_id
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserById($user_id)
    {
        $userModel = new User();

        // Fetch user details from API
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

        // Debug: Log the API response
        error_log("API Response for User ID " . $user_id . ": " . print_r($userApiResponse, true));

        // Check if the API response is successful
        if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
            $user = $userApiResponse['data'];

            // Fetch position details from API
            $roleApiResponse = $userModel->getRoleApi($user['roleId'], $_SESSION['token']);
            $officeApiResponse = $userModel->getOfficeApi($user['officeId'], $_SESSION['token']);
            $departmentApiResponse = $userModel->getDepartmentApi($user['departmentId'], $_SESSION['token']);

            // Debug: Log the role API response
            error_log("API Response for Role ID " . $user['roleId'] . ": " . print_r($roleApiResponse, true));
            error_log("API Response for Office ID " . $user['officeId'] . ": " . print_r($officeApiResponse, true));
            error_log("API Response for Department ID " . $user['departmentId'] . ": " . print_r($departmentApiResponse, true));

            $roleName = 'Unknown';
            $officeName = 'Unknown';
            $departmentName = 'Unknown';

            if ($roleApiResponse && $roleApiResponse['http_code'] === 200 && isset($roleApiResponse['data']) && is_array($roleApiResponse['data'])) {
                $roleData = $roleApiResponse['data'];
                $roleName = $roleData['roleNameKh'] ?? 'Unknown';
            }

            if ($officeApiResponse && $officeApiResponse['http_code'] === 200 && isset($officeApiResponse['data']) && is_array($officeApiResponse['data'])) {
                $officeData = $officeApiResponse['data'];
                $officeName = $officeData['officeNameKh'] ?? 'Unknown';
            }

            if ($departmentApiResponse && $departmentApiResponse['http_code'] === 200 && isset($departmentApiResponse['data']) && is_array($departmentApiResponse['data'])) {
                $departmentData = $departmentApiResponse['data'];
                $departmentName = $departmentData['departmentNameKh'] ?? 'Unknown';
            }

            // Determine gender based on the API response
            $gender = 'Unknown';
            if ($user['gender'] === 'f') {
                $gender = 'ស្រី';
            } elseif ($user['gender'] === 'm') {
                $gender = 'ប្រុស';
            }

            $active = 'Unknow';
            if ($user['active'] === '1'){
                $active = 'Active';
            }else{
                $active = 'Inactive';
            }

            // Fetch additional user details from the API
            $userDetails = [
                'rolename' => $roleName,
                'phone_number' => $user['phoneNumber'],
                'user_name' => $user['lastNameKh'] . " " . $user['firstNameKh'],
                'user_id' => $user['id'] ?? 'Unknown',
                'email' => $user['email'] ?? 'Unknown',
                'office_id' => $user['office']['id'] ?? 'Unknown',
                'office_name' => $officeName,
                'department_id' => $user['department']['id'] ?? 'Unknown',
                'department_name' => $departmentName,
                'position_name' => $user['position']['name'] ?? 'Unknown',
                'profile_picture' => 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png',
                'date_of_birth' => $user['dateOfBirth'],
                'gender' => $gender,
                'user_eng_name' => $user['engName'],
                'active' => $active,
                'activeStatus' => $user['active'],
                'address' => $user['pobAddress'],
                'curaddress' => $user['currentAddress'],
                'password' => $user['password']
            ];

            return $userDetails;
        } else {
            // Handle cases where the API call fails or returns no data
            error_log("API call failed for User ID " . $user_id . ". Returning default values.");

            return [
                'rolename' => 'Unknown',
                'user_name' => 'Unknown',
                'user_id' => 'Unknown',
                'email' => 'Unknown',
                'office_id' => 'Unknown',
                'office_name' => 'Unknown',
                'department_id' => 'Unknown',
                'department_name' => 'Unknown',
                'position_name' => 'Unknown',
                'profile_picture' => 'default-profile.png'
            ];
        }
    }

    public function getUserLeaveRequests($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            lr.*, 
            lt.name AS leave_type_name, 
            lt.duration, 
            lt.color, 
            u.khmer_name AS user_name, 
            u.date_of_birth AS dob, 
            u.email AS user_email, 
            u.profile_picture AS user_profile,
            d.name AS department_name,
            p.name AS position_name
        FROM 
            leave_requests lr
        JOIN 
            leave_types lt ON lr.leave_type_id = lt.id
        JOIN 
            users u ON lr.user_id = u.id
        JOIN 
            departments d ON u.department_id = d.id
        JOIN 
            positions p ON u.position_id = p.id
        WHERE 
            lr.user_id = ? AND lr.status = "Approved" ORDER BY lr.id DESC'
        );
        $stmt->execute([$user_id]);

        // Fetch all results
        return $stmt->fetchAll(); // Return all leave requests for the user
    }

    public function countUserApprovedLeaveRequests($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT 
            COUNT(*) AS leave_request_count
        FROM 
            leave_requests lr
        JOIN 
            leave_types lt ON lr.leave_type_id = lt.id
        JOIN 
            users u ON lr.user_id = u.id
        JOIN 
            departments d ON u.department_id = d.id
        JOIN 
            positions p ON u.position_id = p.id
        WHERE 
            lr.user_id = ? AND lr.status = "Approved"'
        );
        $stmt->execute([$user_id]);

        // Fetch the count result
        return $stmt->fetchColumn(); // Return the count of approved leave requests
    }

    public function getOvertimeIn($user_id)
    {
        $stmt = $this->pdo->prepare('
            SELECT late_in_out.*, users.khmer_name, departments.name AS department_name, users.profile_picture AS profile,
                   offices.name AS office_name, positions.name AS position_name, users.email AS email
            FROM late_in_out
            JOIN users ON late_in_out.user_id = users.id
            LEFT JOIN departments ON users.department_id = departments.id
            LEFT JOIN offices ON users.office_id = offices.id
            LEFT JOIN positions ON users.position_id = positions.id
            WHERE late_in_out.user_id = ? AND late_in_out.late_in IS NOT NULL AND late_in_out.status = "Approved" || late_in_out.status = "Rejected"
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public function getOvertimeInCount($user_id)
    {
        $stmt = $this->pdo->prepare('
        SELECT 
            COUNT(*) AS count
        FROM 
            late_in_out
        WHERE 
            user_id = ? 
            AND late_in IS NOT NULL 
            AND (status = "Approved" OR status = "Rejected")
    ');
        $stmt->execute([$user_id]);
        return $stmt->fetchColumn();
    }

    public function getMission()
    {
        $query = "
        SELECT m.*,u.* m.user_id AS uId
        FROM $this->table m
        JOIN users u ON m.user_id = u.id 
        ORDER BY updated_at DESC
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLateinCount()
    {
        $query = "
        SELECT COUNT(*) AS latein_count
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latein_count'] ?? 0;
    }

    public function getLateCountToday()
    {
        $query = "
        SELECT COUNT(*) AS latein_count
        FROM $this->table_name lt
        JOIN users u ON lt.user_id = u.id
        WHERE lt.status = 'Pending'
        AND DATE(lt.date) = CURDATE()
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latein_count'] ?? 0;
    }

    public function updateRequest($approver_id, $action, $request_id, $comment, $signature)
    {
        try {
            // Start a transaction to ensure atomicity
            $this->pdo->beginTransaction();

            // Insert the approval record with the signature
            $stmt = $this->pdo->prepare(
                'INSERT INTO late_approvals (acted_by, action, late_approval_id, comment, signature, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())'
            );
            $stmt->execute([$approver_id, $action, $request_id, $comment, $signature]);

            // Update the action and updated_at timestamp in the existing late_approvals record
            $stmt = $this->pdo->prepare("UPDATE late_in_out SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$action, $request_id]);

            // Commit the transaction
            $this->pdo->commit();

            // Optionally, fetch the updated_at timestamp if needed
            $stmt = $this->pdo->prepare("SELECT updated_at FROM late_in_out WHERE id = ?");
            $stmt->execute([$request_id]);
            $updatedAt = $stmt->fetchColumn();

            if ($updatedAt === false) {
                throw new Exception("Unable to fetch updated_at timestamp for approval.");
            }

            return $updatedAt; // Return the updated_at timestamp if needed

        } catch (Exception $e) {
            // Rollback the transaction in case of an error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
