# 🔧 FIX HTTP 500 ERROR

## Error Details:
```
HTTP ERROR 500
chpcebuenrollmentform.infinityfree.me is currently unable to handle this request.
```

This means there's a PHP syntax error in your `config.php` file.

---

## ✅ STEP-BY-STEP FIX:

### STEP 1: Access File Manager
1. Go to **InfinityFree Control Panel**
2. Click **"File Manager"**
3. Navigate to `/htdocs/` folder

### STEP 2: Delete Old config.php
1. Find **`config.php`**
2. Right-click → **"Delete"**
3. Confirm delete

### STEP 3: Create New config.php
1. Right-click in empty space
2. Click **"Create New File"**
3. Name it: **`config.php`**

### STEP 4: Edit the File
1. Right-click **`config.php`** → **"Edit"** (or double-click)
2. Notepad will open (should be empty)
3. **COPY ALL THIS CODE** (see below):

---

## 📋 EXACT CODE TO PASTE:

Copy everything below and paste into your `config.php`:

```php
<?php
// Database Configuration for InfinityFree
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

// Error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Site URL
define('BASE_URL', 'https://chpcebuenrollmentform.infinityfree.me/');

// Helper functions
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
```

---

## ✅ STEP 5: Save File
1. Press **Ctrl+S** (save)
2. Close notepad

---

## ✅ STEP 6: TEST THE SITE
1. Visit: `https://chpcebuenrollmentform.infinityfree.me`
2. You should see the **home page with green theme** ✅

---

## 🆘 Still Getting Error?

### Option 1: Check Error Logs
1. Control Panel → "Error Logs"
2. Look for PHP errors
3. Screenshot and share the error

### Option 2: Upload from Deployment Package
1. Check if `deployment_package/config.php` exists
2. Use FTP to upload it directly to `/htdocs/`
3. Replace the current config.php

### Option 3: Start Fresh
If still not working:
1. Delete ALL files from `/htdocs/`
2. Download `deployment_package/` folder from your GitHub repo
3. Upload everything again using FTP
4. Update `config.php` with the code above

---

## ✅ AFTER FIX - CONTINUE:

Once site loads, you still need to:

### **STEP 2: Import Database** (if not done)
- phpMyAdmin → Import `db_setup_infinityfree.sql`

### **STEP 3: Create Admin Account**
- Visit: `https://chpcebuenrollmentform.infinityfree.me/setup_admin.php`
- Create admin with email & password

### **STEP 4: Delete setup_admin.php**
- File Manager → Delete `setup_admin.php`

---

**Try the fix above and let me know if it works!** 🚀
