#!/bin/bash
# 🚀 INFINITYFREE AUTOMATED DEPLOYMENT SCRIPT
# This script prepares your project for InfinityFree deployment

echo "=========================================="
echo "InfinityFree Deployment Preparation"
echo "=========================================="
echo ""

# Step 1: Create deployment package
echo "Step 1: Creating deployment package..."
mkdir -p deployment_package
cd deployment_package

# Copy all necessary files
echo "Copying project files..."
cp -r ../*.php ./
cp -r ../admin ./
cp -r ../enrollee ./
cp -r ../helpers.php ./
cp -r ../db_setup.sql ./
cp -r ../config.example.php ./

# Create .htaccess for security
echo "Creating .htaccess for security..."
cat > .htaccess << 'EOF'
# Block direct access to sensitive files
<FilesMatch "\.env|config\.php|db_setup\.sql">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Force HTTPS (uncomment after SSL is setup)
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteCond %{HTTPS} off
#     RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
# </IfModule>

# Allow .htaccess overrides
<IfModule mod_rewrite.c>
    RewriteEngine On
</IfModule>
EOF

# Create production config template
echo "Creating production config template..."
cat > config_production.php << 'EOF'
<?php
// ========================================
// PRODUCTION CONFIGURATION FOR INFINITYFREE
// ========================================

// Database Configuration
// Get these from your InfinityFree Control Panel > Databases
define('DB_HOST', 'localhost');
define('DB_USER', 'YOUR_DB_USER_HERE');      // From InfinityFree panel
define('DB_PASS', 'YOUR_DB_PASSWORD_HERE');  // From InfinityFree panel
define('DB_NAME', 'YOUR_DB_NAME_HERE');      // From InfinityFree panel

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

// IMPORTANT: Update to your InfinityFree domain
// Replace 'yoursubdomain' with your actual InfinityFree subdomain
define('BASE_URL', 'https://yoursubdomain.infinityfree.net/');

// Session security settings
ini_set('session.cookie_secure', 1);      // HTTPS only
ini_set('session.cookie_httponly', 1);    // No JavaScript access
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
EOF

echo "✅ Deployment package created!"
echo ""
echo "=========================================="
echo "Next Steps for InfinityFree Deployment:"
echo "=========================================="
echo ""
echo "1. Go to https://www.infinityfree.net"
echo "2. Sign up (FREE, no credit card needed)"
echo "3. Create a new website"
echo "4. Get FTP credentials from Control Panel"
echo ""
echo "5. Download FileZilla: https://filezilla-project.org"
echo "6. Connect with FTP credentials:"
echo "   Host: ftp.infinityfree.net"
echo "   Username: Your account"
echo "   Password: Your account password"
echo ""
echo "7. Upload all files from 'deployment_package' to /htdocs/"
echo ""
echo "8. In Control Panel, create MySQL database"
echo "9. Open phpMyAdmin and import 'db_setup.sql'"
echo ""
echo "10. Edit 'config.php' with database credentials:"
echo "    - DB_USER: Your database username"
echo "    - DB_PASS: Your database password"
echo "    - DB_NAME: Your database name"
echo "    - BASE_URL: https://yoursubdomain.infinityfree.net/"
echo ""
echo "11. Visit setup_admin.php to create admin account"
echo "12. DELETE setup_admin.php for security"
echo ""
echo "13. Your site is LIVE! Share the link!"
echo ""
echo "=========================================="
