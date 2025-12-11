# 🚀 LIVE DEPLOYMENT GUIDE
## CHPC Cebu Enrollment System - Production Deployment

**Last Updated:** December 11, 2025  
**Repository:** https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem  
**Current Status:** ✅ Ready for Live Deployment

---

## PHASE 1: PRE-DEPLOYMENT CHECKLIST

### Requirements
- [ ] Web hosting with PHP 7.2+ support
- [ ] MySQL 5.7+ database server
- [ ] FTP/SSH access to server
- [ ] Domain name (or subdomain)
- [ ] SSL certificate (HTTPS recommended)
- [ ] XAMPP/Local testing environment (for final verification)

### Supported Hosting Providers
- **Recommended:** InMotion Hosting, SiteGround, Bluehost, HostGator
- **Budget Options:** Namecheap Hosting, DreamHost, A2 Hosting
- **Enterprise:** AWS, Google Cloud, Microsoft Azure

---

## PHASE 2: LOCAL VERIFICATION (Do This First!)

### Step 1: Test Locally with XAMPP
```bash
# Start Apache and MySQL in XAMPP Control Panel
# Navigate to: http://localhost/Enrollment%20Form/
# Verify all modules work:
✓ Login page loads
✓ Registration works
✓ Admin dashboard accessible
✓ Database operations complete
✓ Password reset workflow functions
```

### Step 2: Database Backup
```bash
# Export local database before uploading
1. Open phpMyAdmin: http://localhost/phpmyadmin/
2. Select 'enrollment_system' database
3. Click "Export" → Select "SQL" format
4. Save as: enrollment_system_backup_[DATE].sql
```

---

## PHASE 3: PREPARE FOR PRODUCTION

### Step 1: Update Configuration File
Edit `config.php` with your live server details:

```php
<?php
// LIVE SERVER DATABASE CONFIGURATION
define('DB_HOST', 'your_server_host');      // e.g., db.example.com or localhost
define('DB_USER', 'your_db_username');      // Your database user
define('DB_PASS', 'your_db_password');      // Your database password (change immediately)
define('DB_NAME', 'enrollment_system');     // Database name

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

// PRODUCTION MODE - Disable debug info
ini_set('display_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/path/to/error.log');

// IMPORTANT: Update BASE_URL to your live domain
define('BASE_URL', 'https://yourdomain.com/enrollment-system/');
// Or if in subdirectory: https://yourdomain.com/

// Session security settings
ini_set('session.cookie_secure', 1);      // HTTPS only
ini_set('session.cookie_httponly', 1);    // No JavaScript access
ini_set('session.cookie_samesite', 'Strict');
```

**⚠️ CRITICAL SECURITY NOTES:**
- Change default database password immediately
- Use HTTPS (SSL/TLS) for production
- Set strong, unique database credentials
- Store config.php with restricted file permissions (644)
- Never commit actual config.php to GitHub

### Step 2: Create Live Database
```sql
-- On your live server, create the database:
CREATE DATABASE enrollment_system;
USE enrollment_system;

-- Then import the schema (see Phase 4)
```

---

## PHASE 4: UPLOAD TO LIVE SERVER

### Method A: Using FTP (Easiest for Beginners)

**Tools Needed:** FileZilla (free), Cyberduck, or WinSCP

1. **Download an FTP Client**
   - FileZilla: https://filezilla-project.org/
   - Setup your FTP credentials from hosting provider

2. **Connect to Server**
   ```
   Host: ftp.yourdomain.com (or your_server_ip)
   Username: Your FTP username
   Password: Your FTP password
   Port: 21 (or 22 for SFTP)
   ```

3. **Upload Files**
   - Drag & drop all project files to public_html/ or /home/yourdomain/
   - Exclude: .git/, .github/, DOCUMENTATION_* files (optional)
   - Include: All .php files, config.php, helpers.php, admin/, enrollee/

4. **Verify File Permissions**
   ```
   - PHP files: 644
   - Directories: 755
   - config.php: 600 (most restrictive)
   - uploads/ directory: 777 (if applicable)
   ```

### Method B: Using Git (Recommended for Developers)

**On your hosting server (via SSH):**

```bash
# Navigate to your public_html directory
cd /home/yourdomain/public_html

# Clone the repository
git clone https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem.git .

# Or clone into a subdirectory
git clone https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem.git enrollment-system

# Update configuration
nano config.php  # Edit with live server details

# Set proper permissions
chmod 644 config.php
chmod 755 -R admin/ enrollee/
chmod 777 uploads/  # If you have uploads directory
```

### Method C: Using cPanel File Manager (Simple)

1. Log in to cPanel
2. Open File Manager
3. Navigate to public_html
4. Click "Upload" → Select all files from local system
5. Or use "Extract" if uploading a ZIP file

---

## PHASE 5: SET UP LIVE DATABASE

### Step 1: Create Database via cPanel/Hosting Panel
- Go to MySQL Databases
- Create new database: `enrollment_system`
- Create database user with password
- Assign user to database with ALL privileges

### Step 2: Import Database Schema
**Option A: phpMyAdmin on Live Server**
```
1. Open phpMyAdmin: yourdomain.com/phpmyadmin/
2. Click "Import"
3. Select db_setup.sql from your computer
4. Click "Go"
5. Tables created automatically
```

**Option B: Command Line (SSH)**
```bash
mysql -h localhost -u username -p enrollment_system < db_setup.sql
# Enter password when prompted
```

