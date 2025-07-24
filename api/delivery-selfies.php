<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, DELETE, OPTIONS');
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
        $stmt = $pdo->query("
            SELECT 
                c.courier_id,
                c.delivery_person,
                c.delivery_photo as image_path,
                c.created_at as uploaded_at,
                a.agent_name,
                c.id
            FROM couriers c
            LEFT JOIN agents a ON c.agent_id = a.agent_id
            WHERE c.delivery_photo IS NOT NULL
            ORDER BY c.updated_at DESC
        ");
        
        $selfies = $stmt->fetchAll();
        
        // Add full path to images
        foreach ($selfies as &$selfie) {
            $selfie['image_path'] = 'uploads/delivery_photos/' . $selfie['image_path'];
        }
        
        echo json_encode($selfies);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load delivery selfies: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? '';
    
    if (empty($id)) {
        echo json_encode(['success' => false, 'message' => 'Selfie ID is required']);
        exit;
    }
    
    try {
        // Get the photo filename
        $stmt = $pdo->prepare("SELECT delivery_photo FROM couriers WHERE id = ?");
        $stmt->execute([$id]);
        $courier = $stmt->fetch();
        
        if (!$courier || empty($courier['delivery_photo'])) {
            echo json_encode(['success' => false, 'message' => 'Delivery selfie not found']);
            exit;
        }
        
        // Delete physical file
        $file_path = '../uploads/delivery_photos/' . $courier['delivery_photo'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Update database
        $stmt = $pdo->prepare("UPDATE couriers SET delivery_photo = NULL, delivery_person = NULL WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Delivery selfie deleted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to delete selfie: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>