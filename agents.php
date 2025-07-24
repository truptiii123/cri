<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
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
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $stmt = $pdo->prepare("SELECT * FROM agents WHERE id = ?");
            $stmt->execute([$id]);
            $agent = $stmt->fetch();
            echo json_encode($agent);
        } else {
            $stmt = $pdo->query("SELECT * FROM agents ORDER BY created_at DESC");
            $agents = $stmt->fetchAll();
            echo json_encode($agents);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load agents']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $agent_name = trim($input['agent_name'] ?? '');
    $username = trim($input['username'] ?? '');
    $password = trim($input['password'] ?? '');
    $mobile = trim($input['mobile'] ?? '');
    $city = trim($input['city'] ?? '');
    
    if (empty($agent_name) || empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Agent name, username and password are required']);
        exit;
    }
    
    try {
        // Check if username already exists
        $stmt = $pdo->prepare("SELECT id FROM agents WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            exit;
        }
        
        $agent_id = generateAgentId($agent_name);
        
        $stmt = $pdo->prepare("INSERT INTO agents (agent_id, agent_name, username, password, mobile, city, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$agent_id, $agent_name, $username, $password, $mobile, $city, $_SESSION['admin_id']]);
        
        echo json_encode(['success' => true, 'message' => 'Agent added successfully', 'agent_id' => $agent_id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to add agent: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Agent ID is required']);
        exit;
    }
    
    try {
        // Check if agent has couriers
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM couriers WHERE agent_id = (SELECT agent_id FROM agents WHERE id = ?)");
        $stmt->execute([$id]);
        $count = $stmt->fetch()['count'];
        
        if ($count > 0) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete agent with existing couriers']);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM agents WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Agent deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete agent: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>