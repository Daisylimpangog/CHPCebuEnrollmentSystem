<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

if (!isset($_POST['course_id'])) {
    redirect('dashboard.php');
}

$course_id = intval($_POST['course_id']);
$user_id = $_SESSION['user_id'];

// Get course details
$course = getCourseById($course_id);
if (!$course) {
    redirect('dashboard.php');
}

// Calculate total amount
$total_amount = $course['total_fee'];
$promo_amount = $course['promo_total'];

// Create enrollment
$sql = "INSERT INTO enrollments (user_id, course_id, status, total_amount) 
        VALUES ($user_id, $course_id, 'pending', $promo_amount)";

if ($conn->query($sql)) {
    $enrollment_id = $conn->insert_id;
    $_SESSION['current_enrollment_id'] = $enrollment_id;
    redirect('application_form.php');
} else {
    $_SESSION['error'] = 'Error creating enrollment: ' . $conn->error;
    redirect('dashboard.php');
}
?>
