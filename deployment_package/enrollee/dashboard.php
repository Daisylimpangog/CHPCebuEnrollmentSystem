<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn()) {
    redirect('../login.php');
}

if (isAdmin()) {
    redirect('../admin/dashboard.php');
}

trackVisit($_SESSION['user_id']);
$enrollments = getUserEnrollments($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Enrollee</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0fdf4;
        }
        
        .navbar {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar h1 {
            font-size: 20px;
        }
        
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            transition: opacity 0.3s;
        }
        
        .navbar a:hover {
            opacity: 0.8;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .welcome-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .welcome-section h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn {
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .section-title {
            color: #333;
            margin: 30px 0 20px 0;
            font-size: 22px;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .course-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .course-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            text-align: center;
            padding: 20px;
        }
        
        .course-content {
            padding: 20px;
        }
        
        .course-content h3 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .course-content p {
            color: #666;
            font-size: 14px;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .price-section {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .price-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .price-row strong {
            color: #333;
        }
        
        .price-total {
            border-top: 2px solid #ddd;
            padding-top: 10px;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            color: #667eea;
        }
        
        .promo {
            color: #28a745;
            font-size: 12px;
            margin-top: 5px;
        }
        
        .enroll-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: transform 0.2s;
        }
        
        .enroll-btn:hover {
            transform: translateY(-2px);
        }
        
        .enrollments-section {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .enrollment-item {
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 5px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .enrollment-item h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .enrollment-item p {
            color: #666;
            font-size: 14px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-on_hold {
            background: #e2e3e5;
            color: #383d41;
        }
        
        .action-links {
            display: flex;
            gap: 10px;
        }
        
        .action-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        .action-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Enrollment System</h1>
        <div>
            <span><?php echo $_SESSION['full_name']; ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo $_SESSION['full_name']; ?>!</h2>
            <p>Browse our available courses and enroll in the program that suits your needs.</p>
            <div class="nav-buttons">
                <a href="courses.php" class="btn">View All Courses</a>
                <a href="my_enrollments.php" class="btn">My Enrollments</a>
                <a href="profile.php" class="btn">My Profile</a>
            </div>
        </div>
        
        <h2 class="section-title">Available Courses</h2>
        
        <div class="courses-grid">
            <?php
            $courses = getAllCourses();
            if ($courses && $courses->num_rows > 0):
                while ($course = $courses->fetch_assoc()):
            ?>
            <div class="course-card">
                <div class="course-image">
                    <?php if (!empty($course['picture_path'])): ?>
                        <img src="<?php echo $course['picture_path']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                    <?php else: ?>
                        <?php echo $course['name']; ?>
                    <?php endif; ?>
                </div>
                <div class="course-content">
                    <h3><?php echo $course['name']; ?></h3>
                    <p><?php echo $course['description']; ?></p>
                    
                    <div class="price-section">
                        <div class="price-row">
                            <strong>Registration Fee:</strong>
                            <span>₱<?php echo number_format($course['registration_fee'], 2); ?></span>
                        </div>
                        <div class="price-row">
                            <strong>Tuition Fee:</strong>
                            <span>₱<?php echo number_format($course['tuition_fee'], 2); ?></span>
                        </div>
                        <div class="price-row">
                            <strong>Miscellaneous Fees:</strong>
                            <span>₱<?php echo number_format($course['total_fee'] - $course['registration_fee'] - $course['tuition_fee'], 2); ?></span>
                        </div>
                        <div class="price-total">
                            <span>Overall Total:</span>
                            <span>₱<?php echo number_format($course['total_fee'], 2); ?></span>
                        </div>
                        <div class="promo">
                            ✓ With 5% Promo: ₱<?php echo number_format($course['promo_total'], 2); ?>
                        </div>
                    </div>
                    
                    <form action="enroll.php" method="POST" style="margin: 0;">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <button type="submit" class="enroll-btn">Enroll Now</button>
                    </form>
                </div>
            </div>
            <?php
                endwhile;
            endif;
            ?>
        </div>
        
        <?php if ($enrollments && $enrollments->num_rows > 0): ?>
        <div class="enrollments-section">
            <h2 class="section-title">Your Enrollments</h2>
            
            <?php while ($enrollment = $enrollments->fetch_assoc()): ?>
            <div class="enrollment-item">
                <div>
                    <h4><?php echo $enrollment['course_name']; ?></h4>
                    <p>Applied: <?php echo date('M d, Y', strtotime($enrollment['application_date'])); ?></p>
                    <p>Total Amount: ₱<?php echo number_format($enrollment['total_amount'], 2); ?></p>
                </div>
                <div>
                    <span class="status-badge status-<?php echo $enrollment['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
