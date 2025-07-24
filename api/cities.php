<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $state = $_GET['state'] ?? '';
        
        if ($state) {
            $stmt = $pdo->prepare("SELECT * FROM cities WHERE state = ? ORDER BY city_name");
            $stmt->execute([$state]);
        } else {
            $stmt = $pdo->query("SELECT * FROM cities ORDER BY state, city_name");
        }
        
        $cities = $stmt->fetchAll();
        echo json_encode($cities);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load cities']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>