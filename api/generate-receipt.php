<?php
require_once '../config.php';

header('Content-Type: text/plain');

if (!isAgentLoggedIn()) {
    echo "Unauthorized access";
    exit;
}

$courier_id = $_GET['courier_id'] ?? '';

if (empty($courier_id)) {
    echo "Courier ID is required";
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, a.agent_name 
        FROM couriers c 
        LEFT JOIN agents a ON c.agent_id = a.agent_id 
        WHERE c.courier_id = ? AND c.agent_id = ?
    ");
    $stmt->execute([$courier_id, $_SESSION['agent_agent_id']]);
    $courier = $stmt->fetch();
    
    if (!$courier) {
        echo "Courier not found or access denied";
        exit;
    }
    
    header('Content-Disposition: attachment; filename="receipt_' . $courier_id . '.txt"');
    
    $content = "FASTTRACK COURIER SERVICES\n";
    $content .= "==========================\n";
    $content .= "COURIER RECEIPT\n";
    $content .= "==========================\n\n";
    
    $content .= "Receipt Date: " . date('d M Y, h:i A') . "\n";
    $content .= "Courier ID: " . $courier['courier_id'] . "\n\n";
    
    $content .= "PARTY DETAILS:\n";
    $content .= "--------------\n";
    $content .= "To Party Name: " . $courier['party_name'] . "\n";
    $content .= "From Party Name: " . $courier['party_name'] . "\n";
    $content .= "Mobile: " . $courier['mobile'] . "\n";
    $content .= "Date: " . date('d M Y', strtotime($courier['created_at'])) . "\n\n";
    
    $content .= "SHIPMENT DETAILS:\n";
    $content .= "-----------------\n";
    $content .= "From City: " . $courier['from_city'] . "\n";
    $content .= "To City: " . $courier['to_city'] . "\n";
    $content .= "Amount: ₹" . number_format($courier['amount'], 2) . "\n";
    $content .= "Status: " . ucfirst($courier['status']) . "\n";
    
    if ($courier['delivery_date']) {
        $content .= "Expected Delivery: " . date('d M Y', strtotime($courier['delivery_date'])) . "\n";
    }
    
    if ($courier['remarks']) {
        $content .= "Remarks: " . $courier['remarks'] . "\n";
    }
    
    $content .= "\nADDRESS:\n";
    $content .= "--------\n";
    $content .= $courier['address'] . "\n\n";
    
    $content .= "AGENT DETAILS:\n";
    $content .= "--------------\n";
    $content .= "Agent: " . $courier['agent_name'] . "\n";
    $content .= "Agent ID: " . $courier['agent_id'] . "\n\n";
    
    $content .= "==========================\n";
    $content .= "Thank you for choosing\n";
    $content .= "FastTrack Courier Services\n";
    $content .= "==========================\n";
    $content .= "Contact: +91 98765 43210\n";
    $content .= "WhatsApp: +91 98765 43210\n";
    $content .= "Email: info@fasttrack.com\n";
    $content .= "Website: www.fasttrack.com\n\n";
    
    $content .= "Track your package online\n";
    $content .= "using Courier ID: " . $courier['courier_id'] . "\n";
    
    echo $content;
    
} catch (Exception $e) {
    echo "Error generating receipt: " . $e->getMessage();
}
?>