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
        $statusFilter = $_GET['status'] ?? '';
        $dateFilter = $_GET['date'] ?? '';
        
        $whereConditions = [];
        $params = [];
        
        if ($statusFilter) {
            $whereConditions[] = "c.status = ?";
            $params[] = $statusFilter;
        }
        
        if ($dateFilter) {
            $whereConditions[] = "DATE(c.created_at) = ?";
            $params[] = $dateFilter;
        }
        
        $whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
        
        $query = "SELECT c.*, a.agent_name 
                  FROM couriers c 
                  LEFT JOIN agents a ON c.agent_id = a.agent_id 
                  $whereClause 
                  ORDER BY c.created_at DESC";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $couriers = $stmt->fetchAll();
        
        echo json_encode($couriers);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load couriers: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>