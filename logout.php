<?php
require_once 'config.php';

header('Content-Type: application/json');

// Destroy all session data
session_destroy();

echo json_encode(['success' => true, 'message' => 'Logged out successfully']);
?>