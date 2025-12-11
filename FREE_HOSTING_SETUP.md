# 🌐 FREE LIVE HOSTING SETUP
## Deploy Your Enrollment System for FREE

**Options for 100% Free Hosting:**

---

## OPTION 1: RENDER.COM (RECOMMENDED - Easiest)
**Best for:** Quick deployment, automatic HTTPS, database included

### Step 1: Create Account
1. Go to: https://render.com
2. Click "Sign up" → Use GitHub account (Daisylimpangog)
3. Authorize Render to access your GitHub

### Step 2: Create New Web Service
1. Dashboard → Click "New +" → Web Service
2. Connect Repository: Select `CHPCebuEnrollmentSystem`
3. Configure:
   - **Name:** chpc-enrollment-system
   - **Environment:** PHP
   - **Build Command:** `composer install` (or leave blank)
   - **Start Command:** Leave blank (uses default)
   - **Region:** Singapore or Asia (closest to Philippines)
   - **Plan:** Free tier

### Step 3: Deploy Database
1. Create PostgreSQL database:
   - Dashboard → "New +" → PostgreSQL
   - Name: `enrollment_db`
   - Plan: Free tier
   
2. Update `config.php`:
```php
<?php
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'enrollment_system');

// Connection details from Render
$conn = new mysqli(
    getenv('DB_HOST'),
    getenv('DB_USER'),
    getenv('DB_PASS'),
    getenv('DB_NAME')
);
?>
```

### Step 4: Add Environment Variables
In Render dashboard:
- Settings → Environment
- Add:
  - `DB_HOST`: From PostgreSQL credentials
  - `DB_USER`: From PostgreSQL credentials
  - `DB_PASS`: From PostgreSQL credentials
  - `DB_NAME`: `enrollment_system`

### Step 5: Deploy
- Click "Deploy"
- Wait 2-3 minutes
- Your site is live at: `https://chpc-enrollment-system.onrender.com`

**✅ FREE FOREVER (with limits)**

---

## OPTION 2: HEROKU (Easy, but limited free tier)
**Best for:** Simple deployment with built-in GitHub integration

### Step 1: Create Account
1. Go to: https://www.heroku.com
2. Sign up with GitHub

### Step 2: Create New App
1. Click "Create new app"
2. Name: `chpc-enrollment-system`
3. Region: Europe (closest free option)

### Step 3: Connect GitHub
1. Settings → Add buildpack → PHP
2. Connect to GitHub repo `CHPCebuEnrollmentSystem`
3. Enable automatic deploys from `master` branch

### Step 4: Add Database
1. Resources → Add-ons → Search "ClearDB MySQL"
2. Select: ClearDB MySQL - Spark (Free)
3. Get connection string from add-on settings

### Step 5: Deploy
- Push to GitHub
- Heroku auto-deploys
- Live at: `https://chpc-enrollment-system.herokuapp.com`

**Note:** Heroku free tier may sleep after 30 mins inactivity

---

## OPTION 3: INFINITYFREE.NET (100% Free, No Card Required)
**Best for:** Completely free, no credit card needed

### Step 1: Create Account
1. Go to: https://www.infinityfree.net
2. Sign up (no credit card needed)
3. Verify email

### Step 2: Create Website
1. Account → "New Website" → "Sign up for free hosting"
2. Choose subdomain: `chpc-enrollment.infinityfree.net`
3. Accept terms

### Step 3: Get FTP Access
1. Control Panel → "FTP Accounts"
2. Create FTP account
3. Note the FTP credentials

### Step 4: Upload Files
1. Download FileZilla: https://filezilla-project.org
2. Connect with FTP credentials:
   - Host: ftp.infinityfree.net
   - Username: Your account
   - Password: Your account password
3. Upload all project files to `/htdocs/` directory

### Step 5: Create Database
1. Control Panel → "Databases"
2. Create MySQL database
3. Note credentials

### Step 6: Update config.php
```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');  // From control panel
define('DB_PASS', 'your_db_pass');  // From control panel
define('DB_NAME', 'your_db_name');  // From control panel

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Production settings
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Update to your InfinityFree domain
define('BASE_URL', 'https://chpc-enrollment.infinityfree.net/');
?>
```

