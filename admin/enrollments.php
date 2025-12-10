<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get enrollment statistics by course
$enrollments_by_course = $conn->query(
    "SELECT c.name, COUNT(e.id) as total, 
            SUM(CASE WHEN e.status = 'approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN e.status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN e.status = 'rejected' THEN 1 ELSE 0 END) as rejected,
            SUM(CASE WHEN e.status = 'on_hold' THEN 1 ELSE 0 END) as on_hold
     FROM courses c
     LEFT JOIN enrollments e ON c.id = e.course_id
     GROUP BY c.id"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollments - Admin</title>
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
        
        .container {
            display: flex;
            height: calc(100vh - 60px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
        }
        
        .sidebar h3 {
            color: #999;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
        }
        
        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 8px;
            border-left: 3px solid transparent;
        }
        
        .sidebar a.active {
            background: #e8eef7;
            color: #667eea;
            border-left-color: #667eea;
        }
        
        .main {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }
        
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .stat-card h4 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .stat-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
        
        .stat-row:last-child {
            border-bottom: none;
        }
        
        .stat-label {
            color: #666;
        }
        
        .stat-value {
            font-weight: 600;
            color: #333;
            font-size: 18px;
        }
        
        .stat-small {
            font-size: 14px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f9f9f9;
            border-bottom: 2px solid #ddd;
        }
        
        th {
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="../logout.php" style="color: white;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="applications.php">📋 Applications</a>
            <a href="enrollments.php" class="active">👥 Enrollments</a>
            <a href="courses.php">🎓 Courses</a>
            <a href="promotions.php">🎯 Promotions</a>
            <a href="password_resets.php">🔐 Password Resets</a>
            <a href="visits.php">📈 Website Visits</a>
        </div>
        
        <div class="main">
            <h2>Enrollments by Course</h2>
            
            <div class="stats-grid">
                <?php
                if ($enrollments_by_course && $enrollments_by_course->num_rows > 0):
                    while ($course = $enrollments_by_course->fetch_assoc()):
                ?>
                <div class="stat-card">
                    <h4><?php echo $course['name']; ?></h4>
                    
                    <div class="stat-row">
                        <span class="stat-label">Total Enrollments</span>
                        <span class="stat-value"><?php echo $course['total'] ?? 0; ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label stat-small">✓ Approved</span>
                        <span class="stat-value stat-small" style="color: #28a745;"><?php echo $course['approved'] ?? 0; ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label stat-small">⏳ Pending</span>
                        <span class="stat-value stat-small" style="color: #ffc107;"><?php echo $course['pending'] ?? 0; ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label stat-small">❌ Rejected</span>
                        <span class="stat-value stat-small" style="color: #dc3545;"><?php echo $course['rejected'] ?? 0; ?></span>
                    </div>
                    
                    <div class="stat-row">
                        <span class="stat-label stat-small">⏸ On Hold</span>
                        <span class="stat-value stat-small" style="color: #6c757d;"><?php echo $course['on_hold'] ?? 0; ?></span>
                    </div>
                </div>
                <?php
                    endwhile;
                endif;
                ?>
            </div>
        </div>
    </div>
</body>
</html>
