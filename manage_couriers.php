<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$success = '';
$error = '';

// Handle status updates
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_status') {
        $courier_id = $_POST['courier_id'] ?? '';
        $status = $_POST['status'] ?? '';
        $location = $_POST['location'] ?? '';
        $remarks = $_POST['remarks'] ?? '';
        
        if ($courier_id && $status) {
            try {
                // Check if courier exists and user has permission
                if (isAdmin()) {
                    $check_stmt = $pdo->prepare("SELECT id FROM couriers WHERE courier_id = ?");
                } else {
                    $check_stmt = $pdo->prepare("SELECT id FROM couriers WHERE courier_id = ? AND agent_id = ?");
                }
                
                if (isAdmin()) {
                    $check_stmt->execute([$courier_id]);
                } else {
                    $check_stmt->execute([$courier_id, $_SESSION['user_id']]);
                }
                
                if (!$check_stmt->fetch()) {
                    throw new Exception("Courier not found or access denied");
                }
                
                // Update courier status
                $stmt = $pdo->prepare("UPDATE couriers SET status = ? WHERE courier_id = ?");
                $stmt->execute([$status, $courier_id]);
                
                // Add tracking entry
                $stmt = $pdo->prepare("INSERT INTO courier_tracking (courier_id, status, location, remarks, updated_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$courier_id, $status, $location, $remarks, $_SESSION['user_id']]);
                
                logActivity($pdo, 'status_updated', "Status updated for courier $courier_id to $status", $courier_id);
                
                $success = "Status updated successfully!";
            } catch (Exception $e) {
                $error = "Error updating status: " . $e->getMessage();
            }
        }
    } elseif ($_POST['action'] === 'upload_selfie') {
        $courier_id = $_POST['courier_id'] ?? '';
        
        if ($courier_id && isset($_FILES['selfie_image'])) {
            $file = $_FILES['selfie_image'];
            
            // Check file size (1MB limit)
            if ($file['size'] > 1048576) {
                $error = "File size must be under 1MB";
            } else {
                $upload_dir = 'uploads/selfie_images/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = $courier_id . '_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                    // Save to database
                    $stmt = $pdo->prepare("INSERT INTO selfie_images (courier_id, image_path, uploaded_by) VALUES (?, ?, ?)");
                    $stmt->execute([$courier_id, $upload_path, $_SESSION['user_id']]);
                    
                    // Update courier status to delivered
                    $stmt = $pdo->prepare("UPDATE couriers SET status = 'delivered' WHERE courier_id = ?");
                    $stmt->execute([$courier_id]);
                    
                    logActivity($pdo, 'selfie_uploaded', "Delivery selfie uploaded for courier $courier_id", $courier_id);
                    
                    $success = "Delivery selfie uploaded successfully!";
                } else {
                    $error = "Error uploading file";
                }
            }
        }
    }
}

// Get couriers based on user role
if (isAdmin()) {
    $stmt = $pdo->query("SELECT c.*, u.agent_name FROM couriers c JOIN users u ON c.agent_id = u.id ORDER BY c.created_at DESC");
} else {
    $stmt = $pdo->prepare("SELECT c.*, u.agent_name FROM couriers c JOIN users u ON c.agent_id = u.id WHERE c.agent_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$couriers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Couriers - Courier Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-boxes"></i> Manage Couriers</h4>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Courier ID</th>
                                        <th>From/To</th>
                                        <th>Sender/Receiver</th>
                                        <th>Status</th>
                                        <th>Agent</th>
                                        <th>Date</th>
                                        <th>Actions</th>
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
                                        <td>
                                            <span class="badge bg-<?= 
                                                $courier['status'] === 'delivered' ? 'success' : 
                                                ($courier['status'] === 'in_transit' ? 'warning' : 'secondary') 
                                            ?>">
                                                <?= ucfirst($courier['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($courier['agent_name']) ?></td>
                                        <td><?= date('d M Y', strtotime($courier['created_at'])) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal<?= $courier['id'] ?>">
                                                <i class="fas fa-edit"></i> Update
                                            </button>
                                            <?php if ($courier['status'] !== 'delivered'): ?>
                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#deliveryModal<?= $courier['id'] ?>">
                                               <i class="fas fa-camera"></i> Upload Selfie
                                            </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    
                                    <!-- Update Status Modal -->
                                    <div class="modal fade" id="updateModal<?= $courier['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Update Status - <?= htmlspecialchars($courier['courier_id']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="update_status">
                                                        <input type="hidden" name="courier_id" value="<?= htmlspecialchars($courier['courier_id']) ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label for="status" class="form-label">Status</label>
                                                            <select class="form-control" name="status" required>
                                                                <option value="pending" <?= $courier['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="in_transit" <?= $courier['status'] === 'in_transit' ? 'selected' : '' ?>>In Transit</option>
                                                                <option value="delivered" <?= $courier['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                                                <option value="cancelled" <?= $courier['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="location" class="form-label">Current Location</label>
                                                            <input type="text" class="form-control" name="location" placeholder="Enter current location">
                                                        </div>
                                                        
                                                        <div class="mb-3">
                                                            <label for="remarks" class="form-label">Remarks</label>
                                                            <textarea class="form-control" name="remarks" rows="3" placeholder="Enter remarks"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update Status</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Delivery Image Modal -->
                                    <div class="modal fade" id="deliveryModal<?= $courier['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Upload Delivery Selfie - <?= htmlspecialchars($courier['courier_id']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form method="POST" enctype="multipart/form-data">
                                                    <div class="modal-body">
                                                        <input type="hidden" name="action" value="upload_selfie">
                                                        <input type="hidden" name="courier_id" value="<?= htmlspecialchars($courier['courier_id']) ?>">
                                                        
                                                        <div class="mb-3">
                                                            <label for="selfie_image" class="form-label">Delivery Selfie (Max 1MB)</label>
                                                            <input type="file" class="form-control" name="selfie_image" accept="image/*" required>
                                                            <small class="text-muted">Supported formats: JPG, PNG, GIF. Maximum size: 1MB</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-success">Upload Selfie & Mark Delivered</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>