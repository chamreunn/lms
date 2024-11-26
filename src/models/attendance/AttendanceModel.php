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

    public function recordAttendanceApi($userId, $date, $check, $token)
    {
        $maxRetries = 3; // Number of retries for the API call
        $retryDelay = 2; // Delay (in seconds) between retries

        $messages = []; // Collect messages from retries

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

                    $messages[] = "Attempt $attempt: cURL error - $error";

                    // If maximum retries reached, return failure
                    if ($attempt >= $maxRetries) {
                        return [
                            'success' => false,
                            'http_code' => $httpCode,
                            'messages' => $messages,
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
                    $messages[] = "Attempt $attempt: Success - {$decodedResponse['message']}";
                    return [
                        'success' => true,
                        'http_code' => $httpCode,
                        'messages' => $messages,
                        'response' => $decodedResponse,
                    ];
                } else {
                    $errorMessage = $decodedResponse['message'] ?? 'An error occurred.';
                    $messages[] = "Attempt $attempt: API Error - $errorMessage (HTTP Code: $httpCode)";

                    // If maximum retries reached, return failure
                    if ($attempt >= $maxRetries) {
                        return [
                            'success' => false,
                            'http_code' => $httpCode,
                            'messages' => $messages,
                            'response' => $decodedResponse,
                        ];
                    }

                    // Wait before retrying
                    sleep($retryDelay);
                }
            } while ($attempt < $maxRetries);

            // If the loop exits without a valid response, return failure
            $messages[] = "Unknown error occurred after $maxRetries attempts.";
            return [
                'success' => false,
                'http_code' => null,
                'messages' => $messages,
                'response' => null,
            ];
        } catch (\Exception $e) {
            // Catch PHP exceptions
            $messages[] = "Exception: {$e->getMessage()}";
            return [
                'success' => false,
                'http_code' => null,
                'messages' => $messages,
                'exception' => $e,
                'response' => null,
            ];
        }
    }
}