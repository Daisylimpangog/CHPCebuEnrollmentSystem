<?php
require_once 'config.php';

// Get all promotions
function getActivePromotions() {
    global $conn;
    $sql = "SELECT * FROM promotions WHERE is_active = 1 AND start_date <= CURDATE() AND end_date >= CURDATE()";
    return $conn->query($sql);
}

// Get all courses with fees
function getAllCourses() {
    global $conn;
    $sql = "SELECT c.*, 
                   (c.registration_fee + c.tuition_fee + (SELECT SUM(fee_amount) FROM course_fees WHERE course_id = c.id)) as total_fee,
                   ((c.registration_fee + c.tuition_fee + (SELECT SUM(fee_amount) FROM course_fees WHERE course_id = c.id)) * (1 - c.promo_percentage/100)) as promo_total
            FROM courses c";
    return $conn->query($sql);
}

// Get course details
function getCourseById($course_id) {
    global $conn;
    $course_id = intval($course_id);
    $sql = "SELECT c.*, 
                   (c.registration_fee + c.tuition_fee + (SELECT SUM(fee_amount) FROM course_fees WHERE course_id = c.id)) as total_fee,
                   ((c.registration_fee + c.tuition_fee + (SELECT SUM(fee_amount) FROM course_fees WHERE course_id = c.id)) * (1 - c.promo_percentage/100)) as promo_total
            FROM courses c WHERE c.id = $course_id";
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc() : null;
}

// Get course fees
function getCourseFees($course_id) {
    global $conn;
    $course_id = intval($course_id);
    $sql = "SELECT * FROM course_fees WHERE course_id = $course_id";
    return $conn->query($sql);
}

// Get user enrollments
function getUserEnrollments($user_id) {
    global $conn;
    $user_id = intval($user_id);
    $sql = "SELECT e.*, c.name as course_name 
            FROM enrollments e 
            JOIN courses c ON e.course_id = c.id 
            WHERE e.user_id = $user_id 
            ORDER BY e.application_date DESC";
    return $conn->query($sql);
}

// Get enrollment application details
function getEnrollmentApplication($enrollment_id) {
    global $conn;
    $enrollment_id = intval($enrollment_id);
    $sql = "SELECT * FROM enrollment_applications WHERE enrollment_id = $enrollment_id";
    $result = $conn->query($sql);
    return $result ? $result->fetch_assoc() : null;
}

// Get all enrollments for admin
function getAllEnrollments() {
    global $conn;
    $sql = "SELECT e.*, u.full_name, u.email, c.name as course_name 
            FROM enrollments e 
            JOIN users u ON e.user_id = u.id 
            JOIN courses c ON e.course_id = c.id 
            ORDER BY e.application_date DESC";
    return $conn->query($sql);
}

// Count website visits
function trackVisit($user_id = null) {
    global $conn;
    
    // Validate user_id if provided
    if ($user_id) {
        $user_id = intval($user_id);
        // Check if user exists
        $check = $conn->query("SELECT id FROM users WHERE id = $user_id LIMIT 1");
        if (!$check || $check->num_rows === 0) {
            $user_id = null; // Set to null if user doesn't exist
        }
    }
    
    $ip_address = $conn->real_escape_string($_SERVER['REMOTE_ADDR']);
    $user_agent = $conn->real_escape_string(substr($_SERVER['HTTP_USER_AGENT'], 0, 255));
    $page_visited = $conn->real_escape_string($_SERVER['REQUEST_URI']);
    
    $user_id_val = ($user_id) ? intval($user_id) : 'NULL';
    
    $sql = "INSERT INTO website_visits (user_id, ip_address, user_agent, page_visited) 
            VALUES ($user_id_val, '$ip_address', '$user_agent', '$page_visited')";
    
    if (!$conn->query($sql)) {
        // Silently fail to avoid breaking page load
        error_log("Failed to track visit: " . $conn->error);
    }
}

// Get statistics for admin dashboard
function getDashboardStats() {
    global $conn;
    $stats = array();
    
    // Total enrollments
    $result = $conn->query("SELECT COUNT(*) as count FROM enrollments");
    $stats['total_enrollments'] = $result->fetch_assoc()['count'];
    
    // Pending enrollments
    $result = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'pending'");
    $stats['pending_enrollments'] = $result->fetch_assoc()['count'];
    
    // Approved enrollments
    $result = $conn->query("SELECT COUNT(*) as count FROM enrollments WHERE status = 'approved'");
    $stats['approved_enrollments'] = $result->fetch_assoc()['count'];
    
    // Total users
    $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'enrollee'");
    $stats['total_users'] = $result->fetch_assoc()['count'];
    
    // Pending password resets
    $result = $conn->query("SELECT COUNT(*) as count FROM password_reset_requests WHERE status = 'pending'");
    $stats['pending_resets'] = $result->fetch_assoc()['count'];
    
    return $stats;
}
?>
