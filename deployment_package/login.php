<?php
require_once 'config.php';
require_once 'helpers.php';

$error = '';
$success = '';

// Handle Registration
if ($_POST && isset($_POST['register'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $full_name = sanitize($_POST['full_name'] ?? '');
        
        // Validation
        if (empty($email) || empty($password) || empty($full_name)) {
            $error = 'All fields are required';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match';
        } else {
            // Check if email already exists
            $check = $conn->query("SELECT id FROM users WHERE email = '$email'");
            if ($check->num_rows > 0) {
                $error = 'Email already registered';
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users (email, password, full_name, user_type, status) 
                        VALUES ('$email', '$hashed_password', '$full_name', 'enrollee', 'active')";
                
                if ($conn->query($sql)) {
                    $success = 'Registration successful! Please log in.';
                    header("Refresh:2; url=login.php");
                } else {
                    $error = 'Registration failed: ' . $conn->error;
                }
            }
        }
    }
}

// Handle Login
if ($_POST && isset($_POST['login'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $error = 'Email and password are required';
        } else {
            $sql = "SELECT id, password, user_type, full_name FROM users WHERE email = '$email'";
            $result = $conn->query($sql);
            
            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_type'] = $user['user_type'];
                    $_SESSION['full_name'] = $user['full_name'];
                    
                    // Track visit
                    trackVisit($user['id']);
                    
                    if ($user['user_type'] === 'admin') {
                        redirect('admin/dashboard.php');
                    } else {
                        redirect('enrollee/dashboard.php');
                    }
                } else {
                    $error = 'Invalid email or password';
                }
            } else {
                $error = 'Invalid email or password';
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
    <title>Enrollment System - Login & Register</title>
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
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 900px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }
        
        .form-section {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .branding {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        
        .branding h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .branding p {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .form-section h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 24px;
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
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        .toggle-form {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        
        .toggle-form a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        .toggle-form a:hover {
            text-decoration: underline;
        }
        
        .form-container {
            display: none;
        }
        
        .form-container.active {
            display: block;
        }
        
        a.forgot-password {
            display: inline-block;
            margin-top: 10px;
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
        }
        
        a.forgot-password:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .branding {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <!-- Login Form -->
            <div id="login-form" class="form-container active">
                <h2>Welcome Back</h2>
                <?php if ($error && strpos($_POST['login'] ?? '', '') === 0): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="login-email">Email Address</label>
                        <input type="email" id="login-email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" required>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                    <input type="hidden" name="login" value="1">
                    
                    <button type="submit">Login</button>
                    <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                </form>
                
                <div class="toggle-form">
                    Don't have an account? <a onclick="toggleForm()">Register here</a>
                </div>
            </div>
            
            <!-- Register Form -->
            <div id="register-form" class="form-container">
                <h2>Create Account</h2>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if ($error && isset($_POST['register'])): ?>
                    <div class="alert alert-error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="register-name">Full Name</label>
                        <input type="text" id="register-name" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">Email Address</label>
                        <input type="email" id="register-email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-password">Password</label>
                        <input type="password" id="register-password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-confirm">Confirm Password</label>
                        <input type="password" id="register-confirm" name="confirm_password" required>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                    <input type="hidden" name="register" value="1">
                    
                    <button type="submit">Register</button>
                </form>
                
                <div class="toggle-form">
                    Already have an account? <a onclick="toggleForm()">Login here</a>
                </div>
            </div>
        </div>
        
        <div class="branding">
            <h1>Healthcare Professionals</h1>
            <p>Enrollment System</p>
            <p style="margin-top: 30px; font-size: 13px;">
                Enroll in our comprehensive healthcare and caregiving programs certified by TESDA.
            </p>
        </div>
    </div>
    
    <script>
        function toggleForm() {
            document.getElementById('login-form').classList.toggle('active');
            document.getElementById('register-form').classList.toggle('active');
        }
    </script>
</body>
</html>
