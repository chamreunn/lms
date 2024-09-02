<?php
require_once 'config/database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function authenticateUser($email, $password)
    {
        $url = 'https://hrms.iauoffsa.us/api/login';
        $data = json_encode(['email' => $email, 'password' => $password]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Ignore SSL certificate verification (only for development)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Debugging output
        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['user'], $responseData['token'])) {
            // Password is assumed to be hashed and verified internally by the API
            return [
                'http_code' => $httpCode,
                'user' => $responseData['user'],
                'token' => $responseData['token']
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function logLoginTrace($userId, $ipAddress)
    {
        $stmt = $this->pdo->prepare('INSERT INTO login_traces (user_id, login_time, ip_address) VALUES (?, NOW(), ?)');
        $stmt->execute([$userId, $ipAddress]);
    }

    public function logUserActivity($userId, $action, $details = null)
    {
        // Assuming you have a PDO connection $pdo
        $stmt = $this->pdo->prepare('INSERT INTO user_activity_log (user_id, action, timestamp, details, ip_address) VALUES (?, ?, NOW(), ?, ?)');
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR']]);
        return true;
    }

    public function create($data)
    {
        // Password hashing
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, username, password_hash, role, khmer_name, english_name, gender, phone_number, date_of_birth, address, department_id, office_id, position_id, profile_picture)
                VALUES (:email, :username, :password, :role, :khmer_name, :english_name, :gender, :phone, :dob, :address, :department, :office, :position, :profile_image)";
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute($data)) {
            print_r($stmt->errorInfo());
            exit();
        }
    }

    public function update($data)
    {
        $sql = "UPDATE users SET 
        email = :email, 
        username = :username, 
        role = :role, 
        khmer_name = :khmer_name, 
        english_name = :english_name, 
        gender = :gender, 
        phone_number = :phone_number, 
        date_of_birth = :dob, 
        address = :address, 
        department_id = :department, 
        office_id = :office, 
        position_id = :position, 
        status = :status, 
        profile_picture = :profile_image 
        WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute($data)) {
            print_r($stmt->errorInfo());
            exit();
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute(['id' => $id])) {
            print_r($stmt->errorInfo());
            exit();
        }
    }

    public function getUserById($user_id)
    {
        $stmt = $this->pdo->prepare(
            'SELECT u.*, m.email AS manager_email
             FROM users u
             LEFT JOIN users m ON u.office_id = m.office_id AND m.position_id = (SELECT doffice_id FROM offices WHERE id = u.office_id)
             WHERE u.id = ?'
        );
        $stmt->execute([$user_id]);
        return $stmt->fetch();
    }

    public function updateProfilePicture($userId, $profilePicturePath)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
        return $stmt->execute([$profilePicturePath, $userId]);
    }

    public function getAllUsers()
    {
        $query = "
        SELECT 
            users.*, 
            departments.name AS department_name, 
            offices.name AS office_name, 
            positions.name AS position_name,
            positions.color AS position_color
        FROM 
            users
        LEFT JOIN 
            departments ON users.department_id = departments.id
        LEFT JOIN 
            offices ON users.office_id = offices.id
        LEFT JOIN 
            positions ON users.position_id = positions.id
    ";

        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC to get an associative array
    }

    public function getUserByPosition($id = null)
    {
        if ($id === null) {
            // Assuming $_SESSION['user_id'] is set and contains the user ID
            $id = $_SESSION['user_id'];
        }

        $stmt = $this->pdo->prepare("
        SELECT users.*, positions.name AS position_name, positions.color AS color 
        FROM users 
        JOIN positions ON users.position_id = positions.id 
        WHERE users.id = :id
    ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();

        if ($result && !empty($result['position_name'])) {
            return $result;
        } else {
            // Position is not valid, handle the error
            $_SESSION['error'] = [
                'title' => "Position Error",
                'message' => "The user's position is not valid or does not exist"
            ];
            header('Location: /elms/error'); // Redirect to an error page or handle it appropriately
            exit;
        }
    }

    public function getAllUserApi($token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/';

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            echo "CURL Error: $error";
            return null;
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            echo "JSON Decode Error: " . json_last_error_msg();
            return null;
        }

        // Check if the response is successful and contains the expected data
        if ($httpCode === 200 && isset($responseData['data'])) {
            $users = $responseData['data'];
            $usersWithRoles = [];

            // Fetch role information for each user
            foreach ($users as $user) {
                $roleResponse = $this->getRoleApi($user['roleId'], $token);
                $user['role'] = ($roleResponse && $roleResponse['http_code'] === 200) ? $roleResponse['data']['roleNameKh'] : 'Unknown';
                $usersWithRoles[] = $user;
            }

            return [
                'http_code' => $httpCode,
                'data' => $usersWithRoles,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            echo "Unexpected API Response: " . print_r($responseData, true);
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function updateUserEmailApi($userId, $newEmail, $token)
    {
        $apiUrl = 'https://hrms.iauoffsa.us/api/v1/users/' . $userId;

        $data = [
            'email' => $newEmail,
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Ensure this is correct
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Using the token from the session
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL certificate verification (use only for debugging)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => $error, 'http_code' => $httpCode, 'response' => $response];
        }

        curl_close($ch);

        return [
            'success' => $httpCode === 200, // Adjust based on API documentation
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function updateUserPasswordApi($userId, $password, $token)
    {
        $apiUrl = 'https://hrms.iauoffsa.us/api/v1/users/' . $userId;

        $data = [
            'password' => $password, // Pass the hashed password
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Use POST or PATCH depending on the API specification
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Using the token from the session
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL verification (not recommended for production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['success' => false, 'error' => $error, 'http_code' => $httpCode, 'response' => $response];
        }

        curl_close($ch);

        return [
            'success' => $httpCode === 200, // Adjust based on API documentation
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function getUserByIdApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/' . $id;

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            return [
                'http_code' => 500,
                'error' => "CURL Error: $error"
            ];
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return [
                'http_code' => $httpCode,
                'error' => "JSON Decode Error: " . json_last_error_msg()
            ];
        }

        // Check if the response is successful and contains the expected data
        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data']
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'error' => "Unexpected API Response",
                'response' => $responseData
            ];
        }
    }

    public function getRoleApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/roles/' . $id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getOfficeApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/offices/' . $id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getDepartmentApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/departments/' . $id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getUserByDepartment($id = null)
    {
        if ($id === null) {
            // Assuming $_SESSION['user_id'] is set and contains the user ID
            $id = $_SESSION['user_id'];
        }

        $stmt = $this->pdo->prepare("SELECT users.*, departments.name AS department_name FROM users JOIN departments ON users.department_id = departments.id WHERE users.id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function getdOffice()
    {
        $stmt = $this->pdo->prepare("
        SELECT users.*, offices.name AS office_name, offices.doffice_id, users.email AS demail, users.phone_number AS dnumber, users.khmer_name AS dkhmer_name
        FROM users 
        JOIN offices ON users.id = offices.doffice_id
        WHERE offices.id = :id
    ");
        $stmt->execute(['id' => $_SESSION['officeId']]);
        return $stmt->fetch();
    }

    // អនុប្រធានការិយាល័យ
    public function getEmailLeaderDOApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'អនុប្រធានការិយាល័យ') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // ប្រធានការិយាល័យ
    public function getEmailLeaderHOApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធានការិយាល័យ') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // អនុប្រធាននាយកដ្ឋាន
    public function getEmailLeaderDDApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'អនុប្រធាននាយកដ្ឋាន') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // ប្រធាននាយកដ្ឋាន
    public function getEmailLeaderHDApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធាននាយកដ្ឋាន') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // អនុប្រធានអង្គភាព
    public function getEmailLeaderDHU1Api($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleLeave']) && $leader['roleLeave'] === 'Deputy Head Of Unit 1') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getEmailLeaderDHU2Api($id, $token)
    {
        $url = 'http://127.0.0.1:8000/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleLeave']) && $leader['roleLeave'] === 'Deputy Head Of Unit 2') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getEmailLeaderHUApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/leader/contact/' . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធានអង្គភាព') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }
}
