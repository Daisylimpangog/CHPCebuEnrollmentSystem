<?php
require_once 'config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Redirect based on user type
if (isAdmin()) {
    redirect('admin/dashboard.php');
} else {
    redirect('enrollee/dashboard.php');
}
?>
