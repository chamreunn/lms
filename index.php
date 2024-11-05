<?php
// Configure session settings BEFORE starting the session
session_set_cookie_params([
    'lifetime' => 86400, // 1 day in seconds
    'path' => '/',
    'domain' => '', // Leave as empty or set to your domain, e.g., 'example.com'
    'secure' => isset($_SERVER['HTTPS']), // Only send over HTTPS if available
    'httponly' => true, // Prevents JavaScript from accessing the session cookie
    'samesite' => 'Lax' // Controls cross-site request handling
]);

// Set server-side session garbage collection max lifetime
ini_set('session.gc_maxlifetime', 86400); // 1 day in seconds

// Now start the session
session_start();

// Include your database and routing files
require 'config/database.php';
require 'routes.php';
