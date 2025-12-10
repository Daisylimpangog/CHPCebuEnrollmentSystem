<?php
require_once '../config.php';
require_once '../helpers.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$success = '';
$error = '';

// Handle password reset approval
if ($_POST && isset($_POST['approve_reset'])) {
    if (!verifyToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security token invalid';
    } else {
        $reset_id = intval($_POST['reset_id'] ?? 0);
        $action = sanitize($_POST['action'] ?? '');
        
        // Get the password reset request details
        $reset_result = $conn->query("SELECT * FROM password_reset_requests WHERE id = $reset_id");
        
        if ($reset_result && $reset_result->num_rows > 0) {
            $reset = $reset_result->fetch_assoc();
            
            if ($action === 'approve') {
                // Use the pre-hashed password from the database
                $new_password_hash = $reset['new_password_hash'] ?? '';
                
                if (empty($new_password_hash)) {
                    $error = 'Password hash not found in request';
                } else {
                    $user_id = $reset['user_id'];
                    
                    // Update the user's password with the pre-hashed password using prepared statement
                    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->bind_param("si", $new_password_hash, $user_id);
                    
                    if ($stmt->execute()) {
                        // Mark the reset request as approved
                        $admin_id = $_SESSION['user_id'];
                        $approve_stmt = $conn->prepare("UPDATE password_reset_requests SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
                        $approve_stmt->bind_param("ii", $admin_id, $reset_id);
                        $approve_stmt->execute();
                        $approve_stmt->close();
                        
                        $success = '✅ Password has been successfully updated! User can now log in with their new password.';
                    } else {
                        $error = 'Error updating password: ' . $stmt->error;
                    }
                    $stmt->close();
                }
            } elseif ($action === 'reject') {
                // Reject the reset request using prepared statement
                $admin_id = $_SESSION['user_id'];
                $reject_stmt = $conn->prepare("UPDATE password_reset_requests SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
                $reject_stmt->bind_param("ii", $admin_id, $reset_id);
                $reject_stmt->execute();
                $reject_stmt->close();
                $success = '❌ Password reset request has been rejected.';
            }
        } else {
            $error = 'Password reset request not found';
        }
    }
}

$resets = $conn->query(
    "SELECT pr.*, u.full_name, u.email FROM password_reset_requests pr 
     JOIN users u ON pr.user_id = u.id 
     ORDER BY pr.requested_at DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Resets - Admin</title>
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
            border-left: 3px solid transparent;
        }
        
        .sidebar a.active {
            background: #dbeafe;
            color: #10b981;
            border-left-color: #10b981;
        }
        
        .main {
            flex: 1;
            overflow-y: auto;
            padding: 30px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: #f9f9f9;
            border-bottom: 2px solid #ddd;
        }
        
        th {
            padding: 15px;
            text-align: left;
            color: #333;
            font-weight: 600;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .btn-group {
            display: flex;
            gap: 5px;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        
        .btn-approve {
            background: #28a745;
            color: white;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.8;
        }
        
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 0;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            opacity: 0.8;
        }
        
        .form-section {
            padding: 25px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 13px;
        }
        
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 5px rgba(16, 185, 129, 0.3);
        }
        
        .form-group input:read-only {
            background-color: #f5f5f5;
            cursor: not-allowed;
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            padding: 20px;
            border-top: 1px solid #eee;
            justify-content: flex-end;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .btn-reject {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-approve {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        
        .btn-approve:hover {
            opacity: 0.9;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
        }
        
        .btn-view {
            background: #0d6efd;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }
        
        .btn-view:hover {
            background: #0b5ed7;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
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
            <a href="promotions.php">🎯 Promotions</a>
            <a href="password_resets.php" class="active">🔐 Password Resets</a>
            <a href="visits.php">📈 Website Visits</a>
        </div>
        
        <div class="main">
            <h2 style="margin-bottom: 30px; color: #333;">Password Reset Requests</h2>
            
            <?php if ($success): ?>
                <div class="alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Enrollee Name</th>
                            <th>Email</th>
                            <th>Requested Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($resets && $resets->num_rows > 0):
                            while ($row = $resets->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo date('M d, Y h:i A', strtotime($row['requested_at'])); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $row['status']; ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <button class="btn btn-view" onclick="openResetModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                        View & Approve
                                    </button>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">
                                        <?php echo ucfirst($row['status']); ?><br>
                                        <?php echo date('M d, Y', strtotime($row['reviewed_at'])); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <tr>
                            <td colspan="5"><div class="no-data">No password reset requests</div></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Password Reset Modal -->
    <div id="resetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>🔐 Approve Password Reset</h3>
                <button class="modal-close" onclick="closeResetModal()">×</button>
            </div>
            
            <form method="POST" id="resetForm">
                <div class="form-section">
                    <div class="form-group">
                        <label>Enrollee Name</label>
                        <input type="text" id="modalName" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="modalEmail" readonly>
                    </div>
                    
                    <div style="background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #10b981;">
                        <p style="color: #2e7d32; margin: 0 0 10px 0; font-weight: 600;">
                            🔑 Password Ready for Approval
                        </p>
                        <small style="color: #555; line-height: 1.6;">
                            The user has submitted a new password. Click the "Approve & Update Password" button below to apply this password change. The user will be able to log in with their new password immediately.
                        </small>
                    </div>
                    
                    <input type="hidden" name="reset_id" id="modalResetId">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
                    <input type="hidden" name="approve_reset" value="1">
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-cancel" onclick="closeResetModal()">Cancel</button>
                        <button type="button" class="btn btn-reject" onclick="rejectReset(document.getElementById('modalResetId').value)">Reject Request</button>
                        <button type="submit" class="btn btn-approve" onclick="handleApprove(event)">Approve & Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function openResetModal(data) {
            document.getElementById('modalName').value = data.full_name;
            document.getElementById('modalEmail').value = data.email;
            document.getElementById('modalResetId').value = data.id;
            document.getElementById('resetModal').style.display = 'flex';
        }
        
        function closeResetModal() {
            document.getElementById('resetModal').style.display = 'none';
        }
        
        function handleApprove(event) {
            event.preventDefault();
            
            if (!confirm('Are you sure you want to approve this password reset and apply the new password?')) {
                return;
            }
            
            document.getElementById('resetForm').submit();
        }
        
        function rejectReset(resetId) {
            if (!confirm('Are you sure you want to reject this password reset request?')) {
                return;
            }
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="reset_id" value="${resetId}">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="approve_reset" value="1">
                <input type="hidden" name="csrf_token" value="<?php echo generateToken(); ?>">
            `;
            document.body.appendChild(form);
            form.submit();
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('resetModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
