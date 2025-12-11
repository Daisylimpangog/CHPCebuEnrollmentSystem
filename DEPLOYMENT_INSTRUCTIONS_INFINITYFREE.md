# 🚀 DEPLOYMENT SETUP FOR chpcebuenrollmentform.infinityfree.me

## ✅ YOUR CREDENTIALS ARE CONFIGURED

**Domain:** chpcebuenrollmentform.infinityfree.me  
**Database:** if0_40651937_enrollmentdb  
**Username:** if0_40651937  
**Status:** READY TO DEPLOY ✅

---

## 🎯 NEXT STEPS (SUPER EASY!)

### STEP 1: Update config.php on Your Live Server
1. Go to **InfinityFree File Manager**
2. Open **`config.php`** (right-click → Edit)
3. **Replace the entire content** with this:

```php
<?php
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

// PRODUCTION MODE
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Site URL
define('BASE_URL', 'https://chpcebuenrollmentform.infinityfree.me/');

// Session security
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);

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
```

4. **Save** (Ctrl+S)
5. Close notepad

---

### STEP 2: Import Database Schema
1. In **InfinityFree Control Panel**, click **"phpMyAdmin"**
2. Select database: **`if0_40651937_enrollmentdb`**
3. Click **"Import"** tab
4. Click **"Browse"** and select **`db_setup.sql`** file
5. Click **"Go"**
6. ✅ **All 9 tables created!**

---

### STEP 3: Create Admin Account
1. Visit: **`https://chpcebuenrollmentform.infinityfree.me/setup_admin.php`**
2. Enter:
   - **Admin Email:** your@email.com (use your email)
   - **Admin Password:** Create a strong password (SAVE IT!)
   - **Confirm Password:** Type it again
3. Click **"Create Admin Account"**
4. ✅ **Admin created!**

---

### STEP 4: Delete setup_admin.php (SECURITY!)
1. Go to **File Manager**
2. Find **`setup_admin.php`**
3. Right-click → **"Delete"**
4. Confirm delete
5. ✅ **Security complete!**

---

### STEP 5: TEST YOUR SITE
1. Visit: **`https://chpcebuenrollmentform.infinityfree.me`**
2. You should see the **home page** with green theme ✅
3. Click **"Login"**
4. Enter your admin credentials
5. You should see the **admin dashboard** ✅

---

## 🎉 YOUR SITE IS NOW LIVE!

### Share this link:
```
https://chpcebuenrollmentform.infinityfree.me
```

### Users can:
- Register new account
- Login
- Browse courses
- Enroll in courses
- Reset password

### You can (Admin):
- Login to: `https://chpcebuenrollmentform.infinityfree.me/admin/`
- Approve applications
- Manage courses
- View enrollments

---

## 🆘 TROUBLESHOOTING

**"Database Connection Error"**
- Make sure config.php has the exact credentials:
  - DB_USER: `if0_40651937`
  - DB_PASS: `9nnXfc3E7ndGd`
  - DB_NAME: `if0_40651937_enrollmentdb`

**"Blank Page"**
- Wait 5 minutes for website to activate
- Clear browser cache (Ctrl+Shift+Delete)
- Check error logs

**"Can't Create Admin Account"**
- Make sure db_setup.sql was imported
- Check tables exist in phpMyAdmin
- Verify config.php is correct

**"Can't Login"**
- Make sure admin account was created
- Check database was imported successfully
- Verify config.php credentials

---

## 📋 QUICK CHECKLIST

```
☑ Files uploaded to /htdocs/
☑ Database created (if0_40651937_enrollmentdb)
☑ config.php updated with credentials ← DO THIS FIRST!
☑ db_setup.sql imported via phpMyAdmin ← DO THIS SECOND!
☑ Admin account created via setup_admin.php ← DO THIS THIRD!
☑ setup_admin.php deleted for security ← DO THIS LAST!
```

---

**REMEMBER:** Do the steps in order! Each step depends on the previous one.

**Your domain:** chpcebuenrollmentform.infinityfree.me ✅
**Status:** READY TO DEPLOY! 🚀
