<?php
require_once 'config.php';
require_once 'helpers.php';

$error = '';
$success = '';

if ($_POST && isset($_POST['request_reset'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($email) || empty($new_password) || empty($confirm_password)) {
            $error = 'All fields are required';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif ($new_password !== $confirm_password) {
            $error = 'Passwords do not match';
        } else {
            // Check if email exists
            $result = $conn->query("SELECT id FROM users WHERE email = '$email'");
            if ($result->num_rows === 0) {
                $error = 'Email not found in the system';
            } else {
                $user = $result->fetch_assoc();
                $user_id = $user['id'];
                
                // Check if there's already a pending reset request
                $check_pending = $conn->query("SELECT id FROM password_reset_requests 
                                              WHERE user_id = $user_id AND status = 'pending'");
                
                if ($check_pending && $check_pending->num_rows > 0) {
                    $error = 'You already have a pending password reset request. Please wait for admin approval.';
                } else {
                    // Hash the new password for secure storage
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    
                    // Create password reset request with the new hashed password stored temporarily
                    $token = bin2hex(random_bytes(32));
                    
                    // Use prepared statement to safely insert the hashed password
                    $stmt = $conn->prepare("INSERT INTO password_reset_requests (user_id, token, new_password_hash, status) VALUES (?, ?, ?, 'pending')");
                    $stmt->bind_param("iss", $user_id, $token, $hashed_password);
                    
                    if ($stmt->execute()) {
                        $success = '✅ Password reset request submitted successfully!<br><br>
                                   An admin will review your request shortly.<br>
                                   Once approved, you can log in with your new password.';
                        
                        // Clear form
                        $_POST['email'] = '';
                        $_POST['new_password'] = '';
                        $_POST['confirm_password'] = '';
                    } else {
                        $error = 'Error submitting request: ' . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }
        
        h2 {
            margin-bottom: 30px;
            color: #333;
            text-align: center;
        }
        
        .alert {
            padding: 12px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(16, 185, 129, 0.3);
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .back-link a {
            color: #10b981;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
        
        .info-text {
            background: #e0f2fe;
            border: 1px solid #0ea5e9;
            color: #0c7fb5;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 20px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>🔐 Reset Password</h2>
        
        <div class="info-text">
            ℹ️ Enter your new password below. An admin will review and approve your request.
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">📧 Registered Email</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" value="<?php echo $_POST['email'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="new_password">🔑 New Password</label>
                <input type="password" id="new_password" name="new_password" placeholder="At least 6 characters" value="<?php echo $_POST['new_password'] ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">✓ Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" value="<?php echo $_POST['confirm_password'] ?? ''; ?>" required>
            </div>
            
            <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
            <input type="hidden" name="request_reset" value="1">
            
            <button type="submit">Submit Reset Request</button>
        </form>
        
        <div class="back-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>
