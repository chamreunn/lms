<?php
// Configure session cookie parameters for long lifetime
session_set_cookie_params([
    'lifetime' => 31536000, // 1 year in seconds
    'path' => '/',
    'domain' => 'https://leavebeta.iauoffsa.us/', // Use your domain or leave blank for default
    'secure' => isset($_SERVER['HTTPS']), // Secure cookies for HTTPS
    'httponly' => true, // Restrict access via JavaScript
    'samesite' => 'Lax', // Prevent cross-site cookie usage
]);

// Configure PHP to keep session data indefinitely
ini_set('session.gc_maxlifetime', 31536000); // 1 year (in seconds)
ini_set('session.cookie_lifetime', 31536000); // 1 year for the cookie to persist

// Start the session
session_start();

// Manually set a session cookie if not already present
if (!isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', session_id(), time() + 31536000, '/', '', isset($_SERVER['HTTPS']), true);
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
