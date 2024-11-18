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
    public function createQR($url, $userId, $name, $qrCodeBase64, $latitude, $longitude)
    {
        $sql = "INSERT INTO {$this->qrcode} (url, user_id, name, image, latitude, longitude) VALUES (:url, :user_id, :name, :qrCodeBase64, :latitude, :longitude)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':url', $url);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':qrCodeBase64', $qrCodeBase64);
        $stmt->bindParam(':latitude', $latitude);
        $stmt->bindParam(':longitude', $longitude);
        return $stmt->execute();
    }

    // Method to get QR code by name (or any identifier)
    public function getQRCodeByName()
    {
        $sql = "SELECT * FROM {$this->qrcode} LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
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

