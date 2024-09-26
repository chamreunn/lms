<?php
require_once 'config/database.php';

class User
{
    private $pdo;

    public $api = "http://127.0.0.1:8000";

    public function getApi()
    {
        return $this->api;
    }

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function authenticateUser($email, $password)
    {
        $url = "{$this->api}/api/login";
        $data = json_encode(['email' => $email, 'password' => $password]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data)
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        // Check if running on localhost
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
            // Ignore SSL certificate verification on localhost (only for development)
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        } else {
            // Enforce SSL certificate verification on live server
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        }

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

    public function getAllUserApi($token)
    {
        $url = "{$this->api}/api/v1/users/";

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

    public function getDepOfficAndDepartment($token, $officeName, $roleName)
    {
        $url = "{$this->api}/api/v1/fetch-deputy-office-department";

        // Prepare query parameters
        $queryParams = [
            'officeName' => $officeName,
            'roleName' => $roleName,
        ];

        // Build the URL with encoded query parameters
        $url .= '?' . http_build_query($queryParams);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json' // Added in case the API expects this header
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Handle cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode
            ];
        }

        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Log the raw response for debugging
        error_log("API Raw Response: " . print_r($responseData, true));

        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return [
                'success' => false,
                'error' => json_last_error_msg(),
                'http_code' => $httpCode
            ];
        }

        // Check if the response contains the expected 'data' field
        if ($httpCode === 200 && isset($responseData['data'])) {
            // Return the correct data with a success message
            return [
                'success' => true,
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            // Handle unexpected API response
            if ($httpCode === 404) {
                error_log("Data not found for office: $officeName and role: $roleName.");
            } elseif ($httpCode === 500) {
                error_log("Internal Server Error from API.");
            } else {
                error_log("Unexpected API response: " . print_r($responseData, true));
            }

            return [
                'success' => false,
                'http_code' => $httpCode,
                'response' => $responseData,
                'error' => isset($responseData['message']) ? $responseData['message'] : 'Unexpected API response'
            ];
        }
    }

    public function updateUserEmailApi($userId, $newEmail, $token)
    {
        $apiUrl = "{$this->api}/api/v1/users/$userId";

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
        $apiUrl = "{$this->api}/api/v1/users/$userId";

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
        $url = "{$this->api}/api/v1/users/" . $id;

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
        $url = "{$this->api}/api/v1/roles/" . $id;

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
        $url = "{$this->api}/api/v1/offices/{$id}";

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
        $url = "{$this->api}/api/v1/departments/" . $id;

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

    // អនុប្រធានការិយាល័យ
    public function getEmailLeaderDOApi($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
                    } else {
                        $firstNameKh[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    } else {
                        $lastNameKh[] = ''; // Fallback to empty if the field is not present
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

    // ជ្រើសរើសថ្នាក់ដឹកនាំ 
    public function getAllManager($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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

            $managers = []; // Consolidated array

            foreach ($leaders as $leader) {
                if (isset($leader['email'], $leader['id'], $leader['firstNameKh'], $leader['lastNameKh'])) {
                    $managers[] = [
                        'email' => $leader['email'],
                        'id' => $leader['id'],
                        'firstNameKh' => $leader['firstNameKh'],
                        'lastNameKh' => $leader['lastNameKh'],
                    ];
                }
            }

            // Log the filtered managers data to check if they are found correctly
            error_log("Filtered Managers: " . print_r($managers, true));

            return [
                'http_code' => $httpCode,
                'managers' => $managers, // Return consolidated managers array
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

        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

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

    // get Admin Email 
    public function getAdminEmails($token)
    {
        $url = "{$this->api}/api/v1/users/";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

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
            $emails = [];
            foreach ($responseData['data'] as $user) {
                if (isset($user['roleLeave']) && $user['roleLeave'] === 'Admin' && isset($user['email'])) {
                    $emails[] = $user['email'];
                }
            }

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // mission to API 
    public function updateMissionToApi($user_id, $start_date, $end_date, $mission, $token)
    {
        $url = "{$this->api}/api/v1/leaves";

        $data = [
            'uid' => $user_id,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'mission' => $mission
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($url);

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

    public function updateLateInToApi($uId, $checkIn, $lateIn, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkIn,
            'lateIn' => $lateIn,
        ];

        // Encode the data to JSON format
        $jsonData = json_encode($data);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Specify the HTTP request method as POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Attach the token for authorization
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL certificate verification (use only for development, not production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode,
                'response' => $response
            ];
        }

        // Close the cURL session
        curl_close($ch);

        // Check for success based on HTTP code (2xx series for success)
        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function updateLateOutToApi($uId, $checkOut, $lateOut, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkOut,
            'lateOut' => $lateOut,
        ];

        // Encode the data to JSON format
        $jsonData = json_encode($data);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Specify the HTTP request method as POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Attach the token for authorization
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL certificate verification (use only for development, not production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode,
                'response' => $response
            ];
        }

        // Close the cURL session
        curl_close($ch);

        // Check for success based on HTTP code (2xx series for success)
        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function updateLeaveEarlyToApi($uId, $checkOut, $exit, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkOut,
            'exit' => $exit,
        ];

        // Encode the data to JSON format
        $jsonData = json_encode($data);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Specify the HTTP request method as POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Attach the token for authorization
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL certificate verification (use only for development, not production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode,
                'response' => $response
            ];
        }

        // Close the cURL session
        curl_close($ch);

        // Check for success based on HTTP code (2xx series for success)
        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function isManagerOnLeaveToday($managerId)
    {
        $today = date('Y-m-d');

        // Check if $managerId is an array
        if (is_array($managerId)) {
            // Convert the array to a comma-separated string for the SQL IN clause
            $placeholders = rtrim(str_repeat('?,', count($managerId)), ',');
            $sql = "SELECT * FROM leave_requests WHERE user_id IN ($placeholders) AND status = 'Approved' AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);

            // Add all the manager IDs to the bind values
            $stmt->execute(array_merge($managerId, [$today]));
        } else {
            // Handle the case where it's a single manager ID
            $sql = "SELECT * FROM leave_requests WHERE user_id = ? AND status = 'Approved' AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$managerId, $today]);
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if the manager has an approved leave request for today
        return !empty($result);
    }

    public function getUserAttendanceByIdApi($id, $token)
    {
        $url = "{$this->api}/api/v1/attendances/user/" . $id;

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

    public function getUserFilterAttendanceByIdApi($userId, $token, $startDate = null, $endDate = null)
    {
        // Base URL for the API
        $url = "{$this->api}/api/v1/attendances/user/" . $userId;

        // Add query parameters for start and end dates if provided
        $queryParams = [];
        if ($startDate) {
            $queryParams['fromDate'] = $startDate;
        }
        if ($endDate) {
            $queryParams['toDate'] = $endDate;
        }

        // If query parameters exist, append them to the URL
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Ignore SSL certificate verification (for local dev)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Handle cURL errors
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

        // Check if the response is successful and contains data
        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data']
            ];
        } else {
            // Log and return unexpected API response
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'error' => "Unexpected API Response",
                'response' => $responseData
            ];
        }
    }
}
