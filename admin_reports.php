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

// Get agents for filter dropdown
$agents = $pdo->query("SELECT id, agent_name FROM users WHERE role = 'agent' ORDER BY agent_name")->fetchAll();

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Courier Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-chart-bar"></i> Reports & Analytics</h4>
                        <div>
                            <a href="export_notes.php?<?= http_build_query($_GET) ?>" class="btn btn-success">
                                <i class="fas fa-file-alt"></i> Export to Notes
                            </a>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <form method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="agent" class="form-label">Agent</label>
                                    <select class="form-control" name="agent">
                                        <option value="">All Agents</option>
                                        <?php foreach ($agents as $agent): ?>
                                        <option value="<?= $agent['id'] ?>" <?= $agent_filter == $agent['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agent['agent_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control" name="status">
                                        <option value="">All Status</option>
                                        <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="in_transit" <?= $status_filter === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                                        <option value="delivered" <?= $status_filter === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="cancelled" <?= $status_filter === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="date_from" class="form-label">From Date</label>
                                    <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($date_from) ?>">
                                </div>
                                <div class="col-md-2">
                                    <label for="date_to" class="form-label">To Date</label>
                                    <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($date_to) ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="admin_reports.php" class="btn btn-secondary">
                                            <i class="fas fa-times"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <div class="card text-white bg-primary">
                                    <div class="card-body text-center">
                                        <h4><?= $stats['total'] ?></h4>
                                        <p>Total</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-white bg-secondary">
                                    <div class="card-body text-center">
                                        <h4><?= $stats['pending'] ?></h4>
                                        <p>Pending</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-white bg-warning">
                                    <div class="card-body text-center">
                                        <h4><?= $stats['in_transit'] ?></h4>
                                        <p>In Transit</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-white bg-success">
                                    <div class="card-body text-center">
                                        <h4><?= $stats['delivered'] ?></h4>
                                        <p>Delivered</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-white bg-danger">
                                    <div class="card-body text-center">
                                        <h4><?= $stats['cancelled'] ?></h4>
                                        <p>Cancelled</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="card text-white bg-info">
                                    <div class="card-body text-center">
                                        <h4>₹<?= number_format($stats['total_amount'], 2) ?></h4>
                                        <p>Total Amount</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Couriers Table -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Courier ID</th>
                                        <th>From/To Party</th>
                                        <th>Sender/Receiver</th>
                                        <th>Weight</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Agent</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($couriers as $courier): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($courier['courier_id']) ?></strong></td>
                                        <td>
                                            <small>
                                                <strong>From:</strong> <?= htmlspecialchars($courier['from_party_name']) ?><br>
                                                <strong>To:</strong> <?= htmlspecialchars($courier['to_party_name']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>Sender:</strong> <?= htmlspecialchars($courier['sender_name']) ?><br>
                                                <strong>Receiver:</strong> <?= htmlspecialchars($courier['receiver_name']) ?>
                                            </small>
                                        </td>
                                        <td><?= $courier['weight'] ?> kg</td>
                                        <td>₹<?= number_format($courier['amount'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $courier['status'] === 'delivered' ? 'success' : 
                                                ($courier['status'] === 'in_transit' ? 'warning' : 
                                                ($courier['status'] === 'cancelled' ? 'danger' : 'secondary')) 
                                            ?>">
                                                <?= ucfirst($courier['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($courier['agent_name']) ?></td>
                                        <td><?= date('d M Y', strtotime($courier['created_date'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <?php if (empty($couriers)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No couriers found matching the selected criteria.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>