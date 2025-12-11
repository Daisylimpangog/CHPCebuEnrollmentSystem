<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'if0_40651937');
define('DB_PASS', '9nnXfc3E7ndGd');
define('DB_NAME', 'if0_40651937_enrollmentdb');
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8");
session_start();
ini_set('display_errors', 0);
error_reporting(E_ALL);
define('BASE_URL', 'https://chpcebuenrollmentform.infinityfree.me/');
function is_logged_in() {
    return isset($_SESSION['user_id']);
}
function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function require_login() {
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit();
    }
}
function require_admin() {
    if (!is_admin()) {
        header('Location: ' . BASE_URL . 'index.php');
        exit();
    }
}
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function validate_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
