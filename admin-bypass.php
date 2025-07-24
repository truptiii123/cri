<?php
require_once 'config.php';

// Direct admin login without password
session_start();
$_SESSION['admin_logged_in'] = true;
$_SESSION['admin_id'] = 1;
$_SESSION['admin_username'] = 'admin';

// Redirect to admin dashboard
header('Location: admin-dashboard.php');
exit;
?>