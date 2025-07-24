<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
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
        // Get recent courier entries
        $stmt = $pdo->query("
            SELECT 
                c.courier_id,
                c.party_name,
                c.from_city,
                c.to_city,
                c.status,
                c.created_at,
                a.agent_name,
                'courier_created' as activity_type
            FROM couriers c
            LEFT JOIN agents a ON c.agent_id = a.agent_id
            ORDER BY c.created_at DESC
            LIMIT 10
        ");
        $courier_activities = $stmt->fetchAll();
        
        // Get recent tracking updates
        $stmt = $pdo->query("
            SELECT 
                t.courier_id,
                t.location,
                t.status,
                t.timestamp,
                t.updated_by,
                c.party_name,
                'tracking_updated' as activity_type
            FROM tracking t
            LEFT JOIN couriers c ON t.courier_id = c.courier_id
            ORDER BY t.timestamp DESC
            LIMIT 10
        ");
        $tracking_activities = $stmt->fetchAll();
        
        // Combine and sort activities
        $all_activities = array_merge($courier_activities, $tracking_activities);
        
        // Sort by timestamp/created_at
        usort($all_activities, function($a, $b) {
            $time_a = $a['activity_type'] === 'courier_created' ? $a['created_at'] : $a['timestamp'];
            $time_b = $b['activity_type'] === 'courier_created' ? $b['created_at'] : $b['timestamp'];
            return strtotime($time_b) - strtotime($time_a);
        });
        
        // Format activities
        $activities = array_slice(array_map(function($activity) {
            if ($activity['activity_type'] === 'courier_created') {
                return [
                    'title' => 'New Courier Added',
                    'description' => "Courier {$activity['courier_id']} for {$activity['party_name']} from {$activity['from_city']} to {$activity['to_city']}",
                    'timestamp' => date('M d, Y H:i', strtotime($activity['created_at'])),
                    'type' => 'courier'
                ];
            } else {
                return [
                    'title' => 'Tracking Updated',
                    'description' => "Courier {$activity['courier_id']} - {$activity['status']} at {$activity['location']} by {$activity['updated_by']}",
                    'timestamp' => date('M d, Y H:i', strtotime($activity['timestamp'])),
                    'type' => 'tracking'
                ];
            }
        }, $all_activities), 0, 15);
        
        echo json_encode($activities);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load activities: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>