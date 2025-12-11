<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || isAdmin()) {
    redirect('../login.php');
}

$enrollment_id = intval($_GET['enrollment_id'] ?? 0);

if ($enrollment_id === 0) {
    redirect('dashboard.php');
}

// Get enrollment details
$enrollment = $conn->query(
    "SELECT e.*, c.name as course_name, u.email as enrollee_email 
     FROM enrollments e 
     JOIN courses c ON e.course_id = c.id 
     JOIN users u ON e.user_id = u.id 
     WHERE e.id = $enrollment_id AND e.user_id = {$_SESSION['user_id']}"
)->fetch_assoc();

if (!$enrollment) {
    redirect('dashboard.php');
}

// Handle payment submission
if ($_POST && isset($_POST['submit_payment'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $payment_method = sanitize($_POST['payment_method'] ?? '');
        $reference_number = sanitize($_POST['reference_number'] ?? '');
        
        if (empty($payment_method) || empty($reference_number)) {
            $error = 'Payment method and reference number are required';
        } else {
            $sql = "INSERT INTO payments (enrollment_id, amount, payment_method, reference_number, status) 
                    VALUES ($enrollment_id, {$enrollment['total_amount']}, '$payment_method', '$reference_number', 'pending')";
            
            if ($conn->query($sql)) {
                $success = 'Payment submitted successfully! Your application is now complete.';
                header("Refresh:3; url=my_enrollments.php");
            } else {
                $error = 'Error submitting payment: ' . $conn->error;
            }
        }
    }
}

$error = $error ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Enrollment</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }
        
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 14px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
            border: 1px solid #cfc;
        }
        
        .payment-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        
        .payment-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .payment-header h2 {
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .amount-display {
            font-size: 48px;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .course-name {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .payment-body {
            padding: 40px;
        }
        
        .summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .summary-row:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 2px solid #ddd;
            font-weight: bold;
            color: #667eea;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        select,
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        select:focus,
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .payment-methods {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .payment-methods h4 {
            color: #333;
            margin-bottom: 12px;
            font-size: 14px;
        }
        
        .method-option {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
        }
        
        .method-option:last-child {
            margin-bottom: 0;
        }
        
        .method-option input[type="radio"] {
            width: auto;
            margin-right: 10px;
            cursor: pointer;
        }
        
        .method-option label {
            margin: 0;
            cursor: pointer;
            flex: 1;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-submit {
            flex: 1;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .btn-cancel {
            background: #ddd;
            color: #333;
        }
        
        .btn-cancel:hover {
            background: #ccc;
        }
        
        .note {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            font-size: 13px;
            line-height: 1.6;
            border-left: 4px solid #4caf50;
        }
        
        .note strong {
            display: block;
            margin-bottom: 8px;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Payment Processing</h1>
        <div>
            <span><?php echo $_SESSION['full_name']; ?></span>
            <a href="../logout.php" style="color: white; text-decoration: none; margin-left: 20px;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="payment-card">
            <div class="payment-header">
                <div class="course-name"><?php echo $enrollment['course_name']; ?></div>
                <h2>Complete Your Payment</h2>
                <div class="amount-display">₱<?php echo number_format($enrollment['total_amount'], 2); ?></div>
            </div>
            
            <div class="payment-body">
                <div class="summary">
                    <div class="summary-row">
                        <span>Course Name:</span>
                        <span><?php echo $enrollment['course_name']; ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Application Date:</span>
                        <span><?php echo date('M d, Y', strtotime($enrollment['application_date'])); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount:</span>
                        <span>₱<?php echo number_format($enrollment['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <form method="POST">
                    <div class="payment-methods">
                        <h4>Payment Method</h4>
                        
                        <div class="method-option">
                            <input type="radio" id="cash" name="payment_method" value="Outright Cash" required>
                            <label for="cash">Outright Cash (Full Payment)</label>
                        </div>
                        
                        <div class="method-option">
                            <input type="radio" id="installment" name="payment_method" value="Installment" required>
                            <label for="installment">Installment (Pay in Multiple Transactions)</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reference">Reference/Receipt Number *</label>
                        <input 
                            type="text" 
                            id="reference" 
                            name="reference_number" 
                            placeholder="Enter reference number from your payment"
                            required>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                    <input type="hidden" name="submit_payment" value="1">
                    
                    <div class="button-group">
                        <a href="dashboard.php" class="btn btn-cancel">Back to Dashboard</a>
                        <button type="submit" class="btn btn-submit">Submit Payment</button>
                    </div>
                    
                    <div class="note">
                        <strong>Important:</strong>
                        Please ensure you have completed your payment before submitting this form. Your application will be reviewed once your payment is confirmed. You will receive an email confirmation of your payment and application status.
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
