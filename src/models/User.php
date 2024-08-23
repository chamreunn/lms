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
        // echo "<pre>";
        // echo "Request URL: $url\n";
        // echo "Request Data: " . print_r($data, true) . "\n";
        // echo "HTTP Code: $httpCode\n";
        // echo "CURL Error: $error\n";
        // echo "Response: $response\n";
        // echo "</pre>";

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

    public function getdOfficeAdminEmail()
    {
        $stmt = $this->pdo->prepare("
        SELECT users.email AS demail, users.phone_number AS dnumber, users.khmer_name AS dkhmer_name
        FROM users
        JOIN offices ON users.office_id = offices.id AND offices.name = 'ការិយាល័យរដ្ឋបាល និងហិរញ្ញវត្ថុ'
        JOIN departments ON users.department_id = departments.id AND departments.name = 'នាយកដ្ឋានកិច្ចការទូទៅ'
        JOIN positions ON users.position_id = positions.id AND positions.name = 'អនុប្រធានការិយាល័យ'
    ");

        $stmt->execute();

        return $stmt->fetch();
    }

    public function gethOffice()
    {
        $stmt = $this->pdo->prepare("
        SELECT users.*, offices.name AS office_name, offices.hoffice_id, users.email AS hemail, users.phone_number AS hnumber, users.khmer_name AS hkhmer_name
        FROM users 
        JOIN offices ON users.id = offices.hoffice_id
        WHERE offices.id = :id
    ");
        $stmt->execute(['id' => $_SESSION['officeId']]);
        return $stmt->fetch();
    }

    public function getDDepart()
    {
        $stmt = $this->pdo->prepare("
        SELECT users.*, departments.name AS department_name, departments.ddepartment_id, users.email AS demail, users.phone_number AS dnumber, users.khmer_name AS dkhmer_name
        FROM users 
        JOIN departments ON users.id = departments.ddepartment_id
        WHERE departments.id = :id
    ");
        $stmt->execute(['id' => $_SESSION['departmentId']]);
        return $stmt->fetch();
    }

    public function getHDepart()
    {
        $stmt = $this->pdo->prepare("
        SELECT users.*, departments.name AS department_name, departments.hdepartment_id, users.email AS hemail, users.phone_number AS hnumber, users.khmer_name AS hkhmer_name
        FROM users 
        JOIN departments ON users.id = departments.hdepartment_id
        WHERE departments.id = :id
    ");
        $stmt->execute(['id' => $_SESSION['departmentId']]);
        return $stmt->fetch();
    }

    public function getDUnit_1()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = :role");
        $stmt->execute(['role' => "Deputy Of Unit 1"]);

        // Fetch the first user with the role "Deputy Of Unit 2"
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDUnit_2()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE role = :role");
        $stmt->execute(['role' => "Deputy Of Unit 2"]);

        // Fetch the first user with the role "Deputy Of Unit 2"
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
