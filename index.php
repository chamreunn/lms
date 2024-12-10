<?php
// Configure session cookie parameters for 1 month lifetime
session_set_cookie_params([
    'lifetime' => 2592000, // 1 month in seconds (30 days)
    'path' => '/',
    'domain' => 'https://leavebeta.iauoffsa.us/', // Use your domain or leave blank for default
    'secure' => isset($_SERVER['HTTPS']), // Secure cookies for HTTPS
    'httponly' => true, // Restrict access via JavaScript
    'samesite' => 'Lax', // Prevent cross-site cookie usage
]);

// Configure PHP to keep session data for 1 month
ini_set('session.gc_maxlifetime', 2592000); // 1 month (in seconds)
ini_set('session.cookie_lifetime', 2592000); // 1 month for the cookie to persist

// Start the session
session_start();

// Manually set a session cookie if not already present
if (!isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', session_id(), time() + 2592000, '/', '', isset($_SERVER['HTTPS']), true);
}

// Check if user_id exists in the session
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page or handle the missing session as needed
    header('Location: /elms/login'); // Adjust the login page URL as needed
    exit();
}

// Include database and routing files
require 'config/database.php';
require 'routes.php';

// Function to handle asynchronous routes
function asyncHandler($callback)
{
    try {
        // Begin output buffering
        ob_start();
        $callback();
        ob_end_flush(); // Flush the output buffer
    } catch (Throwable $e) {
        // Catch any errors and return a 500 response
        ob_end_clean(); // Clear output buffer on error
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage(),
        ]);
    }
}
