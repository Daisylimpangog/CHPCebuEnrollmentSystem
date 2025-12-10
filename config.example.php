<?php
// ============================================================================
// ENROLLMENT SYSTEM - CONFIGURATION TEMPLATE
// ============================================================================
// COPY THIS FILE TO config.php AND UPDATE WITH YOUR CREDENTIALS
// DO NOT COMMIT config.php TO GITHUB (it contains sensitive data)
// ============================================================================

// ============================================================================
// DATABASE CONFIGURATION
// ============================================================================
// For Local Development (XAMPP):
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'enrollment_system');

// For Live Server (Update with your hosting credentials):
// Get credentials from cPanel → MySQL Databases section
// define('DB_HOST', 'your-hosting-mysql-server');  // Often 'localhost'
// define('DB_USER', 'your_database_user');         // e.g., 'cpanel_username_db'
// define('DB_PASS', 'your_database_password');     // Your MySQL user password
// define('DB_NAME', 'your_database_name');         // e.g., 'enrollment_system'

// ============================================================================
// CREATE CONNECTION
// ============================================================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// ============================================================================
// SESSION CONFIGURATION
// ============================================================================
session_start();

// Set secure session options (uncomment for production with HTTPS)
// ini_set('session.cookie_secure', 1);      // HTTPS only
// ini_set('session.cookie_httponly', 1);    // No JavaScript access
// ini_set('session.cookie_samesite', 'Strict');

// ============================================================================
// SECURITY FUNCTIONS
// ============================================================================

/**
 * Generate CSRF token for forms
 */
function generateToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Sanitize user input
 */
function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}

// ============================================================================
// AUTHENTICATION FUNCTIONS
// ============================================================================

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

/**
 * Redirect to another page
 */
function redirect($page) {
    header("Location: $page");
    exit;
}

// ============================================================================
// APPLICATION SETTINGS
// ============================================================================

// Application name
define('APP_NAME', 'Enrollment System');

// Application URL (update for production)
define('APP_URL', 'http://localhost/enrollment-system/');

// Email configuration (optional, for future use)
// define('ADMIN_EMAIL', 'admin@yourdomain.com');
// define('SUPPORT_EMAIL', 'support@yourdomain.com');

// Enrollment settings
define('MIN_PASSWORD_LENGTH', 6);
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

// ============================================================================
// ERROR LOGGING (Optional)
// ============================================================================

// Enable error logging in production
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// ini_set('error_log', '/path/to/error.log');

// ============================================================================
// TIMEZONE (Optional)
// ============================================================================

// Set default timezone
date_default_timezone_set('UTC');
// Or for Asia/Manila: date_default_timezone_set('Asia/Manila');

?>
