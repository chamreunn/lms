<?php
require_once 'config/database.php';

class User
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

    private $telegramUser = "telegram_users";

    protected $attendance = "attendances";

    public function getApi()
    {
        return $this->api;
    }

    public function authenticateUser($email, $password)
    {
        $url = "{$this->api}/api/login";
        $data = json_encode(['email' => $email, 'password' => $password]);

        // Initialize retry mechanism
        $maxRetries = 3; // Maximum number of retries
        $retryDelay = 2; // Seconds to wait before retrying
        $attempt = 0;

        do {
            $attempt++;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Set a timeout to prevent long hangs
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

            // Handle SSL based on environment
            if (strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            } else {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            }

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // Handle cURL errors
            if ($response === false) {
                error_log("Attempt $attempt: CURL Error - $error");
                sleep($retryDelay); // Wait before retrying
                continue;
            }

            // Decode and validate JSON response
            $responseData = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Attempt $attempt: JSON Decode Error - " . json_last_error_msg());
                sleep($retryDelay); // Wait before retrying
                continue;
            }

            // Check if the response contains the expected data
            if ($httpCode === 200 && isset($responseData['user'], $responseData['token'])) {
                return [
                    'success' => true,
                    'message' => 'Authentication successful',
                    'data' => [
                        'user' => $responseData['user'],
                        'token' => $responseData['token']
                    ],
                    'http_code' => $httpCode
                ];
            } else {
                // Log unexpected response details for debugging
                error_log("Attempt $attempt: Unexpected API Response - HTTP Code $httpCode");
                error_log("Response Data: " . print_r($responseData, true));
            }

            // Wait before retrying if needed
            sleep($retryDelay);
        } while ($attempt < $maxRetries);

        // If all attempts fail
        return [
            'success' => false,
            'message' => 'Failed to authenticate after multiple attempts.',
            'data' => null,
            'http_code' => $httpCode ?? null
        ];
    }

    // Call API to log out
    public function logoutFromApi($token)
    {
        $url = "{$this->api}/api/v1/logout";

        // Initialize cURL session
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $token",
            "Content-Type: application/json"
        ]);

        $response = curl_exec($ch);

        // Check for cURL errors
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);

            return [
                'success' => false,
                'message' => "cURL error: $error",
            ];
        }

        // Get HTTP status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Decode API response
        $responseDecoded = json_decode($response, true);

        // Check for success based on HTTP code
        if ($httpCode === 200) {
            return [
                'success' => true,
                'message' => $responseDecoded['message'] ?? 'Logged out successfully',
            ];
        }

        return [
            'success' => false,
            'message' => $responseDecoded['message'] ?? 'Logout failed',
        ];
    }

    public function logLoginTrace($userId, $ipAddress)
    {
        $stmt = $this->pdo->prepare('INSERT INTO login_traces (user_id, login_time, ip_address) VALUES (?, NOW(), ?)');
        $stmt->execute([$userId, $ipAddress]);
    }

    public function logUserActivity($userId, $action, $details = null)
    {
        // Assuming you have a PDO connection $pdo
        $stmt = $this->pdo->prepare('INSERT INTO user_activity_log (user_id, action, timestamp, details, ip_address) VALUES (?, ?, NOW(), ?, ?)');
        $stmt->execute([$userId, $action, $details, $_SERVER['REMOTE_ADDR']]);
        return true;
    }

    public function getAllUserApi($token, $retries = 3)
    {
        $url = "{$this->api}/api/v1/users/";

        // Determine if we're on localhost
        $isLocalhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

        for ($attempt = 1; $attempt <= $retries; $attempt++) {
            // Initialize cURL session
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: Bearer ' . $token
            ));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$isLocalhost); // Ignore SSL verification on localhost

            // Execute cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            // Close the cURL session
            curl_close($ch);

            // Check for cURL errors
            if ($response === false) {
                error_log("Attempt $attempt - CURL Error: $error");
                usleep(200000); // Wait 200 milliseconds before retrying
                continue;
            }

            // Decode the JSON response
            $responseData = json_decode($response, true);

            // Check for JSON decoding errors
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("Attempt $attempt - JSON Decode Error: " . json_last_error_msg());
                usleep(200000); // Wait 200 milliseconds before retrying
                continue;
            }

            // Check if the response is successful and contains the expected data
            if ($httpCode === 200 && isset($responseData['data'])) {
                $users = $responseData['data'];
                $usersWithRoles = [];

                // Fetch role information for each user
                foreach ($users as $user) {
                    $roleResponse = $this->getRoleApi($user['roleId'], $token);
                    $user['role'] = ($roleResponse && $roleResponse['http_code'] === 200) ? $roleResponse['data']['roleNameKh'] : 'Unknown';
                    $usersWithRoles[] = $user;
                }

                return [
                    'http_code' => $httpCode,
                    'data' => $usersWithRoles,
                ];
            } else {
                error_log("Attempt $attempt - Unexpected API Response: " . print_r($responseData, true));
                usleep(200000); // Wait 200 milliseconds before retrying
            }
        }

        // Return error message if all retries fail
        echo "Failed to retrieve users after $retries attempts.";
        return [
            'http_code' => $httpCode ?? 500,
            'error' => 'Failed to retrieve users after multiple attempts.',
        ];
    }

    public function getDepOfficAndDepartment($token, $officeName, $roleName)
    {
        $url = "{$this->api}/api/v1/fetch-deputy-office-department";

        // Prepare query parameters
        $queryParams = [
            'officeName' => $officeName,
            'roleName' => $roleName,
        ];

        // Build the URL with encoded query parameters
        $url .= '?' . http_build_query($queryParams);

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json' // Added in case the API expects this header
        ]);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Handle cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            curl_close($ch);
            return [
                'success' => false,
                'error' => $error,
                'http_code' => $httpCode
            ];
        }

        curl_close($ch);

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Log the raw response for debugging
        error_log("API Raw Response: " . print_r($responseData, true));

        // Handle JSON decode errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return [
                'success' => false,
                'error' => json_last_error_msg(),
                'http_code' => $httpCode
            ];
        }

        // Check if the response contains the expected 'data' field
        if ($httpCode === 200 && isset($responseData['data'])) {
            // Return the correct data with a success message
            return [
                'success' => true,
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            // Handle unexpected API response
            if ($httpCode === 404) {
                error_log("Data not found for office: $officeName and role: $roleName.");
            } elseif ($httpCode === 500) {
                error_log("Internal Server Error from API.");
            } else {
                error_log("Unexpected API response: " . print_r($responseData, true));
            }

            return [
                'success' => false,
                'http_code' => $httpCode,
                'response' => $responseData,
                'error' => isset($responseData['message']) ? $responseData['message'] : 'Unexpected API response'
            ];
        }
    }

    public function updateUserEmailApi($userId, $newEmail, $token)
    {
        $apiUrl = "{$this->api}/api/v1/users/$userId";

        $data = [
            'email' => $newEmail,
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($apiUrl);

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

    public function updateUserProfileApi($userId, $filePath, $token)
    {
        $apiUrl = "{$this->api}/api/v1/users/$userId";

        // Prepare the cURL request with the file
        $ch = curl_init($apiUrl);

        // Use CURLFile to handle file uploads with multipart/form-data
        $data = [
            'img' => new CURLFile($filePath) // This sends the actual file
        ];

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); // POST request to send the file
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token, // Pass the token for authentication
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // cURL will set the correct multipart/form-data boundary

        // Debugging options (enable these only during debugging)
        // curl_setopt($ch, CURLOPT_VERBOSE, true);

        // Ignore SSL certificate verification (for testing purposes only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            // Log the error
            error_log("cURL error: $error");
            return ['success' => false, 'error' => $error, 'http_code' => $httpCode];
        }

        curl_close($ch);

        // Decode the response from the API
        $decodedResponse = json_decode($response, true);

        // Log raw API response for debugging
        error_log("Raw API Response: " . $response);

        return [
            'success' => $httpCode === 200, // Adjust this condition as per your API
            'http_code' => $httpCode,
            'response' => $decodedResponse
        ];
    }

    public function updateUserPasswordApi($userId, $password, $token)
    {
        $apiUrl = "{$this->api}/api/v1/users/$userId";

        $data = [
            'password' => $password, // Pass the hashed password
        ];

        $jsonData = json_encode($data);

        $ch = curl_init($apiUrl);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST"); // Use POST or PATCH depending on the API specification
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token, // Using the token from the session
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

        // Ignore SSL verification (not recommended for production)
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

    public function getUserByIdApi($id, $token, $maxRetries = 3, $cacheEnabled = false)
    {
        return $this->fetchApiData(
            "{$this->api}/api/v1/users/$id",
            $token,
            $maxRetries,
            $cacheEnabled,
            "user_$id.json"
        );
    }

    public function getUserInformationByIdApi($id, $token, $maxRetries = 3, $cacheEnabled = false)
    {
        return $this->fetchApiData(
            "{$this->api}/api/v1/informations/user/$id",
            $token,
            $maxRetries,
            $cacheEnabled,
            "user_information_$id.json"
        );
    }

    private function fetchApiData($url, $token, $maxRetries, $cacheEnabled, $cacheKey, $cacheTTL = 3600)
    {
        $retryCount = 0;
        $connectTimeout = 5; // Timeout for establishing connection
        $requestTimeout = 10; // Timeout for overall request
        $maxRetryDelay = 5; // Max delay between retries in seconds
        $retryDelay = 1; // Initial delay in seconds

        // Optional caching mechanism
        if ($cacheEnabled && file_exists($cacheKey)) {
            $fileModifiedTime = filemtime($cacheKey);
            if (time() - $fileModifiedTime < $cacheTTL) {
                $cachedResponse = file_get_contents($cacheKey);
                $responseData = json_decode($cachedResponse, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return [
                        'http_code' => 200,
                        'data' => $responseData
                    ];
                }
                error_log("Cache read failed or invalid JSON for $cacheKey");
            } else {
                unlink($cacheKey); // Delete expired cache
            }
        }

        do {
            // Initialize cURL session
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer $token"
                ],
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_CONNECTTIMEOUT => $connectTimeout,
                CURLOPT_TIMEOUT => $requestTimeout
            ]);

            // Execute cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            // Close the cURL session
            curl_close($ch);

            if ($response !== false) {
                // Decode JSON response
                $responseData = json_decode($response, true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    if ($httpCode === 200 && isset($responseData['data'])) {
                        // Cache the response if enabled
                        if ($cacheEnabled) {
                            file_put_contents($cacheKey, json_encode($responseData['data']));
                        }

                        return [
                            'http_code' => $httpCode,
                            'data' => $responseData['data']
                        ];
                    } else {
                        error_log("Unexpected API Response (HTTP Code: $httpCode): " . print_r($responseData, true));
                    }
                } else {
                    error_log("JSON Decode Error: " . json_last_error_msg());
                }
            } else {
                error_log("CURL Error: $error (Retry: $retryCount)");
            }

            $retryCount++;
            sleep(min($retryDelay, $maxRetryDelay));
            $retryDelay *= 2; // Exponential backoff
        } while ($retryCount < $maxRetries);

        // Return error after retries are exhausted
        return [
            'http_code' => $httpCode ?? 500,
            'error' => $error ?? "Request failed after $maxRetries retries",
            'response' => $responseData ?? null
        ];
    }


    public function getRoleApi($id, $token)
    {
        $url = "{$this->api}/api/v1/roles/" . $id;

        // Initialize cURL session
        $ch = curl_init($url);

        // Determine if we're on localhost
        $isLocalhost = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_ADDR'] == '127.0.0.1');

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !$isLocalhost); // Ignore SSL certificate verification if on localhost

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        // Check if the response is successful and contains the expected data
        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getOfficeApi($id, $token)
    {
        $url = "{$this->api}/api/v1/offices/{$id}";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

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
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getAllOfficeApi($token)
    {
        $url = "{$this->api}/api/v1/offices";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

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
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getDepartmentApi($id, $token)
    {
        $url = "{$this->api}/api/v1/departments/" . $id;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

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
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getAllDepartmentApi($token)
    {
        $url = "{$this->api}/api/v1/departments/";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

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
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data'],
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // អនុប្រធានការិយាល័យ
    public function getEmailLeaderDOApi($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'អនុប្រធានការិយាល័យ') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    } else {
                        $firstNameKh[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    } else {
                        $lastNameKh[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // ប្រធានការិយាល័យ
    public function getEmailLeaderHOApi($id, $token)
    {

        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធានការិយាល័យ') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // អនុប្រធាននាយកដ្ឋាន
    public function getEmailLeaderDDApi($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'អនុប្រធាននាយកដ្ឋាន') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // ប្រធាននាយកដ្ឋាន
    public function getEmailLeaderHDApi($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធាននាយកដ្ឋាន') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // អនុប្រធានអង្គភាព
    public function getEmailLeaderDHU1Api($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleLeave']) && $leader['roleLeave'] === 'Deputy Head Of Unit 1') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getEmailLeaderDHU2Api($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleLeave']) && $leader['roleLeave'] === 'Deputy Head Of Unit 2') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // ប្រធានអង្គភាព​
    public function getEmailLeaderHUApi($id, $token)
    {
        $url = "{$this->api}/api/v1/users/leader/contact/" . $id;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $token));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            error_log("CURL Error: $error");
            return null;
        }

        // Log the raw API response for debugging
        error_log("API Response: " . $response);

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return null;
        }

        if ($httpCode === 200 && isset($responseData['data'])) {
            $leaders = $responseData['data'];

            // Log the leaders data to ensure it's being received correctly
            error_log("Leaders Data: " . print_r($leaders, true));

            $emails = [];
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($leaders as $leader) {
                if (isset($leader['roleName']) && $leader['roleName'] === 'ប្រធានអង្គភាព') {
                    if (isset($leader['email'])) {
                        $emails[] = $leader['email'];
                    }
                    if (isset($leader['id'])) {
                        $ids[] = $leader['id'];
                    }
                    if (isset($leader['firstNameKh'])) {
                        $firstNameKh[] = $leader['firstNameKh'];
                    }
                    if (isset($leader['lastNameKh'])) {
                        $lastNameKh[] = $leader['lastNameKh'];
                    }
                    if (isset($leader['image'])) {
                        $image[] = $leader['image'];
                    } else {
                        $image[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['roleName'])) {
                        $roleName[] = $leader['roleName'];
                    } else {
                        $roleName[] = ''; // Fallback to empty if the field is not present
                    }
                    if (isset($leader['departmentName'])) {
                        $departmentName[] = $leader['departmentName'];
                    } else {
                        $departmentName[] = ''; // Fallback to empty if the field is not present
                    }
                }
            }

            // Log the filtered emails and ids to check if they are found correctly
            error_log("Filtered Emails: " . print_r($emails, true));
            error_log("Filtered IDs: " . print_r($ids, true));

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
                'image' => $image,
                'roleName' => $roleName,
                'departmentName' => $departmentName,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    // get Admin Email 
    public function getAdminEmails($token)
    {
        $url = "{$this->api}/api/v1/users/";

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
            $ids = [];
            $firstNameKh = [];
            $lastNameKh = [];

            foreach ($responseData['data'] as $user) {
                if (isset($user['roleLeave']) && $user['roleLeave'] === 'Admin') {
                    if (isset($user['email'])) {
                        $emails[] = $user['email'];
                    }
                    if (isset($user['id'])) {
                        $ids[] = $user['id'];
                    }
                    if (isset($user['firstNameKh'])) {
                        $firstNameKh[] = $user['firstNameKh'];
                    }
                    if (isset($user['lastNameKh'])) {
                        $lastNameKh[] = $user['lastNameKh'];
                    }
                }
            }

            return [
                'http_code' => $httpCode,
                'emails' => $emails,
                'ids' => $ids,
                'firstNameKh' => $firstNameKh,
                'lastNameKh' => $lastNameKh,
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }
    }

    public function getApproverByRole($userModel, $userId, $token, $role, $departmentName)
    {
        $approver = null;

        // Initial assignment based on role
        switch ($role) {
            case 'NULL':
                $approver = $userModel->getEmailLeaderDOApi($userId, $token);
                break;
            case 'Deputy Head Of Office':
                $approver = $userModel->getEmailLeaderHOApi($userId, $token);
                break;
            case 'Head Of Office':
                $approver = $userModel->getEmailLeaderDDApi($userId, $token);
                break;
            case 'Deputy Head Of Department':
                $approver = $userModel->getEmailLeaderHDApi($userId, $token);
                break;
            case 'Head Of Department':
                if (in_array($departmentName, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $approver = $userModel->getEmailLeaderDHU1Api($userId, $token);
                } else {
                    $approver = $userModel->getEmailLeaderDHU2Api($userId, $token);
                }
                break;
            case 'Deputy Head Of Unit 1':
            case 'Deputy Head Of Unit 2':
                $approver = $userModel->getEmailLeaderHUApi($userId, $token);
                break;
            default:
                $approver = $userModel->getEmailLeaderHUApi($userId, $token);
                break;
        }

        // Define the fallback hierarchy for each role to ensure the correct order is followed
        $fallbackHierarchy = [
            'NULL' => ['getEmailLeaderDOApi'],
            'Deputy Head Of Office' => ['getEmailLeaderHOApi'],
            'Head Of Office' => ['getEmailLeaderDDApi'],
            'Deputy Head Of Department' => ['getEmailLeaderHDApi'],
            'Head Of Department' => $departmentName === 'នាយកដ្ឋានកិច្ចការទូទៅ' || $departmentName === 'នាយកដ្ឋានសវនកម្មទី២'
                ? ['getEmailLeaderDHU1Api']
                : ['getEmailLeaderDHU2Api'],
            'Deputy Head Of Unit 1' => ['getEmailLeaderHUApi'],
            'Deputy Head Of Unit 2' => ['getEmailLeaderHUApi'],
            'default' => ['getEmailLeaderHUApi']
        ];

        // If the assigned approver is unavailable, follow the hierarchy for their role
        $roleKey = array_key_exists($role, $fallbackHierarchy) ? $role : 'default';
        $fallbackMethods = $fallbackHierarchy[$roleKey];

        foreach ($fallbackMethods as $method) {
            if ($approver && ($userModel->isManagerOnLeaveToday($approver['ids']) || $userModel->isManagerOnMission($approver['ids']))) {
                $approver = $userModel->$method($userId, $token);
            }

            // Break if an available approver is found
            if ($approver && !$userModel->isManagerOnLeaveToday($approver['ids']) && !$userModel->isManagerOnMission($approver['ids'])) {
                break;
            }
        }

        return $approver;
    }

    public function getApproverByRoleWithoutAvailabilityCheck($userModel, $userId, $token, $role, $departmentName)
    {
        $approver = null;

        // Initial assignment based on role
        switch ($role) {
            case 'NULL':
                $approver = $userModel->getEmailLeaderDOApi($userId, $token);
                break;
            case 'Deputy Head Of Office':
                $approver = $userModel->getEmailLeaderHOApi($userId, $token);
                break;
            case 'Head Of Office':
                $approver = $userModel->getEmailLeaderDDApi($userId, $token);
                break;
            case 'Deputy Head Of Department':
                $approver = $userModel->getEmailLeaderHDApi($userId, $token);
                break;
            case 'Head Of Department':
                if (in_array($departmentName, ['នាយកដ្ឋានកិច្ចការទូទៅ', 'នាយកដ្ឋានសវនកម្មទី២'])) {
                    $approver = $userModel->getEmailLeaderDHU1Api($userId, $token);
                } else {
                    $approver = $userModel->getEmailLeaderDHU2Api($userId, $token);
                }
                break;
            case 'Deputy Head Of Unit 1':
            case 'Deputy Head Of Unit 2':
                $approver = $userModel->getEmailLeaderHUApi($userId, $token);
                break;
            default:
                $approver = $userModel->getEmailLeaderHUApi($userId, $token);
                break;
        }

        // Define fallback hierarchy for each role
        $fallbackHierarchy = [
            'NULL' => ['getEmailLeaderHOApi', 'getEmailLeaderDDApi', 'getEmailLeaderHDApi'],
            'Deputy Head Of Office' => ['getEmailLeaderDDApi', 'getEmailLeaderHDApi', 'getEmailLeaderHUApi'],
            'Head Of Office' => ['getEmailLeaderDDApi', 'getEmailLeaderHDApi', 'getEmailLeaderHOApi'],
            'Deputy Head Of Department' => ['getEmailLeaderHDApi', 'getEmailLeaderHOApi', 'getEmailLeaderDDApi'],
            'Head Of Department' => $departmentName === 'នាយកដ្ឋានកិច្ចការទូទៅ' || $departmentName === 'នាយកដ្ឋានសវនកម្មទី២'
                ? ['getEmailLeaderDHU1Api', 'getEmailLeaderHOApi']
                : ['getEmailLeaderDHU2Api', 'getEmailLeaderHOApi'],
            'Deputy Head Of Unit 1' => ['getEmailLeaderHUApi', 'getEmailLeaderHOApi'],
            'Deputy Head Of Unit 2' => ['getEmailLeaderHUApi', 'getEmailLeaderHOApi'],
            'default' => ['getEmailLeaderHUApi']
        ];

        // Apply fallback without checking for leave or mission
        if (!$approver) {
            // Get the fallback methods for the role
            $roleKey = array_key_exists($role, $fallbackHierarchy) ? $role : 'default';
            $fallbackMethods = $fallbackHierarchy[$roleKey];

            foreach ($fallbackMethods as $method) {
                $approver = $userModel->$method($userId, $token);

                // Stop once the next fallback approver is assigned
                if ($approver) {
                    break;
                }
            }
        }

        return $approver;
    }

    // mission to API 
    public function updateMissionToApi($user_id, $start_date, $end_date, $mission, $token)
    {
        $url = "{$this->api}/api/v1/leaves";

        $data = [
            'uid' => $user_id,
            'startDate' => $start_date,
            'endDate' => $end_date,
            'mission' => $mission
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

    public function updateLateInToApi($uId, $checkIn, $lateIn, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkIn,
            'lateIn' => $lateIn,
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

    public function updateLateOutToApi($uId, $checkOut, $lateOut, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkOut,
            'lateOut' => $lateOut,
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

    public function updateLeaveEarlyToApi($uId, $checkOut, $exit, $token)
    {
        $url = "{$this->api}/api/v1/lates";

        // Prepare the data for the POST request
        $data = [
            'uid' => $uId,
            'date' => $checkOut,
            'exit' => $exit,
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

    public function isManagerOnLeaveToday($managerId)
    {
        $today = date('Y-m-d');

        // Check if $managerId is an array
        if (is_array($managerId)) {
            // Convert the array to a comma-separated string for the SQL IN clause
            $placeholders = rtrim(str_repeat('?,', count($managerId)), ',');
            $sql = "SELECT * FROM leave_requests WHERE user_id IN ($placeholders) AND status = 'Approved' AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);

            // Add all the manager IDs to the bind values
            $stmt->execute(array_merge($managerId, [$today]));
        } else {
            // Handle the case where it's a single manager ID
            $sql = "SELECT * FROM leave_requests WHERE user_id = ? AND status = 'Approved' AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$managerId, $today]);
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if the manager has an approved leave request for today
        return !empty($result);
    }

    public function isManagerOnMission($managerId)
    {
        $today = date('Y-m-d');

        // Check if $managerId is an array
        if (is_array($managerId)) {
            // Convert the array to a comma-separated string for the SQL IN clause
            $placeholders = rtrim(str_repeat('?,', count($managerId)), ',');
            $sql = "SELECT * FROM missions WHERE user_id IN ($placeholders) AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);

            // Add all the manager IDs to the bind values
            $stmt->execute(array_merge($managerId, [$today]));
        } else {
            // Handle the case where it's a single manager ID
            $sql = "SELECT * FROM missions WHERE user_id = ? AND ? BETWEEN start_date AND end_date";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$managerId, $today]);
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if the manager has a mission for today
        return !empty($result);
    }

    public function getUserAttendanceByIdApi($id, $token, $page = 1, $limit = 31)
    {
        // Calculate offset
        $offset = ($page - 1) * $limit;

        // API URL with pagination
        $url = "{$this->api}/api/v1/attendances/user/{$id}?page={$page}&limit={$limit}";

        // Initialize cURL session
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        // Execute cURL and capture response
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            return ['http_code' => 500, 'error' => "CURL Error: $error"];
        }

        // Decode API response
        $responseData = json_decode($response, true);
        $attendances = $responseData['data'] ?? [];

        // Process attendance data
        foreach ($attendances as &$attendance) {
            $attendance['lateIn'] = null;
            $attendance['lateOut'] = null;
            $attendance['leaveEarly'] = null;
            $attendance['status'] = null;

            // Late Check-In
            if (isset($attendance['checkIn']) && strtotime($attendance['checkIn']) > strtotime('09:00:00')) {
                $attendance['lateIn'] = 'ចូលយឺត';
            }

            // Early Check-Out
            if (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) < strtotime('16:00:00')) {
                $attendance['leaveEarly'] = 'ចេញមុន';
            }

            // Late Check-Out
            if (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) > strtotime('17:30:00')) {
                $attendance['lateOut'] = 'ចេញយឺត';
            }

            // Leave Status
            if (!empty($attendance['leave'])) {
                $attendance['status'] = 'ច្បាប់';
            }

            // Mission Status
            if (!empty($attendance['mission'])) {
                $attendance['status'] = 'បេសកកម្ម';
            }
        }

        return [
            'http_code' => $httpCode,
            'data' => $attendances
        ];
    }

    public function todayAttendanceByUseridApi($userId, $date, $token)
    {
        try {
            // Construct the API URL
            $url = "{$this->api}/api/v1/attendances/user/{$userId}?date={$date}";

            // Initialize cURL session
            $ch = curl_init($url);

            // Set cURL options
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $token],
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            // Execute the cURL request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            // Close cURL session
            curl_close($ch);

            // Check for cURL errors
            if ($response === false) {
                error_log("CURL Error: $error");
                return [];
            }

            // Decode the JSON response
            $responseData = json_decode($response, true);

            // Handle non-200 HTTP response codes
            if ($httpCode !== 200) {
                error_log("API Error: HTTP Code $httpCode, Message: " . ($responseData['message'] ?? 'Unknown error'));
                return [];
            }

            // Check if data is available in the API response
            if (!isset($responseData['data']) || empty($responseData['data'])) {
                return []; // Return an empty array if no attendance data is found
            }

            // Extract today's attendance records
            $attendances = $responseData['data'];

            // Filter the attendance records for the given date
            $todayAttendances = array_filter($attendances, function ($attendance) use ($date) {
                return isset($attendance['date']) && $attendance['date'] === $date;
            });

            return $todayAttendances; // Return today's attendance records
        } catch (Exception $e) {
            error_log("Error fetching today's attendance records via API: " . $e->getMessage());
            return []; // Return an empty array on error
        }
    }

    public function getAllUserAttendance($token)
    {
        $url = "{$this->api}/api/v1/attendances";

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $token
        ));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Ignore SSL certificate verification

        // Execute cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Check for cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            return [
                'http_code' => 500,
                'error' => "CURL Error: $error"
            ];
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return [
                'http_code' => $httpCode,
                'error' => "JSON Decode Error: " . json_last_error_msg()
            ];
        }

        // Check if the response is successful and contains the expected data
        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data']
            ];
        } else {
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'error' => "Unexpected API Response",
                'response' => $responseData
            ];
        }
    }

    public function getAttendanceByUserid($userId, $date, $token)
    {
        try {
            // Check if an attendance record exists for the user on the given date
            $sql = "SELECT * FROM {$this->attendance} WHERE userId = :user_id AND date = :date";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':date', $date);
            $stmt->execute();

            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$attendance) {
                return [
                    'error' => true,
                    'message' => 'No attendance record found for the specified user and date.'
                ];
            }

            // Fetch user details from the external API
            $userApiResponse = $this->getUserByIdApi($attendance['userId'], $token);

            $userInfo = [];
            if ($userApiResponse['http_code'] === 200 && isset($userApiResponse['data'])) {
                $userData = $userApiResponse['data'];
                $userInfo = [
                    'khmer_name' => isset($userData['lastNameKh'], $userData['firstNameKh'])
                        ? $userData['lastNameKh'] . " " . $userData['firstNameKh']
                        : 'N/A',
                    'phone_number' => $userData['phoneNumber'] ?? 'N/A',
                    'email' => $userData['email'] ?? 'N/A',
                    'dob' => $userData['date_of_birth'] ?? 'N/A',
                    'deputy_head_name' => $userData['deputy_head_name'] ?? 'N/A'
                ];
            } else {
                error_log("Failed to fetch user data for user ID: $userId");
                $userInfo = [
                    'khmer_name' => 'N/A',
                    'phone_number' => 'N/A',
                    'email' => 'N/A',
                    'dob' => 'N/A',
                    'deputy_head_name' => 'N/A'
                ];
            }

            // Combine attendance data with user information
            return [
                'error' => false,
                'attendance' => $attendance,
                'user_info' => $userInfo
            ];
        } catch (Exception $e) {
            error_log("Error fetching attendance or user data: " . $e->getMessage());
            return [
                'error' => true,
                'message' => 'An unexpected error occurred while processing the request.'
            ];
        }
    }

    public function todayAttendanceByUserid($userId, $date)
    {
        try {
            // Query to fetch today's attendance records for the user
            $sql = "SELECT * FROM {$this->attendance} 
                WHERE userId = :user_id AND date = :date 
                ORDER BY id DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                error_log("Database Query Error: " . implode(" | ", $stmt->errorInfo()));
                return [];
            }

            // Ensure result is always an array
            $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            return $attendances; // Return the attendance records
        } catch (Exception $e) {
            error_log("Error fetching today's attendance records: " . $e->getMessage());
            return []; // Return an empty array on error
        }
    }

    public function fullAttendanceByUserid($userId, $date)
    {
        try {
            // Fetch all attendance records for the user
            $sql = "SELECT * FROM {$this->attendance} WHERE userId = :user_id AND date != :date ORDER BY id DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':date', $date);
            $stmt->execute();

            $attendances = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$attendances) {
                return []; // Return an empty array if no attendance records are found
            }

            // Analyze each record to determine statuses
            foreach ($attendances as &$attendance) {
                // Default statuses
                $attendance['lateIn'] = null;
                $attendance['lateOut'] = null;
                $attendance['leaveEarly'] = null;
                $attendance['status'] = null;

                // Check for late check-in
                if (isset($attendance['checkIn']) && strtotime($attendance['checkIn']) > strtotime('09:00:00')) {
                    $attendance['lateIn'] = 'ចូលយឺត'; // Late Check-In
                }

                // Check for early check-out
                if (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) < strtotime('16:00:00')) {
                    $attendance['leaveEarly'] = 'ចេញមុន'; // Leave Early
                }

                // Check for late check-out
                if (isset($attendance['checkOut']) && strtotime($attendance['checkOut']) > strtotime('17:30:00')) {
                    $attendance['lateOut'] = 'ចេញយឺត'; // Late Check-Out
                }

                // Check for leave
                if (isset($attendance['leave']) && $attendance['leave'] == 1) {
                    $attendance['status'] = 'ច្បាប់'; // Leave
                }

                // Check for mission
                if (isset($attendance['mission']) && $attendance['mission'] == 1) {
                    $attendance['status'] = 'បេសកកម្ម'; // Mission
                }
            }

            return $attendances; // Return the modified attendance records
        } catch (Exception $e) {
            error_log("Error fetching attendance records: " . $e->getMessage());
            return []; // Return an empty array on error
        }
    }

    public function getUserFilterAttendanceByIdApi($userId, $token, $startDate = null, $endDate = null)
    {
        // Base URL for the API
        $url = "{$this->api}/api/v1/attendances/user/" . $userId;

        // Add query parameters for start and end dates if provided
        $queryParams = [];
        if ($startDate) {
            $queryParams['fromDate'] = $startDate;
        }
        if ($endDate) {
            $queryParams['toDate'] = $endDate;
        }

        // If query parameters exist, append them to the URL
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Initialize cURL session
        $ch = curl_init($url);

        // Set cURL options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  // Ignore SSL certificate verification (for local dev)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Execute the cURL request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        // Close the cURL session
        curl_close($ch);

        // Handle cURL errors
        if ($response === false) {
            error_log("CURL Error: $error");
            return [
                'http_code' => 500,
                'error' => "CURL Error: $error"
            ];
        }

        // Decode the JSON response
        $responseData = json_decode($response, true);

        // Handle JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("JSON Decode Error: " . json_last_error_msg());
            return [
                'http_code' => $httpCode,
                'error' => "JSON Decode Error: " . json_last_error_msg()
            ];
        }

        // Check if the response is successful and contains data
        if ($httpCode === 200 && isset($responseData['data'])) {
            return [
                'http_code' => $httpCode,
                'data' => $responseData['data']
            ];
        } else {
            // Log and return unexpected API response
            error_log("Unexpected API Response: " . print_r($responseData, true));
            return [
                'http_code' => $httpCode,
                'error' => "Unexpected API Response",
                'response' => $responseData
            ];
        }
    }

    // Modify function to get EmailLeaderDOApi using telegram_id
    public function getTelegramIdByUserId($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->telegramUser} WHERE user_id = :user_id");

        // Execute the statement with the user_id from the session or passed argument
        $stmt->execute(['user_id' => $userId]);

        // Fetch all matching data (assuming one record per user)
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return the user data if found, otherwise return null
        return $result ? $result : null;
    }

    public function sendHolds($title, $managerId, $start_date, $end_date, $duration_days, $remarks, $link)
    {
        $telegramUser = $this->getTelegramIdByUserId($managerId);
        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            $notifications = [
                "🔔 *$title*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ:* `{$_SESSION['user_khmer_name']}`",
                "📅 *ចាប់ពី:* `{$start_date}`",
                "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                "💬 *មូលហេតុ:* `{$remarks}`",
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Creating a keyboard for the notification
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ពិនិត្យមើលសំណើ', 'url' => $link]
                    ]
                ]
            ];

            // Send the Telegram notification
            $telegramModel = new TelegramModel($this->pdo);
            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage, $keyboard);

            // Log success or failure of the Telegram notification
            if ($success) {
                error_log("Telegram notification sent to user ID: {$managerId}");
            } else {
                error_log("Failed to send Telegram notification to user ID: {$managerId}");
            }
        }
    }

    // user telegram apply leave 
    public function sendTelegramNotification($userModel, $managerId, $start_date, $end_date, $duration_days, $remarks, $leaveRequestId, $link)
    {
        $telegramUser = $userModel->getTelegramIdByUserId($managerId);
        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            $notifications = [
                "🔔 *ស្នើសុំច្បាប់ឈប់សម្រាក*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ:* `{$_SESSION['user_khmer_name']}`",
                "📅 *ចាប់ពី:* `{$start_date}`",
                "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                "💬 *មូលហេតុ:* `{$remarks}`",
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Creating a keyboard for the notification
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ពិនិត្យមើលសំណើ', 'url' => $link]
                    ]
                ]
            ];

            // Send the Telegram notification
            $telegramModel = new TelegramModel($this->pdo);
            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage, $keyboard);

            // Log success or failure of the Telegram notification
            if ($success) {
                error_log("Telegram notification sent to user ID: {$managerId}");
            } else {
                error_log("Failed to send Telegram notification to user ID: {$managerId}");
            }
        }
    }

    public function sendCheckToTelegram($userId, $date, $check)
    {
        $userModel = new User();
        $telegramUser = $userModel->getTelegramIdByUserId($userId);

        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            $notifications = [
                "🔔 *វត្តមានប្រចាំថ្ងៃ*",
                "---------------------------------------------",
                "👤 *អ្នកប្រើប្រាស់:* `{$_SESSION['user_khmer_name']}`",
                "📅 *កាលបរិច្ឆេទ:* `{$date}`",
                "🕒 *ម៉ោង:* `{$check}`",
            ];

            $telegramMessage = implode("\n", $notifications);
            $telegramModel = new TelegramModel($this->pdo);

            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage);

            if ($success) {
                error_log("Telegram attendance notification sent to user ID: {$userId}");
            } else {
                error_log("Failed to send Telegram attendance notification to user ID: {$userId}");
            }
        }
    }

    // send notification to next Manager 
    public function sendTelegramNextManager($managerId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status, $link)
    {
        // Step 1: Get the Telegram ID for the manager
        $telegramManager = $this->getTelegramIdByUserId($managerId);

        if ($telegramManager && !empty($telegramManager['telegram_id'])) {
            // Log the telegram_id for debugging
            error_log("Found telegram_id: " . $telegramManager['telegram_id']);

            // Step 2: Prepare the Telegram message
            // Determine dynamic response based on status
            switch ($status) {
                case 'Approved':
                    $dynamicResponse = "`✅{$_SESSION['user_khmer_name']}` *បានអនុម័ត ច្បាប់ឈប់សម្រាកនេះ។*"; // Include approver's name
                    break;
                case 'Rejected':
                    $dynamicResponse = "`❌{$_SESSION['user_khmer_name']}` *មិនអនុម័ត ច្បាប់ឈប់សម្រាកនេះ។*";
                    break;
                case 'Pending':
                default:
                    $dynamicResponse = "🕒 *Your leave request is still pending.*";
                    break;
            }

            // Creating a list of notifications with the status first
            $notifications = [
                "🔔 *ស្នើសុំច្បាប់ឈប់សម្រាក*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ:* `{$uname}`",
                "📅 *ចាប់ពី:* `{$start_date}`",
                "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                "💬 *មូលហេតុ:* `{$uremarks}`",
                "---------------------------------------------",
                "📋 *ស្ថានភាព:*",
                "{$dynamicResponse}"
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Step 3: Create the inline keyboard with a single "View the Request" button
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ពិនិត្យមើលសំណើ', 'url' => $link] // URL to open the request
                    ]
                ]
            ];

            // Step 4: Attempt to send the Telegram notification with the "View the Request" button
            $telegramModel = new TelegramModel($this->pdo);
            $success = $telegramModel->sendTelegramNotification($telegramManager['telegram_id'], $telegramMessage, $keyboard);

            // Step 5: Check if the notification was successfully sent
            if ($success) {
                error_log("Telegram notification sent successfully to user with telegram_id: " . $telegramManager['telegram_id']);
            } else {
                error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramManager['telegram_id']);
                $_SESSION['error'] = [
                    'title' => "Telegram Notification Error",
                    'message' => "Could not send Telegram notification. Please check your settings or contact support."
                ];
            }
        }
    }

    // send notification back to User 
    public function sendBackToUser($userId, $uname, $start_date, $end_date, $duration_days, $uremarks, $status)
    {
        // Step 1: Get the Telegram ID for the user
        $telegramUser = $this->getTelegramIdByUserId($userId);

        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            // Log the telegram_id for debugging
            error_log("Found telegram_id: " . $telegramUser['telegram_id']);

            // Step 2: Prepare the Telegram message
            // Determine dynamic response based on status
            switch ($status) {
                case 'Approved':
                    $dynamicResponse = "`✅{$_SESSION['user_khmer_name']}` *បានអនុម័ត ច្បាប់ឈប់សម្រាករបស់អ្នក។*"; // Include approver's name
                    break;
                case 'Rejected':
                    $dynamicResponse = "`❌{$_SESSION['user_khmer_name']}` *មិនអនុម័ត ច្បាប់ឈប់សម្រាកនេះ។*";
                    break;
                case 'Pending':
                default:
                    $dynamicResponse = "🕒 *Your leave request is still pending.*";
                    break;
            }

            // Creating a list of notifications with the status first
            $notifications = [
                "🔔 *ស្នើសុំច្បាប់ឈប់សម្រាក*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ:* `{$uname}`",
                "📅 *ចាប់ពី:* `{$start_date}`",
                "📅 *ដល់កាលបរិចេ្ឆទ:* `{$end_date}`",
                "🗓️ *រយៈពេល:* `{$duration_days}` ថ្ងៃ",
                "💬 *មូលហេតុ:* `{$uremarks}`",
                "---------------------------------------------",
                "📋 *ស្ថានភាព:*",
                "{$dynamicResponse}"
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Step 3: Attempt to send the Telegram notification
            $telegramModel = new TelegramModel($this->pdo);
            $success = $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage);

            // Step 4: Check if the notification was successfully sent
            if ($success) {
                error_log("Telegram notification sent successfully to user with telegram_id: " . $telegramUser['telegram_id']);
            } else {
                error_log("Failed to send Telegram notification to user with telegram_id: " . $telegramUser['telegram_id']);
                $_SESSION['error'] = [
                    'title' => "Telegram Notification Error",
                    'message' => "Could not send Telegram notification. Please check your settings or contact support."
                ];
            }
        }
    }

    // New method to handle Telegram notifications LateOut
    public function sendTelegramNotificationToAdmin($adminId, $userNameKh, $lateMinutes, $date, $time, $reason)
    {
        $userModel = new User();
        $telegramUser = $userModel->getTelegramIdByUserId($adminId);

        if ($telegramUser && !empty($telegramUser['telegram_id'])) {
            // Log the Telegram ID for debugging
            error_log("Found telegram_id: " . $telegramUser['telegram_id']);

            $notifications = [
                "🔔 *សំណើចេញយឺត*",
                "---------------------------------------------",
                "👤 *អ្នកស្នើ: *`{$userNameKh}`",
                "⏰ *ចេញយឺត:  *`{$lateMinutes} នាទី`",
                "🗓️ *កាលបរិច្ឆេទ:   *`{$date}`",
                "🕒 *ម៉ោង:    *`{$time}`",
                "💬 *មូលហេតុ:  *`{$reason}`",
            ];

            // Joining notifications into a single message with new lines
            $telegramMessage = implode("\n", $notifications);

            // Create the inline keyboard with a single "View the Request" button
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ពិនិត្យមើលសំណើ', 'url' => 'https://leave.iauoffsa.us/elms/overtimeout/'] // Using URL to open the request
                    ]
                ]
            ];

            // Send the Telegram notification
            $telegramModel = new TelegramModel($this->pdo);
            return $telegramModel->sendTelegramNotification($telegramUser['telegram_id'], $telegramMessage, $keyboard);
        } else {
            // Log the failure to find a valid telegram_id
            error_log("No valid telegram_id found for adminId: " . $adminId);
            return false; // Return false if notification could not be sent
        }
    }

    public function getUser2FA($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_authenticators WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function convertToKhmerNumbers($number)
    {
        $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
        $number = (string) $number;
        $converted = '';
        for ($i = 0; $i < strlen($number); $i++) {
            $digit = $number[$i];
            $converted .= is_numeric($digit) ? $khmerNumbers[$digit] : $digit;
        }
        return $converted;
    }

    function convertDateToKhmer($date)
    {
        if (empty($date)) {
            return '';
        }

        // Convert Arabic numerals to Khmer numerals
        $khmerNumbers = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];

        // Khmer month names
        $khmerMonths = [
            1 => 'មករា',
            2 => 'កុម្ភៈ',
            3 => 'មិនា',
            4 => 'មេសា',
            5 => 'ឧសភា',
            6 => 'មិថុនា',
            7 => 'កក្កដា',
            8 => 'សីហា',
            9 => 'កញ្ញា',
            10 => 'តុលា',
            11 => 'វិច្ឆិកា',
            12 => 'ធ្នូ'
        ];

        // Parse the date
        $timestamp = strtotime($date);
        if (!$timestamp) {
            return ''; // Return empty if the date is invalid
        }

        // Extract date components
        $day = date('j', $timestamp);
        $month = (int) date('n', $timestamp);
        $year = date('Y', $timestamp);

        // Convert day and year to Khmer numerals
        $khmerDay = '';
        $khmerYear = '';

        foreach (str_split($day) as $digit) {
            $khmerDay .= $khmerNumbers[$digit];
        }

        foreach (str_split($year) as $digit) {
            $khmerYear .= $khmerNumbers[$digit];
        }

        // Return formatted Khmer date
        return $khmerDay . ' ' . $khmerMonths[$month] . ' ' . $khmerYear;
    }
}
