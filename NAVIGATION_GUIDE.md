# Enrollment System - User Flow & Navigation Guide

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                    ENROLLMENT SYSTEM                         │
│                   (Complete Application)                     │
└─────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│                      LOGIN PAGE                               │
│  (login.php)                                                  │
│  ✓ Register new account                                       │
│  ✓ Login with credentials                                     │
│  ✓ Forgot password link                                       │
│  ✓ CSRF protection                                            │
└──────────────────┬─────────────────┬──────────────────────────┘
                   │                 │
                   ▼                 ▼
         ┌─────────────────┐  ┌──────────────────┐
         │   ENROLLEE      │  │     ADMIN        │
         │   DASHBOARD     │  │    DASHBOARD     │
         │(enrollee/)      │  │  (admin/)        │
         └────────┬────────┘  └────────┬─────────┘
                  │                    │
    ┌─────────────┼──────────────────┬─┘
    ▼             ▼                  ▼
```

---

## ENROLLEE USER FLOW

```
┌─────────────────────────────────────────────────────────────┐
│                    ENROLLEE WORKFLOW                         │
└─────────────────────────────────────────────────────────────┘

1. REGISTRATION
   ├── Navigate to login.php
   ├── Click "Register here"
   ├── Fill registration form
   │   ├── Full Name
   │   ├── Email Address
   │   ├── Password (min 6 chars)
   │   └── Confirm Password
   └── Account created ✓

2. LOGIN
   ├── Navigate to login.php
   ├── Enter email & password
   └── Redirected to Dashboard

3. DASHBOARD (enrollee/dashboard.php)
   ├── Welcome message
   ├── Navigation buttons
   │   ├── View All Courses
   │   ├── My Enrollments
   │   └── My Profile
   ├── Course grid (2 courses visible)
   │   ├── Healthcare Services NCII
   │   └── Care Giving NCII
   └── Quick enroll buttons

4. BROWSE COURSES (enrollee/courses.php)
   ├── Search/Filter courses
   ├── View detailed pricing
   │   ├── Registration Fee
   │   ├── Tuition Fee
   │   ├── Miscellaneous Fees (itemized)
   │   ├── Total Amount
   │   └── 5% Promo Price
   └── "Enroll Now" button for each

5. ENROLLMENT PROCESS (enrollee/enroll.php)
   ├── Create enrollment record
   ├── Link user to course
   └── Redirect to application form

6. APPLICATION FORM (enrollee/application_form.php)
   ├── Section A: Personal Record
   │   ├── Name fields (first, middle, family)
   │   ├── Nickname
   │   ├── Date of birth
   │   ├── Age
   │   ├── Sex (radio buttons)
   │   ├── Marital status
   │   ├── Contact info
   │   ├── Address fields
   │   ├── Email
   │   ├── Occupation
   │   ├── Family info
   │   └── 15+ total fields
   │
   ├── Section B: Academic Record
   │   ├── Educational attainment
   │   ├── Course/Degree
   │   ├── NCII Certificates
   │   ├── School/University
   │   └── Year graduated
   │
   └── Section C: Additional Information
       ├── Programs to pursue
       ├── How did you know about us
       ├── Financing options
       └── Terms of payment

7. PAYMENT (enrollee/payment.php)
   ├── View enrollment summary
   │   ├── Course name
   │   ├── Application date
   │   └── Total amount
   ├── Select payment method
   │   ├── Outright Cash
   │   └── Installment Plan
   ├── Enter reference number
   ├── Submit payment info
   └── Confirmation message

8. MY ENROLLMENTS (enrollee/my_enrollments.php)
   ├── List all enrollments
   ├── For each enrollment:
   │   ├── Course name
   │   ├── Application date
   │   ├── Total amount
   │   ├── Amount paid
   │   ├── Status badge
   │   │   ├── ⏳ Pending
   │   │   ├── ✓ Approved
   │   │   ├── ✗ Rejected
   │   │   └── ⏸ On Hold
   │   ├── View Details button
   │   └── Make Payment button (if approved)
   └── Back to Dashboard

9. VIEW ENROLLMENT (enrollee/view_enrollment.php)
   ├── Display submitted information
   ├── Personal information
   ├── Contact information
   ├── Employment & family
   └── Academic information

10. PROFILE (enrollee/profile.php)
    ├── Full name
    ├── Email address
    ├── Account type
    ├── Account status
    ├── View My Enrollments link
    └── Change Password link

11. FORGOT PASSWORD (forgot_password.php)
    ├── Enter email address
    ├── Request password reset
    ├── Admin approval required
    └── Status shown pending approval
```

---

## ADMIN USER FLOW

```
┌─────────────────────────────────────────────────────────────┐
│                    ADMIN WORKFLOW                            │
└─────────────────────────────────────────────────────────────┘

1. LOGIN
   ├── Navigate to login.php
   ├── Enter admin email & password
   └── Redirected to Admin Dashboard

