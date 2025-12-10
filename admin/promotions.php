<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

// Handle promotion upload
if ($_POST && isset($_POST['add_promotion'])) {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $discount = intval($_POST['discount'] ?? 0);
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    
    if (empty($title)) {
        $error = 'Title is required';
    } else {
        $picture_path = '';
        
        // Handle file upload
        if (isset($_FILES['picture']) && $_FILES['picture']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['picture']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = 'promo_' . time() . '.' . $ext;
                $upload_path = 'uploads/promotions/' . $new_filename;
                
                if (!is_dir('uploads/promotions')) {
                    mkdir('uploads/promotions', 0777, true);
                }
                
                if (move_uploaded_file($_FILES['picture']['tmp_name'], $upload_path)) {
                    $picture_path = $upload_path;
                }
            }
        }
        
        $sql = "INSERT INTO promotions (title, description, picture_path, discount_percentage, start_date, end_date, is_active, created_by) 
                VALUES ('$title', '$description', '$picture_path', $discount, '$start_date', '$end_date', 1, {$_SESSION['user_id']})";
        
        if ($conn->query($sql)) {
            $success = 'Promotion added successfully!';
        } else {
            $error = 'Error adding promotion: ' . $conn->error;
        }
    }
}

$promotions = $conn->query("SELECT * FROM promotions ORDER BY created_at DESC");
$error = $error ?? '';
$success = $success ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promotions - Admin</title>
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
        
        .container {
            display: flex;
            height: calc(100vh - 60px);
        }
        
        .sidebar {
            width: 250px;
            background: white;
            border-right: 1px solid #ddd;
            padding: 20px;
            overflow-y: auto;
        }
        
        .sidebar h3 {
            color: #999;
            margin-bottom: 15px;
            font-size: 14px;
            text-transform: uppercase;
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
        
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-error {
            background: #fee;
            color: #c33;
        }
        
        .alert-success {
            background: #efe;
            color: #3c3;
        }
        
        .form-card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-card h3 {
            color: #333;
            margin-bottom: 20px;
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
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        input[type="text"],
        input[type="number"],
        input[type="date"],
        textarea {
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
        textarea:focus {
            outline: none;
            border-color: #667eea;
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
        
        .promotions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .promo-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .promo-image {
            width: 100%;
            height: 150px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .promo-content {
            padding: 15px;
        }
        
        .promo-content h4 {
            color: #333;
            margin-bottom: 8px;
        }
        
        .promo-content p {
            color: #666;
            font-size: 13px;
            margin-bottom: 10px;
            line-height: 1.5;
        }
        
        .promo-discount {
            background: #ffc107;
            color: #000;
            padding: 8px;
            text-align: center;
            font-weight: 600;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .promo-dates {
            font-size: 12px;
            color: #999;
            margin-bottom: 10px;
        }
        
        .promo-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #e2e3e5;
            color: #383d41;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <h1>Admin Dashboard</h1>
        <div>
            <a href="../logout.php" style="color: white;">Logout</a>
        </div>
    </div>
    
    <div class="container">
        <div class="sidebar">
            <h3>Menu</h3>
            <a href="dashboard.php">📊 Dashboard</a>
            <a href="applications.php">📋 Applications</a>
            <a href="enrollments.php">👥 Enrollments</a>
            <a href="courses.php">🎓 Courses</a>
            <a href="promotions.php" class="active">🎯 Promotions</a>
            <a href="password_resets.php">🔐 Password Resets</a>
            <a href="visits.php">📈 Website Visits</a>
        </div>
        
        <div class="main">
            <h2 style="margin-bottom: 20px; color: #333;">Promotions</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="form-card">
                <h3>Add New Promotion</h3>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="title">Title *</label>
                            <input type="text" id="title" name="title" required>
                        </div>
                        <div class="form-group">
                            <label for="discount">Discount Percentage</label>
                            <input type="number" id="discount" name="discount" min="0" max="100">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" id="start_date" name="start_date">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" id="end_date" name="end_date">
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-row full">
                        <div class="form-group">
                            <label for="picture">Promotion Picture</label>
                            <input type="file" id="picture" name="picture" accept="image/*">
                        </div>
                    </div>
                    
                    <input type="hidden" name="add_promotion" value="1">
                    <button type="submit" class="btn">Add Promotion</button>
                </form>
            </div>
            
            <h3 style="margin: 30px 0 20px 0; color: #333;">Active Promotions</h3>
            
            <div class="promotions-grid">
                <?php
                if ($promotions && $promotions->num_rows > 0):
                    while ($promo = $promotions->fetch_assoc()):
                ?>
                <div class="promo-card">
                    <?php if (!empty($promo['picture_path'])): ?>
                        <div class="promo-image">
                            <img src="<?php echo $promo['picture_path']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                    <?php else: ?>
                        <div class="promo-image"><?php echo $promo['title']; ?></div>
                    <?php endif; ?>
                    
                    <div class="promo-content">
                        <h4><?php echo $promo['title']; ?></h4>
                        <p><?php echo $promo['description']; ?></p>
                        
                        <?php if ($promo['discount_percentage']): ?>
                            <div class="promo-discount"><?php echo $promo['discount_percentage']; ?>% Off</div>
                        <?php endif; ?>
                        
                        <div class="promo-dates">
                            <?php echo $promo['start_date']; ?> to <?php echo $promo['end_date']; ?>
                        </div>
                        
                        <span class="promo-status <?php echo $promo['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                            <?php echo $promo['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                    </div>
                </div>
                <?php
                    endwhile;
                else:
                ?>
                <p style="color: #999;">No promotions added yet</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
