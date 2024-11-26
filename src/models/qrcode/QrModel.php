<?php
class QrModel
{
    public $pdo;

    protected $qrcode = "qr_codes";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo; // Store the PDO instance
    }

    // Method to save the QR code to the database
    public function createQR($url, $userId, $name, $qrCodeBase64, $latitude, $longitude, $ipAddress, $userAgent, $deviceId, $qrToken)
    {
        $sql = "INSERT INTO {$this->qrcode} 
            (url, user_id, name, image, latitude, longitude, ip_address, user_agent, device_id, qr_token) 
            VALUES (:url, :user_id, :name, :qrCodeBase64, :latitude, :longitude, :ip_address, :user_agent, :device_id, :qr_token)";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':url' => $url,
            ':user_id' => $userId,
            ':name' => $name,
            ':qrCodeBase64' => $qrCodeBase64,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':ip_address' => $ipAddress,
            ':user_agent' => $userAgent,
            ':device_id' => $deviceId,
            ':qr_token' => $qrToken
        ]);
    }

    public function validateQR($token, $userId)
    {
        $sql = "SELECT * FROM {$this->qrcode} WHERE qr_token = :qr_token AND user_id = :user_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':qr_token' => $token,
            ':user_id' => $userId
        ]);

        $qrData = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($qrData) {
            // Token and user ID match
            return [
                'status' => 'valid',
                'message' => 'QR Code is valid.',
                'data' => $qrData
            ];
        } else {
            // No match found
            return [
                'status' => 'invalid',
                'message' => 'QR Code is invalid or expired.'
            ];
        }
    }

    public function logScan($logData)
    {
        $sql = "INSERT INTO qr_scan_logs (user_id, qr_token, scan_time, ip_address) 
            VALUES (:user_id, :qr_token, :scan_time, :ip_address)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $logData['user_id'],
            ':qr_token' => $logData['qr_token'],
            ':scan_time' => $logData['scan_time'],
            ':ip_address' => $logData['ip_address']
        ]);
    }

    // Method to get QR code by name (or any identifier)
    public function getQRCodeByName($user_id)
    {
        $sql = "SELECT * FROM {$this->qrcode} WHERE user_id =:user_id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        // Bind the QR code id to the SQL query
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC); // Returns associative array with QR code data
    }

    public function deleteQRCode($qrCodeId)
    {
        // SQL query to delete a record based on the QR code id
        $sql = "DELETE FROM {$this->qrcode} WHERE id = :qrCodeId";

        // Prepare the SQL statement
        $stmt = $this->pdo->prepare($sql);

        // Bind the QR code id to the SQL query
        $stmt->bindParam(':qrCodeId', $qrCodeId, PDO::PARAM_INT);

        // Execute the query and return the result (true/false)
        return $stmt->execute();
    }

    public function getAllUserQRcode()
    {
        $sql = "SELECT * FROM {$this->qrcode}"; // Fetch all QR codes
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (empty($results)) {
            return []; // Return an empty array if no QR codes are found
        }
    
        // Instantiate the user model to interact with the API
        $userModel = new User();
        $userCache = []; // Cache for user data to avoid redundant API calls
    
        foreach ($results as &$request) {
            $requestUserId = $request['user_id'];
    
            // Check cache for user details
            if (!isset($userCache[$requestUserId])) {
                $retryCount = 3; // Retry up to 3 times for API call
    
                while ($retryCount > 0) {
                    $userApiResponse = $userModel->getUserByIdApi($requestUserId, $_SESSION['token']);
    
                    // Check if the API response is valid and contains data
                    if ($userApiResponse && $userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                        $userCache[$requestUserId] = $userApiResponse['data'];
                        break;
                    }
    
                    $retryCount--;
                    usleep(200000); // Wait 200ms before retrying
                }
            }
    
            // Retrieve user details from cache or fallback to default values
            $user = $userCache[$requestUserId] ?? null;
    
            if ($user) {
                // Map API data to the request details
                $request['user_name'] = trim(($user['lastNameKh'] ?? '') . " " . ($user['firstNameKh'] ?? 'Unknown'));
                $request['dob'] = $user['dateOfBirth'] ?? 'Unknown';
                $request['user_email'] = $user['email'] ?? 'Unknown';
                $request['department_name'] = $user['department']['name'] ?? 'Unknown';
                $request['position_name'] = $user['position']['name'] ?? 'Unknown';
                $request['profile'] = 'https://hrms.iauoffsa.us/images/' . ($user['image'] ?? 'default-profile.png');
            } else {
                // Fallback for missing user data
                $request['user_name'] = 'Unknown';
                $request['dob'] = 'Unknown';
                $request['user_email'] = 'Unknown';
                $request['department_name'] = 'Unknown';
                $request['position_name'] = 'Unknown';
                $request['profile'] = 'default-profile.png';
                error_log("Failed to fetch user data for User ID $requestUserId after retries.");
            }
    
            // Ensure 'attachments' is always set for consistency
            $request['attachments'] = $request['attachments'] ?? '';
        }
    
        return $results;
    }
    
}

