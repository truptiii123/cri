<?php
require_once 'config.php';

if (!isAgentLoggedIn()) {
    header('Location: index.php');
    exit;
}

$agent_id = $_SESSION['agent_agent_id'];

// Get agent statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as total_couriers FROM couriers WHERE agent_id = ?");
$stmt->execute([$agent_id]);
$totalCouriers = $stmt->fetch()['total_couriers'];

$stmt = $pdo->prepare("SELECT COUNT(*) as pending_couriers FROM couriers WHERE agent_id = ? AND status = 'pending'");
$stmt->execute([$agent_id]);
$pendingCouriers = $stmt->fetch()['pending_couriers'];

$stmt = $pdo->prepare("SELECT COUNT(*) as delivered_couriers FROM couriers WHERE agent_id = ? AND status = 'delivered'");
$stmt->execute([$agent_id]);
$deliveredCouriers = $stmt->fetch()['delivered_couriers'];

$stmt = $pdo->prepare("SELECT SUM(amount) as total_business FROM couriers WHERE agent_id = ? AND status = 'delivered'");
$stmt->execute([$agent_id]);
$totalBusiness = $stmt->fetch()['total_business'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(amount) as today_business FROM couriers WHERE agent_id = ? AND DATE(created_at) = CURDATE()");
$stmt->execute([$agent_id]);
$todayBusiness = $stmt->fetch()['today_business'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agent Dashboard - FastTrack Courier</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="dashboard-body">
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <i class="fas fa-shipping-fast"></i>
                    <span>Agent Panel</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#dashboard" class="nav-link active" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="#add-courier" class="nav-link" onclick="showSection('add-courier')">
                        <i class="fas fa-plus"></i> Add Courier
                    </a></li>
                    <li><a href="#my-couriers" class="nav-link" onclick="showSection('my-couriers')">
                        <i class="fas fa-box"></i> My Couriers
                    </a></li>
                    <li><a href="#tracking" class="nav-link" onclick="showSection('tracking')">
                        <i class="fas fa-search"></i> Update Tracking
                    </a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <button onclick="logout()" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['agent_username']); ?></h1>
                <div class="header-actions">
                    <div class="realtime-indicator">
                        <span id="currentDateTime"></span>
                    </div>
                    <button class="btn btn-primary" onclick="window.open('index.php', '_blank')">
                        <i class="fas fa-external-link-alt"></i> View Site
                    </button>
                    <button onclick="logout()" class="header-logout-btn">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </button>
                </div>
            </header>

            <!-- Dashboard Section -->
            <section id="dashboard" class="dashboard-section active">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalCouriers; ?></h3>
                            <p>Total Couriers</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $pendingCouriers; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $deliveredCouriers; ?></h3>
                            <p>Delivered</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₹<?php echo number_format($todayBusiness, 2); ?></h3>
                            <p>Today's Business</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3>₹<?php echo number_format($totalBusiness, 2); ?></h3>
                            <p>Total Business</p>
                        </div>
                    </div>
                </div>

                <div class="recent-activities">
                    <h2>Recent Couriers</h2>
                    <div class="activity-list" id="recentCouriers">
                        <!-- Recent couriers will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Add Courier Section -->
            <section id="add-courier" class="dashboard-section">
                <div class="section-header">
                    <h2>Add New Courier</h2>
                    <button class="btn btn-success" onclick="downloadReceipt()" id="downloadReceiptBtn" style="display: none;">
                        <i class="fas fa-download"></i> Download Receipt
                    </button>
                </div>
                <div class="form-container">
                    <form id="addCourierForm" class="courier-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="to_party_name">To Party Name *</label>
                                <input type="text" id="to_party_name" name="to_party_name" required>
                            </div>
                            <div class="form-group">
                                <label for="from_party_name">From Party Name *</label>
                                <input type="text" id="from_party_name" name="from_party_name" required>
                            </div>
                            <div class="form-group">
                                <label for="mobile">Mobile Number *</label>
                                <input type="tel" id="mobile" name="mobile" required>
                            </div>
                            <div class="form-group full-width">
                                <label for="address">From Address *</label>
                                <textarea id="address" name="address" rows="3" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="to_city">To City *</label>
                                <div class="searchable-select">
                                    <input type="text" id="to_city" name="to_city" class="city-search-input" placeholder="Search and select city..." required>
                                    <div class="city-dropdown" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="from_city">From City *</label>
                                <div class="searchable-select">
                                    <input type="text" id="from_city" name="from_city" class="city-search-input" placeholder="Search and select city..." required>
                                    <div class="city-dropdown" style="display: none;"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="amount">Amount (₹)</label>
                                <input type="number" id="amount" name="amount" step="0.01" min="0">
                            </div>
                            <div class="form-group">
                                <label for="delivery_date">Expected Delivery Date</label>
                                <input type="date" id="delivery_date" name="delivery_date">
                            </div>
                            <div class="form-group full-width">
                                <label for="remarks">Remarks (if any)</label>
                                <textarea id="remarks" name="remarks" rows="2"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Courier
                        </button>
                        <div class="receipt-option" id="downloadReceiptSection" style="display: none;">
                            <h4>📄 Download Receipt</h4>
                            <p>Your courier has been added successfully! Download the receipt for your records.</p>
                            <button type="button" class="btn btn-success" onclick="downloadReceipt()">
                                <i class="fas fa-download"></i> Download Receipt
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- My Couriers Section -->
            <section id="my-couriers" class="dashboard-section">
                <div class="section-header">
                    <h2>My Couriers</h2>
                    <div class="filters">
                        <select id="statusFilter" onchange="filterMyCouriers()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        <input type="date" id="dateFilter" onchange="filterMyCouriers()">
                        <button class="export-btn" onclick="exportMyData()">
                            <i class="fas fa-file-alt"></i> Export to Notepad
                        </button>
                    </div>
                </div>
                <div class="couriers-table-container">
                    <table class="data-table" id="myCouriersTable">
                        <thead>
                            <tr>
                                <th>Courier ID</th>
                                <th>Party Name</th>
                                <th>Mobile</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myCouriersTableBody">
                            <!-- Couriers will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Tracking Section -->
            <section id="tracking" class="dashboard-section">
                <div class="section-header">
                    <h2>Update Any Courier Tracking</h2>
                    <div class="realtime-indicator">
                        <span>Can update any courier in system</span>
                    </div>
                </div>
                <div class="form-container">
                    <form id="updateTrackingForm" class="tracking-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="courier_id_track">Courier ID *</label>
                                <input type="text" id="courier_id_track" name="courier_id" required>
                            </div>
                            <div class="form-group">
                                <label for="location">Current Location *</label>
                                <input type="text" id="location" name="location" required>
                            </div>
                            <div class="form-group">
                                <label for="tracking_status">Status *</label>
                                <select id="tracking_status" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Package picked up">Package picked up</option>
                                    <option value="In transit">In transit</option>
                                    <option value="Reached sorting facility">Reached sorting facility</option>
                                    <option value="Out for delivery">Out for delivery</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courier_status">Update Courier Status</label>
                                <select id="courier_status" name="courier_status">
                                    <option value="">Keep Current</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_transit">In Transit</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="form-group delivery-section" id="deliverySection" style="display: none;">
                                <label for="delivery_person">Delivery Person Name *</label>
                                <input type="text" id="delivery_person" name="delivery_person">
                            </div>
                            <div class="form-group delivery-section" id="photoSection" style="display: none;">
                                <label for="delivery_photo">Upload Selfie with Customer * (Max 500KB)</label>
                                <input type="file" id="delivery_photo" name="delivery_photo" accept="image/*">
                                <small style="color: #6b46c1;">Upload a selfie with the customer when marking as delivered</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Update Any Courier
                        </button>
                    </form>
                </div>
            </section>
        </main>
    </div>

    <script src="agent-dashboard.js"></script>
</body>
</html>