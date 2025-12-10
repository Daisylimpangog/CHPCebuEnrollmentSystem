# GitHub Deployment Guide

This guide will help you push your enrollment system to GitHub and deploy it to a live server.

## Part 1: Push to GitHub

### Step 1: Create a GitHub Repository

1. Go to [github.com](https://github.com)
2. Click **+** → **New repository**
3. Repository name: `enrollment-system`
4. Description: `Web-based enrollment management system for educational institutions`
5. Choose **Public** (for free hosting) or **Private**
6. **DO NOT** initialize with README (we already have one)
7. Click **Create repository**

### Step 2: Initialize Git Locally

Open PowerShell in your project folder and run:

```powershell
cd "C:\xampp\htdocs\Enrollment Form"

# Initialize git
git init

# Add all files
git add .

# Create initial commit
git commit -m "Initial commit: Enrollment system v1.0"

# Add remote repository (replace YOUR-USERNAME and YOUR-REPO)
git remote add origin https://github.com/YOUR-USERNAME/enrollment-system.git

# Push to GitHub
git branch -M main
git push -u origin main
```

**Example:**
```powershell
git remote add origin https://github.com/johnsmith/enrollment-system.git
git push -u origin main
```

### Step 3: Verify on GitHub

1. Go to your GitHub repository
2. Verify all files are uploaded
3. Check that `.gitignore` is protecting sensitive files

## Part 2: GitHub Configuration Best Practices

### Create a `.env.example` File

Create a template for environment variables (without sensitive data):

```php
# Database Configuration Example
# Copy to .env file and fill with your credentials

DB_HOST=localhost
DB_USER=your_database_user
DB_PASS=your_database_password
DB_NAME=enrollment_system
```

Save as: `.env.example` (add to GitHub)

### Protect Sensitive Information

Files that should NOT be in GitHub (protected by `.gitignore`):
- `config.php` (contains DB credentials)
- `.env` (local environment variables)
- Any local backup files

## Part 3: Deploy to Live Server

### Option A: GitHub → Hosting (Using cPanel Git)

**Prerequisites:**
- Hosting account with cPanel
- SSH access enabled

**Steps:**

1. **Access SSH Terminal in cPanel:**
   - cPanel → Terminal (or SSH)
   - Or use PuTTY/MobaXterm

2. **Clone Your Repository:**
   ```bash
   cd /public_html
   git clone https://github.com/YOUR-USERNAME/enrollment-system.git .
   ```

3. **Create Local Config:**
   ```bash
   cp config.example.php config.php
   nano config.php  # Edit with your hosting credentials
   ```

4. **Set Permissions:**
   ```bash
   find . -type f -name "*.php" -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   ```

### Option B: GitHub → Hosting (Manual Download)

1. Go to your GitHub repository
2. Click **Code** → **Download ZIP**
3. Extract and upload to hosting via cPanel File Manager or FTP
4. Create and configure `config.php` on the server

### Option C: Automated Deployment (GitHub Actions)

Create `.github/workflows/deploy.yml`:

```yaml
name: Deploy to Hosting

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Deploy via FTP
        uses: SamKirkland/FTP-Deploy-Action@v4.3.4
        with:
          server: your-ftp-server.com
          username: ${{ secrets.FTP_USERNAME }}
          password: ${{ secrets.FTP_PASSWORD }}
          local-dir: ./
          server-dir: ./public_html/
```

Setup GitHub Secrets:
1. Go to Settings → Secrets → New repository secret
2. Add `FTP_USERNAME` and `FTP_PASSWORD`

## Part 4: Updating Code on Live Server

### Method 1: Pull Changes from GitHub

```bash
ssh user@your-domain.com
cd /public_html
git pull origin main
```

### Method 2: Re-upload via ZIP

1. Make changes locally
2. Commit to GitHub: `git push`
3. Download ZIP from GitHub
4. Upload via FTP/cPanel File Manager

## Part 5: Backups and Version Control

### Create Release Versions

```bash
# Tag a version
git tag -a v1.0.0 -m "Release version 1.0.0"
git push origin v1.0.0

# Create another release
git tag -a v1.1.0 -m "Release version 1.1.0"
git push origin v1.1.0
```

View releases on GitHub: **Releases** tab

### Database Backups

Export from hosting cPanel:
1. cPanel → phpMyAdmin
2. Select database → Export
3. Download SQL file
4. Store locally and in GitHub Issues (as attachment, if needed)

## Part 6: Collaboration

### For Team Members

1. Clone repository: `git clone https://github.com/YOUR-USERNAME/enrollment-system.git`
2. Create branch: `git checkout -b feature/new-feature`
3. Make changes and commit: `git commit -am "Add new feature"`
4. Push branch: `git push origin feature/new-feature`
5. Create Pull Request on GitHub
6. Code review and merge

### Branch Strategy

```
main (production)
  ↑
develop (staging)
  ↑
feature/feature-name (development)
```

## GitHub Repository Structure

After pushing, your GitHub repo should look like:

```
enrollment-system/
├── .github/
│   └── workflows/
│       └── php-quality.yml
├── .gitignore
├── README.md
├── DEPLOYMENT_GUIDE.md
├── QUICK_DEPLOY_CHECKLIST.txt
├── config.php
├── helpers.php
├── login.php
├── forgot_password.php
├── application_form.php
├── index.php
├── db_setup.sql
├── enrollee/
├── admin/
└── LICENSE
```

## Security Checklist for GitHub

✅ **Before Making Public:**
- [ ] `.gitignore` includes `config.php` with real credentials
- [ ] `.gitignore` includes `.env` files
- [ ] No API keys or passwords in commits
- [ ] No sensitive data in commit messages
- [ ] Review git history: `git log --oneline`

✅ **For Deployment:**
- [ ] Create `.env.example` with template variables
- [ ] Document in README how to configure
- [ ] Add deployment instructions in DEPLOYMENT_GUIDE.md
- [ ] Set up GitHub Secrets for CI/CD

## Useful GitHub Commands

```bash
# View remote URL
git remote -v

# Change remote URL
git remote set-url origin https://github.com/NEW-USERNAME/enrollment-system.git

# View commit history
git log --oneline --graph

# Undo last commit (before push)
git reset --soft HEAD~1

# View current branch
git branch

# Sync with remote
git fetch origin
git merge origin/main

# Delete local branch
git branch -d feature-name

# Delete remote branch
git push origin --delete feature-name
```

## Troubleshooting GitHub

### "Permission denied (publickey)"
**Solution:** Generate SSH key
```bash
ssh-keygen -t rsa -b 4096 -C "your-email@example.com"
# Add public key to GitHub Settings → SSH keys
```

### "fatal: not a git repository"
**Solution:** Initialize git
```bash
git init
git remote add origin https://github.com/USERNAME/repo.git
```

### "Everything up-to-date" but changes not pushed
**Solution:** Commit changes first
```bash
git add .
git commit -m "Your message"
git push
```

### Large files rejected by GitHub
**Solution:** Use Git LFS or remove from repo
```bash
git rm --cached large-file.zip
git add .gitignore
git commit -m "Remove large file"
```

## Next Steps

1. ✅ Create GitHub account (if not already done)
2. ✅ Create repository on GitHub
3. ✅ Push code locally via PowerShell/Terminal
4. ✅ Verify files on GitHub
5. ✅ Set up live server deployment
6. ✅ Configure database on live server
7. ✅ Update `config.php` for production
8. ✅ Test live website
9. ✅ Change default admin password
10. ✅ Set up automated backups

## Resources

- [GitHub Guides](https://guides.github.com/)
- [Git Cheat Sheet](https://github.github.com/training-kit/downloads/github-git-cheat-sheet.pdf)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [SSH Key Setup](https://docs.github.com/en/authentication/connecting-to-github-with-ssh)

---

**For Production Deployment:** See [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

**For Quick Checklist:** See [QUICK_DEPLOY_CHECKLIST.txt](QUICK_DEPLOY_CHECKLIST.txt)
