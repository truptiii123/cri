<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'vendor/autoload.php';

use TCPDF;

requireAdmin();

// Get filter parameters (same as admin_reports.php)
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

// Create PDF
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('Courier Tracking System');
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Courier Report - ' . date('Y-m-d'));

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);

// Create HTML content
$html = '
<style>
    .header { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; }
    .stats { margin-bottom: 20px; }
    .stat-item { display: inline-block; margin-right: 20px; padding: 5px; border: 1px solid #ccc; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ddd; padding: 5px; font-size: 8px; }
    th { background-color: #f2f2f2; font-weight: bold; }
    .status-pending { background-color: #f8f9fa; }
    .status-in_transit { background-color: #fff3cd; }
    .status-delivered { background-color: #d1edff; }
    .status-cancelled { background-color: #f8d7da; }
</style>

<div class="header">COURIER TRACKING REPORT</div>
<div style="text-align: center; margin-bottom: 20px;">Generated on: ' . date('d M Y, h:i A') . '</div>

<div class="stats">
    <div class="stat-item">Total: ' . $stats['total'] . '</div>
    <div class="stat-item">Pending: ' . $stats['pending'] . '</div>
    <div class="stat-item">In Transit: ' . $stats['in_transit'] . '</div>
    <div class="stat-item">Delivered: ' . $stats['delivered'] . '</div>
    <div class="stat-item">Cancelled: ' . $stats['cancelled'] . '</div>
    <div class="stat-item">Total Amount: ₹' . number_format($stats['total_amount'], 2) . '</div>
</div>

<table>
    <thead>
        <tr>
            <th>Courier ID</th>
            <th>From Party</th>
            <th>To Party</th>
            <th>Sender</th>
            <th>Receiver</th>
            <th>Weight</th>
            <th>Amount</th>
            <th>Status</th>
            <th>Agent</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>';

foreach ($couriers as $courier) {
    $html .= '<tr class="status-' . $courier['status'] . '">
        <td>' . htmlspecialchars($courier['courier_id']) . '</td>
        <td>' . htmlspecialchars($courier['from_party_name']) . '</td>
        <td>' . htmlspecialchars($courier['to_party_name']) . '</td>
        <td>' . htmlspecialchars($courier['sender_name']) . '</td>
        <td>' . htmlspecialchars($courier['receiver_name']) . '</td>
        <td>' . $courier['weight'] . ' kg</td>
        <td>₹' . number_format($courier['amount'], 2) . '</td>
        <td>' . ucfirst($courier['status']) . '</td>
        <td>' . htmlspecialchars($courier['agent_name']) . '</td>
        <td>' . date('d M Y', strtotime($courier['created_date'])) . '</td>
    </tr>';
}

$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Clean output buffer
ob_clean();

// Output PDF
$filename = 'courier_report_' . date('Y-m-d_H-i-s') . '.pdf';
$pdf->Output($filename, 'D');
?>