<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$enrollment_id = intval($_GET['id'] ?? 0);

if ($enrollment_id === 0) {
    redirect('my_enrollments.php');
}

// Get enrollment and application details
$enrollment = $conn->query(
    "SELECT e.*, c.name as course_name 
     FROM enrollments e 
     JOIN courses c ON e.course_id = c.id 
     WHERE e.id = $enrollment_id AND e.user_id = {$_SESSION['user_id']}"
)->fetch_assoc();

if (!$enrollment) {
    redirect('my_enrollments.php');
}

$application = getEnrollmentApplication($enrollment_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Details</title>
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
            max-width: 900px;
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
        
        .header {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-info h2 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .header-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .status-badge {
            padding: 10px 16px;
            background: #667eea;
            color: white;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
        }
        
        .details-container {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .section {
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 1px solid #eee;
        }
        
        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 15px;
        }
        
        .detail-row.full {
            grid-template-columns: 1fr;
        }
        
        .detail-item {
            display: flex;
            flex-direction: column;
        }
        
        .detail-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: #333;
            font-size: 14px;
            font-weight: 500;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            font-weight: 600;
        }
        
        .btn:hover {
            transform: translateY(-2px);
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
        <a href="my_enrollments.php" class="back-link">← Back to My Enrollments</a>
        
        <div class="header">
            <div class="header-info">
                <h2><?php echo $enrollment['course_name']; ?></h2>
                <p>Applied: <?php echo date('M d, Y', strtotime($enrollment['application_date'])); ?></p>
                <p>Total Amount: ₱<?php echo number_format($enrollment['total_amount'], 2); ?></p>
            </div>
            <div class="status-badge">
                <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
            </div>
        </div>
        
        <div class="details-container">
            <?php if ($application): ?>
            <!-- Personal Information -->
            <div class="section">
                <h3>Personal Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value">
                            <?php echo $application['first_name']; ?> 
                            <?php echo $application['middle_name'] ? $application['middle_name'] . ' ' : ''; ?>
                            <?php echo $application['family_name']; ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nickname</span>
                        <span class="detail-value"><?php echo $application['nickname'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value">
                            <?php echo $application['date_of_birth'] ? date('M d, Y', strtotime($application['date_of_birth'])) : 'N/A'; ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Sex</span>
                        <span class="detail-value"><?php echo $application['sex'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Marital Status</span>
                        <span class="detail-value"><?php echo $application['marital_status'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Contact Number</span>
                        <span class="detail-value"><?php echo $application['contact_number'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="section">
                <h3>Contact Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?php echo $application['email_address'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Telephone</span>
                        <span class="detail-value"><?php echo $application['telephone'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row full">
                    <div class="detail-item">
                        <span class="detail-label">Present Address</span>
                        <span class="detail-value"><?php echo $application['present_address'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="section">
                <h3>Additional Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Occupation</span>
                        <span class="detail-value"><?php echo $application['occupation'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Company</span>
                        <span class="detail-value"><?php echo $application['company'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Educational Attainment</span>
                        <span class="detail-value"><?php echo $application['educational_attainment'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">How Will You Finance</span>
                        <span class="detail-value"><?php echo $application['how_will_you_finance'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <a href="my_enrollments.php" class="btn">Back to Enrollments</a>
        </div>
    </div>
</body>
</html>
