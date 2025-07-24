<?php
require_once 'config.php';

if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Get dashboard statistics
$stmt = $pdo->query("SELECT COUNT(*) as total_agents FROM agents WHERE status = 'active'");
$totalAgents = $stmt->fetch()['total_agents'];

$stmt = $pdo->query("SELECT COUNT(*) as total_couriers FROM couriers");
$totalCouriers = $stmt->fetch()['total_couriers'];

$stmt = $pdo->query("SELECT COUNT(*) as pending_couriers FROM couriers WHERE status = 'pending'");
$pendingCouriers = $stmt->fetch()['pending_couriers'];

$stmt = $pdo->query("SELECT COUNT(*) as delivered_couriers FROM couriers WHERE status = 'delivered'");
$deliveredCouriers = $stmt->fetch()['delivered_couriers'];

$stmt = $pdo->query("SELECT SUM(amount) as total_revenue FROM couriers WHERE status = 'delivered'");
$totalRevenue = $stmt->fetch()['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FastTrack Courier</title>
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
                    <span>FastTrack Admin</span>
                </div>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="#dashboard" class="nav-link active" onclick="showSection('dashboard')">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a></li>
                    <li><a href="#agents" class="nav-link" onclick="showSection('agents')">
                        <i class="fas fa-users"></i> Agents
                    </a></li>
                    <li><a href="#couriers" class="nav-link" onclick="showSection('couriers')">
                        <i class="fas fa-box"></i> Couriers
                    </a></li>
                    <li><a href="#delivery-selfies" class="nav-link" onclick="showSection('delivery-selfies')">
                        <i class="fas fa-camera"></i> Delivery Selfies
                    </a></li>
                    <li><a href="#reports" class="nav-link" onclick="showSection('reports')">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a></li>
                    <li><a href="#update-courier" class="nav-link" onclick="showSection('update-courier')">
                        <i class="fas fa-edit"></i> Update Courier
                    </a></li>
                    <li><a href="#settings" class="nav-link" onclick="showSection('settings')">
                        <i class="fas fa-cog"></i> Settings
                    </a></li>
                    <li><a href="#profile" class="nav-link" onclick="showSection('profile')">
                        <i class="fas fa-user"></i> Profile
                    </a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <div class="realtime-indicator">
                    <span>Live Updates</span>
                </div>
                <button onclick="logout()" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </div>
        </aside>

        <main class="main-content">
            <header class="dashboard-header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></h1>
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
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <h3><?php echo $totalAgents; ?></h3>
                            <p>Active Agents</p>
                        </div>
                    </div>
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
                            <p>Pending Deliveries</p>
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
                            <h3>₹<?php echo number_format($totalRevenue, 2); ?></h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>

                <div class="recent-activities">
                    <h2>Recent Activities</h2>
                    <div class="activity-list" id="recentActivities">
                        <!-- Activities will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Agents Section -->
            <section id="agents" class="dashboard-section">
                <div class="section-header">
                    <h2>Agent Management</h2>
                    <button class="btn btn-primary" onclick="openAddAgentModal()">
                        <i class="fas fa-plus"></i> Add Agent
                    </button>
                </div>
                <div class="agents-table-container">
                    <table class="data-table" id="agentsTable">
                        <thead>
                            <tr>
                                <th>Agent ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Mobile</th>
                                <th>City</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="agentsTableBody">
                            <!-- Agents will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Couriers Section -->
            <section id="couriers" class="dashboard-section">
                <div class="section-header">
                    <h2>Courier Management</h2>
                    <div class="filters">
                        <select id="statusFilter" onchange="filterCouriers()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="in_transit">In Transit</option>
                            <option value="delivered">Delivered</option>
                        </select>
                        <input type="date" id="dateFilter" onchange="filterCouriers()">
                        <button class="btn btn-secondary" onclick="exportCouriers()">
                            <i class="fas fa-download"></i> Export Excel
                        </button>
                        <button class="export-btn" onclick="exportCouriersToTxt()">
                            <i class="fas fa-file-alt"></i> Export to Notepad
                        </button>
                    </div>
                </div>
                <div class="couriers-table-container">
                    <table class="data-table" id="couriersTable">
                        <thead>
                            <tr>
                                <th onclick="sortTable(0)">Courier ID <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(1)">Party Name <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(2)">From <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(3)">To <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(4)">Amount <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(5)">Status <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(6)">Agent <i class="fas fa-sort"></i></th>
                                <th onclick="sortTable(7)">Date <i class="fas fa-sort"></i></th>
                                <th>Remarks</th>
                                <th>Delivery Info</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="couriersTableBody">
                            <!-- Couriers will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Delivery Selfies Section -->
            <section id="delivery-selfies" class="dashboard-section">
                <div class="section-header">
                    <h2>Delivery Selfies</h2>
                </div>
                <div class="delivery-selfies-container">
                    <div class="selfies-grid" id="deliverySelfiesGrid">
                        <!-- Delivery selfies will be loaded here -->
                    </div>
                </div>
            </section>

            <!-- Reports Section -->
            <section id="reports" class="dashboard-section">
                <div class="section-header">
                    <h2>Real-time Reports & Analytics</h2>
                    <div class="realtime-indicator">
                        <span>Live Data</span>
                    </div>
                </div>
                <div class="stats-grid" style="margin-bottom: 2rem;">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="todayRevenue">₹0</h3>
                            <p>Today's Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="monthRevenue">₹0</h3>
                            <p>This Month's Revenue</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="totalRevenue">₹0</h3>
                            <p>Total Revenue</p>
                        </div>
                    </div>
                </div>
                <div class="reports-grid">
                    <div class="report-card">
                        <h3>Monthly Revenue Trend</h3>
                        <div class="chart-container">
                            <div id="revenueChart" style="display: flex; align-items: end; justify-content: center; height: 250px;"></div>
                        </div>
                    </div>
                    <div class="report-card">
                        <h3>Agent Performance</h3>
                        <div class="chart-container">
                            <div id="agentChart" style="display: flex; align-items: end; justify-content: center; height: 250px;"></div>
                        </div>
                    </div>
                </div>
                <div class="report-card" style="margin-top: 2rem;">
                    <h3>Real-time Agent Performance</h3>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Agent Name</th>
                                <th>Agent ID</th>
                                <th>Total Couriers</th>
                                <th>Delivered</th>
                                <th>Pending</th>
                                <th>In Transit</th>
                                <th>Total Business</th>
                                <th>Last Activity</th>
                            </tr>
                        </thead>
                        <tbody id="agentPerformanceBody">
                            <!-- Agent performance data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- Update Courier Section -->
            <section id="update-courier" class="dashboard-section">
                <div class="section-header">
                    <h2>Update Any Courier Tracking</h2>
                    <div class="realtime-indicator">
                        <span>Admin can update any courier</span>
                    </div>
                </div>
                <div class="form-container">
                    <form id="updateCourierForm" class="courier-form">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="courier_id_update">Courier ID *</label>
                                <input type="text" id="courier_id_update" name="courier_id" required>
                            </div>
                            <div class="form-group">
                                <label for="location_update">Current Location *</label>
                                <input type="text" id="location_update" name="location" required>
                            </div>
                            <div class="form-group">
                                <label for="status_update">Status *</label>
                                <select id="status_update" name="status" required>
                                    <option value="">Select Status</option>
                                    <option value="Package picked up">Package picked up</option>
                                    <option value="In transit">In transit</option>
                                    <option value="Reached sorting facility">Reached sorting facility</option>
                                    <option value="Out for delivery">Out for delivery</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="courier_status_update">Update Courier Status</label>
                                <select id="courier_status_update" name="courier_status">
                                    <option value="">Keep Current</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_transit">In Transit</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Update Any Courier
                        </button>
                    </form>
                </div>
            </section>

            <!-- Settings Section -->
            <section id="settings" class="dashboard-section">
                <div class="section-header">
                    <h2>Settings</h2>
                </div>
                <div class="settings-grid">
                    <div class="settings-card">
                        <h3>Change Password</h3>
                        <form id="changePasswordForm">
                            <input type="hidden" id="admin_username" name="username">
                            <input type="password" name="new_password" placeholder="New Password" required>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
                    </div>
                    <div class="settings-card">
                        <h3>System Settings</h3>
                        <div class="setting-item">
                            <label>Default Delivery Days</label>
                            <input type="number" id="defaultDays" value="3" min="1" max="30">
                        </div>
                        <div class="setting-item">
                            <label>WhatsApp Number</label>
                            <input type="text" id="whatsappNumber" value="+917767834383">
                        </div>
                        <button class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                    </div>
                </div>
            </section>

            <!-- Profile Section -->
            <section id="profile" class="dashboard-section">
                <div class="section-header">
                    <h2>Admin Profile</h2>
                </div>
                <div class="settings-grid">
                    <div class="settings-card">
                        <h3>Update Profile</h3>
                        <form id="adminProfileForm">
                            <input type="text" name="username" id="profile_username" placeholder="Username" required>
                            <input type="password" name="new_password" placeholder="New Password (optional)">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- Add Agent Modal -->
    <div id="addAgentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addAgentModal')">&times;</span>
            <h2>Add New Agent</h2>
            <form id="addAgentForm">
                <input type="text" name="agent_name" placeholder="Agent Name" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="text" name="mobile" placeholder="Mobile Number" required>
                <input type="text" name="city" placeholder="City" required>
                <button type="submit" class="btn btn-primary">Add Agent</button>
            </form>
        </div>
    </div>

    <!-- Edit Agent Modal -->
    <div id="editAgentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editAgentModal')">&times;</span>
            <h2>Edit Agent</h2>
            <form id="editAgentForm" onsubmit="handleEditAgent(event)">
                <input type="hidden" id="edit_agent_id" name="agent_id">
                <input type="text" id="edit_agent_name" name="agent_name" placeholder="Agent Name" required>
                <input type="text" id="edit_username" name="username" placeholder="Username" required>
                <input type="password" id="edit_password" name="password" placeholder="New Password (optional)">
                <input type="text" id="edit_mobile" name="mobile" placeholder="Mobile Number">
                <input type="text" id="edit_city" name="city" placeholder="City">
                <select id="edit_status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button type="submit" class="btn btn-primary">Update Agent</button>
            </form>
        </div>
    </div>

    <!-- Update Courier Modal -->
    <div id="updateCourierModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('updateCourierModal')">&times;</span>
            <h2>Update Courier Tracking</h2>
            <form id="updateCourierModalForm" onsubmit="handleUpdateCourier(event)">
                <input type="text" name="courier_id" placeholder="Courier ID" required>
                <input type="text" name="location" placeholder="Current Location" required>
                <select name="status" required>
                    <option value="">Select Status</option>
                    <option value="Package picked up">Package picked up</option>
                    <option value="In transit">In transit</option>
                    <option value="Reached sorting facility">Reached sorting facility</option>
                    <option value="Out for delivery">Out for delivery</option>
                    <option value="Delivered">Delivered</option>
                </select>
                <select name="courier_status">
                    <option value="">Keep Current Status</option>
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                </select>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>

    <!-- Delivery Selfie Modal -->
    <div id="selfieModal" class="modal">
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="closeModal('selfieModal')">&times;</span>
            <h2 id="selfieModalTitle">Delivery Selfie</h2>
            <div style="text-align: center;">
                <img id="selfieModalImage" src="" alt="Delivery Selfie" style="max-width: 100%; border-radius: 10px;">
            </div>
            <div style="margin-top: 1rem; text-align: center;">
                <button class="btn btn-danger" onclick="deleteSelfie()">
                    <i class="fas fa-trash"></i> Delete Selfie
                </button>
            </div>
        </div>
    </div>

    <script src="admin-dashboard.js"></script>
</body>
</html>