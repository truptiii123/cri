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
        $stmt = $pdo->query("SELECT DISTINCT state FROM cities ORDER BY state");
        $states = $stmt->fetchAll();
        echo json_encode($states);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load states']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>