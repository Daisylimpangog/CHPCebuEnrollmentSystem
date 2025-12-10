# Enrollment System - Online Deployment Guide

## Prerequisites
- A web hosting account with PHP 7.2+ and MySQL 5.7+
- FTP/SFTP access or cPanel file manager
- Domain name (optional, can use hosting provider's subdomain)

## Step 1: Choose a Web Hosting Provider

### Recommended Affordable Options:
1. **Bluehost** (bluehost.com) - $2.95/month
   - Includes free domain first year
   - Unlimited MySQL databases
   - PHP 8.0+ support
   - cPanel access

2. **Hostinger** (hostinger.com) - $2.99/month
   - Fast performance
   - Unlimited databases
   - Free SSL certificate
   - Easy WordPress/PHP setup

3. **SiteGround** (siteground.com) - $2.99/month
   - Excellent support
   - Daily backups
   - Free SSL
   - cPanel included

4. **HostGator** (hostgator.com) - $2.75/month
   - Unlimited storage
   - Multiple PHP versions
   - MySQL databases included

## Step 2: Upload Files to Hosting

### Using cPanel File Manager (Easiest):
1. Log in to your hosting cPanel
2. Open "File Manager"
3. Navigate to `public_html` folder
4. Upload all files from your Enrollment Form directory:
   - config.php
   - helpers.php
   - login.php
   - forgot_password.php
   - application_form.php
   - index.php
   - enrollee/ (entire folder)
   - admin/ (entire folder)
   - All other PHP files

### Using FTP (Alternative):
1. Download FileZilla (filezilla-project.org)
2. Get FTP credentials from hosting provider
3. Connect and upload files to `/public_html/` directory

## Step 3: Update Database Configuration

1. Edit `config.php` and update database credentials:
```php
define('DB_HOST', 'your-hosting-mysql-server'); // Usually 'localhost'
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'your_database_name');
```

2. Get credentials from hosting cPanel MySQL section

## Step 4: Create Database

### Option A: Using cPanel MySQL Manager
1. Log in to cPanel
2. Go to "MySQL Databases"
3. Create new database (e.g., `enrollment_system`)
4. Create new user (e.g., `enroll_user`)
5. Assign user to database with all privileges

### Option B: Using phpMyAdmin
1. Open phpMyAdmin from cPanel
2. Create new database
3. Import `db_setup.sql`:
   - Click database name
   - Click "Import" tab
   - Select `db_setup.sql` file
   - Click "Go"

## Step 5: Import Database Schema

1. In cPanel, open **phpMyAdmin**
2. Select your new database
3. Click **Import** tab
4. Upload `db_setup.sql` file
5. Click **Go** to execute

Database tables will be created automatically.

## Step 6: Set File Permissions

Some hosting providers require specific permissions:

1. In cPanel File Manager, set permissions:
   - Folders: 755
   - PHP files: 644
   - config.php: 644

Right-click file → Change Permissions

## Step 7: Test Installation

1. Visit: `yourdomain.com/login.php`
2. Test admin login: `admin@chp.com` / `Admin@123`
3. Test user registration
4. Test password reset flow

## Troubleshooting

### "Cannot connect to database" error:
- Verify database credentials in `config.php`
- Check MySQL server address (may not be 'localhost')
- Verify database user has correct privileges

### "Permission denied" errors:
- Ensure file permissions are set to 644 for PHP files
- Set folder permissions to 755

### "Session not working" error:
- Some hosts have session issues with custom paths
- Edit `config.php` session configuration if needed

### SSL Certificate (HTTPS)
- Enable free SSL in cPanel under "AutoSSL"
- Update `config.php` if needed for HTTPS redirect

## Security Checklist Before Going Live

- [ ] Change admin password from `Admin@123` to something secure
- [ ] Review and update `config.php` database credentials
- [ ] Enable SSL/HTTPS
- [ ] Set proper file permissions (644 for files, 755 for directories)
- [ ] Consider adding .htaccess protection for sensitive folders
- [ ] Backup database regularly
- [ ] Monitor website for errors in logs
- [ ] Update PHP/MySQL versions when hosting supports it

## Backup Strategy

1. Regular database backups through cPanel
2. Download source files periodically
3. Set up automated backups (most hosts provide this)

## Domain Setup

If using a custom domain:
1. Update domain nameservers to hosting provider's
2. Wait 24-48 hours for propagation
3. Verify domain points to hosting server

## Support Resources

- Your hosting provider's support team (24/7 chat usually available)
- PHP documentation: php.net
- MySQL documentation: dev.mysql.com
- cPanel documentation: docs.cpanel.net

---

**Estimated Time to Deploy:** 30-45 minutes
**Cost:** $2.99-$5.99/month typically
