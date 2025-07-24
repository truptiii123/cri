<?php
require_once '../config.php';

if (!isAdminLoggedIn()) {
    header('HTTP/1.1 403 Forbidden');
    exit('Unauthorized');
}

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="couriers_' . date('Y-m-d') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get all couriers with agent information
    $stmt = $pdo->query("
        SELECT 
            c.courier_id,
            c.party_name,
            c.mobile,
            c.address,
            c.from_city,
            c.to_city,
            c.delivery_date,
            c.amount,
            c.status,
            c.remarks,
            c.created_at,
            a.agent_name,
            c.delivery_person,
            c.delivery_photo
        FROM couriers c
        LEFT JOIN agents a ON c.agent_id = a.agent_id
        ORDER BY c.created_at DESC
    ");
    $couriers = $stmt->fetchAll();
    
    // Start HTML table for Excel
    echo '<table border="1">';
    echo '<tr>';
    echo '<th>Courier ID</th>';
    echo '<th>Party Name</th>';
    echo '<th>Mobile</th>';
    echo '<th>Address</th>';
    echo '<th>From City</th>';
    echo '<th>To City</th>';
    echo '<th>Delivery Date</th>';
    echo '<th>Amount</th>';
    echo '<th>Status</th>';
    echo '<th>Remarks</th>';
    echo '<th>Agent Name</th>';
    echo '<th>Delivery Person</th>';
    echo '<th>Created Date</th>';
    echo '</tr>';
    
    foreach ($couriers as $courier) {
        echo '<tr>';
        echo '<td>' . htmlspecialchars($courier['courier_id']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['party_name']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['mobile']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['address']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['from_city']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['to_city']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['delivery_date']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['amount']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['status']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['remarks']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['agent_name']) . '</td>';
        echo '<td>' . htmlspecialchars($courier['delivery_person'] ?? 'N/A') . '</td>';
        echo '<td>' . date('Y-m-d H:i:s', strtotime($courier['created_at'])) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>