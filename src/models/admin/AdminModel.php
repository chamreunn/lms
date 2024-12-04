<?php
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class AdminModel
{
    private $pdo;

    protected $table_name = "late_in_out";

    protected $lateApproval = "late_approvals";

    protected $approval = "leave_approvals";

    protected $leaveRequest = "leave_requests";

    protected $table = "missions";

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

    public function getAllPendingLate()
    {
        // Fetch only late-in records without joining the users table
        $query = "SELECT * FROM $this->table_name WHERE status = 'Pending'";

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
                $record['uId'] = $user['id'] ?? 'Unknown';
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

    public function getAllApprovedLate($status, $offset, $limit)
    {
        // Fetch only late-in records with status 'Approved' and apply pagination
        $query = "SELECT * FROM $this->table_name WHERE status = '$status' ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each approved late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the approved late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['uId'] = $user['id'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getAllTodayLate($status, $offset, $limit)
    {
        // Fetch only late-in records with status 'Approved' for today and apply pagination
        $query = "SELECT * FROM $this->table_name 
              WHERE status = :status AND DATE(date) = CURDATE() 
              ORDER BY id DESC 
              LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each approved late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the approved late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['uId'] = $user['id'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getTodayLateCount($status)
    {
        // Query to count late-in records with status 'Approved' for today
        $query = "SELECT COUNT(*) AS late_count FROM $this->table_name 
              WHERE status = :status AND DATE(date) = CURDATE()";

        $stmt = $this->pdo->prepare($query);

        // Bind the status parameter
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        // Execute the query
        $stmt->execute();

        // Fetch the count result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of late-in records
        return $result['late_count'];
    }


    public function getTotalTodayLate($status)
    {
        // Count only the late-in records with the given status for today
        $query = "SELECT COUNT(*) FROM $this->table_name 
              WHERE status = :status AND DATE(date) = CURDATE()";

        $stmt = $this->pdo->prepare($query);

        // Bind the status parameter to prevent SQL injection
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getTotalApprovedLate($status)
    {
        $query = "SELECT COUNT(*) FROM $this->table_name WHERE status = '$status' ORDER BY id DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getTotalMission()
    {
        $query = "SELECT COUNT(*) FROM $this->table ORDER BY id DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function getMissionsTodayCount()
    {
        // Query to count missions active today
        $query = "SELECT COUNT(*) FROM $this->table 
              WHERE start_date <= CURDATE() 
              AND end_date >= CURDATE()";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Execute the query
        $stmt->execute();

        // Fetch and return the count as an integer
        return (int) $stmt->fetchColumn();
    }

    public function getLateCountByStatus($status)
    {
        // SQL query to count records with the given status and where updated_at is today
        $query = "SELECT COUNT(*) as count 
              FROM $this->table_name 
              WHERE status = :status 
              AND DATE(updated_at) = CURDATE()";

        // Prepare and execute the statement
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the count result
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If the result is empty or null, default to 0
        return $result['count'] ?? 0;
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

    public function getUserByIdAPI($user_id)
    {
        $userModel = new User();

        // Fetch user details from both APIs
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);
        $userInformationApiResponse = $userModel->getUserInformationByIdApi($user_id, $_SESSION['token']);

        // Initialize default user details
        $defaultDetails = [
            'rolename' => 'N/A',
            'user_name' => 'N/A',
            'user_id' => 'N/A',
            'email' => 'N/A',
            'office_id' => 'N/A',
            'office_name' => 'N/A',
            'department_id' => 'N/A',
            'department_name' => 'N/A',
            'position_name' => 'N/A',
            'profile_picture' => 'default-profile.png',
            'date_of_birth' => 'N/A',
            'gender' => 'N/A',
            'user_eng_name' => 'N/A',
            'active' => 'N/A',
            'activeStatus' => 'N/A',
            'address' => 'N/A',
            'curaddress' => 'N/A',
            'password' => 'N/A',
            'marital_status' => 'N/A',
            'dob' => 'N/A',
            'identify_card' => 'N/A',
            'exprireDateIdenCard' => 'N/A',
            'passport' => 'N/A',
            'exprirePassport' => 'N/A',
            'nationality' => 'N/A',
            'date_enteing_public_service' => 'N/A',
            'user_information' => [],
            'additional_position_current_job' => [],
            'working_history_public' => [],
            'working_history_private' => [],
            'modal_certificate' => [],
            'education_level' => [],
            'ability_language' => [],
            'family' => [],
            'documents' => []
        ];

        // Extract main user data
        $user = $userApiResponse['data'] ?? [];

        // Extract user information data
        $userInformation = $userInformationApiResponse['response'] ?? [];

        // Map and merge data into user details
        $userDetails = array_merge($defaultDetails, [
            'user_id' => $user['id'] ?? 'N/A',
            'email' => $user['email'] ?? 'N/A',
            'phone_number' => $user['phoneNumber'] ?? 'N/A',
            'user_name' => ($user['lastNameKh'] ?? '') . " " . ($user['firstNameKh'] ?? 'N/A'),
            'profile_picture' => isset($user['image']) ? 'https://hrms.iauoffsa.us/images/' . $user['image'] : 'default-profile.png',
            'date_of_birth' => $user['dateOfBirth'] ?? 'N/A',
            'gender' => $user['gender'] === 'f' ? 'ស្រី' : 'ប្រុស',
            'user_eng_name' => $user['engName'] ?? 'N/A',
            'active' => isset($user['active']) && $user['active'] === '1' ? 'Active' : 'Inactive',
            'activeStatus' => $user['active'] ?? 'N/A',
            'address' => $user['pobAddress'] ?? 'N/A',
            'curaddress' => $user['currentAddress'] ?? 'N/A',
            'password' => $user['password'] ?? 'N/A',
            'marital_status' => $this->getMaritalStatus($user['status'] ?? '') ?? 'N/A',
            'dob' => $user['dateOfBirth'] ?? 'N/A',
            'identify_card' => $user['identifyCard'] ?? 'N/A',
            'exprireDateIdenCard' => $user['exprireDateIdenCard'] ?? 'N/A',
            'passport' => $user['passport'] ?? 'N/A',
            'exprirePassport' => $user['exprirePassport'] ?? 'N/A',
            'nationality' => $user['nationality'] ?? 'N/A',

            // User information data
            'date_enteing_public_service' => $userInformation['userInformation'][0]['date_enteing_public_service'] ?? 'N/A',
            'economy_enteing_public_service' => $userInformation['userInformation'][0]['economy_enteing_public_service'] ?? 'N/A',
            'user_information' => $userInformation['userInformation'] ?? [],
            'additional_position_current_job' => !empty($userInformation['additionalPositionCurrentJob']) ? $userInformation['additionalPositionCurrentJob'] : [],
            'working_history_public' => !empty($userInformation['userWoringHistoryPublicSetor']) ? $userInformation['userWoringHistoryPublicSetor'] : [],
            'working_history_private' => !empty($userInformation['userWoringHistoryPrivateSetor']) ? $userInformation['userWoringHistoryPrivateSetor'] : [],
            'modal_certificate' => !empty($userInformation['userModalCertificate']) ? $userInformation['userModalCertificate'] : [],
            'education_level' => !empty($userInformation['userEducationLevel']) ? $userInformation['userEducationLevel'] : [],
            'ability_language' => !empty($userInformation['userAbilityLanguage']) ? $userInformation['userAbilityLanguage'] : [],
            'family' => $userInformation['userFamily'] ?? [],
            'documents' => !empty($userInformation['userDocument']) ? $userInformation['userDocument'] : []
        ]);

        // Fetch role, office, and department details
        $roleApiResponse = $userModel->getRoleApi($user['roleId'] ?? null, $_SESSION['token']);
        if ($roleApiResponse && $roleApiResponse['http_code'] === 200 && isset($roleApiResponse['data'])) {
            $userDetails['rolename'] = $roleApiResponse['data']['roleNameKh'] ?? 'N/A';
            $userDetails['position_color'] = $roleApiResponse['data']['color'] ?? 'N/A';
        }

        $officeApiResponse = $userModel->getOfficeApi($user['officeId'] ?? null, $_SESSION['token']);
        if ($officeApiResponse && $officeApiResponse['http_code'] === 200 && isset($officeApiResponse['data'])) {
            $userDetails['office_name'] = $officeApiResponse['data']['officeNameKh'] ?? 'N/A';
            $userDetails['office_id'] = $officeApiResponse['data']['id'] ?? 'N/A';
        }

        $departmentApiResponse = $userModel->getDepartmentApi($user['departmentId'] ?? null, $_SESSION['token']);
        if ($departmentApiResponse && $departmentApiResponse['http_code'] === 200 && isset($departmentApiResponse['data'])) {
            $userDetails['department_name'] = $departmentApiResponse['data']['departmentNameKh'] ?? 'N/A';
            $userDetails['department_id'] = $departmentApiResponse['data']['id'] ?? 'N/A';
        }

        return $userDetails;
    }

    /**
     * Helper function to map marital status.
     */
    private function getMaritalStatus($status)
    {
        switch ($status) {
            case '1':
                return 'នៅលីវ'; // Single
            case '2':
                return 'ភ្ជាប់ពាក្យ'; // Engaged
            case '3':
                return 'រៀបអាពាហ៍ពិពាហ៍'; // Married
            default:
                return 'N/A';
        }
    }

    public function getUserLeaveRequests($user_id)
    {
        // Fetch only leave requests without joining the users table
        $query = "SELECT lr.*, lr.status AS leave_status, lr.id AS leave_id, lr.leave_type_id 
              FROM leave_requests lr 
              WHERE lr.user_id = ? AND lr.status = 'Approved' 
              ORDER BY lr.id DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user_id]);

        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all leave requests for the user

        $userModel = new User();

        // Fetch user data from the API
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

        // Check if the API response is successful
        if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Add user information to each leave request record
            foreach ($leaveRequests as &$record) {
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
            }
        } else {
            // If the API call fails or returns no data, add placeholder data
            foreach ($leaveRequests as &$record) {
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png';
            }
        }

        return $leaveRequests;
    }

    public function countUserApprovedLeaveRequests($user_id)
    {
        // Query to count only approved leave requests for the user
        $stmt = $this->pdo->prepare(
            'SELECT 
            COUNT(*) AS leave_request_count
        FROM 
            leave_requests lr
        WHERE 
            lr.user_id = ? AND lr.status = "Approved"'
        );
        $stmt->execute([$user_id]);

        // Fetch the count result
        return $stmt->fetchColumn(); // Return the count of approved leave requests
    }

    public function getOvertimeIn($user_id)
    {
        // Fetch overtime records without joining the users table
        $stmt = $this->pdo->prepare('
            SELECT late_in_out.*, late_in_out.status AS overtime_status, late_in_out.id AS overtime_id
            FROM late_in_out
            WHERE late_in_out.user_id = ? 
            AND late_in_out.late_in IS NOT NULL 
            AND (late_in_out.status = "Approved" OR late_in_out.status = "Rejected")
            ORDER BY late_in_out.created_at DESC
        ');
        $stmt->execute([$user_id]);

        $overtimeRecords = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all overtime records

        $userModel = new User();

        // Fetch user data from the API
        $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

        // Check if the API response is successful
        if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
            $user = $userApiResponse['data']; // Assuming the API returns a single user object

            // Add user information to each overtime record
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['office_name'] = $user['office']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
            }
        } else {
            // If the API call fails or returns no data, add placeholder data
            foreach ($overtimeRecords as &$record) {
                $record['khmer_name'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['office_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png';
            }
        }

        return $overtimeRecords;
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
         FROM ' . $this->leaveRequest . ' lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.id = ?'
        );
        $stmt->execute([$leave_id]);
        $leaveRequest = $stmt->fetch();

        if ($leaveRequest) {
            // Check if an attachment is required and if it is missing
            if ($leaveRequest['attRequired'] === 'Yes') {
                if (empty($leaveRequest['attachment'])) {
                    // Handle the case where attachment is required but missing
                    error_log("Attachment required for leave request ID: $leave_id but is missing.");
                    $leaveRequest['attachment_error'] = "Attachment is required for this leave type.";
                } else {
                    $leaveRequest['attachment_error'] = null; // No issues regarding attachment
                }
            } else {
                $leaveRequest['attachment_error'] = null; // No attachment required
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

                // Set profile image or first letter of the name if image is not found
                $profileImage = 'https://hrms.iauoffsa.us/images/' . $userData['image'] ?? null;

                if (empty($userData['image'])) {
                    // Generate the first alphabet of the Khmer name
                    $firstLetter = mb_substr($userData['lastNameKh'] ?? '', 0, 1, 'UTF-8') ?: mb_substr($userData['firstNameKh'] ?? '', 0, 1, 'UTF-8');
                    $leaveRequest['profile'] = '<div class="avatar">' . $firstLetter . '</div>'; // Placeholder for initials
                } else {
                    $leaveRequest['profile'] = $profileImage; // Set profile image
                }
            } else {
                // Handle API error or missing data
                error_log("Failed to fetch user data for leave request ID: $leave_id");
                $leaveRequest['khmer_name'] = null;
                $leaveRequest['phone_number'] = null;
                $leaveRequest['dob'] = null;
                $leaveRequest['deputy_head_name'] = null;
                $leaveRequest['profile'] = '<div class="avatar-placeholder">N/A</div>'; // Placeholder for missing profile
            }

            // Optional: Add logic to fetch approvals, office positions, etc.
            $leaveRequest['approvals'] = $this->getApprovalsByLeaveRequestId($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['doffice'] = $this->getDOfficePositions($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['hoffice'] = $this->getHOfficePositions($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['ddepartment'] = $this->getDDepartmentPositions($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['hdepartment'] = $this->getHDepartmentPositions($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['dunit'] = $this->getDUnitPositions($leaveRequest['id'], $_SESSION['token']);
            $leaveRequest['unit'] = $this->getUnitPositions($leaveRequest['id'], $_SESSION['token']);
        }

        return $leaveRequest;
    }


    public function getApprovalsByLeaveRequestId($leave_request_id, $token)
    {
        // Query to get approval details without fetching user and position data directly
        $stmt = $this->pdo->prepare(
            'SELECT a.*,   -- Include the signature column
                (SELECT COUNT(*) FROM ' . $this->approval . ' WHERE leave_request_id = ?) AS approval_count FROM leave_approvals a WHERE a.leave_request_id = ? ORDER BY id DESC'
        );

        // Execute the query with the leave request ID parameter
        $stmt->execute([$leave_request_id, $leave_request_id]);
        $approvals = $stmt->fetchAll();

        // Check if an attachment is required for the leave type
        $attachmentStmt = $this->pdo->prepare(
            'SELECT lt.attachment_required 
         FROM ' . $this->leaveRequest . ' lr
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

    private function fetchAttachmentsByLeaveRequestId($leave_request_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leave_attachments WHERE leave_request_id = ?');
        $stmt->execute([$leave_request_id]);
        return $stmt->fetchAll();
    }

    public function getDOfficePositions($leave_request_id, $token)
    {
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
            FROM leave_approvals a
            WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'អនុប្រធានការិយាល័យ') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
        return $results;
    }

    public function getHOfficePositions($leave_request_id, $token)
    {
        // Query to get the approval details
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
            FROM leave_approvals a
            WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'ប្រធានការិយាល័យ') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
        return $results;
    }

    public function getHDepartmentPositions($leave_request_id, $token)
    {
        // Query to get the approval details
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
            FROM leave_approvals a
            WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'ប្រធាននាយកដ្ឋាន') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
        return $results;
    }

    public function getDDepartmentPositions($leave_request_id, $token)
    {
        // Query to get the approval details
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
            FROM leave_approvals a
            WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'អនុប្រធាននាយកដ្ឋាន') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
        return $results;
    }

    public function getDUnitPositions($leave_request_id, $token)
    {
        // Query to get the approval details
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
            FROM leave_approvals a
            WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'អនុប្រធានអង្គភាព') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
        return $results;
    }

    public function getUnitPositions($leave_request_id, $token)
    {
        // Query to get the approval details
        $stmt = $this->pdo->prepare(
            'SELECT a.approver_id ,a.status AS approver_status
        FROM leave_approvals a
        WHERE a.leave_request_id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $approvals = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize an array to hold combined results
        $results = [];

        // Fetch user details and position details for each approver
        foreach ($approvals as $approval) {
            // Fetch user information using the API
            $userModel = new User();
            $userApiResponse = $userModel->getUserByIdApi($approval['approver_id'], $token);

            if ($userApiResponse && isset($userApiResponse['data'])) {
                // Fetch position details using the API (position details should be part of user data)
                $positionApiResponse = $userModel->getRoleApi($userApiResponse['data']['roleId'], $token);

                if ($positionApiResponse && $positionApiResponse['http_code'] === 200 && isset($positionApiResponse['data'])) {
                    // Check if the position name matches "ប្រធានការិយាល័យ"
                    if ($positionApiResponse['data']['roleNameKh'] === 'ប្រធានអង្គភាព') {
                        // Combine approval details with user and position details
                        $results[] = array_merge($approval, [
                            'approver_name' => $userApiResponse['data']['firstNameKh'] ?? 'Unknown',
                            'profile_picture' => $userApiResponse['data']['profile_picture'] ?? null,
                            'position_name' => $positionApiResponse['data']['roleNameKh'] ?? 'Unknown Position',
                            'position_color' => $positionApiResponse['data']['color'] ?? 'N/A',
                            'updated_at' => $positionApiResponse['data']['updated_at'] ?? 'N/A',
                        ]);
                    }
                } else {
                    // Handle case where position details are not found or do not match
                    // Note: Here, we skip adding the result if position details are not found or do not match
                }
            } else {
                // Handle case where user details are not found
                // Note: Here, we skip adding the result if user details are not found
            }
        }

        // Return the combined results
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

    public function getApprovedLateByFilter($filters)
    {
        // Base query for fetching approved late-in records
        $query = "SELECT * FROM $this->table_name WHERE status = 'Approved'";

        // Initialize an array for the parameters to bind
        $params = [];

        // Filter by start_date if provided
        if (!empty($filters['start_date'])) {
            $query .= " AND date >= :start_date";
            $params[':start_date'] = $filters['start_date']; // Binding start date parameter
        }

        // Filter by end_date if provided
        if (!empty($filters['end_date'])) {
            $query .= " AND date <= :end_date";
            $params[':end_date'] = $filters['end_date']; // Binding end date parameter
        }

        // Filter by type if provided
        if (!empty($filters['type'])) {
            $query .= " AND type = :type"; // Add type filter to the query
            $params[':type'] = $filters['type']; // Binding type parameter
        }

        // Apply pagination limits
        $query .= " LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the pagination parameters
        $limit = $filters['limit'] ?? 5; // Default limit to 5 if not specified
        $offset = $filters['offset'] ?? 0; // Default offset to 0 if not specified
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Bind additional parameters if they exist
        foreach ($params as $key => &$value) {
            $stmt->bindParam($key, $value);
        }

        // Execute the statement
        $stmt->execute();

        // Fetch the results
        $lateInRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each approved late-in record
        foreach ($lateInRecords as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the approved late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['uId'] = $user['id'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $lateInRecords;
    }

    public function getMissionFilter($filters)
    {
        // Start with a base query
        $query = "SELECT * FROM $this->table WHERE 1=1";

        // Prepare parameters for binding
        $params = [];

        // Filter by start_date if provided
        if (!empty($filters['start_date'])) {
            $query .= " AND start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        // Filter by end_date if provided
        if (!empty($filters['end_date'])) {
            $query .= " AND end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Apply pagination limits
        $query .= " LIMIT :limit OFFSET :offset";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Bind limit and offset
        $stmt->bindValue(':limit', $filters['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $filters['offset'], PDO::PARAM_INT);

        // Bind additional parameters if provided
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Execute the query
        $stmt->execute();

        // Fetch results
        $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each approved late-in record
        foreach ($missions as &$record) {
            // Fetch user_id from the current late-in record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the approved late-in record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['uId'] = $user['id'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $missions;
    }

    public function getMissionTodayFilter($filters)
    {
        // Start with a base query to fetch all missions
        $query = "SELECT * FROM $this->table WHERE 1=1"; // `1=1` allows easy appending of additional conditions

        // Prepare parameters for binding
        $params = [];

        // Filter by start_date if provided
        if (!empty($filters['start_date'])) {
            $query .= " AND start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        // Filter by end_date if provided
        if (!empty($filters['end_date'])) {
            $query .= " AND end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Apply pagination limits
        $query .= " LIMIT :limit OFFSET :offset";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Bind limit and offset parameters for pagination
        $stmt->bindValue(':limit', $filters['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $filters['offset'], PDO::PARAM_INT);

        // Bind any additional parameters (start_date and end_date) if provided
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Execute the query
        $stmt->execute();

        // Fetch the result set as an associative array
        $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assuming you want to fetch additional user details for each mission
        $userModel = new User();

        // Add user information to each mission record
        foreach ($missions as &$mission) {
            // Check if 'user_id' exists in the current mission
            if (isset($mission['user_id'])) {
                $user_id = $mission['user_id'];

                // Fetch user data from API using the user_id and session token
                $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

                // Check if the API response is successful and contains data
                if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                    $user = $userApiResponse['data']; // API returns user data

                    // Append user info to the mission record
                    $mission['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                        ? $user['lastNameKh'] . " " . $user['firstNameKh']
                        : 'Unknown';
                    $mission['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                    $mission['uId'] = $user['id'] ?? 'Unknown';
                    $mission['email'] = $user['email'] ?? 'Unknown';
                    $mission['department_name'] = $user['department']['name'] ?? 'Unknown';
                    $mission['position_name'] = $user['position']['name'] ?? 'Unknown';
                    $mission['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
                } else {
                    // Handle cases where the API call fails or returns no data
                    $mission['khmer_name'] = 'Unknown';
                    $mission['dob'] = 'Unknown';
                    $mission['email'] = 'Unknown';
                    $mission['department_name'] = 'Unknown';
                    $mission['position_name'] = 'Unknown';
                    $mission['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
                }
            }
        }

        return $missions;
    }

    public function getTotalMissionCount()
    {
        // Query to count total records
        $query = "SELECT COUNT(*) AS total FROM $this->table";
        $stmt = $this->pdo->query($query);

        // Fetch the total count
        return $stmt->fetchColumn();
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

    public function getApprovedLeaveCount()
    {
        $query = "
        SELECT COUNT(*) as approved_count
        FROM $this->leaveRequest
        WHERE status = 'Approved'
        AND MONTH(start_date) = MONTH(CURRENT_DATE())
        AND YEAR(start_date) = YEAR(CURRENT_DATE())
        AND MONTH(end_date) = MONTH(CURRENT_DATE())
        AND YEAR(end_date) = YEAR(CURRENT_DATE())
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['approved_count'];
    }

    public function getLatesInCount()
    {
        $query = "
    SELECT COUNT(*) as late_in_count
    FROM $this->table_name
    WHERE late_in IS NOT NULL
    AND status = 'Approved'
    AND MONTH(date) = MONTH(CURRENT_DATE())
    AND YEAR(date) = YEAR(CURRENT_DATE())
    ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['late_in_count'];
    }

    public function getLatesOutCount()
    {
        $query = "
        SELECT COUNT(*) as late_out_count
        FROM $this->table_name
        WHERE late_out IS NOT NULL
        AND status = 'Approved'
        AND MONTH(date) = MONTH(CURRENT_DATE())
        AND YEAR(date) = YEAR(CURRENT_DATE())
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['late_out_count'];
    }

    public function getLeavesEarlyCount()
    {
        $query = "
        SELECT COUNT(*) as leave_early_count
        FROM $this->table_name
        WHERE leave_early IS NOT NULL
        AND status = 'Approved'
        AND MONTH(date) = MONTH(CURRENT_DATE())
        AND YEAR(date) = YEAR(CURRENT_DATE())
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['leave_early_count'];
    }

    public function getMissions()
    {
        $query = "
        SELECT COUNT(*) as missions
        FROM $this->table
        WHERE MONTH(start_date) = MONTH(CURRENT_DATE())
        AND YEAR(start_date) = YEAR(CURRENT_DATE())
         AND MONTH(end_date) = YEAR(CURRENT_DATE())
          AND YEAR(end_date) = YEAR(CURRENT_DATE())
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC)['missions'];
    }

    public function getAllMissions($offset, $limit)
    {
        $query = "SELECT * FROM $this->table ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $userModel = new User();

        foreach ($missions as &$mission) {
            if (isset($mission['user_id'])) {
                $user_id = $mission['user_id'];

                // Use retry function for API call
                $user = $this->fetchUserDataWithRetry($userModel, $user_id, $_SESSION['token']);

                if ($user) {
                    $mission['khmer_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'];
                    $mission['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                    $mission['uId'] = $user['id'] ?? 'Unknown';
                    $mission['email'] = $user['email'] ?? 'Unknown';
                    $mission['department_name'] = $user['department']['name'] ?? 'Unknown';
                    $mission['position_name'] = $user['position']['name'] ?? 'Unknown';
                    $mission['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
                } else {
                    // Placeholder data if API retries fail
                    $mission['khmer_name'] = 'Unknown';
                    $mission['dob'] = 'Unknown';
                    $mission['email'] = 'Unknown';
                    $mission['department_name'] = 'Unknown';
                    $mission['position_name'] = 'Unknown';
                    $mission['profile_picture'] = 'default-profile.png';
                }
            }
        }

        return $missions;
    }

    private function fetchUserDataWithRetry($userModel, $user_id, $token, $retries = 3)
    {
        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            $userApiResponse = $userModel->getUserByIdApi($user_id, $token);

            // Check if response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                return $userApiResponse['data'];
            }

            // Small delay between retries
            usleep(200000); // 200 milliseconds
        }

        return null; // Return null if all retries fail
    }

    public function getAllMissionTodays($offset, $limit)
    {
        // Query to fetch all missions where today's date falls between start_date and end_date
        $query = "SELECT * FROM $this->table 
              WHERE start_date <= CURDATE() 
              AND end_date >= CURDATE() ORDER BY id DESC
              LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        // Execute the query
        $stmt->execute();

        // Fetch all matching missions
        $missions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assuming you want to fetch additional user details for each mission
        $userModel = new User();

        foreach ($missions as &$mission) {
            // Check if 'user_id' exists in the current mission
            if (isset($mission['user_id'])) {
                $user_id = $mission['user_id'];

                // Fetch user data from API using the user_id and session token
                $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

                // Process API response and append user details to the mission
                if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                    $user = $userApiResponse['data']; // API returns user data

                    // Append user info to the mission record
                    $mission['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                        ? $user['lastNameKh'] . " " . $user['firstNameKh']
                        : 'Unknown';
                    $mission['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                    $mission['uId'] = $user['id'] ?? 'Unknown';
                    $mission['email'] = $user['email'] ?? 'Unknown';
                    $mission['department_name'] = $user['department']['name'] ?? 'Unknown';
                    $mission['position_name'] = $user['position']['name'] ?? 'Unknown';
                    $mission['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
                } else {
                    // If API call fails or no data is returned, set default values
                    $mission['khmer_name'] = 'Unknown';
                    $mission['dob'] = 'Unknown';
                    $mission['email'] = 'Unknown';
                    $mission['department_name'] = 'Unknown';
                    $mission['position_name'] = 'Unknown';
                    $mission['profile_picture'] = 'default-profile.png'; // Default image
                }
            }
        }

        return $missions;
    }

    public function getLeaves($offset, $limit)
    {
        // Fetch all leave requests from the leave_requests table and join with leave_types table
        $query = "SELECT lr.*, lt.name AS leave_type_name, lt.color AS leaveTypeColor
              FROM leave_requests lr
              LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
              LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assuming you want to fetch user details for the leave requests
        $userModel = new User();

        foreach ($leaveRequests as &$leaveRequest) {
            // Fetch user_id from the current leave request record if it exists
            if (isset($leaveRequest['user_id'])) {
                $user_id = $leaveRequest['user_id'];

                // Fetch user data from API using the user_id
                $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

                // Check if the API response is successful
                if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                    $user = $userApiResponse['data']; // Assuming the API returns a single user object

                    // Add user information to the leave request record
                    $leaveRequest['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                        ? $user['lastNameKh'] . " " . $user['firstNameKh']
                        : 'Unknown';
                    $leaveRequest['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                    $leaveRequest['uId'] = $user['id'] ?? 'Unknown';
                    $leaveRequest['email'] = $user['email'] ?? 'Unknown';
                    $leaveRequest['department_name'] = $user['department']['name'] ?? 'Unknown';
                    $leaveRequest['position_name'] = $user['position']['name'] ?? 'Unknown';
                    $leaveRequest['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
                } else {
                    // Handle cases where the API call fails or returns no data
                    $leaveRequest['khmer_name'] = 'Unknown';
                    $leaveRequest['dob'] = 'Unknown';
                    $leaveRequest['email'] = 'Unknown';
                    $leaveRequest['department_name'] = 'Unknown';
                    $leaveRequest['position_name'] = 'Unknown';
                    $leaveRequest['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
                }
            }
        }

        return $leaveRequests;
    }

    public function getTotalLeaveCount()
    {
        // Query to count total records in the leave table
        $query = "SELECT COUNT(*) AS total FROM $this->leaveRequest";

        // Use prepared statement for safety and avoid side effects
        $stmt = $this->pdo->query($query);

        // Fetch the total count
        return $stmt->fetchColumn();
    }

    public function getLeaveFilters($filters)
    {
        // Start with a base query
        $query = "SELECT lr.*, lt.name AS leave_type_name, lt.color AS leaveTypeColor
              FROM $this->leaveRequest lr
              LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
              WHERE 1=1 ORDER BY lr.id DESC";

        // Prepare parameters for binding
        $params = [];

        // Filter by start_date if provided
        if (!empty($filters['start_date'])) {
            $query .= " AND start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }

        // Filter by end_date if provided
        if (!empty($filters['end_date'])) {
            $query .= " AND end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        // Filter by status if provided
        if (!empty($filters['status'])) {
            $query .= " AND status = :status";
            $params[':status'] = $filters['status'];
        }

        // Apply pagination limits
        $query .= " LIMIT :limit OFFSET :offset";

        // Prepare the query
        $stmt = $this->pdo->prepare($query);

        // Bind limit and offset
        $stmt->bindValue(':limit', $filters['limit'], PDO::PARAM_INT);
        $stmt->bindValue(':offset', $filters['offset'], PDO::PARAM_INT);

        // Bind additional parameters if provided
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // Execute the query
        $stmt->execute();

        // Fetch results
        $leaves = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $userModel = new User();

        // Add user information and additional data to each leave record
        foreach ($leaves as &$record) {
            // Fetch user_id from the current leave record
            $user_id = $record['user_id'];

            // Fetch user data from API using the user_id
            $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave record
                $record['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                    ? $user['lastNameKh'] . " " . $user['firstNameKh']
                    : 'Unknown';
                $record['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $record['uId'] = $user['id'] ?? 'Unknown';
                $record['email'] = $user['email'] ?? 'Unknown';
                $record['department_name'] = $user['department']['name'] ?? 'Unknown';
                $record['position_name'] = $user['position']['name'] ?? 'Unknown';
                $record['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $record['khmer_name'] = 'Unknown';
                $record['dob'] = 'Unknown';
                $record['email'] = 'Unknown';
                $record['department_name'] = 'Unknown';
                $record['position_name'] = 'Unknown';
                $record['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
            }
        }

        return $leaves;
    }

    public function getLeaveToday($offset, $limit)
    {
        $query = "SELECT lr.*, lt.name AS leave_type_name, lt.color AS leaveTypeColor
              FROM leave_requests lr
              LEFT JOIN leave_types lt ON lr.leave_type_id = lt.id
              WHERE CURDATE() BETWEEN lr.start_date AND lr.end_date AND lr.status = 'Approved'
              LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        // Bind the parameters to prevent SQL injection
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assuming you want to fetch user details for the leave requests
        $userModel = new User();

        foreach ($leaveRequests as &$leaveRequest) {
            // Fetch user_id from the current leave request record if it exists
            if (isset($leaveRequest['user_id'])) {
                $user_id = $leaveRequest['user_id'];

                // Fetch user data from API using the user_id
                $userApiResponse = $userModel->getUserByIdApi($user_id, $_SESSION['token']);

                // Check if the API response is successful
                if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data'])) {
                    $user = $userApiResponse['data']; // Assuming the API returns a single user object

                    // Add user information to the leave request record
                    $leaveRequest['khmer_name'] = isset($user['lastNameKh']) && isset($user['firstNameKh'])
                        ? $user['lastNameKh'] . " " . $user['firstNameKh']
                        : 'Unknown';
                    $leaveRequest['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                    $leaveRequest['uId'] = $user['id'] ?? 'Unknown';
                    $leaveRequest['email'] = $user['email'] ?? 'Unknown';
                    $leaveRequest['department_name'] = $user['department']['name'] ?? 'Unknown';
                    $leaveRequest['position_name'] = $user['position']['name'] ?? 'Unknown';
                    $leaveRequest['profile_picture'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png'); // Use a default profile image if none exists
                } else {
                    // Handle cases where the API call fails or returns no data
                    $leaveRequest['khmer_name'] = 'Unknown';
                    $leaveRequest['dob'] = 'Unknown';
                    $leaveRequest['email'] = 'Unknown';
                    $leaveRequest['department_name'] = 'Unknown';
                    $leaveRequest['position_name'] = 'Unknown';
                    $leaveRequest['profile_picture'] = 'default-profile.png'; // Use a default profile image if API fails
                }
            }
        }

        return $leaveRequests;
    }

    public function getLeaveTodayCount()
    {
        $query = "SELECT COUNT(*) AS leave_count
              FROM leave_requests lr
              WHERE CURDATE() BETWEEN lr.start_date AND lr.end_date
              AND lr.status = 'Approved'";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['leave_count'] ?? 0; // Return the count or 0 if no result found
    }

    public function getAllIPAddresses()
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ip_addresses');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createIP($ipAddress)
    {
        $stmt = $this->pdo->prepare('INSERT INTO ip_addresses (ip_address, status) VALUES (:ip_address, 1)');
        return $stmt->execute([':ip_address' => $ipAddress]);
    }

    public function updateIPStatus($id, $status)
    {
        $stmt = $this->pdo->prepare('UPDATE ip_addresses SET status = :status WHERE id = :id');
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    public function deleteIP($id)
    {
        $stmt = $this->pdo->prepare('DELETE FROM ip_addresses WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getIPByAddress($ipAddress)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM ip_addresses WHERE ip_address = :ip_address LIMIT 1");
        $stmt->execute([':ip_address' => $ipAddress]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}
