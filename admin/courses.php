<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$error = '';
$success = '';
$action = $_GET['action'] ?? '';
$course_id = intval($_GET['id'] ?? 0);
$course = null;
$course_fees = null;

// Handle course update
if ($_POST && isset($_POST['update_course'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $course_id = intval($_POST['course_id'] ?? 0);
        $name = sanitize($_POST['name'] ?? '');
        $description = sanitize($_POST['description'] ?? '');
        $registration_fee = floatval($_POST['registration_fee'] ?? 0);
        $tuition_fee = floatval($_POST['tuition_fee'] ?? 0);
        $promo_percentage = intval($_POST['promo_percentage'] ?? 5);
        
        if (empty($name) || empty($description)) {
            $error = 'Course name and description are required';
        } else {
            $sql = "UPDATE courses SET name = '$name', description = '$description', 
                    registration_fee = $registration_fee, tuition_fee = $tuition_fee,
                    promo_percentage = $promo_percentage WHERE id = $course_id";
            
            if ($conn->query($sql)) {
                $success = 'Course updated successfully!';
                header("Refresh:2; url=courses.php?action=edit&id=$course_id");
            } else {
                $error = 'Error updating course: ' . $conn->error;
            }
        }
    }
}

// Handle fee update/add
if ($_POST && isset($_POST['save_fee'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $course_id = intval($_POST['course_id'] ?? 0);
        $fee_id = intval($_POST['fee_id'] ?? 0);
        $fee_name = sanitize($_POST['fee_name'] ?? '');
        $fee_amount = floatval($_POST['fee_amount'] ?? 0);
        
        if (empty($fee_name) || $fee_amount < 0) {
            $error = 'Fee name and valid amount are required';
        } else {
            if ($fee_id > 0) {
                // Update existing fee
                $sql = "UPDATE course_fees SET fee_name = '$fee_name', fee_amount = $fee_amount 
                        WHERE id = $fee_id AND course_id = $course_id";
            } else {
                // Add new fee
                $sql = "INSERT INTO course_fees (course_id, fee_name, fee_amount) 
                        VALUES ($course_id, '$fee_name', $fee_amount)";
            }
            
            if ($conn->query($sql)) {
                $success = 'Fee saved successfully!';
                header("Refresh:1; url=courses.php?action=edit&id=$course_id");
            } else {
                $error = 'Error saving fee: ' . $conn->error;
            }
        }
    }
}

// Handle fee deletion
if ($_POST && isset($_POST['delete_fee'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $fee_id = intval($_POST['fee_id'] ?? 0);
        $course_id = intval($_POST['course_id'] ?? 0);
        
        if ($conn->query("DELETE FROM course_fees WHERE id = $fee_id AND course_id = $course_id")) {
            $success = 'Fee deleted successfully!';
            header("Refresh:1; url=courses.php?action=edit&id=$course_id");
        } else {
            $error = 'Error deleting fee: ' . $conn->error;
        }
    }
}

// Get courses
$courses = $conn->query("SELECT * FROM courses ORDER BY created_at DESC");

// Get specific course details if editing
if ($action === 'edit' && $course_id > 0) {
    $course = $conn->query("SELECT * FROM courses WHERE id = $course_id")->fetch_assoc();
    $course_fees = $conn->query("SELECT * FROM course_fees WHERE course_id = $course_id ORDER BY id");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses</title>
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
        
        .sidebar {
            position: fixed;
            left: 0;
            top: 60px;
            width: 250px;
            background: #2c3e50;
            height: calc(100vh - 60px);
            padding: 20px 0;
            overflow-y: auto;
        }
        
        .sidebar h3 {
            color: white;
            padding: 15px 20px;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #34495e;
        }
        
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 15px 20px;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar a:hover,
        .sidebar a.active {
            background: #34495e;
            border-left-color: #667eea;
        }
        
        .container {
            margin-left: 250px;
            padding: 20px;
            max-width: 1200px;
            margin-right: auto;
        }
        
        .page-header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-header h2 {
            color: #333;
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
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .course-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s;
        }
        
        .course-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transform: translateY(-3px);
        }
        
        .course-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
        }
        
        .course-card-header h3 {
            margin-bottom: 5px;
            font-size: 18px;
        }
        
        .course-card-header p {
            font-size: 12px;
            opacity: 0.9;
        }
        
        .course-card-body {
            padding: 20px;
        }
        
        .course-info {
            margin-bottom: 15px;
        }
        
        .info-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        
        .info-value {
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .course-card-footer {
            padding: 15px 20px;
            background: #f9f9f9;
            display: flex;
            gap: 10px;
        }
        
        .btn {
            flex: 1;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5568d3;
        }
        
        .btn-danger {
            background: #d32f2f;
            color: white;
        }
        
        .btn-danger:hover {
            background: #b71c1c;
        }
        
        .edit-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h3 {
            background: #667eea;
            color: white;
            padding: 12px 15px;
            margin: -30px -30px 20px -30px;
            border-radius: 8px 8px 0 0;
            font-size: 16px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-row.full {
            grid-template-columns: 1fr;
        }
        
        .form-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        textarea {
            resize: vertical;
            min-height: 100px;
        }
        
        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.1);
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .button-group button,
        .button-group a {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
        }
        
        .button-group .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .button-group .btn-cancel {
            background: #ddd;
            color: #333;
        }
        
        .fees-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .fees-table thead {
            background: #f0f0f0;
        }
        
        .fees-table th {
            padding: 12px;
            text-align: left;
            color: #333;
            font-weight: 600;
            border-bottom: 2px solid #ddd;
        }
        
        .fees-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        .fees-table tr:hover {
            background: #f9f9f9;
        }
        
        .fee-actions {
            display: flex;
            gap: 5px;
        }
        
        .fee-actions button,
        .fee-actions a {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .fee-actions .btn-edit {
            background: #667eea;
            color: white;
        }
        
        .fee-actions .btn-delete {
            background: #d32f2f;
            color: white;
        }
        
        .add-fee-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        
        .add-fee-form .form-row {
            margin-bottom: 15px;
        }
        
        .add-fee-form .button-group {
            margin-top: 15px;
        }
        
        .add-fee-form .button-group button {
            padding: 10px;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .container {
                margin-left: 0;
            }
            
            .courses-grid {
                grid-template-columns: 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
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
    
    <div class="sidebar">
        <h3>Menu</h3>
        <a href="dashboard.php">📊 Dashboard</a>
        <a href="applications.php">📋 Applications</a>
        <a href="enrollments.php">👥 Enrollments</a>
        <a href="courses.php" class="active">🎓 Courses</a>
        <a href="promotions.php">🎯 Promotions</a>
        <a href="password_resets.php">🔐 Password Resets</a>
        <a href="visits.php">📈 Website Visits</a>
    </div>
    
    <div class="container">
        <div class="page-header">
            <h2>Manage Courses</h2>
        </div>
        
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($action === 'edit' && $course): ?>
            <!-- Edit Course Form -->
            <a href="courses.php" class="back-link">← Back to Courses</a>
            
            <div class="edit-form">
                <form method="POST">
                    <div class="form-section">
                        <h3>📚 Course Information</h3>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="name">Course Name</label>
                                <input type="text" id="name" name="name" value="<?php echo $course['name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row full">
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" required><?php echo $course['description']; ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="registration_fee">Registration Fee (₱)</label>
                                <input type="number" id="registration_fee" name="registration_fee" step="0.01" value="<?php echo $course['registration_fee']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="tuition_fee">Tuition Fee (₱)</label>
                                <input type="number" id="tuition_fee" name="tuition_fee" step="0.01" value="<?php echo $course['tuition_fee']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="promo_percentage">Promo Discount (%)</label>
                                <input type="number" id="promo_percentage" name="promo_percentage" min="0" max="100" value="<?php echo $course['promo_percentage']; ?>">
                            </div>
                        </div>
                        
                        <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                        <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>">
                        <input type="hidden" name="update_course" value="1">
                        
                        <div class="button-group">
                            <a href="courses.php" class="btn-cancel">Cancel</a>
                            <button type="submit" class="btn-submit">Save Course</button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Course Fees Section -->
            <div class="edit-form">
                <div class="form-section">
                    <h3>💰 Course Fees & Charges</h3>
                    
                    <?php if ($course_fees && $course_fees->num_rows > 0): ?>
                        <table class="fees-table">
                            <thead>
                                <tr>
                                    <th>Fee Name</th>
                                    <th>Amount (₱)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($fee = $course_fees->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $fee['fee_name']; ?></td>
                                    <td><?php echo number_format($fee['fee_amount'], 2); ?></td>
                                    <td>
                                        <div class="fee-actions">
                                            <a href="courses.php?action=edit&id=<?php echo $course_id; ?>&edit_fee=<?php echo $fee['id']; ?>" class="btn-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this fee?');">
                                                <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                                                <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                                                <input type="hidden" name="fee_id" value="<?php echo $fee['id']; ?>">
                                                <input type="hidden" name="delete_fee" value="1">
                                                <button type="submit" class="btn-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p style="color: #999; margin-bottom: 20px;">No fees added yet.</p>
                    <?php endif; ?>
                    
                    <!-- Add Fee Form -->
                    <div class="add-fee-form">
                        <h4 style="margin-bottom: 15px;">➕ Add New Fee</h4>
                        <form method="POST">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fee_name">Fee Name</label>
                                    <input type="text" id="fee_name" name="fee_name" placeholder="e.g., ID, Scrub Suit, BLS..." required>
                                </div>
                                <div class="form-group">
                                    <label for="fee_amount">Amount (₱)</label>
                                    <input type="number" id="fee_amount" name="fee_amount" step="0.01" min="0" placeholder="0.00" required>
                                </div>
                            </div>
                            
                            <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="hidden" name="save_fee" value="1">
                            
                            <div class="button-group">
                                <button type="submit" class="btn-submit">Add Fee</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- List All Courses -->
            <div class="courses-grid">
                <?php if ($courses && $courses->num_rows > 0): ?>
                    <?php while ($course = $courses->fetch_assoc()): ?>
                    <div class="course-card">
                        <div class="course-card-header">
                            <h3><?php echo $course['name']; ?></h3>
                            <p>ID: <?php echo $course['id']; ?></p>
                        </div>
                        <div class="course-card-body">
                            <div class="course-info">
                                <div class="info-label">Description</div>
                                <div class="info-value"><?php echo substr($course['description'], 0, 100); ?>...</div>
                            </div>
                            <div class="course-info">
                                <div class="info-label">Registration Fee</div>
                                <div class="info-value">₱<?php echo number_format($course['registration_fee'], 2); ?></div>
                            </div>
                            <div class="course-info">
                                <div class="info-label">Tuition Fee</div>
                                <div class="info-value">₱<?php echo number_format($course['tuition_fee'], 2); ?></div>
                            </div>
                            <div class="course-info">
                                <div class="info-label">Total Estimated Cost</div>
                                <div class="info-value">₱<?php echo number_format($course['registration_fee'] + $course['tuition_fee'], 2); ?></div>
                            </div>
                            <div class="course-info">
                                <div class="info-label">Promo Discount</div>
                                <div class="info-value"><?php echo $course['promo_percentage']; ?>%</div>
                            </div>
                        </div>
                        <div class="course-card-footer">
                            <a href="courses.php?action=edit&id=<?php echo $course['id']; ?>" class="btn btn-primary">✏️ Edit</a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center; grid-column: 1 / -1; padding: 40px;">No courses found.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