### Step 7: Import Database
1. Control Panel → "MySQL Databases" → "phpMyAdmin"
2. Import `db_setup.sql`
3. All tables created

### Step 8: Go Live
- Navigate to: `https://chpc-enrollment.infinityfree.net`
- Setup admin: `https://chpc-enrollment.infinityfree.net/setup_admin.php`
- **DELETE setup_admin.php** after creating admin!

**✅ COMPLETELY FREE - No hidden costs, no credit card**

---

## OPTION 4: 000WEBHOST.COM (Free with Ads)
**Best for:** Easy setup, good performance

### Step 1: Sign Up
- Go to: https://www.000webhost.com
- Sign up (free option available)

### Step 2: Upload via File Manager
- Control Panel → File Manager
- Upload all project files
- Delete public_html folder contents first

### Step 3: Create Database
- MySQL Databases → Create
- Upload `db_setup.sql`

### Step 4: Configure
- Update `config.php` with credentials
- Delete `setup_admin.php` after admin creation

### Step 5: Access
- Your free domain: `yourusername.000webhostapp.com`

---

## OPTION 5: RAILWAY.APP (Very Easy, GitHub Integration)
**Best for:** Modern deployment with GitHub integration

### Step 1: Connect GitHub
1. Go to: https://railway.app
2. Click "Deploy on Railway"
3. Authorize GitHub access
4. Select `CHPCebuEnrollmentSystem` repo

### Step 2: Deploy
- Railway auto-detects PHP project
- Automatic deployment on GitHub push
- Live URL generated instantly

### Step 3: Add Database
- From Dashboard → Add service → PostgreSQL
- Connect environment variables automatically

### Step 4: Go Live
- Your live URL: `https://yourdomain-random.railway.app`
- Updates automatically on GitHub push

---

## 🏆 RECOMMENDED SETUP

**Best Free Option:** **INFINITYFREE.NET**
- ✅ 100% Free (no credit card needed)
- ✅ No ads
- ✅ 5GB storage
- ✅ 250GB bandwidth
- ✅ MySQL database
- ✅ Easy FTP upload
- ✅ Can run for years free

**Steps (15 minutes total):**
1. Sign up at infinityfree.net (free, no card)
2. Create website → get FTP + domain
3. Upload via FileZilla (5 min)
4. Create MySQL database (2 min)
5. Import db_setup.sql (1 min)
6. Update config.php (2 min)
7. Create admin account (2 min)
8. Delete setup_admin.php (30 sec)
9. **DONE! 🎉 Site is LIVE and accessible to everyone!**

---

## TROUBLESHOOTING FREE HOSTING

**Database connection error?**
- Verify credentials in config.php match control panel
- Check database exists
- Verify user has permissions

**Files not showing?**
- Make sure uploaded to correct directory
- Check file permissions (644 for PHP files)
- Clear browser cache

**Can't access site?**
- Wait 5-10 minutes for propagation
- Check DNS settings
- Try clearing cache or incognito window

---

## COMPARISON TABLE

| Service | Cost | Domain | Database | Ease | Uptime |
|---------|------|--------|----------|------|--------|
| **InfinityFree** | FREE | Free subdomain | MySQL | ⭐⭐⭐⭐ | 99.9% |
| Render | FREE | Free subdomain | PostgreSQL | ⭐⭐⭐⭐⭐ | 99.9% |
| Railway | FREE (limited) | Free subdomain | PostgreSQL | ⭐⭐⭐⭐⭐ | 99.9% |
| Heroku | Paid now | Free subdomain | MySQL/Postgres | ⭐⭐⭐⭐ | 99.5% |
| 000webhost | FREE (ads) | Free subdomain | MySQL | ⭐⭐⭐ | 99% |

---

## NEXT STEPS

1. **Choose:** InfinityFree (recommended for reliability)
2. **Sign Up:** Free, no credit card
3. **Upload:** Via FTP (15 minutes)
4. **Go Live:** Share link with friends/students
5. **Monitor:** Check admin dashboard

Your enrollment system will be accessible 24/7 to anyone with the link!

---

**WHICH OPTION DO YOU WANT TO USE?**
- Reply with the option number (1-5)
- I'll create step-by-step automation scripts for your choice!
