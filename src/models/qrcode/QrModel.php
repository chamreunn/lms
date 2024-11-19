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
    public function createQR($url, $userId, $name, $qrCodeBase64, $latitude, $longitude, $ipAddress, $userAgent)
    {
        $sql = "INSERT INTO {$this->qrcode} (url, user_id, name, image, latitude, longitude, ip_address, user_agent) 
        VALUES (:url, :user_id, :name, :qrCodeBase64, :latitude, :longitude, :ip_address, :user_agent)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':url' => $url,
            ':user_id' => $userId,
            ':name' => $name,
            ':qrCodeBase64' => $qrCodeBase64,
            ':latitude' => $latitude,
            ':longitude' => $longitude,
            ':ip_address' => $ipAddress,
            ':user_agent' => $userAgent
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

