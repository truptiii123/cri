<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isAgentLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$agent_id = $_SESSION['agent_agent_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $recent = $_GET['recent'] ?? null;
        
        if ($recent) {
            $stmt = $pdo->prepare("SELECT * FROM couriers WHERE agent_id = ? ORDER BY created_at DESC LIMIT ?");
            $stmt->execute([$agent_id, (int)$recent]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM couriers WHERE agent_id = ? ORDER BY created_at DESC");
            $stmt->execute([$agent_id]);
        }
        
        $couriers = $stmt->fetchAll();
        echo json_encode($couriers);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load couriers']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    
    try {
        $courier_id = generateCourierId($input['party_name']);
        $delivery_date = $input['delivery_date'] ?: date('Y-m-d', strtotime('+3 days'));
        
        $stmt = $pdo->prepare("INSERT INTO couriers (courier_id, party_name, mobile, address, from_city, to_city, delivery_date, amount, remarks, agent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $courier_id,
            $input['party_name'],
            $input['mobile'],
            $input['address'],
            $input['from_city'],
            $input['to_city'],
            $delivery_date,
            $input['amount'] ?? 0,
            $input['remarks'] ?? '',
            $agent_id
        ]);
        
        // Add initial tracking entry
        $stmt = $pdo->prepare("INSERT INTO tracking (courier_id, location, status, updated_by) VALUES (?, ?, ?, ?)");
        $stmt->execute([$courier_id, $input['from_city'], 'Package received', $agent_id]);
        
        echo json_encode(['success' => true, 'courier_id' => $courier_id]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to add courier: ' . $e->getMessage()]);
    }
}
?>