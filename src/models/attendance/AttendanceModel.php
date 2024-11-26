<?php

class AttendanceModel
{
    public $pdo;

    protected $attendance = "attendances";

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo; // Store the PDO instance
    }

    // Method to save the QR code to the database
    public function recordAttendance($userId, $date, $check, $isCheckIn = true)
    {
        // Check if an attendance record exists for the user on the given date
        $sql = "SELECT * FROM {$this->attendance} WHERE userId = :user_id AND date = :date";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        $existingRecord = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // Update the appropriate field based on the time of the action
            if ($isCheckIn) {
                $sql = "UPDATE {$this->attendance} SET checkIn = :checkIn WHERE userId = :user_id AND date = :date";
            } else {
                $sql = "UPDATE {$this->attendance} SET checkOut = :checkOut WHERE userId = :user_id AND date = :date";
            }
        } else {
            // Insert a new record
            $sql = "INSERT INTO {$this->attendance} (userId, date, checkIn, checkOut) 
                VALUES (:user_id, :date, :checkIn, :checkOut)";
        }

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':date', $date);

        if ($existingRecord) {
            // Update query parameters
            if ($isCheckIn) {
                $stmt->bindParam(':checkIn', $check);
            } else {
                $stmt->bindParam(':checkOut', $check);
            }
        } else {
            // Insert query parameters
            if ($isCheckIn) {
                $stmt->bindParam(':checkIn', $check);
                $stmt->bindValue(':checkOut', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':checkIn', null, PDO::PARAM_NULL);
                $stmt->bindParam(':checkOut', $check);
            }
        }

        return $stmt->execute();
    }

    public function recordAttendanceApi($userId, $date, $check, $token)
    {
        $maxRetries = 3; // Number of retries for the API call
        $retryDelay = 2; // Delay (in seconds) between retries

        try {
            $userModel = new User();
            $url = "{$userModel->api}/api/v1/attendances";

            $data = [
                'uid' => $userId,
                'date' => $date,
                'timestamp' => $check,
            ];

            $jsonData = json_encode($data);

            $attempt = 0;

            do {
                $attempt++;
                $ch = curl_init($url);

                // Set cURL options
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token,
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                // Ignore SSL certificate verification (use only for debugging)
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                // Set timeout options
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Connection timeout
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Overall request timeout

                // Execute cURL
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    $error = curl_error($ch);
                    curl_close($ch);

                    // If maximum retries reached, return failure
                    if ($attempt >= $maxRetries) {
                        return [
                            'success' => false,
                            'error' => "Request failed after $maxRetries attempts: $error",
                            'http_code' => $httpCode,
                            'response' => null,
                        ];
                    }

                    // Wait before retrying
                    sleep($retryDelay);
                    continue;
                }

                // Close cURL
                curl_close($ch);

                // Decode JSON response
                $decodedResponse = json_decode($response, true);

                // Check HTTP status code for success
                if ($httpCode >= 200 && $httpCode < 300) {
                    return [
                        'success' => true,
                        'http_code' => $httpCode,
                        'response' => $decodedResponse,
                    ];
                } else {
                    // If maximum retries reached, return failure
                    if ($attempt >= $maxRetries) {
                        return [
                            'success' => false,
                            'http_code' => $httpCode,
                            'response' => $decodedResponse,
                            'error' => $decodedResponse['message'] ?? 'An error occurred.', // Adjust based on API response format
                        ];
                    }

                    // Wait before retrying
                    sleep($retryDelay);
                }
            } while ($attempt < $maxRetries);

            // If the loop exits without a valid response, return failure
            return [
                'success' => false,
                'error' => 'Unknown error occurred after maximum retries.',
                'http_code' => null,
                'response' => null,
            ];
        } catch (\Exception $e) {
            // Catch PHP exceptions
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'exception' => $e,
                'http_code' => null,
                'response' => null,
            ];
        }
    }
}