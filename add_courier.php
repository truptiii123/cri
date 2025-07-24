<?php
require_once 'config/database.php';
require_once 'config/session.php';
requireLogin();

$success = '';
$error = '';

if ($_POST) {
    $courier_id = 'CID' . date('Ymd') . rand(1000, 9999);
    $to_party_name = $_POST['to_party_name'] ?? '';
    $from_party_name = $_POST['from_party_name'] ?? '';
    $sender_name = $_POST['sender_name'] ?? '';
    $sender_phone = $_POST['sender_phone'] ?? '';
    $sender_address = $_POST['sender_address'] ?? '';
    $receiver_name = $_POST['receiver_name'] ?? '';
    $receiver_phone = $_POST['receiver_phone'] ?? '';
    $receiver_address = $_POST['receiver_address'] ?? '';
    $weight = $_POST['weight'] ?? '';
    $amount = $_POST['amount'] ?? '';
    $remarks = $_POST['remarks'] ?? '';
    $created_date = $_POST['created_date'] ?? date('Y-m-d');
    $to_party_name_note = $_POST['to_party_name_note'] ?? '';
    $from_party_name_note = $_POST['from_party_name_note'] ?? '';
    
    if ($to_party_name && $from_party_name && $sender_name && $receiver_name) {
        try {
            $stmt = $pdo->prepare("INSERT INTO couriers (courier_id, to_party_name, from_party_name, sender_name, sender_phone, sender_address, receiver_name, receiver_phone, receiver_address, weight, amount, remarks, agent_id, created_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $courier_id, $to_party_name, $from_party_name, $sender_name, $sender_phone, 
                $sender_address, $receiver_name, $receiver_phone, $receiver_address, 
                $weight, $amount, $remarks, $_SESSION['user_id'], $created_date
            ]);
            
            logActivity($pdo, 'courier_added', "New courier added: $courier_id", $courier_id);
            
            $success = "Courier added successfully! Courier ID: $courier_id";
            
            // Store courier data for ticket generation
            $_SESSION['last_courier'] = [
                'courier_id' => $courier_id,
                'to_party_name' => $to_party_name,
                'from_party_name' => $from_party_name,
                'created_date' => $created_date,
                'sender_name' => $sender_name,
                'receiver_name' => $receiver_name,
                'weight' => $weight,
              'amount' => $amount,
              'to_party_name_note' => $to_party_name_note,
              'from_party_name_note' => $from_party_name_note
            ];
            
        } catch (Exception $e) {
            $error = "Error adding courier: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all required fields";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Courier - Courier Tracking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-plus"></i> Add New Courier</h4>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <?= htmlspecialchars($success) ?>
                                <div class="mt-2">
                                    <a href="generate_ticket.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-download"></i> Download Ticket
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="to_party_name" class="form-label">To Party Name *</label>
                                        <input type="text" class="form-control" id="to_party_name" name="to_party_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="from_party_name" class="form-label">From Party Name *</label>
                                        <input type="text" class="form-control" id="from_party_name" name="from_party_name" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="created_date" class="form-label">Date *</label>
                                        <input type="date" class="form-control" id="created_date" name="created_date" value="<?= date('Y-m-d') ?>" required>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <h5>Sender Details</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sender_name" class="form-label">Sender Name *</label>
                                        <input type="text" class="form-control" id="sender_name" name="sender_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sender_phone" class="form-label">Sender Phone</label>
                                        <input type="text" class="form-control" id="sender_phone" name="sender_phone">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sender_address" class="form-label">Sender Address</label>
                                <textarea class="form-control" id="sender_address" name="sender_address" rows="3"></textarea>
                            </div>
                            
                            <hr>
                            <h5>Receiver Details</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receiver_name" class="form-label">Receiver Name *</label>
                                        <input type="text" class="form-control" id="receiver_name" name="receiver_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="receiver_phone" class="form-label">Receiver Phone</label>
                                        <input type="text" class="form-control" id="receiver_phone" name="receiver_phone">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="receiver_address" class="form-label">Receiver Address</label>
                                <textarea class="form-control" id="receiver_address" name="receiver_address" rows="3"></textarea>
                            </div>
                            
                            <hr>
                            <h5>Package Details</h5>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="weight" class="form-label">Weight (kg)</label>
                                        <input type="number" step="0.01" class="form-control" id="weight" name="weight">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="amount" class="form-label">Amount</label>
                                        <input type="number" step="0.01" class="form-control" id="amount" name="amount">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="remarks" class="form-label">Remarks</label>
                                <textarea class="form-control" id="remarks" name="remarks" rows="3"></textarea>
                            </div>
                            
                            <hr>
                            <h5>Party Information</h5>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="to_party_name_note" class="form-label">To Party Name (for notes)</label>
                                        <input type="text" class="form-control" id="to_party_name_note" name="to_party_name_note">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="from_party_name_note" class="form-label">From Party Name (for notes)</label>
                                        <input type="text" class="form-control" id="from_party_name_note" name="from_party_name_note">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Add Courier
                            </button>
                            
                            <?php if ($success && isset($_SESSION['last_courier'])): ?>
                            <a href="download_party_notes.php" class="btn btn-info ms-2">
                                <i class="fas fa-download"></i> Download Party Notes
                            </a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>