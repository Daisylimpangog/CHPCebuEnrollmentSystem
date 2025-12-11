<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_POST && isset($_POST['setup_admin'])) {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $full_name = sanitize($_POST['full_name'] ?? '');
    
    if (empty($email) || empty($password) || empty($full_name)) {
        $error = 'All fields are required';
    } else {
        // Check if admin already exists
        $check = $conn->query("SELECT id FROM users WHERE user_type = 'admin' LIMIT 1");
        
        if ($check && $check->num_rows > 0) {
            $error = 'Admin account already exists. Delete it first if you want to create a new one.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO users (email, password, full_name, user_type, status) 
                    VALUES ('$email', '$hashed_password', '$full_name', 'admin', 'active')";
            
            if ($conn->query($sql)) {
                $message = 'Admin account created successfully! You can now log in.';
            } else {
                $error = 'Error creating admin: ' . $conn->error;
            }
        }
    }
}

// Check if admin exists
$admin_check = $conn->query("SELECT email FROM users WHERE user_type = 'admin' LIMIT 1");
$admin_exists = $admin_check && $admin_check->num_rows > 0;
$admin_email = $admin_exists ? $admin_check->fetch_assoc()['email'] : 'None';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - Enrollment System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .status-box {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .status-label {
            color: #666;
            font-size: 13px;
            margin-bottom: 5px;
        }
        
        .status-value {
            color: #333;
            font-weight: 600;
            font-size: 16px;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        
        .btn-back {
            background: #ddd;
            color: #333;
            text-decoration: none;
            text-align: center;
            display: block;
        }
        
        .btn-back:hover {
            background: #ccc;
        }
        
        .info-box {
            background: #e8eef7;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            border-left: 4px solid #667eea;
            font-size: 13px;
            color: #555;
            line-height: 1.6;
        }
        
        .info-box strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Admin Setup</h1>
        <p class="subtitle">Create or verify your admin account</p>
        
        <div class="status-box">
            <div class="status-label">Current Admin Status:</div>
            <div class="status-value"><?php echo $admin_exists ? '✅ Exists' : '❌ Not Found'; ?></div>
            <?php if ($admin_exists): ?>
                <div class="status-label" style="margin-top: 10px;">Email:</div>
                <div class="status-value"><?php echo $admin_email; ?></div>
            <?php endif; ?>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">✅ <?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if (!$admin_exists): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@chp.com" value="admin@chp.com" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter strong password" value="Admin@123" required>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Administrator" value="Administrator" required>
                </div>
                
                <input type="hidden" name="setup_admin" value="1">
                
                <button type="submit" class="btn-submit">Create Admin Account</button>
            </form>
            
            <div class="info-box">
                <strong>⚠️ Important:</strong> This page should be deleted or protected after creating the admin account for security reasons.
            </div>
        <?php else: ?>
            <div class="info-box">
                <strong>✅ Admin account already exists!</strong><br><br>
                You can now log in at the main login page with your admin credentials.
            </div>
        <?php endif; ?>
        
        <a href="login.php" class="btn-back" style="margin-top: 20px;">← Back to Login</a>
    </div>
</body>
</html>
