<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $agent_id = $input['id'] ?? '';
    $agent_name = $input['agent_name'] ?? '';
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    $mobile = $input['mobile'] ?? '';
    $city = $input['city'] ?? '';
    $status = $input['status'] ?? 'active';
    
    if (empty($agent_id) || empty($agent_name) || empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Required fields are missing']);
        exit;
    }
    
    try {
        // Check if agent exists
        $stmt = $pdo->prepare("SELECT * FROM agents WHERE id = ?");
        $stmt->execute([$agent_id]);
        $agent = $stmt->fetch();
        
        if (!$agent) {
            echo json_encode(['success' => false, 'message' => 'Agent not found']);
            exit;
        }
        
        // Update agent
        if (!empty($password)) {
            $stmt = $pdo->prepare("UPDATE agents SET agent_name = ?, username = ?, password = ?, mobile = ?, city = ?, status = ? WHERE id = ?");
            $stmt->execute([$agent_name, $username, $password, $mobile, $city, $status, $agent_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE agents SET agent_name = ?, username = ?, mobile = ?, city = ?, status = ? WHERE id = ?");
            $stmt->execute([$agent_name, $username, $mobile, $city, $status, $agent_id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Agent updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update agent: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>