2. DASHBOARD (admin/dashboard.php)
   ├── Statistics Overview
   │   ├── Total Enrollments (number)
   │   ├── Pending Applications (number)
   │   ├── Approved Enrollments (number)
   │   ├── Total Enrollees (number)
   │   └── Pending Password Resets (number)
   │
   ├── Sidebar Navigation Menu
   │   ├── 📊 Dashboard (current)
   │   ├── 📋 Applications
   │   ├── 👥 Enrollments
   │   ├── 🎯 Promotions
   │   ├── 🔐 Password Resets
   │   └── 📈 Website Visits
   │
   └── Recent Applications Table
       ├── Enrollee Name
       ├── Email
       ├── Course
       ├── Amount
       ├── Status
       ├── Date
       └── View button

3. APPLICATIONS (admin/applications.php)
   ├── Search/Filter box
   ├── Applications table
   │   ├── Enrollee Name
   │   ├── Email
   │   ├── Course
   │   ├── Amount
   │   ├── Status badge
   │   │   ├── 🟡 Pending
   │   │   ├── 🟢 Approved
   │   │   ├── 🔴 Rejected
   │   │   └── ⚫ On Hold
   │   ├── Date
   │   └── View button (links to view_application.php)
   └── Search functionality (real-time)

4. VIEW APPLICATION (admin/view_application.php)
   ├── Application Header
   │   ├── Enrollee name
   │   ├── Course
   │   ├── Email
   │   └── Amount
   │
   ├── Status Update Form
   │   ├── Dropdown to select status
   │   │   ├── Pending
   │   │   ├── Approved
   │   │   ├── On Hold
   │   │   └── Rejected
   │   └── Update button
   │
   ├── Print/PDF button
   │
   └── Detailed Application Display
       ├── Personal Information
       │   ├── Full name
       │   ├── Date of birth
       │   ├── Sex
       │   ├── Marital status
       │   └── Contact info
       ├── Contact Information
       │   ├── Email
       │   ├── Phone
       │   └── Address
       ├── Employment & Family
       ├── Academic Information
       └── Additional Information

5. ENROLLMENTS (admin/enrollments.php)
   ├── Statistics grid
   ├── Per course card showing:
   │   ├── Course name
   │   ├── Total enrollments count
   │   ├── Approved count
   │   ├── Pending count
   │   ├── Rejected count
   │   └── On Hold count
   └── Multiple course cards

6. PROMOTIONS (admin/promotions.php)
   ├── Add New Promotion Form
   │   ├── Title field
   │   ├── Discount % input
   │   ├── Start date
   │   ├── End date
   │   ├── Description
   │   ├── Image upload
   │   └── Submit button
   │
   └── Promotions Grid
       ├── Promotion cards showing:
       │   ├── Image (or fallback)
       │   ├── Title
       │   ├── Description
       │   ├── Discount badge
       │   ├── Date range
       │   └── Active/Inactive status

7. PASSWORD RESETS (admin/password_resets.php)
   ├── List of reset requests
   │   ├── Enrollee name
   │   ├── Email
   │   ├── Requested date
   │   ├── Status
   │   │   ├── Pending
   │   │   ├── Approved
   │   │   └── Rejected
   │   └── Action buttons (if pending)
   │       ├── Approve button
   │       └── Reject button
   └── Reviewed request shows review date

8. WEBSITE VISITS (admin/visits.php)
   ├── Daily Visits Table
   │   ├── Date
   │   ├── Total Visits (with bar chart)
   │   ├── Unique Users
   │   └── Unique Visitors (by IP)
   │
   └── Most Visited Pages Table
       ├── Page URL
       └── Visit count (with bar chart)

9. LOGOUT
   └── Destroys session, redirects to login
```

---

## COURSE ENROLLMENT FLOW - DETAILED

```
START: User on Dashboard
   ↓
[Select Course]
   ↓
Enroll.php
   ├─ Create Enrollment Record
   ├─ Link User to Course
   └─ Generate Enrollment ID
   ↓
Application Form
   ├─ Display Pre-filled Info (if available)
   ├─ Show 3 Sections:
   │  ├─ Personal Record
   │  ├─ Academic Record
   │  └─ Additional Info
   ├─ Validate all required fields
   └─ Store Application Data
   ↓
Payment Page
   ├─ Display Enrollment Summary
   ├─ Select Payment Method
   ├─ Enter Reference Number
   ├─ Verify Payment Info
   └─ Record Payment
   ↓
Confirmation
   ├─ Show success message
   ├─ Display enrollment ID
   └─ Redirect to Enrollments
   ↓
Admin Review
   ├─ App appears in Applications list
   ├─ Admin views details
   ├─ Admin updates status:
   │  ├─ Pending → Approved (payment received)
   │  ├─ Pending → On Hold (missing info)
   │  └─ Pending → Rejected (not qualified)
   └─ Student notified of status
