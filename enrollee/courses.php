<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$courses = getAllCourses();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Enrollment System</title>
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
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .page-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .course-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }
        
        .course-image {
            width: 100%;
            height: 250px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            text-align: center;
            padding: 20px;
            font-weight: 600;
        }
        
        .course-content {
            padding: 25px;
        }
        
        .course-content h3 {
            color: #333;
            margin-bottom: 12px;
            font-size: 20px;
        }
        
        .course-content p {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        .fee-breakdown {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .fee-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .fee-item:last-child {
            margin-bottom: 0;
        }
        
        .fee-label {
            color: #555;
        }
        
        .fee-value {
            font-weight: 600;
            color: #333;
        }
        
        .divider {
            border-top: 1px solid #ddd;
            margin: 12px 0;
        }
        
        .total-fee {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            color: #667eea;
        }
        
        .promo-info {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            margin-top: 10px;
            text-align: center;
        }
        
        .enroll-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            font-size: 15px;
            transition: transform 0.2s;
        }
        
        .enroll-btn:hover {
            transform: translateY(-2px);
        }
        
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-btn:hover {
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
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <div class="page-header">
            <h2>Available Courses</h2>
            <p>Select a course to enroll and get started on your professional journey.</p>
        </div>
        
        <div class="courses-grid">
            <?php
            if ($courses && $courses->num_rows > 0):
                while ($course = $courses->fetch_assoc()):
                    $miscellaneous = $course['total_fee'] - $course['registration_fee'] - $course['tuition_fee'];
            ?>
            <div class="course-card">
                <div class="course-image">
                    <?php echo $course['name']; ?>
                </div>
                <div class="course-content">
                    <h3><?php echo $course['name']; ?></h3>
                    <p><?php echo $course['description']; ?></p>
                    
                    <div class="fee-breakdown">
                        <div class="fee-item">
                            <span class="fee-label">Registration Fee</span>
                            <span class="fee-value">₱<?php echo number_format($course['registration_fee'], 2); ?></span>
                        </div>
                        <div class="fee-item">
                            <span class="fee-label">Tuition Fee</span>
                            <span class="fee-value">₱<?php echo number_format($course['tuition_fee'], 2); ?></span>
                        </div>
                        <div class="fee-item">
                            <span class="fee-label">Miscellaneous Fees</span>
                            <span class="fee-value">₱<?php echo number_format($miscellaneous, 2); ?></span>
                        </div>
                        <div class="divider"></div>
                        <div class="total-fee">
                            <span>Overall Total</span>
                            <span>₱<?php echo number_format($course['total_fee'], 2); ?></span>
                        </div>
                        <div class="promo-info">
                            With 5% Promo: ₱<?php echo number_format($course['promo_total'], 2); ?>
                        </div>
                    </div>
                    
                    <form action="enroll.php" method="POST">
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
    </div>
</body>
</html>
