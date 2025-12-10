-- Enrollment System Database
CREATE DATABASE IF NOT EXISTS enrollment_system;
USE enrollment_system;

-- Users Table (for login)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    user_type ENUM('enrollee', 'admin') DEFAULT 'enrollee',
    status ENUM('active', 'inactive', 'pending') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    picture_path VARCHAR(255),
    registration_fee DECIMAL(10, 2),
    tuition_fee DECIMAL(10, 2),
    promo_percentage INT DEFAULT 5,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Course Fees Table
CREATE TABLE course_fees (
    id INT PRIMARY KEY AUTO_INCREMENT,
    course_id INT NOT NULL,
    fee_name VARCHAR(100) NOT NULL,
    fee_amount DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollments Table
CREATE TABLE enrollments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'on_hold') DEFAULT 'pending',
    total_amount DECIMAL(10, 2),
    paid_amount DECIMAL(10, 2) DEFAULT 0,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approval_date TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

-- Enrollment Applications Table
CREATE TABLE enrollment_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL UNIQUE,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    family_name VARCHAR(100) NOT NULL,
    nickname VARCHAR(100),
    age INT,
    date_of_birth DATE,
    place_of_birth VARCHAR(100),
    sex ENUM('Male', 'Female'),
    marital_status ENUM('Single', 'Married', 'Widowed', 'Separated'),
    citizenship VARCHAR(100),
    religion VARCHAR(100),
    present_address TEXT,
    provincial_address TEXT,
    contact_number VARCHAR(20),
    email_address VARCHAR(100),
    occupation VARCHAR(100),
    company VARCHAR(100),
    telephone VARCHAR(20),
    name_of_spouse VARCHAR(100),
    no_of_children INT,
    educational_attainment VARCHAR(100),
    course_degree VARCHAR(100),
    ncii_certificate VARCHAR(100),
    university_college_school VARCHAR(100),
    year_graduated INT,
    program_to_pursue TEXT,
    how_did_you_know TEXT,
    how_will_you_finance TEXT,
    terms_of_payment VARCHAR(50),
    submitted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);

-- Payments Table
CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    enrollment_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    reference_number VARCHAR(100),
    FOREIGN KEY (enrollment_id) REFERENCES enrollments(id) ON DELETE CASCADE
);

-- Promotions Table
CREATE TABLE promotions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(100) NOT NULL,
    description TEXT,
    picture_path VARCHAR(255),
    discount_percentage INT,
    start_date DATE,
    end_date DATE,
    is_active TINYINT DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Password Reset Requests Table
CREATE TABLE password_reset_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(255) UNIQUE NOT NULL,
    new_password_hash VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    reviewed_by INT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- Website Visits Tracker
CREATE TABLE website_visits (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    ip_address VARCHAR(50),
    user_agent TEXT,
    page_visited VARCHAR(255),
    visit_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert Default Courses
INSERT INTO courses (name, description, registration_fee, tuition_fee) VALUES 
('Healthcare Services NCII', 'Healthcare Services National Certificate II', 2500, 34860),
('Care Giving TUITION NCII', 'Care Giving National Certificate II', 2500, 30570);

-- Insert Course Fees for Healthcare Services
INSERT INTO course_fees (course_id, fee_name, fee_amount) VALUES
(1, 'ID', 170),
(1, '2 Sets Scrub Suit', 1760),
(1, '2 Sets Polo Shirt', 900),
(1, 'Basic Life Support BLS', 2100),
(1, 'OJT Fee', 5000),
(1, 'Graduation Fee', 1500),
(1, 'TOR & Certificate of Training', 2629);

-- Insert Course Fees for Care Giving
INSERT INTO course_fees (course_id, fee_name, fee_amount) VALUES
(2, 'ID', 170),
(2, '2 Sets Scrub Suit', 1760),
(2, '2 Sets Polo Shirt', 900),
(2, 'Basic Life Support (BLS)', 2100),
(2, 'OJT Fee', 5000),
(2, 'Graduation Fee', 1500),
(2, 'TOR & Certificate Training', 2629);

-- Insert Default Admin Account
INSERT INTO users (email, password, full_name, user_type, status) VALUES
('admin@chp.com', '$2y$10$QT3PBFpCUGR9wZjc6rC4Du5.ZvJhwLRqFW5J2K8zF8E.xY7q9c7C2', 'Administrator', 'admin', 'active');
