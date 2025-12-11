<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';

// Handle enrollment deletion
if ($_POST && isset($_POST['delete_enrollment'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $enrollment_id = intval($_POST['enrollment_id'] ?? 0);
        
        // Verify the enrollment belongs to the current user
        $check = $conn->query("SELECT id, status FROM enrollments WHERE id = $enrollment_id AND user_id = {$_SESSION['user_id']}");
        
        if ($check && $check->num_rows > 0) {
            $enrollment = $check->fetch_assoc();
            
            // Only allow deletion if status is pending or on_hold
            if (in_array($enrollment['status'], ['pending', 'on_hold'])) {
                // Delete enrollment application first (if exists)
                $conn->query("DELETE FROM enrollment_applications WHERE enrollment_id = $enrollment_id");
                
                // Delete payments (if any)
                $conn->query("DELETE FROM payments WHERE enrollment_id = $enrollment_id");
                
                // Delete enrollment
                if ($conn->query("DELETE FROM enrollments WHERE id = $enrollment_id")) {
                    $success = 'Enrollment deleted successfully!';
                } else {
                    $error = 'Error deleting enrollment: ' . $conn->error;
                }
            } else {
                $error = 'You can only delete pending or on-hold enrollments. Contact admin to cancel approved enrollments.';
            }
        } else {
            $error = 'Enrollment not found or you do not have permission to delete it.';
        }
    }
}

$enrollments = getUserEnrollments($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrollments</title>
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
        
        .enrollments-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .enrollment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .enrollment-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-title h3 {
            font-size: 20px;
            margin-bottom: 5px;
        }
        
        .card-title p {
            font-size: 13px;
            opacity: 0.9;
        }
        
        .status-badge {
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .detail-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-row:last-child {
            margin-bottom: 0;
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
            font-size: 16px;
            font-weight: 600;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            flex: 1;
            padding: 12px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
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
        
        .btn-danger {
            background: #fee;
            color: #d32f2f;
            border: 1px solid #fcc;
        }
        
        .btn-danger:hover {
            background: #fdd;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .empty-state {
            background: white;
            padding: 60px 20px;
            border-radius: 8px;
            text-align: center;
            color: #999;
        }
        
        .empty-state p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .empty-state a {
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
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
        
        <div class="page-header">
            <h2>My Enrollments</h2>
            <p>View your enrollment status and track your applications</p>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($enrollments && $enrollments->num_rows > 0): ?>
            <div class="enrollments-list">
                <?php while ($enrollment = $enrollments->fetch_assoc()): ?>
                <div class="enrollment-card">
                    <div class="card-header">
                        <div class="card-title">
                            <h3><?php echo $enrollment['course_name']; ?></h3>
                            <p>Applied on <?php echo date('M d, Y', strtotime($enrollment['application_date'])); ?></p>
                        </div>
                        <div class="status-badge">
                            <?php echo ucfirst(str_replace('_', ' ', $enrollment['status'])); ?>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="detail-row">
                            <div class="detail-item">
                                <span class="detail-label">Total Amount</span>
                                <span class="detail-value">₱<?php echo number_format($enrollment['total_amount'], 2); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Amount Paid</span>
                                <span class="detail-value">₱<?php echo number_format($enrollment['paid_amount'] ?? 0, 2); ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Status</span>
                                <span class="detail-value">
                                    <?php 
                                        $status_text = '';
                                        switch($enrollment['status']) {
                                            case 'pending':
                                                $status_text = '⏳ Pending';
                                                break;
                                            case 'approved':
                                                $status_text = '✓ Approved';
                                                break;
                                            case 'rejected':
                                                $status_text = '✗ Rejected';
                                                break;
                                            case 'on_hold':
                                                $status_text = '⏸ On Hold';
                                                break;
                                        }
                                        echo $status_text;
                                    ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="action-buttons">
                            <a href="view_enrollment.php?id=<?php echo $enrollment['id']; ?>" class="btn btn-primary">View Details</a>
                            <?php if ($enrollment['status'] === 'approved'): ?>
                                <a href="payment.php?enrollment_id=<?php echo $enrollment['id']; ?>" class="btn btn-secondary">Make Payment</a>
                            <?php endif; ?>
                            <?php if (in_array($enrollment['status'], ['pending', 'on_hold'])): ?>
                                <form method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this enrollment? This action cannot be undone.');">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                                    <input type="hidden" name="enrollment_id" value="<?php echo $enrollment['id']; ?>">
                                    <input type="hidden" name="delete_enrollment" value="1">
                                    <button type="submit" class="btn btn-danger">🗑️ Delete</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>You haven't enrolled in any courses yet.</p>
                <a href="courses.php">Browse Courses</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
