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
    $password = trim($input['password'] ?? '');
    $role = trim($input['role'] ?? '');
    
    if (empty($username) || empty($password) || empty($role)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    try {
        if ($role === 'admin') {
            $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Check if password is hashed or plain text
                $passwordMatch = false;
                if (password_verify($password, $user['password'])) {
                    $passwordMatch = true;
                } elseif ($password === $user['password']) {
                    // For plain text passwords (temporary)
                    $passwordMatch = true;
                }
                
                if ($passwordMatch) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['admin_username'] = $user['username'];
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
                        'redirect' => 'admin-dashboard.php',
                        'user' => [
                            'id' => $user['id'],
                            'username' => $user['username'],
                            'role' => 'admin'
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
            }
        } elseif ($role === 'agent') {
            $stmt = $pdo->prepare("SELECT * FROM agents WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Check if password is hashed or plain text
                $passwordMatch = false;
                if (password_verify($password, $user['password'])) {
                    $passwordMatch = true;
                } elseif ($password === $user['password']) {
                    // For plain text passwords (temporary)
                    $passwordMatch = true;
                }
                
                if ($passwordMatch) {
                    $_SESSION['agent_logged_in'] = true;
                    $_SESSION['agent_id'] = $user['id'];
                    $_SESSION['agent_username'] = $user['username'];
                    $_SESSION['agent_agent_id'] = $user['agent_id'];
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'Login successful',
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
                    echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid username or password or account disabled']);
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