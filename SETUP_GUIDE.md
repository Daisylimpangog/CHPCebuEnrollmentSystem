# Enrollment Application System - Setup Guide

## Quick Start

### Prerequisites
- XAMPP (or any Apache/PHP/MySQL stack)
- PHP 7.2 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Database Import

1. Start Apache and MySQL from XAMPP Control Panel
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create new database:
   - Click "New"
   - Database name: `enrollment_system`
   - Collation: `utf8_general_ci`
   - Click "Create"

4. Import SQL file:
   - Select `enrollment_system` database
   - Click "Import" tab
   - Choose `db_setup.sql` file
   - Click "Go"

### Step 2: Test the System

#### Default Test Users:
After importing the database, you can test with:

**Admin Account** (Insert into database):
```sql
INSERT INTO users (email, password, full_name, user_type, status) 
VALUES ('admin@example.com', '$2y$10$...', 'Admin User', 'admin', 'active');
```

Use bcrypt hash generator for password. Default: `admin123`

**Enrollee Test**:
- Register new account on login page
- Use any email and password (min 6 characters)

#### Access URLs:
- **Login Page**: `http://localhost/Enrollment%20Form/login.php`
- **Index**: `http://localhost/Enrollment%20Form/index.php`
- **Enrollee Dashboard** (after login): Auto redirects
- **Admin Dashboard** (after admin login): Auto redirects

### Step 3: Key Features to Test

#### As Enrollee:
1. **Registration**: Click "Register here" on login page
2. **Browse Courses**: Navigate to "View All Courses"
3. **Enroll**: Click "Enroll Now" on any course
4. **Application Form**: Fill and submit application
5. **Payment**: Submit payment information
6. **View Status**: Check "My Enrollments"

#### As Admin:
1. **Dashboard**: See statistics overview
2. **Applications**: Review all applications
3. **View Details**: Click "View" to see full application
4. **Update Status**: Change approval status
5. **Promotions**: Add new promotions with discounts
6. **Password Resets**: Approve/reject reset requests
7. **Analytics**: Check website visit statistics

## File Structure

```
Enrollment Form/
├── ROOT FILES
│   ├── index.php ........................... Main entry point
│   ├── login.php ........................... Login & registration
│   ├── forgot_password.php ................. Password reset request
│   ├── logout.php .......................... Session logout
│   ├── config.php .......................... Database & config settings
│   ├── helpers.php ......................... Helper functions
│   ├── db_setup.sql ........................ Database schema
│   └── README.md ........................... System documentation
│
├── ENROLLEE FOLDER (enrollee/)
│   ├── dashboard.php ....................... Main enrollee dashboard
│   ├── courses.php ......................... View all available courses
│   ├── enroll.php .......................... Create enrollment record
│   ├── application_form.php ............... Detailed application form
│   ├── payment.php ......................... Payment processing
│   ├── my_enrollments.php ................. View my enrollments
│   ├── view_enrollment.php ................ View enrollment details
│   └── profile.php ......................... User profile page
│
└── ADMIN FOLDER (admin/)
    ├── dashboard.php ....................... Admin overview & stats
    ├── applications.php .................... All applications list
    ├── view_application.php ............... Detailed app + approval
    ├── enrollments.php .................... Enrollment analytics
    ├── promotions.php ..................... Manage promotions
    ├── password_resets.php ................ Handle password resets
    └── visits.php .......................... Website analytics
```

## Database Tables

### users
```sql
- id (INT, PK)
- email (VARCHAR, UNIQUE)
- password (VARCHAR, hashed)
- full_name (VARCHAR)
- user_type (ENUM: 'enrollee', 'admin')
- status (ENUM: 'active', 'inactive', 'pending')
- created_at, updated_at (TIMESTAMPS)
```

### courses
```sql
- id (INT, PK)
- name (VARCHAR)
- description (TEXT)
- picture_path (VARCHAR)
- registration_fee (DECIMAL)
- tuition_fee (DECIMAL)
- promo_percentage (INT, default 5)
- created_at (TIMESTAMP)
```

### enrollments
```sql
- id (INT, PK)
- user_id (INT, FK)
- course_id (INT, FK)
- status (ENUM: 'pending', 'approved', 'rejected', 'on_hold')
- total_amount (DECIMAL)
- paid_amount (DECIMAL)
- application_date (TIMESTAMP)
```

### enrollment_applications
```sql
- id (INT, PK)
- enrollment_id (INT, FK)
- first_name, middle_name, family_name, nickname (VARCHAR)
- age, date_of_birth, place_of_birth (INT/DATE)
- sex, marital_status (ENUM)
- contact_number, email_address (VARCHAR)
- occupation, company (VARCHAR)
- educational_attainment (VARCHAR)
- [... and 15+ more fields for complete profile ...]
```

### payments
```sql
- id (INT, PK)
- enrollment_id (INT, FK)
- amount (DECIMAL)
- payment_method (VARCHAR)
- payment_date (TIMESTAMP)
- status (ENUM: 'pending', 'completed', 'failed')
```

## Course Fee Breakdown

### Healthcare Services NCII
| Item | Amount |
|------|--------|
| Registration | ₱2,500 |
| Tuition | ₱34,860 |
| ID | ₱170 |
| 2 Sets Scrub Suit | ₱1,760 |
| 2 Sets Polo Shirt | ₱900 |
| Basic Life Support BLS | ₱2,100 |
| OJT Fee | ₱5,000 |
| Graduation Fee | ₱1,500 |
| TOR & Certificate | ₱1,800 |
| **Total** | **₱50,590** |
| **With 5% Promo** | **₱48,061** |

### Care Giving NCII
| Item | Amount |
|------|--------|
| Registration | ₱2,500 |
| Tuition | ₱30,570 |
| ID | ₱170 |
| 2 Sets Scrub Suit | ₱1,760 |
| 2 Sets Polo Shirt | ₱900 |
| Basic Life Support | ₱2,100 |
| OJT Fee | ₱5,000 |
| Graduation Fee | ₱1,500 |
| TOR & Certificate | ₱1,800 |
| **Total** | **₱46,300** |
| **With 5% Promo** | **₱43,985** |

## Troubleshooting

### Database Connection Error
- Check XAMPP MySQL is running
- Verify `config.php` database credentials
- Ensure `enrollment_system` database exists

### Can't Access Pages
- Ensure index.php exists in root folder
- Check PHP is enabled in XAMPP
- Verify file permissions

### Login Issues
- Clear browser cache/cookies
- Check user exists in database
- Verify password is correct
- Ensure user status is 'active'

### Missing Images
- Create `admin/uploads/promotions/` folder
- Set proper permissions (777)
- Upload promotion images through admin panel

## Security Notes

✓ CSRF token protection on all forms
✓ Password hashing with bcrypt
✓ SQL injection prevention with real_escape_string
✓ Input sanitization on all user inputs
✓ Session-based authentication
✓ Role-based access control

## Next Steps

1. **Customize**: Modify colors and branding in CSS
2. **Add Logo**: Update branding section in login.php
3. **Email Integration**: Add email notifications for:
   - Registration confirmation
   - Application status updates
   - Payment confirmations
4. **Payment Gateway**: Integrate actual payment processor (Paymongo, PayPal, etc.)
5. **Export Features**: Add PDF export for applications and receipts

## Support

For technical support, refer to the code comments or README.md file included in the system.

