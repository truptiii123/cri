<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

// Get dashboard statistics
$stats = [];

if (isAdmin()) {
    // Admin stats
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM couriers");
    $stats['total_couriers'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM couriers WHERE status = 'pending'");
    $stats['pending'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as delivered FROM couriers WHERE status = 'delivered'");
    $stats['delivered'] = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as agents FROM users WHERE role = 'agent'");
    $stats['total_agents'] = $stmt->fetchColumn();
} else {
    // Agent stats
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM couriers WHERE agent_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $stats['total_couriers'] = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM couriers WHERE agent_id = ? AND status = 'pending'");
    $stmt->execute([$_SESSION['user_id']]);
    $stats['pending'] = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as delivered FROM couriers WHERE agent_id = ? AND status = 'delivered'");
    $stmt->execute([$_SESSION['user_id']]);
    $stats['delivered'] = $stmt->fetchColumn();
}

// Get recent activity
$activity_query = "SELECT al.*, u.agent_name FROM activity_logs al 
                   JOIN users u ON al.user_id = u.id 
                   ORDER BY al.created_at DESC LIMIT 10";
$recent_activities = $pdo->query($activity_query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Courier Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: #fff;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
        }
        .activity-item {
            border-left: 3px solid #007bff;
            padding-left: 15px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center text-white mb-4">
                        <h5>Courier System</h5>
                        <small><?= htmlspecialchars($_SESSION['agent_name']) ?></small>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="add_courier.php">
                                <i class="fas fa-plus"></i> Add New Courier
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="manage_couriers.php">
                                <i class="fas fa-boxes"></i> Manage Couriers
                            </a>
                        </li>
                        <?php if (isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_reports.php">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="delivery_selfies.php">
                                <i class="fas fa-images"></i> Delivery Selfies
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <span class="badge bg-primary">
                                <i class="fas fa-clock"></i> 
                                <span id="current-time"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['total_couriers'] ?></h4>
                                        <p>Total Couriers</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-boxes fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['pending'] ?></h4>
                                        <p>Pending</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['delivered'] ?></h4>
                                        <p>Delivered</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-check fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (isAdmin()): ?>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h4><?= $stats['total_agents'] ?></h4>
                                        <p>Total Agents</p>
                                    </div>
                                    <div>
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5><i class="fas fa-history"></i> Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <div id="activity-feed">
                                    <?php foreach ($recent_activities as $activity): ?>
                                    <div class="activity-item">
                                        <strong><?= htmlspecialchars($activity['agent_name']) ?></strong>
                                        <span class="text-muted"><?= htmlspecialchars($activity['action']) ?></span>
                                        <p class="mb-1"><?= htmlspecialchars($activity['description']) ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> 
                                            <?= date('d M Y, h:i A', strtotime($activity['created_at'])) ?>
                                        </small>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update current time
        function updateTime() {
            const now = new Date();
            const options = {
                timeZone: 'Asia/Kolkata',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('current-time').textContent = now.toLocaleString('en-IN', options);
        }
        
        updateTime();
        setInterval(updateTime, 1000);
        
        // Auto-refresh activity feed every 30 seconds
        setInterval(function() {
            fetch('get_recent_activity.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('activity-feed').innerHTML = data;
                });
        }, 30000);
    </script>
</body>
</html>