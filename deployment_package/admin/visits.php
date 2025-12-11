<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Get website visit statistics
$visits = $conn->query(
    "SELECT 
        COUNT(*) as total_visits,
        COUNT(DISTINCT user_id) as unique_users,
        COUNT(DISTINCT ip_address) as unique_visitors,
        DATE(visit_date) as visit_date
     FROM website_visits
     GROUP BY DATE(visit_date)
     ORDER BY visit_date DESC
     LIMIT 30"
);

// Get most visited pages
$popular_pages = $conn->query(
    "SELECT page_visited, COUNT(*) as visits
     FROM website_visits
     GROUP BY page_visited
     ORDER BY visits DESC
     LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Visits - Admin</title>
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
            margin-bottom: 30px;
        }
        
        h3 {
            color: #333;
            margin: 30px 0 20px 0;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
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
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .visit-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 6px;
            border-radius: 3px;
            margin-top: 5px;
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
            <a href="enrollments.php">👥 Enrollments</a>
            <a href="courses.php">🎓 Courses</a>
            <a href="promotions.php">🎯 Promotions</a>
            <a href="password_resets.php">🔐 Password Resets</a>
            <a href="visits.php" class="active">📈 Website Visits</a>
        </div>
        
        <div class="main">
            <h2>Website Visits Analytics</h2>
            
            <h3>Daily Visits</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Total Visits</th>
                            <th>Unique Users</th>
                            <th>Unique Visitors (IP)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($visits && $visits->num_rows > 0):
                            while ($row = $visits->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['visit_date'])); ?></td>
                            <td>
                                <?php echo $row['total_visits']; ?>
                                <div class="visit-bar" style="width: <?php echo min($row['total_visits'] * 10, 100); ?>%;"></div>
                            </td>
                            <td><?php echo $row['unique_users']; ?></td>
                            <td><?php echo $row['unique_visitors']; ?></td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #999;">No visit data available</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <h3>Most Visited Pages</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th>Visits</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($popular_pages && $popular_pages->num_rows > 0):
                            while ($row = $popular_pages->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['page_visited']; ?></td>
                            <td>
                                <?php echo $row['visits']; ?>
                                <div class="visit-bar" style="width: <?php echo min($row['visits'] * 5, 100); ?>%;"></div>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="2" style="text-align: center; color: #999;">No page visit data available</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
