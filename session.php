<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header('Location: dashboard.php');
        exit;
    }
}

function logActivity($pdo, $action, $description, $courier_id = null) {
    if (!isLoggedIn()) return;
    
    $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, description, courier_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $action, $description, $courier_id]);
}
?>