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

    public function logLoginTrace($userId, $ipAddress)
    {
        $stmt = $this->pdo->prepare('INSERT INTO login_traces (user_id, login_time, ip_address) VALUES (?, NOW(), ?)');
        $stmt->execute([$userId, $ipAddress]);
    }

    function logUserActivity($userId, $action, $details = null)
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

        $stmt = $this->pdo->prepare("SELECT users.*, positions.name AS position_name FROM users JOIN positions ON users.position_id = positions.id WHERE users.id = :id");
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
}
