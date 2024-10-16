<?php
require_once 'src/models/User.php';
require_once 'src/vendor/autoload.php'; // Ensure PHPMailer is autoloaded
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class HeadDepartmentModel
{
    private $pdo;
    private $table_name = "leave_requests";

    private $approval = "leave_approvals";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function create($user_id, $user_email, $leave_type_id, $position, $office, $department, $leave_type_name, $start_date, $end_date, $remarks, $duration_days, $attachment, $transfer)
    {
        // Prepare and execute the SQL statement
        $stmt = $this->pdo->prepare("
            INSERT INTO $this->table_name (user_id, uemails, leave_type_id, position, office, department, leave_type, start_date, end_date, remarks, num_date, attachment, transfer, status, head_department, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
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
            $transfer,
            'Pending',
            'Approved'
        ]);

        // Return the ID of the newly created leave request
        return $this->pdo->lastInsertId();
    }

    public function getRequestsByUserId($user_id)
    {
        // Prepare and execute the SQL query (remove JOINs with users, departments, and positions)
        $stmt = $this->pdo->prepare('SELECT lr.*, lt.name as leave_type_name, lt.duration, lt.color FROM ' . $this->table_name . ' lr JOIN leave_types lt ON lr.leave_type_id = lt.id WHERE lr.user_id = ? ORDER BY id DESC');
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

            // Fetch additional data using existing methods
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id'], $_SESSION['token']);
            $result['doffice'] = $this->getDOfficePositions($result['id'], $_SESSION['token']);
            $result['hoffice'] = $this->getHOfficePositions($result['id'], $_SESSION['token']);
            $result['ddepartment'] = $this->getDDepartmentPositions($result['id'], $_SESSION['token']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id'], $_SESSION['token']);
            $result['dunit'] = $this->getDUnitPositions($result['id'], $_SESSION['token']);
            $result['unit'] = $this->getUnitPositions($result['id'], $_SESSION['token']);
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
            FROM ' . $this->table_name . ' lr
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

            // Fetch additional data using existing methods
            $result['approvals'] = $this->getApprovalsByLeaveRequestId($result['id'], $_SESSION['token']);
            $result['doffice'] = $this->getDOfficePositions($result['id'], $_SESSION['token']);
            $result['hoffice'] = $this->getHOfficePositions($result['id'], $_SESSION['token']);
            $result['ddepartment'] = $this->getDDepartmentPositions($result['id'], $_SESSION['token']);
            $result['hdepartment'] = $this->getHDepartmentPositions($result['id'], $_SESSION['token']);
            $result['dunit'] = $this->getDUnitPositions($result['id'], $_SESSION['token']);
            $result['unit'] = $this->getUnitPositions($result['id'], $_SESSION['token']);
        }

        return $results;
    }

    private function fetchAttachmentsByLeaveRequestId($leave_request_id)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM leave_attachments WHERE leave_request_id = ?');
        $stmt->execute([$leave_request_id]);
        return $stmt->fetchAll();
    }

    public function getApprovalsByLeaveRequestId($leave_request_id, $token)
    {
        // Query to get approval details without fetching user and position data directly
        $stmt = $this->pdo->prepare(
            'SELECT a.*,   -- Include the signature column
                (SELECT COUNT(*) FROM leave_approvals WHERE leave_request_id = ?) AS approval_count
         FROM leave_approvals a
         WHERE a.leave_request_id = ?'
        );

        // Execute the query with the leave request ID parameter
        $stmt->execute([$leave_request_id, $leave_request_id]);
        $approvals = $stmt->fetchAll();

        // Check if an attachment is required for the leave type
        $attachmentStmt = $this->pdo->prepare(
            'SELECT lt.attachment_required 
         FROM leave_requests lr
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

    public function sendEmailBackToUser($uEmail, $adminApproved, $leaveRequestId, $status, $updatedAt, $remarks)
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
            $updated_at_formatted = (new DateTime($updatedAt))->format('j F, Y H:i:s');

            // Recipients
            $mail->setFrom('no-reply@example.com', 'ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់');
            $mail->addAddress($uEmail);

            // Email Content
            $mail->isHTML(true);
            $mail->Subject = "ការស្នើសុំច្បាប់ត្រូវបាន $status";

            // Updated body with "khmer MEF1" font
            $body = "
        <html>
        <head>
            <style>
                @font-face {
                    font-family: 'khmer MEF1';
                    src: url('../../public/dist/fonts/Khmer-MEF1.ttf') format('truetype');
                }
                body {
                    font-family: 'khmer MEF1', Arial, sans-serif;
                    background-color: #f7f7f7;
                    margin: 0;
                    padding: 0;
                }
                .container {
                    max-width: 600px;
                    margin: 40px auto;
                    background-color: #ffffff;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background-color: #4CAF50;
                    color: white;
                    padding: 20px;
                    text-align: center;
                    font-size: 24px;
                    font-weight: bold;
                }
                .content {
                    padding: 20px;
                    font-size: 16px;
                    color: #333333;
                }
                .content p {
                    margin: 0 0 15px;
                }
                .status-badge {
                    display: inline-block;
                    background-color: " . ($status === 'Approved' ? '#28a745' : '#dc3545') . ";
                    color: white;
                    padding: 5px 10px;
                    border-radius: 4px;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                .footer {
                    background-color: #f1f1f1;
                    text-align: center;
                    padding: 10px;
                    font-size: 12px;
                    color: #666666;
                    border-top: 1px solid #e2e2e2;
                }
            </style>
        </head>
        <body>
            <div class='container-fluid'>
                <p><strong>Status:</strong> $status</p>
                <p><strong>Approved by:</strong> $adminApproved</p>
                <p><strong>Date:</strong> $updated_at_formatted</p>"
                . (!empty($remarks) ? "<p><strong>Remarks:</strong> $remarks</p>" : "") . "
            </div>
            <div class='footer'>
                &copy; " . date("Y") . " ប្រព័ន្ធគ្រប់គ្រងការសុំច្បាប់។ រក្សាសិទ្ធិគ្រប់យ៉ាង។
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

    public function getAllLeaveRequests()
    {
        // Fetch all leave requests from the database
        $stmt = $this->pdo->prepare('SELECT * FROM leave_requests 
        WHERE dhead_department IN (?, ?)
        AND head_department = ?
        AND position IN (?, ?, ?, ?, ?)
        AND department = ?
        AND user_id != ?
        ');
        $stmt->execute(['Approved', 'Rejected', 'Pending', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $_SESSION['user_id']]);
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data for each leave request using the API
        foreach ($leaveRequests as &$request) {
            // Get user data from API
            $userApiResponse = $userModel->getUserByIdApi($request['user_id'], $_SESSION['token']);

            // Debug: Log the API response for each user
            error_log("API Response for User ID " . $request['user_id'] . ": " . print_r($userApiResponse, true));

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $request['user_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png'; // Use a default profile image if API fails

                // Debug: Log the API failure case
                error_log("API call failed for User ID " . $request['user_id'] . ". Setting default values.");
            }
        }

        // Debug: Log the final leave requests array
        error_log("Final leave requests data: " . print_r($leaveRequests, true));

        return $leaveRequests; // Return the modified leave requests
    }

    public function pendingCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
        WHERE dhead_department IN (?, ?)
        AND head_department = ?
        AND position IN (?, ?, ?, ?, ?)
        AND department = ?
        AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Pending', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
    }

    public function approvedCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
        WHERE dhead_department IN (?, ?)
        AND head_department = ?
        AND position IN (?, ?, ?, ?, ?)
        AND department = ?
        AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Approved', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
    }

    public function rejectedCount()
    {
        // Prepare the SQL statement to count leave requests with the given criteria
        $stmt = $this->pdo->prepare('SELECT COUNT(*) as leave_count FROM leave_requests 
        WHERE dhead_department IN (?, ?)
        AND head_department = ?
        AND position IN (?, ?, ?, ?, ?)
        AND department = ?
        AND user_id != ?');

        // Execute the query with the session values
        $stmt->execute(['Approved', 'Rejected', 'Rejected', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $_SESSION['user_id']]);

        // Fetch the result as an associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the count of leave requests
        return $result['leave_count'] ?? 0; // Return 0 if the count is not found
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

    public function submitApproval($leave_request_id, $approver_id, $status, $remarks)
    {
        // Insert the approval record with the signature
        $stmt = $this->pdo->prepare(
            'INSERT INTO leave_approvals (leave_request_id, approver_id, status, remarks, updated_at)
        VALUES (?, ?, ?, ?, NOW())'
        );
        $stmt->execute([$leave_request_id, $approver_id, $status, $remarks]);

        // Get the updated_at timestamp
        $stmt = $this->pdo->prepare(
            'SELECT updated_at FROM leave_approvals WHERE leave_request_id = ? AND approver_id = ? ORDER BY updated_at DESC LIMIT 1'
        );
        $stmt->execute([$leave_request_id, $approver_id]);
        $updatedAt = $stmt->fetchColumn();

        if ($updatedAt === false) {
            throw new Exception("Unable to fetch updated_at timestamp for approval.");
        }

        // Update leave request status based on the approval chain
        $this->updateLeaveRequestStatus($leave_request_id, $status);

        return $updatedAt; // Return the updated_at timestamp
    }

    public function getUserApi($id, $token)
    {
        $url = 'https://hrms.iauoffsa.us/api/v1/users/' . $id;

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
                if (isset($user['roleLeave']) && $user['roleLeave'] === 'User') {
                    $ids[] = $user['id'];
                }
            }

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function updateToApi($user_id, $start_date, $end_date, $leave, $token)
    {
        $apis = new User();
        $api = $apis->getApi();

        $url = "{$api}/api/v1/leaves";

        $data = [
            'uid' => $user_id,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'leave' => $leave
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

    private function updateLeaveRequestStatus($leave_request_id, $latestStatus)
    {
        // Fetch the current status and other relevant details of the leave request
        $stmt = $this->pdo->prepare(
            'SELECT head_department, num_date, status, position
             FROM leave_requests
             WHERE id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $leaveRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$leaveRequest) {
            throw new Exception("Invalid leave request ID: $leave_request_id");
        }

        $currentHeadDepartmentStatus = $leaveRequest['head_department'];
        $currentStatus = $leaveRequest['status'];
        $duration = $leaveRequest['num_date'];
        $positionName = $leaveRequest['position'];

        // If the current status is already 'Rejected', no further updates are needed
        if ($currentHeadDepartmentStatus == 'Rejected') {
            return;
        }

        // Determine the new status based on the latest approval status
        $newStatus = ($latestStatus == 'Rejected') ? 'Rejected' : 'Approved';

        // Update both status and head_department if the leave duration is <= 3 days
        // and the position name is one of the specified values
        if ($duration <= 3 && in_array($positionName, ['មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល'])) {
            $stmt = $this->pdo->prepare(
                'UPDATE leave_requests SET head_department = ?, status = ? WHERE id = ?'
            );
            $stmt->execute([$newStatus, $newStatus, $leave_request_id]);
        } else {
            // Otherwise, update only the head_department status
            $stmt = $this->pdo->prepare(
                'UPDATE leave_requests SET head_department = ? WHERE id = ?'
            );
            $stmt->execute([$newStatus, $leave_request_id]);
        }
    }

    public function gethapproved($approver_id)
    {
        // Fetch all leave requests from the database
        $stmt = $this->pdo->prepare('SELECT * FROM leave_requests 
         WHERE dhead_department IN (?, ?)
         AND head_department = ?
         AND position IN (?, ?, ?, ?, ?)
         AND department = ?
         AND user_id != ? ORDER BY id DESC
         ');
        $stmt->execute(['Approved', 'Rejected', 'Approved', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $approver_id]);
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data for each leave request using the API
        foreach ($leaveRequests as &$request) {
            // Get user data from API
            $userApiResponse = $userModel->getUserByIdApi($request['user_id'], $_SESSION['token']);

            // Debug: Log the API response for each user
            error_log("API Response for User ID " . $request['user_id'] . ": " . print_r($userApiResponse, true));

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $request['user_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png'; // Use a default profile image if API fails

                // Debug: Log the API failure case
                error_log("API call failed for User ID " . $request['user_id'] . ". Setting default values.");
            }
        }

        // Debug: Log the final leave requests array
        error_log("Final leave requests data: " . print_r($leaveRequests, true));

        return $leaveRequests; // Return the modified leave requests
    }

    public function gethrejected($approver_id)
    {
        // Fetch all leave requests from the database
        $stmt = $this->pdo->prepare('SELECT * FROM leave_requests 
         WHERE dhead_department IN (?, ?)
         AND head_department = ?
         AND position IN (?, ?, ?, ?, ?)
         AND department = ?
         AND user_id != ?
         ');
        $stmt->execute(['Approved', 'Rejected', 'Rejected', 'មន្រ្តីលក្ខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', 'ប្រធានការិយាល័យ', 'អនុប្រធាននាយកដ្ឋាន', $_SESSION['departmentName'], $approver_id]);
        $leaveRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize UserModel
        $userModel = new User();

        // Fetch user data for each leave request using the API
        foreach ($leaveRequests as &$request) {
            // Get user data from API
            $userApiResponse = $userModel->getUserByIdApi($request['user_id'], $_SESSION['token']);

            // Debug: Log the API response for each user
            error_log("API Response for User ID " . $request['user_id'] . ": " . print_r($userApiResponse, true));

            // Check if the API response is successful
            if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data']) && is_array($userApiResponse['data']) && !empty($userApiResponse['data'])) {
                $user = $userApiResponse['data']; // Assuming the API returns a single user object

                // Add user information to the leave request
                $request['user_name'] = $user['lastNameKh'] . " " . $user['firstNameKh'] ?? 'Unknown';
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = $user['image'] ?? 'default-profile.png'; // Use a default profile image if none exists
            } else {
                // Handle cases where the API call fails or returns no data
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png'; // Use a default profile image if API fails

                // Debug: Log the API failure case
                error_log("API call failed for User ID " . $request['user_id'] . ". Setting default values.");
            }
        }

        // Debug: Log the final leave requests array
        error_log("Final leave requests data: " . print_r($leaveRequests, true));

        return $leaveRequests; // Return the modified leave requests
    }

    public function getUserApproveByTeam($approver_id)
    {
        // Get the approver's office and department
        $stmt = $this->pdo->prepare('SELECT office_id, department_id FROM users WHERE id = ?');
        $stmt->execute([$approver_id]);
        $approver = $stmt->fetch();

        if ($approver) {
            $office_id = $approver['office_id'];
            $department_id = $approver['department_id'];
            // Query to get pending requests for users in the same office or department as the approver
            // and who have the specified positions, including additional user details
            $stmt = $this->pdo->prepare('
            SELECT lr.*, u.email, u.profile_picture AS profile, u.khmer_name, lt.color
            FROM leave_requests lr 
            JOIN users u ON lr.user_id = u.id 
            JOIN positions p ON u.position_id = p.id 
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = ? 
            AND u.office_id = ? 
            AND u.department_id = ?
            AND u.role = ? 
            AND p.name IN (?, ?, ?) 
            AND lr.user_id != ?
        ');
            $stmt->execute(['Approved', $office_id, $department_id, 'User', 'មន្ត្រីលក្ខខន្តិកៈ', 'ភ្នាក់ងាររដ្ឋបាល', 'អនុប្រធានការិយាល័យ', $approver_id]);
            return $stmt->fetchAll();
        } else {
            return [];
        }
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
            'SELECT head_department, num_date FROM ' . $this->table_name . ' WHERE id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $leaveRequest = $stmt->fetch();

        if (!$leaveRequest) {
            throw new Exception("Invalid leave request ID: $leave_request_id");
        }

        $currentStatus = $leaveRequest['head_department'];
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
            'UPDATE ' . $this->table_name . ' SET dhead_unit = ? WHERE id = ?'
        );
        $stmt->execute([$newStatus, $leave_request_id]);
    }

    // if Manager on leave 
    public function updatePendingApproval($leave_request_id, $approver_id, $status, $remarks)
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
        $this->updateRequestPendingApproval($leave_request_id, $status);

        return $updatedAt; // Return the updated_at timestamp
    }

    private function updateRequestPendingApproval($leave_request_id, $latestStatus)
    {
        // Fetch the current status of the leave request
        $stmt = $this->pdo->prepare(
            'SELECT head_department, num_date FROM ' . $this->table_name . ' WHERE id = ?'
        );
        $stmt->execute([$leave_request_id]);
        $leaveRequest = $stmt->fetch();

        if (!$leaveRequest) {
            throw new Exception("Invalid leave request ID: $leave_request_id");
        }

        $currentStatus = $leaveRequest['head_department'];
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
            'UPDATE ' . $this->table_name . ' SET head_department = ?, dhead_unit = ? WHERE id = ?'
        );
        $stmt->execute([$newStatus, $newStatus, $leave_request_id]);
    }
    //  end if manager on leave 

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
         FROM ' . $this->table_name . ' lr
         JOIN leave_types lt ON lr.leave_type_id = lt.id
         WHERE lr.id = ?'
        );
        $stmt->execute([$leave_id]);
        $leaveRequest = $stmt->fetch();

        if ($leaveRequest) {
            // ពិនិត្យមើលថាតើការភ្ជាប់ឯកសារត្រូវការឬអត់ និងវាមាននៅក្នុងសំណើចាកចេញឬអត់
            if ($leaveRequest['attRequired'] === 'Yes') {
                if (empty($leaveRequest['attachment'])) {
                    // ដោះស្រាយករណីដែលត្រូវការភ្ជាប់ឯកសារ ប៉ុន្តែបាត់បង់
                    error_log("ការភ្ជាប់ឯកសារត្រូវការសម្រាប់សំណើចាកចេញ ID: $leave_id ប៉ុន្តែមិនមាន។");
                    $leaveRequest['attachment_error'] = "ត្រូវការភ្ជាប់ឯកសារសម្រាប់ប្រភេទច្បាប់នេះ។";
                } else {
                    $leaveRequest['attachment_error'] = null; // គ្មានបញ្ហាថាត្រូវការភ្ជាប់ឯកសារនេះ
                }
            } else {
                $leaveRequest['attachment_error'] = null; // មិនត្រូវការភ្ជាប់ឯកសារនេះទេ
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
                $leaveRequest['profile'] = 'https://hrms.iauoffsa.us/images/' . $userData['image'] ?? null;
            } else {
                // Handle API error or missing data
                error_log("Failed to fetch user data for leave request ID: $leave_id");
                $leaveRequest['khmer_name'] = null;
                $leaveRequest['phone_number'] = null;
                $leaveRequest['dob'] = null;
                $leaveRequest['deputy_head_name'] = null;
            }

            // Fetch other details such as approvals, office positions, department, and unit
            $leaveRequest['approvals'] = $this->getApprovalsByLeaveRequestId($leaveRequest['id'], $token);
            $leaveRequest['doffice'] = $this->getDOfficePositions($leaveRequest['id'], $token);
            $leaveRequest['hoffice'] = $this->getHOfficePositions($leaveRequest['id'], $token);
            $leaveRequest['ddepartment'] = $this->getDDepartmentPositions($leaveRequest['id'], $token);
            $leaveRequest['hdepartment'] = $this->getHDepartmentPositions($leaveRequest['id'], $token);
            $leaveRequest['dunit'] = $this->getDUnitPositions($leaveRequest['id'], $token);
            $leaveRequest['unit'] = $this->getUnitPositions($leaveRequest['id'], $token);
        }

        return $leaveRequest;
    }
}
