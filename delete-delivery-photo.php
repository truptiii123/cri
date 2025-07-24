<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if (!isAdminLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $courier_id = $_GET['courier_id'] ?? '';
    
    if (empty($courier_id)) {
        echo json_encode(['success' => false, 'message' => 'Courier ID is required']);
        exit;
    }
    
    try {
        // Get current photo filename
        $stmt = $pdo->prepare("SELECT delivery_photo FROM couriers WHERE courier_id = ?");
        $stmt->execute([$courier_id]);
        $courier = $stmt->fetch();
        
        if (!$courier || empty($courier['delivery_photo'])) {
            echo json_encode(['success' => false, 'message' => 'No delivery photo found']);
            exit;
        }
        
        // Delete physical file
        $file_path = '../uploads/delivery_photos/' . $courier['delivery_photo'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Update database
        $stmt = $pdo->prepare("UPDATE couriers SET delivery_photo = NULL, delivery_person = NULL WHERE courier_id = ?");
        $stmt->execute([$courier_id]);
        
        echo json_encode(['success' => true, 'message' => 'Delivery photo deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete photo: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>