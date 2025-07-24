<?php
require_once 'config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $username = trim($input['username'] ?? '');
    $role = trim($input['role'] ?? '');
    
    if (empty($username) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'Username and role are required']);
        exit;
    }
    
    try {
        if ($role === 'admin') {
            // Direct admin login - no password check
            if ($username === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = 1;
                $_SESSION['admin_username'] = 'admin';
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Admin login successful',
                    'redirect' => 'admin-dashboard.php',
                    'user' => [
                        'id' => 1,
                        'username' => 'admin',
                        'role' => 'admin'
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid admin username']);
            }
        } elseif ($role === 'agent') {
            // Direct agent login - no password check
            $stmt = $pdo->prepare("SELECT * FROM agents WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                $_SESSION['agent_logged_in'] = true;
                $_SESSION['agent_id'] = $user['id'];
                $_SESSION['agent_username'] = $user['username'];
                $_SESSION['agent_agent_id'] = $user['agent_id'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Agent login successful',
                    'redirect' => 'agent-dashboard.php',
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'agent_id' => $user['agent_id'],
                        'agent_name' => $user['agent_name'],
                        'role' => 'agent'
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Agent not found or inactive']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid role selected']);
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Login failed. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>