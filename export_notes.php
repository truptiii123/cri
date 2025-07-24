<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireAdmin();

// Get filter parameters
$agent_filter = $_GET['agent'] ?? '';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';

// Build query
$where_conditions = [];
$params = [];

if ($agent_filter) {
    $where_conditions[] = "c.agent_id = ?";
    $params[] = $agent_filter;
}

if ($status_filter) {
    $where_conditions[] = "c.status = ?";
    $params[] = $status_filter;
}

if ($date_from) {
    $where_conditions[] = "c.created_date >= ?";
    $params[] = $date_from;
}

if ($date_to) {
    $where_conditions[] = "c.created_date <= ?";
    $params[] = $date_to;
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get couriers with filters
$query = "SELECT c.*, u.agent_name FROM couriers c 
          JOIN users u ON c.agent_id = u.id 
          $where_clause 
          ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$couriers = $stmt->fetchAll();

// Get statistics
$stats_query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status = 'in_transit' THEN 1 ELSE 0 END) as in_transit,
    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
    SUM(amount) as total_amount
    FROM couriers c $where_clause";

$stmt = $pdo->prepare($stats_query);
$stmt->execute($params);
$stats = $stmt->fetch();

// Generate text content
$content = "COURIER TRACKING REPORT\n";
$content .= "Generated on: " . date('d M Y, h:i A') . "\n";
$content .= "========================================\n\n";

$content .= "STATISTICS:\n";
$content .= "Total Couriers: " . $stats['total'] . "\n";
$content .= "Pending: " . $stats['pending'] . "\n";
$content .= "In Transit: " . $stats['in_transit'] . "\n";
$content .= "Delivered: " . $stats['delivered'] . "\n";
$content .= "Cancelled: " . $stats['cancelled'] . "\n";
$content .= "Total Amount: Rs. " . number_format($stats['total_amount'], 2) . "\n\n";

$content .= "COURIER DETAILS:\n";
$content .= "========================================\n";

foreach ($couriers as $courier) {
    $content .= "Courier ID: " . $courier['courier_id'] . "\n";
    $content .= "From Party: " . $courier['from_party_name'] . "\n";
    $content .= "To Party: " . $courier['to_party_name'] . "\n";
    $content .= "Sender: " . $courier['sender_name'] . "\n";
    $content .= "Receiver: " . $courier['receiver_name'] . "\n";
    $content .= "Weight: " . $courier['weight'] . " kg\n";
    $content .= "Amount: Rs. " . number_format($courier['amount'], 2) . "\n";
    $content .= "Status: " . ucfirst($courier['status']) . "\n";
    $content .= "Agent: " . $courier['agent_name'] . "\n";
    $content .= "Date: " . date('d M Y', strtotime($courier['created_date'])) . "\n";
    $content .= "----------------------------------------\n";
}

// Set headers for download
header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="courier_report_' . date('Y-m-d_H-i-s') . '.txt"');
header('Content-Length: ' . strlen($content));

echo $content;
exit;
?>