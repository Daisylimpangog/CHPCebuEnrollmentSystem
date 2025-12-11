# 🚀 LIVE DEPLOYMENT - QUICK START (5 STEPS)

## Current Status
- ✅ System fully built with 27 PHP files
- ✅ Database schema complete (9 tables)
- ✅ GitHub repository created and pushed
- ✅ All security features implemented
- ✅ Documentation complete

**Repository:** https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem

---

## STEP 1: GET HOSTING
Choose any PHP 7.2+ hosting provider:
- **Recommended:** InMotion Hosting, SiteGround, Bluehost ($3-8/month)
- **Budget:** Namecheap ($2/month), A2 Hosting
- **Enterprise:** AWS, Google Cloud, Azure

---

## STEP 2: UPLOAD FILES (3 OPTIONS)

### Option A: FTP (Easiest)
1. Download FileZilla: https://filezilla-project.org/
2. Use FTP credentials from hosting provider
3. Drag & drop all project files to public_html/

### Option B: Git (Recommended)
```bash
cd /home/yourdomain/public_html
git clone https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem.git .
```

### Option C: cPanel File Manager
1. Log in to cPanel
2. Click File Manager
3. Upload files or extract ZIP

---

## STEP 3: CREATE LIVE DATABASE

1. **Via cPanel:**
   - MySQL Databases → Create new database
   - Name: `enrollment_system`
   - Create user with strong password
   - Assign all privileges

2. **Import Schema:**
   - Open phpMyAdmin on live server
   - Click "Import"
   - Select `db_setup.sql`
   - Click "Go"

**Result:** All 9 tables created automatically

---

## STEP 4: UPDATE CONFIGURATION

Edit `config.php` with your live server details:

```php
define('DB_HOST', 'your_server_host');    // From cPanel
define('DB_USER', 'your_db_user');        // Username you created
define('DB_PASS', 'your_db_password');    // Password you created
define('DB_NAME', 'enrollment_system');   // Database name

// Update this to your live domain
define('BASE_URL', 'https://yourdomain.com/');

// Production settings
ini_set('display_errors', 0);  // Hide errors from public
error_reporting(E_ALL);
```

---

## STEP 5: CREATE ADMIN & LAUNCH

1. **Create Admin Account:**
   - Navigate to: `https://yourdomain.com/setup_admin.php`
   - Enter admin email and strong password
   - Click "Create Admin Account"

2. **Delete setup file immediately:**
   - Delete or rename `setup_admin.php` (security!)

3. **TEST:**
   - Login: `https://yourdomain.com/login.php`
   - Admin: `https://yourdomain.com/admin/`
   - Enrollee: `https://yourdomain.com/enrollee/`

---

## THAT'S IT! 🎉

Your enrollment system is now LIVE!

**Users can now:**
- Register and login
- Browse courses
- Enroll in courses
- Request password resets
- View their enrollments
- Update profiles

**Admin can:**
- Approve applications
- Manage courses
- View enrollments
- Manage users
- Approve password resets

---

## IMPORTANT REMINDERS

✅ **Delete setup_admin.php** - Critical for security!
✅ **Use strong password** for admin account
✅ **Enable HTTPS** (most hosting has free SSL)
✅ **Backup database** regularly
✅ **Monitor error logs** for any issues

---

## TROUBLESHOOTING

**Database Connection Error?**
- Check config.php has correct host, user, password
- Verify database exists in cPanel
- Check user has permissions

**Blank page?**
- Check error logs
- Verify PHP 7.2+ installed
- Check config.php syntax

**Files not uploading?**
- Use FileZilla (more reliable than browser)
- Check FTP permissions
- Try uploading in smaller batches

---

## DOCUMENTATION

For detailed setup, see:
- `LIVE_DEPLOYMENT_GUIDE.md` - Complete guide with all options
- `PROJECT_STRUCTURE.md` - File organization
- `DOCUMENTATION_INDEX.md` - All available docs
- `README.md` - Quick overview

---

**Need help? Check the full LIVE_DEPLOYMENT_GUIDE.md in your project folder!**
