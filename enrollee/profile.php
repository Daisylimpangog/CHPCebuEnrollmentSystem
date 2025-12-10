<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Enrollment System</title>
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
        
        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .profile-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .profile-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .profile-header p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .profile-body {
            padding: 30px;
        }
        
        .info-item {
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .info-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .info-value {
            color: #333;
            font-size: 16px;
            font-weight: 500;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #e8eef7;
            color: #667eea;
        }
        
        .btn-secondary:hover {
            background: #d8def7;
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
        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>
        
        <div class="profile-card">
            <div class="profile-header">
                <h2><?php echo $_SESSION['full_name']; ?></h2>
                <p>Enrollee Profile</p>
            </div>
            
            <div class="profile-body">
                <div class="info-item">
                    <span class="info-label">Email Address</span>
                    <span class="info-value"><?php
                        // Get current user email
                        $user = $conn->query("SELECT email FROM users WHERE id = {$_SESSION['user_id']}")->fetch_assoc();
                        echo $user['email'];
                    ?></span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Account Type</span>
                    <span class="info-value">Student Enrollee</span>
                </div>
                
                <div class="info-item">
                    <span class="info-label">Account Status</span>
                    <span class="info-value" style="color: #28a745;">✓ Active</span>
                </div>
                
                <div class="button-group">
                    <a href="my_enrollments.php" class="btn btn-primary">View My Enrollments</a>
                    <a href="../forgot_password.php" class="btn btn-secondary">Change Password</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
