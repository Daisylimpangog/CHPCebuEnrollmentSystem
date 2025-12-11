<?php
// ========================================
// PRODUCTION CONFIGURATION
// For: chpcebuenrollmentform.infinityfree.me
// ========================================

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'if0_40651937');
define('DB_PASS', '9nnXfc3E7ndGd');
define('DB_NAME', 'if0_40651937_enrollmentdb');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Session configuration
session_start();

// PRODUCTION MODE - Hide errors from public
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error.log');

// Site URL
define('BASE_URL', 'https://chpcebuenrollmentform.infinityfree.me/');

// Session security settings
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

// Helper function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Helper function to redirect to login
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}

// Helper function to redirect to admin dashboard
function require_admin() {
    if (!is_admin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}

// CSRF Token generation
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF Token validation
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
