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
        $maxRetries = 3; // Maximum number of retry attempts
        $retryDelay = 2; // Delay (in seconds) between retries

        $messages = []; // Log messages for tracking retries and errors
        $userModel = new User();
        $url = "{$userModel->api}/api/v1/attendances";

        $data = [
            'uid' => $userId,
            'date' => $date,
            'timestamp' => $check,
        ];

        $jsonData = json_encode($data);

        $attempt = 0;

        while ($attempt < $maxRetries) {
            $attempt++;

            try {
                $ch = curl_init($url);

                // Set cURL options
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $token,
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                // Ignore SSL verification (for debugging purposes)
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

                // Set timeout options
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Connection timeout
                curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Request timeout

                // Execute cURL
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                // Check for cURL errors
                if (curl_errno($ch)) {
                    $error = curl_error($ch);
                    curl_close($ch);

                    $messages[] = "Attempt $attempt: cURL error - $error";

                    if ($attempt >= $maxRetries) {
                        // Return failure after reaching max retries
                        return [
                            'success' => false,
                            'http_code' => null,
                            'messages' => $messages,
                            'response' => null,
                        ];
                    }

                    // Retry after delay
                    sleep($retryDelay);
                    continue;
                }

                // Close cURL after execution
                curl_close($ch);

                // Decode JSON response
                $decodedResponse = json_decode($response, true);

                // Handle HTTP success codes (200-299)
                if ($httpCode >= 200 && $httpCode < 300) {
                    $messages[] = "Attempt $attempt: Success - " . ($decodedResponse['message'] ?? 'Request successful.');
                    return [
                        'success' => true,
                        'http_code' => $httpCode,
                        'messages' => $messages,
                        'response' => $decodedResponse,
                    ];
                }

                // Handle HTTP errors
                $errorMessage = $decodedResponse['message'] ?? 'API Error occurred.';
                $messages[] = "Attempt $attempt: API Error - $errorMessage (HTTP Code: $httpCode)";

                if ($attempt >= $maxRetries) {
                    // Return failure after reaching max retries
                    return [
                        'success' => false,
                        'http_code' => $httpCode,
                        'messages' => $messages,
                        'response' => $decodedResponse,
                    ];
                }

                // Retry after delay
                sleep($retryDelay);
            } catch (\Exception $e) {
                // Catch and log exceptions
                $messages[] = "Attempt $attempt: Exception - {$e->getMessage()}";

                if ($attempt >= $maxRetries) {
                    return [
                        'success' => false,
                        'http_code' => null,
                        'messages' => $messages,
                        'exception' => $e,
                        'response' => null,
                    ];
                }

                // Retry after delay
                sleep($retryDelay);
            }
        }

        // If no successful response after retries
        $messages[] = "Failed after $maxRetries attempts.";
        return [
            'success' => false,
            'http_code' => null,
            'messages' => $messages,
            'response' => null,
        ];
    }

    /**
     * Determine attendance status based on the check type and time.
     * 
     * @param string $check The type of attendance check (e.g., 'in', 'out').
     * @return string Status message including lateness or early leave details.
     */
    public function determineAttendanceStatus($check)
    {
        $currentHour = (int) date('H');
        $currentMinute = (int) date('i');
        $workStartHour = 9;  // Start of the workday (e.g., 9 AM)
        $workEndHour = 17;  // End of the workday (e.g., 5 PM)

        switch ($check) {
            case 'in':
                if ($currentHour > $workStartHour || ($currentHour == $workStartHour && $currentMinute > 0)) {
                    return "ចូលយឺត"; // Late Check-In
                }
                return "មកទាន់ម៉ោង"; // On Time

            case 'out':
                if ($currentHour > $workEndHour || ($currentHour == $workEndHour && $currentMinute > 0)) {
                    return "ចេញយឺត"; // Late Check-Out
                } elseif ($currentHour < $workEndHour || ($currentHour == $workEndHour && $currentMinute < 0)) {
                    return "ចេញមុន"; // Leaving Early
                }
                return "ចេញត្រឹមម៉ោង"; // On Time Check-Out

            default:
                return "ស្ថានភាពវត្តមានមិនត្រូវបានកំណត់"; // Unknown Attendance Status
        }
    }
}