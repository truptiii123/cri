<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

// Get recent activity
$activity_query = "SELECT al.*, u.agent_name FROM activity_logs al 
                   JOIN users u ON al.user_id = u.id 
                   ORDER BY al.created_at DESC LIMIT 10";
$recent_activities = $pdo->query($activity_query)->fetchAll();

foreach ($recent_activities as $activity): ?>
<div class="activity-item">
    <strong><?= htmlspecialchars($activity['agent_name']) ?></strong>
    <span class="text-muted"><?= htmlspecialchars($activity['action']) ?></span>
    <p class="mb-1"><?= htmlspecialchars($activity['description']) ?></p>
    <small class="text-muted">
        <i class="fas fa-clock"></i> 
        <?= date('d M Y, h:i A', strtotime($activity['created_at'])) ?>
    </small>
</div>
<?php endforeach;
?>