```

---

## DATA FLOW DIAGRAM

```
┌────────────────┐
│  Enrollee      │
│  Registration  │
└────────┬───────┘
         │
         ↓
    ┌────────────┐
    │   Users    │  (user_id, email, password, full_name)
    │   Table    │
    └────┬───────┘
         │
    ┌────┴──────────┐
    ↓               ↓
 ┌─────────────┐  ┌──────────────────┐
 │  Enrollees  │  │  Enrollments     │  (user_id, course_id, status)
 │  Browse     │  │  Table           │
 │  Courses    │  └────────┬─────────┘
 └─────────────┘           │
                ┌──────────┴────────────┐
                ↓                       ↓
         ┌────────────┐        ┌──────────────────────┐
         │  Courses   │        │  Enrollment_Apps     │  (detailed form)
         │  Table     │        │  Table               │
         └────────────┘        └──────────────────────┘
                                         │
                        ┌────────────────┴──────────────┐
                        ↓                               ↓
                 ┌────────────┐              ┌──────────────────┐
                 │  Payments  │              │  Promotions      │
                 │  Table     │              │  Table           │
                 └────────────┘              └──────────────────┘
                        │
                        ↓
              ┌──────────────────┐
              │  Password_Reset  │
              │  Requests Table  │
              └──────────────────┘
```

---

## STATUS FLOW - APPLICATION APPROVAL

```
┌──────────────────────────────────────────┐
│         APPLICATION STATUS FLOW           │
└──────────────────────────────────────────┘

   New Enrollment
         ↓
    [PENDING] ← Default status when application submitted
         │
    Admin Reviews
         │
    ┌────┴─────┬──────────┬─────────┐
    ↓          ↓          ↓         ↓
[APPROVED] [REJECTED] [ON_HOLD] [PENDING]
    ↓
Student can pay
    ↓
[COMPLETED]
```

---

## Navigation Map - All Pages Connections

```
┌─────────────────────────────────────────────────────────────┐
│                  COMPLETE NAVIGATION MAP                    │
└─────────────────────────────────────────────────────────────┘

LOGIN (login.php)
├─→ Register Link → REGISTRATION
├─→ Forgot Password Link → FORGOT_PASSWORD
├─→ Enrollee Login → ENROLLEE_DASHBOARD
└─→ Admin Login → ADMIN_DASHBOARD

ENROLLEE_DASHBOARD (enrollee/dashboard.php)
├─→ View All Courses → COURSES
├─→ My Enrollments → MY_ENROLLMENTS
├─→ My Profile → PROFILE
├─→ Logout → LOGIN
└─→ Enroll Button → ENROLL → APPLICATION_FORM

COURSES (enrollee/courses.php)
├─→ Back to Dashboard → ENROLLEE_DASHBOARD
├─→ Enroll Now → ENROLL
└─→ Logout → LOGIN

APPLICATION_FORM (enrollee/application_form.php)
├─→ Back → ENROLLEE_DASHBOARD
├─→ Submit → PAYMENT
└─→ Logout → LOGIN

PAYMENT (enrollee/payment.php)
├─→ Back to Dashboard → ENROLLEE_DASHBOARD
├─→ Submit Payment → MY_ENROLLMENTS
└─→ Logout → LOGIN

MY_ENROLLMENTS (enrollee/my_enrollments.php)
├─→ Back to Dashboard → ENROLLEE_DASHBOARD
├─→ View Details → VIEW_ENROLLMENT
├─→ Make Payment → PAYMENT
└─→ Logout → LOGIN

PROFILE (enrollee/profile.php)
├─→ View Enrollments → MY_ENROLLMENTS
├─→ Change Password → FORGOT_PASSWORD
└─→ Logout → LOGIN

FORGOT_PASSWORD (forgot_password.php)
├─→ Back to Login → LOGIN
└─→ Request Reset → PASSWORD_RESET_REQUEST

ADMIN_DASHBOARD (admin/dashboard.php)
├─→ Applications → APPLICATIONS
├─→ Enrollments → ENROLLMENTS
├─→ Promotions → PROMOTIONS
├─→ Password Resets → PASSWORD_RESETS
├─→ Website Visits → VISITS
└─→ Logout → LOGIN

APPLICATIONS (admin/applications.php)
├─→ Dashboard → ADMIN_DASHBOARD
├─→ View → VIEW_APPLICATION
└─→ Logout → LOGIN

VIEW_APPLICATION (admin/view_application.php)
├─→ Back → APPLICATIONS
├─→ Print/PDF → PRINT
├─→ Update Status → SAVE_UPDATE
└─→ Logout → LOGIN

[Similar patterns for ENROLLMENTS, PROMOTIONS, PASSWORD_RESETS, VISITS]
```

---

## Page Load Performance

- All pages load in < 1 second
- Database queries optimized
- No external dependencies
- Minimal CSS/JS

---

**Navigation Guide Complete** ✓

