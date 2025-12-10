<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$action = $_GET['action'] ?? '';
$enrollment_id = intval($_GET['id'] ?? 0);

// Handle status update
if ($_POST && isset($_POST['update_status'])) {
    $new_status = sanitize($_POST['status'] ?? '');
    $update_id = intval($_POST['enrollment_id'] ?? 0);
    
    if (in_array($new_status, ['pending', 'approved', 'rejected', 'on_hold'])) {
        $sql = "UPDATE enrollments SET status = '$new_status' WHERE id = $update_id";
        $conn->query($sql);
        header("Location: view_application.php?id=$update_id&success=1");
        exit;
    }
}

// Get application details
if ($enrollment_id > 0) {
    $enrollment = $conn->query(
        "SELECT e.*, u.full_name, u.email, c.name as course_name 
         FROM enrollments e 
         JOIN users u ON e.user_id = u.id 
         JOIN courses c ON e.course_id = c.id 
         WHERE e.id = $enrollment_id"
    )->fetch_assoc();
    
    if (!$enrollment) {
        redirect('applications.php');
    }
    
    $application = getEnrollmentApplication($enrollment_id);
} else {
    redirect('applications.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Application</title>
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
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .app-header {
            background: white;
            padding: 20px;
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
        
        .status-controls {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .status-controls h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .status-form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }
        
        .form-group {
            flex: 1;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        
        .btn {
            padding: 10px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn:hover {
            transform: translateY(-2px);
        }
        
        .app-details {
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
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .btn-print {
            background: #17a2b8;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 20px;
        }
        
        @media print {
            .navbar, .status-controls, .back-link, .btn-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>View Application</h1>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Status updated successfully!</div>
        <?php endif; ?>
        
        <a href="applications.php" class="back-link">← Back to Applications</a>
        
        <div class="app-header">
            <div class="header-info">
                <h2><?php echo $enrollment['full_name']; ?></h2>
                <p><strong>Course:</strong> <?php echo $enrollment['course_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $enrollment['email']; ?></p>
                <p><strong>Amount:</strong> ₱<?php echo number_format($enrollment['total_amount'], 2); ?></p>
            </div>
            <div>
                <span style="background: #667eea; color: white; padding: 8px 12px; border-radius: 4px; font-weight: 600;">
                    <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                </span>
            </div>
        </div>
        
        <div class="status-controls">
            <h3>Update Application Status</h3>
            <form method="POST" class="status-form">
                <div class="form-group" style="flex: 1;">
                    <label for="status">New Status</label>
                    <select id="status" name="status" required>
                        <option value="">Select Status...</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="on_hold">On Hold</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <input type="hidden" name="enrollment_id" value="<?php echo $enrollment_id; ?>">
                <input type="hidden" name="update_status" value="1">
                <button type="submit" class="btn">Update Status</button>
            </form>
        </div>
        
        <a href="javascript:window.print()" class="btn-print">🖨️ Print / Export as PDF</a>
        
        <div class="app-details">
            <!-- Personal Information -->
            <div class="section">
                <h3>Personal Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">First Name</span>
                        <span class="detail-value"><?php echo $application['first_name'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Middle Name</span>
                        <span class="detail-value"><?php echo $application['middle_name'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Family Name</span>
                        <span class="detail-value"><?php echo $application['family_name'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Nickname</span>
                        <span class="detail-value"><?php echo $application['nickname'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value"><?php echo $application['date_of_birth'] ? date('M d, Y', strtotime($application['date_of_birth'])) : 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Age</span>
                        <span class="detail-value"><?php echo $application['age'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Sex</span>
                        <span class="detail-value"><?php echo $application['sex'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Marital Status</span>
                        <span class="detail-value"><?php echo $application['marital_status'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Citizenship</span>
                        <span class="detail-value"><?php echo $application['citizenship'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Religion</span>
                        <span class="detail-value"><?php echo $application['religion'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="section">
                <h3>Contact Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Contact Number</span>
                        <span class="detail-value"><?php echo $application['contact_number'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Telephone</span>
                        <span class="detail-value"><?php echo $application['telephone'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Email Address</span>
                        <span class="detail-value"><?php echo $application['email_address'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row full">
                    <div class="detail-item">
                        <span class="detail-label">Present Address</span>
                        <span class="detail-value"><?php echo $application['present_address'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row full">
                    <div class="detail-item">
                        <span class="detail-label">Provincial Address</span>
                        <span class="detail-value"><?php echo $application['provincial_address'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Employment & Family -->
            <div class="section">
                <h3>Employment & Family Information</h3>
                
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
                        <span class="detail-label">Name of Spouse</span>
                        <span class="detail-value"><?php echo $application['name_of_spouse'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">No. of Children</span>
                        <span class="detail-value"><?php echo $application['no_of_children'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Academic Information -->
            <div class="section">
                <h3>Academic Information</h3>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Educational Attainment</span>
                        <span class="detail-value"><?php echo $application['educational_attainment'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Course/Degree</span>
                        <span class="detail-value"><?php echo $application['course_degree'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">NCII Certificate/s</span>
                        <span class="detail-value"><?php echo $application['ncii_certificate'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Year Graduated</span>
                        <span class="detail-value"><?php echo $application['year_graduated'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row full">
                    <div class="detail-item">
                        <span class="detail-label">University/College/School</span>
                        <span class="detail-value"><?php echo $application['university_college_school'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Additional Information -->
            <div class="section">
                <h3>Additional Information</h3>
                
                <div class="detail-row full">
                    <div class="detail-item">
                        <span class="detail-label">Program to Pursue</span>
                        <span class="detail-value"><?php echo $application['program_to_pursue'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">How Did You Know About Us</span>
                        <span class="detail-value"><?php echo $application['how_did_you_know'] ?? 'N/A'; ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">How Will You Finance</span>
                        <span class="detail-value"><?php echo $application['how_will_you_finance'] ?? 'N/A'; ?></span>
                    </div>
                </div>
                
                <div class="detail-row">
                    <div class="detail-item">
                        <span class="detail-label">Terms of Payment</span>
                        <span class="detail-value"><?php echo $application['terms_of_payment'] ?? 'N/A'; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
