# 🚀 INFINITYFREE DEPLOYMENT PREPARATION - WINDOWS
# Run this in PowerShell to prepare files for InfinityFree

Write-Host "===========================================" -ForegroundColor Green
Write-Host "InfinityFree Deployment Preparation" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green
Write-Host ""

# Step 1: Create deployment folder
Write-Host "Step 1: Creating deployment package..." -ForegroundColor Yellow
$deployPath = "deployment_package"
if (Test-Path $deployPath) {
    Remove-Item $deployPath -Recurse -Force
}
New-Item -ItemType Directory -Path $deployPath | Out-Null

# Copy PHP files
Write-Host "Copying PHP files..." -ForegroundColor Cyan
Write-Host "Copying PHP files..." -ForegroundColor Cyan
Get-ChildItem -Filter "*.php" | Copy-Item -Destination $deployPath

# Copy directories
Write-Host "Copying directories..." -ForegroundColor Cyan
Copy-Item -Path "admin" -Destination "$deployPath\admin" -Recurse -Force
Copy-Item -Path "enrollee" -Destination "$deployPath\enrollee" -Recurse -Force

# Copy essential files
Write-Host "Copying essential files..." -ForegroundColor Cyan
Copy-Item -Path "db_setup.sql" -Destination "$deployPath\db_setup.sql" -Force

# Create .htaccess for security
Write-Host "Creating .htaccess for security..." -ForegroundColor Cyan
$htaccess = @"
# Block direct access to sensitive files
<FilesMatch "\.env|config\.php|db_setup\.sql">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Force HTTPS (uncomment after SSL is setup)
# <IfModule mod_rewrite.c>
#     RewriteEngine On
#     RewriteCond {HTTPS} off
#     RewriteRule ^(.*)$ https://{HTTP_HOST}{REQUEST_URI} [L,R=301]
# </IfModule>

# Allow .htaccess overrides
<IfModule mod_rewrite.c>
    RewriteEngine On
</IfModule>
"@
Set-Content -Path "$deployPath\.htaccess" -Value $htaccess -Force

# Create deployment instructions
Write-Host "Creating deployment instructions..." -ForegroundColor Cyan
$instructions = @"
# 🚀 INFINITYFREE DEPLOYMENT INSTRUCTIONS

## Files Ready to Upload
All files in this folder are ready to upload to InfinityFree hosting.

## Quick Setup (15 minutes)

### 1. Create InfinityFree Account (FREE)
- Go to: https://www.infinityfree.net
- Sign up (no credit card needed)
- Verify email

### 2. Create Website
- Control Panel > New Website
- Choose free domain: yoursubdomain.infinityfree.net
- Accept terms

### 3. Get FTP Access
- Control Panel > FTP Accounts
- Create FTP account
- Save credentials

### 4. Upload Files
- Download FileZilla: https://filezilla-project.org
- Connect with FTP credentials:
  - Host: ftp.infinityfree.net
  - Username: Your FTP username
  - Password: Your FTP password
  - Port: 21
- Drag all files from this folder to /htdocs/

### 5. Create Database
- Control Panel > MySQL Databases
- Create new database
- Save credentials (host, user, password, name)

### 6. Import Database Schema
- Control Panel > phpMyAdmin
- Select your database
- Click "Import"
- Choose db_setup.sql
- Click "Go"
- All 9 tables created ✅

### 7. Update Configuration
- Edit config.php with database credentials:
  - DB_HOST: localhost
  - DB_USER: Your database user
  - DB_PASS: Your database password
  - DB_NAME: Your database name
  - BASE_URL: https://yoursubdomain.infinityfree.net/

### 8. Create Admin Account
- Visit: https://yoursubdomain.infinityfree.net/setup_admin.php
- Enter admin email: your@email.com
- Create strong password
- Click "Create Admin Account"

### 9. Delete Setup File (IMPORTANT FOR SECURITY!)
- Delete or rename setup_admin.php
- This prevents unauthorized admin creation

### 10. Test Your Site
- Login: https://yoursubdomain.infinityfree.net/login.php
- Admin Dashboard: https://yoursubdomain.infinityfree.net/admin/
- Enrollee Dashboard: https://yoursubdomain.infinityfree.net/enrollee/

## ✅ YOU'RE LIVE!
Your enrollment system is now accessible to everyone!

## Features Available
- Student registration and login
- Course browsing and enrollment
- Admin application approval
- Enrollee profile management
- Password reset workflow
- Admin dashboard with analytics

## Support
For issues, check the documentation in the repository:
https://github.com/Daisylimpangog/CHPCebuEnrollmentSystem
"@
Set-Content -Path "$deployPath\INFINITYFREE_INSTRUCTIONS.txt" -Value $instructions -Force

Write-Host "✅ Deployment package created!" -ForegroundColor Green
Write-Host ""
Write-Host "===========================================" -ForegroundColor Green
Write-Host "Deployment Package Ready" -ForegroundColor Green
Write-Host "===========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Location: $deployPath" -ForegroundColor Cyan
Write-Host ""
Write-Host "Contents:" -ForegroundColor Yellow
Write-Host "  ✓ All PHP files" -ForegroundColor White
Write-Host "  ✓ Database schema" -ForegroundColor White
Write-Host "  ✓ Security configuration" -ForegroundColor White
Write-Host "  ✓ Setup instructions" -ForegroundColor White
Write-Host ""
Write-Host "NEXT STEPS:" -ForegroundColor Yellow
Write-Host ""
Write-Host "1. Go to: https://www.infinityfree.net" -ForegroundColor Cyan
Write-Host "2. Sign up (FREE, no credit card needed)" -ForegroundColor Cyan
Write-Host "3. Create website (get FTP credentials)" -ForegroundColor Cyan
Write-Host "4. Download FileZilla: https://filezilla-project.org" -ForegroundColor Cyan
Write-Host "5. Upload files from deployment package to /htdocs/" -ForegroundColor Cyan
Write-Host "6. Create MySQL database in Control Panel" -ForegroundColor Cyan
Write-Host "7. Import db_setup.sql via phpMyAdmin" -ForegroundColor Cyan
Write-Host "8. Edit config.php with database credentials" -ForegroundColor Cyan
Write-Host "9. Visit setup_admin.php to create admin account" -ForegroundColor Cyan
Write-Host "10. DELETE setup_admin.php for security" -ForegroundColor Cyan
Write-Host ""
Write-Host "Time: ~15 minutes" -ForegroundColor Green
Write-Host "Result: FREE live website!" -ForegroundColor Green
Write-Host ""
Write-Host "===========================================" -ForegroundColor Green
