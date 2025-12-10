<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            display: flex;
            height: calc(100vh - 60px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #ddd;
            padding: 20px;
        }
        
        .sidebar h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
            color: #999;
        }
        
        .sidebar a {
            display: block;
            padding: 12px 15px;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 8px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar a:hover,
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
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 4px solid;
        }
        
        .stat-card.total {
            border-left-color: #667eea;
        }
        
        .stat-card.pending {
            border-left-color: #ffc107;
        }
        
        .stat-card.approved {
            border-left-color: #28a745;
        }
        
        .stat-card.users {
            border-left-color: #17a2b8;
        }
        
        .stat-card.resets {
            border-left-color: #fd7e14;
        }
        
        .stat-label {
            color: #999;
            font-size: 13px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #333;
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
            font-size: 14px;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
            color: #666;
        }
        
        tr:hover {
            background: #f9f9f9;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 12px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #764ba2;
        }
        
        .btn-sm {
            padding: 6px 10px;
            font-size: 12px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
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
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <div>
            <span><?php echo $_SESSION['full_name']; ?></span>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="dashboard.php" class="active">📊 Dashboard</a>
            <a href="applications.php">📋 Applications</a>
            <a href="enrollments.php">👥 Enrollments</a>
            <a href="courses.php">🎓 Courses</a>
            <a href="promotions.php">🎯 Promotions</a>
            <a href="password_resets.php">🔐 Password Resets</a>
            <a href="visits.php">📈 Website Visits</a>
        </div>
        
        <div class="main">
            <div class="page-header">
                <h2>Dashboard</h2>
                <p>Overview of enrollment system statistics</p>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card total">
                    <div class="stat-label">Total Enrollments</div>
                    <div class="stat-value"><?php echo $stats['total_enrollments']; ?></div>
                </div>
                
                <div class="stat-card pending">
                    <div class="stat-label">Pending Applications</div>
                    <div class="stat-value"><?php echo $stats['pending_enrollments']; ?></div>
                </div>
                
                <div class="stat-card approved">
                    <div class="stat-label">Approved Enrollments</div>
                    <div class="stat-value"><?php echo $stats['approved_enrollments']; ?></div>
                </div>
                
                <div class="stat-card users">
                    <div class="stat-label">Total Enrollees</div>
                    <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                </div>
                
                <div class="stat-card resets">
                    <div class="stat-label">Pending Password Resets</div>
                    <div class="stat-value"><?php echo $stats['pending_resets']; ?></div>
                </div>
            </div>
            
            <h3 style="margin: 30px 0 20px 0; color: #333;">Recent Applications</h3>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Enrollee Name</th>
                            <th>Email</th>
                            <th>Course</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $enrollments = getAllEnrollments();
                        if ($enrollments && $enrollments->num_rows > 0):
                            while ($row = $enrollments->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td>₱<?php echo number_format($row['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($row['application_date'])); ?></td>
                            <td>
                                <a href="view_application.php?id=<?php echo $row['id']; ?>" class="btn btn-sm">View</a>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #999;">No applications yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