### Step 3: Verify Tables Created
```sql
USE enrollment_system;
SHOW TABLES;
-- Should show: 
-- courses, enrollments, password_reset_requests, 
-- users, visits, enrollee_profile, course_promotions
```

---

## PHASE 6: FINAL CONFIGURATION

### Step 1: Create Admin Account
Navigate to: `https://yourdomain.com/setup_admin.php`

```
1. Enter Admin Email: your.admin@example.com
2. Enter Admin Password: [Create strong password: A1!bC2@dE3#fG4$]
3. Confirm Password
4. Click "Create Admin Account"
5. Delete or rename setup_admin.php after creation
```

**Security:** Delete setup_admin.php immediately after creating admin:
```bash
# Via FTP or File Manager: Delete setup_admin.php
# Or rename it: setup_admin.php.bak
```

### Step 2: Test All Features
Navigate through each module:
```
✓ https://yourdomain.com/ - Home page loads
✓ https://yourdomain.com/login.php - Login form works
✓ https://yourdomain.com/forgot_password.php - Password reset works
✓ https://yourdomain.com/admin/dashboard.php - Admin dashboard
✓ https://yourdomain.com/enrollee/dashboard.php - Enrollee dashboard
```

### Step 3: Verify Email Configuration (Optional but Recommended)
For password reset notifications, configure email:

**Option A: Use PHP mail() function**
- Most hosting providers support this by default
- Test password reset workflow

**Option B: Use SMTP (Gmail, SendGrid, etc.)**
- Add to config.php:
```php
// Email configuration for password resets
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
define('SMTP_PORT', 587);
```

---

## PHASE 7: SSL/HTTPS SETUP

### Enable HTTPS
Most hosting providers offer free SSL via Let's Encrypt:

1. **Via cPanel:**
   - AutoSSL → Install SSL certificate
   - Auto-renews annually

2. **Update config.php:**
```php
// Force HTTPS
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}

// Update BASE_URL to HTTPS
define('BASE_URL', 'https://yourdomain.com/');
```

---

## PHASE 8: SECURITY HARDENING

### Update Security Headers
Add to top of index.php and login.php:
```php
<?php
// Security Headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

// Rest of code...
?>
```

### Protect Sensitive Files
Create `.htaccess` in root:
```apache
# Block direct access to config files
<FilesMatch "\.env|config\.php|db_setup\.sql">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Force HTTPS
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>
```

---

## PHASE 9: MONITORING & MAINTENANCE

### Regular Backups
- **Database:** Export monthly via phpMyAdmin
- **Files:** Download via FTP weekly
- **Hosting Provider:** Check if automatic backups enabled

### Monitor Performance
- Check error logs: `/logs/error.log`
- Monitor database size
- Review user access logs
- Set up email alerts for errors

### Update & Patches
- Keep PHP version current
- Update MySQL version regularly
- Check GitHub repo for security updates

---

## PHASE 10: LAUNCH CHECKLIST

Before going live, verify:

```
PRE-LAUNCH VERIFICATION
□ Domain points to hosting server
□ SSL/HTTPS working (lock icon visible)
□ Admin account created (setup_admin.php deleted)
□ All PHP files uploaded correctly
□ Database imported and tables created
□ config.php updated with live credentials
□ BASE_URL updated to live domain
□ Login page accessible and functional
□ Admin dashboard works
□ Password reset workflow tested
□ User registration tested
□ Course enrollment tested
□ All green theme styling displays correctly
□ Forms submit without errors
□ Database records save properly
□ Backups created (file + database)
```

---

## TROUBLESHOOTING

### "Database Connection Failed"
```
✓ Check config.php has correct credentials
✓ Verify database user exists and has permissions
✓ Check database host (might not be 'localhost' on shared hosting)
✓ Verify MySQL service is running
```

### "File Not Found" or "404 Error"
```
✓ Check all .php files uploaded
✓ Verify admin/ and enrollee/ directories exist
✓ Check BASE_URL in config.php matches domain
✓ Verify .htaccess isn't blocking access (if using rewrites)
```

### "Permission Denied"
```
✓ Set directories to 755
✓ Set .php files to 644
✓ Set config.php to 600 (most restrictive)
✓ Contact hosting provider if still issues
```

### "Blank Page / White Screen"
```
✓ Check error logs: tail -f /logs/error.log
✓ Enable error reporting in config.php
✓ Check PHP version compatibility (7.2+)
✓ Verify all required PHP extensions installed
```

---

## SUPPORT & DOCUMENTATION

### Quick Links
- **GitHub Repository:** https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem
- **Documentation Index:** See DOCUMENTATION_INDEX.md
- **Project Structure:** See PROJECT_STRUCTURE.md
- **Setup Complete Guide:** See ✅_SETUP_COMPLETE_SUMMARY.md

### For Issues
1. Check error logs first
2. Review documentation
3. Check GitHub issues
4. Contact hosting provider if server-related

---

## NEXT STEPS AFTER LAUNCH

1. **Promote Your Application**
   - Share enrollment link on social media
   - Email link to potential students
   - Post on website/portal

2. **Monitor User Activity**
   - Check admin dashboard
   - Review enrollments
   - Track password resets

3. **Gather Feedback**
   - User surveys
   - Improvement requests
   - Bug reports

4. **Optimize Performance**
   - Analyze access logs
   - Optimize database queries
   - Cache static assets

---

**🎉 CONGRATULATIONS!**
Your enrollment system is now live and ready to serve students!

For any questions or support, refer to the comprehensive documentation included in the repository.
