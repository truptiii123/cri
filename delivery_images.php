<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireAdmin();

// Get delivery images with courier and agent details
$stmt = $pdo->query("
    SELECT di.*, c.courier_id, c.sender_name, c.receiver_name, 
           c.from_party_name, c.to_party_name, u.agent_name
    FROM delivery_images di
    JOIN couriers c ON di.courier_id = c.courier_id
    JOIN users u ON di.uploaded_by = u.id
    ORDER BY di.uploaded_at DESC
");
$delivery_images = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Images - Courier Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .delivery-image {
            max-width: 200px;
            max-height: 150px;
            object-fit: cover;
            cursor: pointer;
        }
        .modal-img {
            max-width: 100%;
            max-height: 80vh;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-images"></i> Delivery Images</h4>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($delivery_images)): ?>
                        <div class="text-center py-4">
                            <p class="text-muted">No delivery images uploaded yet.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Courier ID</th>
                                        <th>From/To Party</th>
                                        <th>Sender/Receiver</th>
                                        <th>Agent</th>
                                        <th>Upload Date</th>
                                        <th>Image</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($delivery_images as $image): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($image['courier_id']) ?></strong></td>
                                        <td>
                                            <small>
                                                <strong>From:</strong> <?= htmlspecialchars($image['from_party_name']) ?><br>
                                                <strong>To:</strong> <?= htmlspecialchars($image['to_party_name']) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                <strong>Sender:</strong> <?= htmlspecialchars($image['sender_name']) ?><br>
                                                <strong>Receiver:</strong> <?= htmlspecialchars($image['receiver_name']) ?>
                                            </small>
                                        </td>
                                        <td><?= htmlspecialchars($image['agent_name']) ?></td>
                                        <td><?= date('d M Y, h:i A', strtotime($image['uploaded_at'])) ?></td>
                                        <td>
                                            <img src="<?= htmlspecialchars($image['image_path']) ?>" 
                                                 class="delivery-image img-thumbnail" 
                                                 alt="Delivery Image"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 data-image="<?= htmlspecialchars($image['image_path']) ?>"
                                                 data-courier="<?= htmlspecialchars($image['courier_id']) ?>">
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delivery Image - <span id="modalCourierId"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="modal-img" alt="Delivery Image">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a id="downloadLink" href="" download class="btn btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle image modal
        document.addEventListener('DOMContentLoaded', function() {
            const imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const imagePath = button.getAttribute('data-image');
                const courierId = button.getAttribute('data-courier');
                
                document.getElementById('modalImage').src = imagePath;
                document.getElementById('modalCourierId').textContent = courierId;
                document.getElementById('downloadLink').href = imagePath;
            });
        });
    </script>
</body>
</html>