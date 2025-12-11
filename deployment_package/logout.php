<?php
require_once 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

session_destroy();
redirect('login.php');
?>
