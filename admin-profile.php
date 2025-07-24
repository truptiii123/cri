<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT id, username FROM admin WHERE id = ?");
        $stmt->execute([$_SESSION['admin_id']]);
        $admin = $stmt->fetch();
        
        echo json_encode(['success' => true, 'data' => $admin]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load profile']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $username = $input['username'] ?? '';
    $new_password = $input['new_password'] ?? '';
    
    if (empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Username is required']);
        exit;
    }
    
    try {
        if (!empty($new_password)) {
            $stmt = $pdo->prepare("UPDATE admin SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$username, $new_password, $_SESSION['admin_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE admin SET username = ? WHERE id = ?");
            $stmt->execute([$username, $_SESSION['admin_id']]);
        }
        
        $_SESSION['admin_username'] = $username;
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update profile: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>