# Enrollment Application System

A complete enrollment management system for healthcare and caregiving training programs with admin and enrollee features.

## Features

### For Enrollees:
- **User Authentication**: Login/Register with secure password handling
- **Course Browsing**: View available courses with detailed pricing breakdown
  - Healthcare Services NCII
  - Care Giving NCII
- **Enrollment Process**:
  - Browse and select courses
  - Complete application form with comprehensive personal/academic information
  - View enrollment status
  - Process payments with multiple payment methods
- **Profile Management**: View profile and track enrollments
- **Password Reset**: Request password changes (admin approval required)
- **Promotions**: View active promotional discounts

### For Admins:
- **Dashboard**: Overview with key statistics
- **Application Management**: 
  - View all applications
  - Review detailed application information
  - Approve, hold, or reject enrollments
  - Download/print applications
- **Enrollment Tracking**: Monitor enrollments by course
- **Promotion Management**: Add and manage promotional campaigns with images
- **Password Reset Approval**: Review and approve/reject password reset requests
- **Analytics**: Track website visits and popular pages

## Installation

### 1. Database Setup
```bash
# Import the database schema
- Open phpMyAdmin
- Create a new database: enrollment_system
- Import db_setup.sql file
```

### 2. Configuration
Update `config.php` with your database credentials:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'enrollment_system');
```

### 3. File Structure
```
Enrollment Form/
├── index.php
├── login.php (Login/Register)
├── forgot_password.php
├── logout.php
├── config.php (Database & session configuration)
├── helpers.php (Helper functions)
├── db_setup.sql (Database schema)
├── enrollee/
│   ├── dashboard.php
│   ├── courses.php
│   ├── enroll.php
│   ├── application_form.php
│   ├── payment.php
│   ├── my_enrollments.php
│   ├── view_enrollment.php
│   └── profile.php
└── admin/
    ├── dashboard.php
    ├── applications.php
    ├── view_application.php
    ├── enrollments.php
    ├── promotions.php
    ├── password_resets.php
    └── visits.php
```

## Course Information

### Healthcare Services NCII
- **Registration Fee**: ₱2,500
- **Tuition Fee**: ₱34,860
- **Miscellaneous Fees**: ₱12,320
- **Total**: ₱49,680
- **With 5% Promo**: ₱47,146

### Care Giving NCII
- **Registration Fee**: ₱2,500
- **Tuition Fee**: ₱30,570
- **Miscellaneous Fees**: ₱10,130
- **Total**: ₱43,200
- **With 5% Promo**: ₱41,040

## User Roles

### Enrollee
- Username: Use email during registration
- Can browse courses and enroll
- Submit applications and make payments
- Track enrollment status
- Request password reset

### Admin
- Login with admin account (must be created via database)
- Review and manage applications
- Approve or reject enrollments
- Manage promotions
- Track website analytics

## Payment Methods Supported
- Outright Cash (Full Payment)
- Installment Payment Plans

## Security Features
- CSRF token protection
- Password hashing with bcrypt
- Session-based authentication
- Input sanitization
- SQL injection prevention

## Database Schema
- **users**: User accounts (enrollees and admins)
- **courses**: Available courses
- **course_fees**: Detailed fee breakdown per course
- **enrollments**: Enrollment records
- **enrollment_applications**: Detailed application forms
- **payments**: Payment transaction records
- **promotions**: Marketing promotions
- **password_reset_requests**: Password reset requests
- **website_visits**: Analytics tracking

## Usage

### For New Enrollees:
1. Go to login page
2. Click "Register here"
3. Fill in registration form
4. View available courses
5. Click "Enroll Now" on desired course
6. Complete application form
7. Process payment
8. Submit and wait for admin approval

### For Admins:
1. Login with admin credentials
2. Navigate to Dashboard
3. Review pending applications
4. Click "View" to see detailed application
5. Update status (Approve/Hold/Reject)
6. Manage promotions from Promotions menu
7. Review password reset requests
8. Check website analytics

## Support
For issues or questions, contact the system administrator.

## License
This system is designed for Healthcare Professionals training center.
