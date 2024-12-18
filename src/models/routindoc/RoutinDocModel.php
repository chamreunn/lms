<?php
class RoutinDocModel
{
    private $pdo;

    public $api;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;

        if (isset($_SERVER['SERVER_NAME']) && ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1')) {
            // Local development environment
            $this->api = "http://127.0.0.1:8000";
        } else {
            // Production environment
            $this->api = "http://172.25.26.6:8000";
        }
    }

    public function getUserReportById($id, $token, $startDate = null, $endDate = null, $maxRetries = 3, $cacheEnabled = false)
    {
        $query = http_build_query(array_filter([
            'userId' => $id,
            'start_date' => $startDate,
            'end_date' => $endDate
        ]));

        return $this->fetchApiData(
            "{$this->api}/api/v1/reports?$query",
            $token,
            $maxRetries,
            $cacheEnabled,
            "user_{$id}_reports.json"
        );
    }

    public function addUserReportById($userId, $startDate, $description, $note, $token)
    {
        $url = "{$this->api}/api/v1/reports";

        // Prepare the data for the POST request
        $data = [
            'userId' => $userId,
            'note' => $note,
            'description' => $description,
            'date' => $startDate,
        ];

        // Encode the data to JSON format
        $jsonData = json_encode($data);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Specify the HTTP request method as POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Attach the token for authorization
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL certificate verification (use only for development, not production)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode,
                'response' => $response
            ];
        }

        // Close the cURL session
        curl_close($ch);

        // Check for success based on HTTP code (2xx series for success)
        $isSuccess = $httpCode >= 200 && $httpCode < 300;

        return [
            'success' => $isSuccess,
            'http_code' => $httpCode,
            'response' => $response
        ];
    }

    public function fetchApiData($url, $token, $maxRetries = 3, $cacheEnabled = true, $cacheKey = '', $cacheTTL = 3600)
    {
        $retryCount = 0;
        $connectTimeout = 3; // Reduced connection timeout
        $requestTimeout = 8; // Reduced request timeout
        $retryDelay = 1; // Fixed retry delay in seconds

        // Optional caching mechanism
        if ($cacheEnabled && file_exists($cacheKey)) {
            $fileModifiedTime = filemtime($cacheKey);
            if (time() - $fileModifiedTime < $cacheTTL) {
                $cachedResponse = file_get_contents($cacheKey);
                $responseData = json_decode($cachedResponse, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return [
                        'http_code' => 200,
                        'userReports' => $responseData['userReports'] ?? [] // Adjust for correct key
                    ];
                }
            }
        }

        do {
            // Initialize cURL session
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["Authorization: Bearer $token"],
                CURLOPT_SSL_VERIFYPEER => true, // Enforce SSL verification for security
                CURLOPT_CONNECTTIMEOUT => $connectTimeout,
                CURLOPT_TIMEOUT => $requestTimeout
            ]);

            // Execute cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            // Close the cURL session
            curl_close($ch);

            if ($response !== false && $httpCode === 200) {
                $responseData = json_decode($response, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    // Extract `userReports` from the response
                    $data = $responseData['userReports'] ?? [];

                    // Store in cache if enabled
                    if ($cacheEnabled) {
                        file_put_contents($cacheKey, json_encode($responseData));
                    }

                    return [
                        'http_code' => $httpCode,
                        'userReports' => $data,
                    ];
                }
            }

            $retryCount++;
            sleep($retryDelay);
        } while ($retryCount < $maxRetries);

        return [
            'http_code' => $httpCode ?? 500,
            'error' => $error ?? "Request failed after $maxRetries retries",
            'response' => $response ?? null
        ];
    }

}