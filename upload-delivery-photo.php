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
    $courier_id = $_POST['courier_id'] ?? '';
    $delivery_person = $_POST['delivery_person'] ?? '';
    
    if (empty($courier_id) || empty($delivery_person)) {
        echo json_encode(['success' => false, 'message' => 'Courier ID and delivery person name are required']);
        exit;
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['delivery_photo']) || $_FILES['delivery_photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Please upload a delivery photo']);
        exit;
    }
    
    $file = $_FILES['delivery_photo'];
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validate file type
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Only JPG, PNG and GIF files are allowed']);
        exit;
    }
    
    // Validate file size
    if ($file['size'] > $max_size) {
        echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
        exit;
    }
    
    try {
        // Create uploads directory if it doesn't exist
        $upload_dir = '../uploads/delivery_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $courier_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Update courier record
            $stmt = $pdo->prepare("UPDATE couriers SET delivery_person = ?, delivery_photo = ?, status = 'delivered' WHERE courier_id = ?");
            $stmt->execute([$delivery_person, $filename, $courier_id]);
            
            // Add tracking entry
            $updated_by = isAdminLoggedIn() ? 'admin' : $_SESSION['agent_agent_id'];
            $stmt = $pdo->prepare("INSERT INTO tracking (courier_id, location, status, updated_by) VALUES (?, ?, ?, ?)");
            $stmt->execute([$courier_id, 'Delivered', 'Package delivered with photo proof', $updated_by]);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Delivery photo uploaded successfully',
                'filename' => $filename
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to update delivery: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>