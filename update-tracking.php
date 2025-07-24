<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isAgentLoggedIn() && !isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $courier_id = $input['courier_id'] ?? '';
    $location = $input['location'] ?? '';
    $status = $input['status'] ?? '';
    $courier_status = $input['courier_status'] ?? '';
    
    if (empty($courier_id) || empty($location) || empty($status)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }
    
    try {
        // Check if courier exists and belongs to agent (if agent is updating)
        if (isAgentLoggedIn()) {
            $agent_id = $_SESSION['agent_agent_id'];
            $stmt = $pdo->prepare("SELECT * FROM couriers WHERE courier_id = ? AND agent_id = ?");
            $stmt->execute([$courier_id, $agent_id]);
            $updated_by = $agent_id;
        } else {
            // Admin can update any courier
            $stmt = $pdo->prepare("SELECT * FROM couriers WHERE courier_id = ?");
            $stmt->execute([$courier_id]);
            $updated_by = 'admin';
        }
        
        $courier = $stmt->fetch();
        
        if (!$courier) {
            echo json_encode(['success' => false, 'message' => 'Courier not found or access denied']);
            exit;
        }
        
        // Add tracking entry
        $stmt = $pdo->prepare("INSERT INTO tracking (courier_id, location, status, updated_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courier_id, $location, $status, $updated_by]);
        
        // Update courier status if provided
        if (!empty($courier_status)) {
            $stmt = $pdo->prepare("UPDATE couriers SET status = ? WHERE courier_id = ?");
            $stmt->execute([$courier_status, $courier_id]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Tracking updated successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update tracking: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>