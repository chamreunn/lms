<?php
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminModel
{
    private $pdo;

    private $table_name = "late_in_out";

    private $lateApproval = "late_approvals";

    private $approval = "leave_approvals";

    private $leaveRequest = "leave_requests";

    private $table = "missions";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    // Commit transaction
    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    // Rollback transaction
    public function rollBackTransaction()
    {
        $this->pdo->rollBack();
    }

    public function getLateById($late_id)
    {
        // Fetch a single late-in record by ID without joining the users table
        $query = "SELECT lt.*, lt.status AS late_status, lt.id AS late_id 
              FROM $this->table_name lt 
              WHERE lt.id = :late_id AND lt.status = 'Pending'";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':late_id', $late_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the late-in record
        $lateInRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a record is found
        if (!$lateInRecord) {
            return null; // Return null if no record is found
        }

        $userModel = new User();
        $user_id = $lateInRecord['user_id'];

        // Fetch user data from API using the user_id
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

        // Check if the API response is successful and contains user data
        if ($userApiResponse && isset($userApiResponse['http_code']) && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Fetch user role from the API
            $roleResponse = $userModel->getRoleApi($user['roleId'], $_SESSION['token']);

            // Add the role to the user data
            $user['role'] = ($roleResponse && $roleResponse['http_code'] === 200 && isset($roleResponse['data']['roleNameKh']))
                ? $roleResponse['data']['roleNameKh']
                : 'Unknown';

            // Merge user data into the late-in record
            $lateInRecord['khmer_name'] = isset($user['lastNameKh'], $user['firstNameKh'])
                ? $user['lastNameKh'] . ' ' . $user['firstNameKh']
                : 'Unknown';
            $lateInRecord['dob'] = $user['dateOfBirth'] ?? 'Unknown';
            $lateInRecord['email'] = $user['email'] ?? 'Unknown';
            $lateInRecord['contact'] = $user['phoneNumber'] ?? 'Unknown';
            $lateInRecord['address'] = $user['pobAddress'] ?? 'Unknown';
            $lateInRecord['department_name'] = $user['department']['name'] ?? 'Unknown';
            $lateInRecord['position_name'] = $user['position']['name'] ?? 'Unknown';
            $lateInRecord['role'] = $user['role'] ?? 'Unknown';
            $lateInRecord['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');

            return $lateInRecord;
        } else {
            // Log the unexpected response for debugging purposes
            error_log("Unexpected API Response: " . print_r($userApiResponse, true));
            return [
                'http_code' => $userApiResponse['http_code'] ?? 500, // Default to 500 if http_code is missing
                'error' => "Unexpected API Response",
                'response' => $userApiResponse
            ];
        }
    }

    public function getAllLateById($late_id)
    {
        // Fetch a single late-in record by ID without joining the users table
        $query = "SELECT * FROM $this->table_name WHERE id = :id ORDER BY id DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $late_id, PDO::PARAM_INT);
        $stmt->execute();

        // Fetch the late-in record
        $lateInRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a record is found
        if (!$lateInRecord) {
            return null; // Return null if no record is found
        }

        $userModel = new User();
        $user_id = $lateInRecord['user_id'];

        // Fetch user data from API using the user_id
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

        // Check if the API response is successful and contains user data
        if ($userApiResponse && isset($userApiResponse['http_code']) && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Fetch user role from the API
            $roleResponse = $userModel->getRoleApi($user['roleId'], $_SESSION['token']);

            // Add the role to the user data
            $user['role'] = ($roleResponse && $roleResponse['http_code'] === 200 && isset($roleResponse['data']['roleNameKh']))
                ? $roleResponse['data']['roleNameKh']
                : 'Unknown';

            // Merge user data into the late-in record
            $lateInRecord['khmer_name'] = isset($user['lastNameKh'], $user['firstNameKh'])
                ? $user['lastNameKh'] . ' ' . $user['firstNameKh']
                : 'Unknown';
            $lateInRecord['dob'] = $user['dateOfBirth'] ?? 'Unknown';
            $lateInRecord['email'] = $user['email'] ?? 'Unknown';
            $lateInRecord['contact'] = $user['phoneNumber'] ?? 'Unknown';
            $lateInRecord['address'] = $user['pobAddress'] ?? 'Unknown';
            $lateInRecord['department_name'] = $user['department']['name'] ?? 'Unknown';
            $lateInRecord['position_name'] = $user['position']['name'] ?? 'Unknown';
            $lateInRecord['role'] = $user['role'] ?? 'Unknown';
            $lateInRecord['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');

            return $lateInRecord;
        } else {
            // Log the unexpected response for debugging purposes
            error_log("Unexpected API Response: " . print_r($userApiResponse, true));
            return [
                'http_code' => $userApiResponse['http_code'] ?? 500, // Default to 500 if http_code is missing
                'error' => "Unexpected API Response",
                'response' => $userApiResponse
            ];
        }
    }

    public function updateStatus($lateId, $status)
    {
        $sql = "UPDATE $this->table_name SET status = :status WHERE id = :lateId";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':lateId', $lateId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getAllLatein()
    {
        // Fetch only late-in records without joining the users table
        $query = "SELECT lt.*, lt.status AS late_status, lt.id AS late_id FROM $this->table_name lt WHERE lt.status = 'Pending' AND lt.late_in is NOT NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['user_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['user_email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getAll()
    {
        // Fetch only late-in records without joining the users table
        $query = "SELECT lt.*, lt.status AS late_status, lt.id AS late_id FROM $this->table_name lt WHERE lt.status != 'Pending' ORDER BY lt.id DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['user_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['user_email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getAllLateout()
    {
        // Fetch only late-in records without joining the users table
        $query = "SELECT lt.*, lt.status AS late_status, lt.id AS late_id FROM $this->table_name lt WHERE lt.status = 'Pending' AND lt.late_out is NOT NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['user_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['user_email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getAllLeaveEarly()
    {
        // Fetch only late-in records without joining the users table
        $query = "SELECT lt.*, lt.status AS late_status, lt.id AS late_id FROM $this->table_name lt WHERE lt.status = 'Pending' AND lt.leave_early is NOT NULL";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['user_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['user_email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['user_profile'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function create($user_id, $user_email, $leave_type_id, $position, $office, $department, $leave_type_name, $start_date, $end_date, $remarks, $duration_days, $attachment)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("
            INSERT INTO $this->leaveRequest (user_id, uemails, leave_type_id, position, office, department, leave_type, start_date, end_date, remarks, num_date, attachment, status, dhead_office, created_at) 
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
            'Pending',
            'Approved'
        ]);

        // Return the ID of the newly created leave request
        return $this->pdo->lastInsertId();
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
            if ($user['active'] === '1') {
                $active = 'Active';
            } else {
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
        FROM $this->table_name
        WHERE status = 'Pending'
        AND late_in IS NOT NULL
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['latein_count'];
    }

    public function getLateoutCount()
    {
        $query = "
            SELECT COUNT(*) AS lateout_count
            FROM $this->table_name
            WHERE status = 'Pending'
            AND late_out IS NOT NULL
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['lateout_count'] ?? 0;
    }

    public function getLeaveEarlyCount()
    {
        $query = "
        SELECT COUNT(*) AS leaveearly_count
        FROM $this->table_name
        WHERE status = 'Pending'
        AND leave_early IS NOT NULL
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['leaveearly_count'] ?? 0;
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

    public function getAllLate()
    {
        $query = "
        SELECT COUNT(*) AS AllLate
        FROM $this->table_name lt
        WHERE lt.status = 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['AllLate'];
    }

    public function getAllLeave()
    {
        // Query to select leave requests that are 'Approved'
        $query = "
        SELECT lr.*, lt.name , lt.color AS ltColor
        FROM $this->leaveRequest lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        WHERE lr.status = 'Approved'
        AND CURDATE() BETWEEN start_date AND end_date
    ";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all leave requests as an associative array
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create an instance of the User model to fetch user details from the API
        $userModel = new User();
        $token = $_SESSION['token']; // Assuming token is available in session

        // Loop through each leave request to fetch user details
        foreach ($leaveRequests as &$leave) {
            $user_id = $leave['user_id']; // Assuming 'user_id' is present in each leave request record

            // Fetch user details from the API
            $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

            // Check if the API response is successful and contains data
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a 'data' object with user details

                // Add user details to the leave record
                $leave['khmer_name'] = $user['firstNameKh'] ?? 'Unknown';
                $leave['department_name'] = $user['department']['name'] ?? 'Unknown';
                $leave['profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Default profile image
                $leave['office_name'] = $user['office']['name'] ?? 'Unknown';
                $leave['position_name'] = $user['position']['name'] ?? 'Unknown';
                $leave['email'] = $user['email'] ?? 'Unknown';
            } else {
                // Fallback to 'Unknown' if the API call fails
                $leave['khmer_name'] = 'Unknown';
                $leave['department_name'] = 'Unknown';
                $leave['profile'] = 'default-profile.png';
                $leave['office_name'] = 'Unknown';
                $leave['position_name'] = 'Unknown';
                $leave['email'] = 'Unknown';
            }

            // Fetch approver details if 'approver_id' exists in the leave record
            if (!empty($leave['approver_id'])) {
                $approver_id = $leave['approver_id'];

                // Fetch approver details from the API
                $approverApiResponse = $userModel->getUserByIdApi($approver_id, $token);

                if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                    $approver = $approverApiResponse['data'];

                    // Add approver details to the leave record
                    $leave['approver_name'] = $approver['firstNameKh'] ?? 'Unknown';
                    $leave['approver_email'] = $approver['email'] ?? 'Unknown';
                } else {
                    // Fallback to 'Unknown' if the API call fails
                    $leave['approver_name'] = 'Unknown';
                    $leave['approver_email'] = 'Unknown';
                }
            } else {
                // If no 'approver_id' exists, set as 'Unknown'
                $leave['approver_name'] = 'Unknown';
                $leave['approver_email'] = 'Unknown';
            }
        }

        // Return the leave requests with user and approver details
        return $leaveRequests;
    }

    public function countApprovedLeavesToday()
    {
        // Query to count leave requests that are 'Approved' and where today's date falls between start_date and end_date
        $query = "
        SELECT COUNT(*) AS leave_count 
        FROM $this->leaveRequest 
        WHERE status = 'Approved'
        AND CURDATE() BETWEEN start_date AND end_date
    ";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch the count result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the leave count
        return $result['leave_count'];
    }

    public function getAllLeaves()
    {
        // Query to select leave requests that are 'Approved'
        $query = "
        SELECT lr.*, lt.name , lt.color AS ltColor
        FROM $this->leaveRequest lr
        JOIN leave_types lt ON lr.leave_type_id = lt.id
        WHERE lr.status = 'Approved'
    ";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch all leave requests as an associative array
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Create an instance of the User model to fetch user details from the API
        $userModel = new User();
        $token = $_SESSION['token']; // Assuming token is available in session

        // Loop through each leave request to fetch user details
        foreach ($leaveRequests as &$leave) {
            $user_id = $leave['user_id']; // Assuming 'user_id' is present in each leave request record

            // Fetch user details from the API
            $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

            // Check if the API response is successful and contains data
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a 'data' object with user details

                // Add user details to the leave record
                $leave['khmer_name'] = $user['firstNameKh'] ?? 'Unknown';
                $leave['department_name'] = $user['department']['name'] ?? 'Unknown';
                $leave['profile'] = 'https://hrms.iauoffsa.us/images/' . $user['image'] ?? 'default-profile.png'; // Default profile image
                $leave['office_name'] = $user['office']['name'] ?? 'Unknown';
                $leave['position_name'] = $user['position']['name'] ?? 'Unknown';
                $leave['email'] = $user['email'] ?? 'Unknown';
            } else {
                // Fallback to 'Unknown' if the API call fails
                $leave['khmer_name'] = 'Unknown';
                $leave['department_name'] = 'Unknown';
                $leave['profile'] = 'default-profile.png';
                $leave['office_name'] = 'Unknown';
                $leave['position_name'] = 'Unknown';
                $leave['email'] = 'Unknown';
            }

            // Fetch approver details if 'approver_id' exists in the leave record
            if (!empty($leave['approver_id'])) {
                $approver_id = $leave['approver_id'];

                // Fetch approver details from the API
                $approverApiResponse = $userModel->getUserByIdApi($approver_id, $token);

                if ($approverApiResponse && $approverApiResponse['http_code'] === 200 && isset($approverApiResponse['data'])) {
                    $approver = $approverApiResponse['data'];

                    // Add approver details to the leave record
                    $leave['approver_name'] = $approver['firstNameKh'] ?? 'Unknown';
                    $leave['approver_email'] = $approver['email'] ?? 'Unknown';
                } else {
                    // Fallback to 'Unknown' if the API call fails
                    $leave['approver_name'] = 'Unknown';
                    $leave['approver_email'] = 'Unknown';
                }
            } else {
                // If no 'approver_id' exists, set as 'Unknown'
                $leave['approver_name'] = 'Unknown';
                $leave['approver_email'] = 'Unknown';
            }
        }

        // Return the leave requests with user and approver details
        return $leaveRequests;
    }

    public function getApprovedLateCount()
    {
        $query = "
        SELECT COUNT(*) AS AllLate
        FROM $this->table_name lt
        WHERE lt.status != 'Pending'
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['AllLate'];
    }

    public function updateRequest($approver_id, $action, $request_id, $comment)
    {
        try {
            // Insert the approval record
            $stmt = $this->pdo->prepare(
                "INSERT INTO $this->lateApproval (acted_by, action, lateId, comment, created_at)
             VALUES (?, ?, ?, ?, NOW())"
            );
            $stmt->execute([$approver_id, $action, $request_id, $comment]);

            // Update the action and updated_at timestamp in the existing late_in_out record
            $stmt = $this->pdo->prepare("UPDATE $this->table_name SET status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$action, $request_id]);

            // Optionally, fetch the updated_at timestamp if needed
            $stmt = $this->pdo->prepare("SELECT updated_at FROM late_in_out WHERE id = ?");
            $stmt->execute([$request_id]);
            $updatedAt = $stmt->fetchColumn();

            if ($updatedAt === false) {
                throw new Exception("Unable to fetch updated_at timestamp for approval.");
            }

            return $updatedAt; // Return the updated_at timestamp if needed

        } catch (Exception $e) {
            // Handle the exception without rolling back transactions
            throw $e;
        }
    }

    public function getRequestsByUserId($user_id)
    {
        // Prepare and execute the SQL query (remove JOINs with users, departments, and positions)
        $stmt = $this->pdo->prepare('SELECT lr.*, lt.name as leave_type_name, lt.duration, lt.color FROM ' . $this->leaveRequest . ' lr JOIN leave_types lt ON lr.leave_type_id = lt.id WHERE lr.user_id = ? ORDER BY id DESC');
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
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $result['user_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh']) ? $user['lastNameKh'] . " " . $user['firstNameKh'] : 'Unknown';
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
        }

        return $results;
    }

    public function getRequestsByFilters($user_id, $filters)
    {
        // Base SQL query (remove JOINs with users, departments, positions, and offices)
        $sql = 'SELECT lr.*, 
                   lt.name as leave_type_name, 
                   lt.duration, 
                   lt.color
            FROM ' . $this->leaveRequest . ' lr
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
        }

        return $results;
    }


    public function handleFileUpload($file, $allowed_extensions, $max_size, $upload_path)
    {
        $file_name = $file['name'];
        $file_tmp_name = $file['tmp_name'];
        $file_error = $file['error'];
        $file_size = $file['size'];

        if ($file_error === UPLOAD_ERR_NO_FILE) {
            // No file was uploaded
            return null;
        }

        if ($file_error !== UPLOAD_ERR_OK) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "An error occurred during the file upload."
            ];
            return false;
        }

        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_extensions)) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Invalid attachment file type."
            ];
            return false;
        }

        if ($file_size > $max_size) {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Attachment file size exceeds the limit."
            ];
            return false;
        }

        $unique_file_name = uniqid('', true) . '.' . $file_ext;
        $destination = $upload_path . $unique_file_name;

        if (move_uploaded_file($file_tmp_name, $destination)) {
            return $unique_file_name;
        } else {
            $_SESSION['error'] = [
                'title' => "File Error",
                'message' => "Failed to move the uploaded file."
            ];
            return false;
        }
    }

    public function calculateBusinessDays(DateTime $start_date, DateTime $end_date)
    {
        // Fetch holidays from the database
        $holidayModel = new CalendarModel();
        $holidays = $holidayModel->getHoliday(); // Assume this returns an array of holiday dates

        // Convert holidays to DateTime objects for comparison
        $holidayDates = array_map(function ($holiday) {
            return new DateTime($holiday['holiday_date']);
        }, $holidays);

        // Proceed to calculate the number of business days between the start and end date
        $business_days = 0;
        $current_date = clone $start_date;

        while ($current_date <= $end_date) {
            $day_of_week = $current_date->format('N');

            // Check if the current date is a weekday and not a holiday
            if ($day_of_week < 6 && !in_array($current_date, $holidayDates)) {
                $business_days++;
            }

            $current_date->modify('+1 day');
        }

        return $business_days;
    }

    public function sendEmailNotification($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $remarks, $leaveType)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format dates
            $start_date_formatted = (new DateTime($start_date))->format('j F, Y');
            $end_date_formatted = (new DateTime($end_date))->format('j F, Y');

            //Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');
            $mail->addAddress($managerEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
            <html>
            <head>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <style>
                    .profile-img {
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #e2e2e2;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .header {
                        background-color: #007bff;
                        color: white;
                        padding: 10px;
                        border-radius: 10px 10px 0 0;
                    }
                    .icon {
                        vertical-align: middle;
                        margin-right: 10px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #f9f9f9;
                    }
                    .btn {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 10px;
                        color: white;
                        background-color: #007bff;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        padding: 10px;
                        text-align: center;
                        background-color: #f1f1f1;
                        border-radius: 0 0 10px 10px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h4><img src='http://localhost/elms/public/img/icons/brands/logo2.png' class='icon' alt='Leave Request' /> Leave Request Notification</h4>
                    </div>
                    <div class='content'>
                        <p>$message</p>
                        <p><strong>រយៈពេល :</strong> $duration_days ថ្ងៃ</p>
                        <p><strong>ប្រភេទច្បាប់ :</strong> $leaveType</p>
                        <p><strong>ចាប់ពីថ្ងៃ :</strong> $start_date_formatted</p>
                        <p><strong>ដល់ថ្ងៃ​ :</strong> $end_date_formatted</p>
                        <p><strong>មូលហេតុ :</strong> $remarks</p>
                        <a href='http://localhost/elms/view-leave-detail?leave_id={$leaveRequestId}' class='btn'>ចុចទីនេះ</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Leave Management System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";

            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $managerEmail");
                return true;
            } else {
                error_log("Email failed to send to $managerEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function sendEmailNotificationToHOffice($managerEmail, $message, $leaveRequestId, $start_date, $end_date, $duration_days, $leaveType, $remarks, $uremarks, $username, $updatedAt)
    {
        $mail = new PHPMailer(true);

        try {
            // Enable SMTP debugging
            $mail->SMTPDebug = 2; // Or set to 3 for more verbose output
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP Debug level $level; message: $str");
            };

            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com'; // SMTP username
            $mail->Password = 'kyph nvwd ncpa gyzi'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format dates
            $start_date_formatted = (new DateTime($start_date))->format('j F, Y');
            $end_date_formatted = (new DateTime($end_date))->format('j F, Y');
            $updated_at_formatted = (new DateTime($updatedAt))->format('j F, Y H:i:s');

            // Recipients
            $mail->setFrom('no-reply@example.com', 'NO REPLY');
            $mail->addAddress($managerEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Leave Request Notification';
            $body = "
            <html>
            <head>
                <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css'>
                <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
                <style>
                    .profile-img {
                        width: 100px;
                        height: 100px;
                        border-radius: 50%;
                    }
                    .container {
                        max-width: 600px;
                        margin: 0 auto;
                        padding: 20px;
                        border: 1px solid #e2e2e2;
                        border-radius: 10px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    }
                    .header {
                        background-color: #007bff;
                        color: white;
                        padding: 10px;
                        border-radius: 10px 10px 0 0;
                    }
                    .icon {
                        vertical-align: middle;
                        margin-right: 10px;
                    }
                    .content {
                        padding: 20px;
                        background-color: #f9f9f9;
                    }
                    .btn {
                        display: inline-block;
                        padding: 10px 20px;
                        margin-top: 10px;
                        color: white;
                        background-color: #007bff;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .footer {
                        padding: 10px;
                        text-align: center;
                        background-color: #f1f1f1;
                        border-radius: 0 0 10px 10px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h4>
                            <img src='http://localhost/elms/public/img/icons/brands/logo2.png' class='icon' alt='Leave Request' /> 
                            Leave Request Notification
                        </h4>
                    </div>
                    <div class='content'>
                        <p>$username</p>
                        <p><strong>រយៈពេល :</strong> $duration_days ថ្ងៃ</p>
                        <p><strong>ប្រភេទច្បាប់ :</strong> $leaveType</p>
                        <p><strong>ចាប់ពីថ្ងៃ :</strong> $start_date_formatted</p>
                        <p><strong>ដល់ថ្ងៃ​ :</strong> $end_date_formatted</p>
                        <p><strong>មូលហេតុ :</strong> $uremarks</p>
                        <hr>
                        <p>$message</p>"
                . (!empty($remarks) ? "<p><strong>មតិយោបល់ :</strong> $remarks</p>" : "") . "
                        <p><strong>បានអនុម័តនៅថ្ងៃ:</strong> $updated_at_formatted</p>
                        <a href='http://localhost/elms/view-leave-detail?leave_id={$leaveRequestId}' class='btn'>ចុចទីនេះ</a>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date("Y") . " Leave Management System. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
            ";
            $mail->Body = $body;

            if ($mail->send()) {
                error_log("Email sent successfully to $managerEmail");
                return true;
            } else {
                error_log("Email failed to send to $managerEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function sendEmailBackToUser($uEmail, $approved_at, $approved_by, $status, $comment, $title)
    {
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'pothhchamreun@gmail.com';
            $mail->Password = 'kyph nvwd ncpa gyzi';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set charset to UTF-8 for Unicode support
            $mail->CharSet = 'UTF-8';

            // Format date
            $updated_at_formatted = (new DateTime($approved_at))->format('j F, Y H:i:s');

            // Recipients
            $mail->setFrom('no-reply@example.com', "ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់ | $title");
            $mail->addAddress($uEmail);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = "ការស្នើសុំច្បាប់ត្រូវបាន $status";

            // Updated body with modern design
            $body = "
        <html>
        <head>
            <style>
                @font-face {
                    font-family: 'khmer MEF1';
                    src: url('public/dist/fonts/Khmer-MEF1.ttf') format('truetype');
                }
                body {
                    font-family: 'khmer MEF1', Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    line-height: 1.6;
                }
                .container {
                    max-width: 600px;
                    margin: 30px auto;
                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                    padding: 20px;
                }
                .header {
                    background-color: #1abc9c;
                    color: #ffffff;
                    padding: 20px;
                    text-align: center;
                    font-size: 24px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .content {
                    padding: 20px;
                    font-size: 16px;
                    color: #333;
                }
                .content p {
                    margin-bottom: 20px;
                }
                .status-badge {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: " . ($status === 'Approved' ? '#28a745' : '#e74c3c') . ";
                    color: white;
                    font-weight: bold;
                    border-radius: 5px;
                    text-transform: uppercase;
                    font-size: 14px;
                }
                .footer {
                    text-align: center;
                    padding: 15px;
                    background-color: #e9ecef;
                    color: #555555;
                    font-size: 12px;
                    border-top: 1px solid #dddddd;
                    margin-top: 30px;
                }
                .footer a {
                    color: #1abc9c;
                    text-decoration: none;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    $title
                </div>
                <div class='content'>
                    <p><strong>ស្ថានភាព:</strong> <span class='status-badge'>$status</span></p>
                    <p><strong>អនុម័តដោយ:</strong> $approved_by</p>
                    <p><strong>កាលបរិចេ្ឆទអនុម័ត:</strong> $updated_at_formatted</p>"
                . (!empty($comment) ? "<p><strong>មតិយោបល់:</strong> $comment</p>" : "") . "
                </div>
                <div class='footer'>
                    &copy; " . date("Y") . " <a href='#'>ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់</a> | All Rights Reserved.
                </div>
            </div>
        </body>
        </html>
        ";

            $mail->Body = $body;

            // Send email
            if ($mail->send()) {
                error_log("Email sent successfully to $uEmail");
                return true;
            } else {
                error_log("Email failed to send to $uEmail: " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public function deleteLeaveRequest($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM $this->leaveRequest WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // if Manager on leave 
    public function updateApproval($leave_request_id, $approver_id, $status, $remarks)
    {
        // Insert the approval record with the signature
        $stmt = $this->pdo->prepare(
            'INSERT INTO ' . $this->approval . ' (leave_request_id, approver_id, status, remarks, updated_at)
        VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$leave_request_id, $approver_id, $status, $remarks]);

        // Get the updated_at timestamp
        $stmt = $this->pdo->prepare(
            'SELECT updated_at FROM ' . $this->approval . ' WHERE leave_request_id = ? AND approver_id = ? ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute([$leave_request_id, $approver_id]);
        $updatedAt = $stmt->fetchColumn();

        if ($updatedAt === false) {
            throw new Exception("Unable to fetch updated_at timestamp for approval.");
        }

        // Update leave request status based on the approval chain
        $this->updateRequestApproval($leave_request_id, $status);

        return $updatedAt; // Return the updated_at timestamp
    }

    private function updateRequestApproval($leave_request_id, $latestStatus)
    {
        // Fetch the current status of the leave request
        $stmt = $this->pdo->prepare(
            'SELECT dhead_office, num_date FROM ' . $this->leaveRequest . ' WHERE id = ?'
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
            'UPDATE ' . $this->leaveRequest . ' SET head_office = ? WHERE id = ?'
        );
        $stmt->execute([$newStatus, $leave_request_id]);
    }
    //  end if manager on leave 
}
