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

}

