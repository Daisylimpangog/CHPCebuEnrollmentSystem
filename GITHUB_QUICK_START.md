# Quick GitHub Setup Guide

## 5-Minute GitHub Push

### Prerequisites
- GitHub account (free at github.com)
- Git installed on Windows (https://git-scm.com/download/win)

### Step 1: Create GitHub Repository (2 minutes)

1. Go to https://github.com/new
2. Repository name: `enrollment-system`
3. Click **Create repository** (don't add README)
4. Copy your repository URL from the page (looks like: `https://github.com/YOUR-USERNAME/enrollment-system.git`)

### Step 2: Push Code to GitHub (3 minutes)

Open **PowerShell** and run:

```powershell
cd "C:\xampp\htdocs\Enrollment Form"

git init
git add .
git commit -m "Initial commit: Enrollment system v1.0"
git branch -M main
git remote add origin https://github.com/YOUR-USERNAME/enrollment-system.git
git push -u origin main
```

**Replace:** `YOUR-USERNAME` with your GitHub username

### Step 3: Verify (30 seconds)

1. Go to your GitHub repository
2. Refresh the page
3. You should see all your files listed

✅ **Done!** Your code is now on GitHub!

---

## What NOT to Push to GitHub

These files are already protected by `.gitignore`:
- ❌ `config.php` (contains database password)
- ❌ `.env` files
- ❌ Session files
- ❌ Backup files

This is intentional for security! Only `config.example.php` should be in GitHub.

---

## Deploying to Live Server

### For Hosting (Bluehost, Hostinger, etc.)

**Option A: Using cPanel Terminal**
```bash
cd /public_html
git clone https://github.com/YOUR-USERNAME/enrollment-system.git .
cp config.example.php config.php
# Then edit config.php with hosting credentials
```

**Option B: Manual Upload**
1. Go to your GitHub repo
2. Click **Code** → **Download ZIP**
3. Extract and upload to hosting via cPanel File Manager

---

## Common Issues

**"fatal: not a git repository"**
→ Run `git init` first

**"Permission denied"**
→ You may need SSH key (see GITHUB_DEPLOYMENT.md)

**Files not uploading**
→ Check `.gitignore` to make sure files aren't ignored

---

## Next Steps

1. ✅ Push to GitHub (done above)
2. ⬜ Choose hosting provider ($2-6/month)
3. ⬜ Deploy to live server (see DEPLOYMENT_GUIDE.md)
4. ⬜ Update config.php on live server
5. ⬜ Change default admin password

**Full Guide:** See [GITHUB_DEPLOYMENT.md](GITHUB_DEPLOYMENT.md)
