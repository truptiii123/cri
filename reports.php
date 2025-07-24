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
        // Today's revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as today_revenue FROM couriers WHERE DATE(created_at) = CURDATE() AND status = 'delivered'");
        $today_revenue = $stmt->fetch()['today_revenue'];
        
        // This month's revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as month_revenue FROM couriers WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) AND status = 'delivered'");
        $month_revenue = $stmt->fetch()['month_revenue'];
        
        // Total revenue
        $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) as total_revenue FROM couriers WHERE status = 'delivered'");
        $total_revenue = $stmt->fetch()['total_revenue'];
        
        // Agent performance
        $stmt = $pdo->query("
            SELECT 
                a.agent_name,
                a.agent_id,
                COUNT(c.id) as total_couriers,
                COALESCE(SUM(c.amount), 0) as total_business,
                COUNT(CASE WHEN c.status = 'delivered' THEN 1 END) as delivered_count,
                COUNT(CASE WHEN c.status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN c.status = 'in_transit' THEN 1 END) as in_transit_count
            FROM agents a 
            LEFT JOIN couriers c ON a.agent_id = c.agent_id 
            WHERE a.status = 'active'
            GROUP BY a.id, a.agent_name, a.agent_id
            ORDER BY total_business DESC
        ");
        $agent_performance = $stmt->fetchAll();
        
        // Monthly revenue chart data (last 6 months)
        $stmt = $pdo->query("
            SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COALESCE(SUM(amount), 0) as revenue
            FROM couriers 
            WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH) 
            AND status = 'delivered'
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month
        ");
        $monthly_revenue = $stmt->fetchAll();
        
        // Daily revenue for current month
        $stmt = $pdo->query("
            SELECT 
                DAY(created_at) as day,
                COALESCE(SUM(amount), 0) as revenue
            FROM couriers 
            WHERE MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE())
            AND status = 'delivered'
            GROUP BY DAY(created_at)
            ORDER BY day
        ");
        $daily_revenue = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'data' => [
                'today_revenue' => $today_revenue,
                'month_revenue' => $month_revenue,
                'total_revenue' => $total_revenue,
                'agent_performance' => $agent_performance,
                'monthly_revenue' => $monthly_revenue,
                'daily_revenue' => $daily_revenue
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to load reports: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>