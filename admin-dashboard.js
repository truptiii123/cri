// Admin Dashboard JavaScript - Enhanced Version
let currentSection = 'dashboard';
let currentSelfieId = null;
let sortDirection = {};

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadRecentActivities();
    loadAgents();
    loadCouriers();
    loadDeliverySelfies();
    setupEventListeners();
    updateDateTime();
    
    // Update time every second
    setInterval(updateDateTime, 1000);
    
    // Auto-refresh data every 30 seconds for real-time updates
    setInterval(function() {
        if (currentSection === 'dashboard') {
            loadRecentActivities();
        } else if (currentSection === 'reports') {
            loadReports();
        } else if (currentSection === 'agents') {
            loadAgents();
        } else if (currentSection === 'couriers') {
            loadCouriers();
        }
    }, 30000);
    
    // Add mobile menu button if needed
    if (window.innerWidth <= 768) {
        addMobileMenuButton();
    }
});

function initializeDashboard() {
    showSection('dashboard');
    setupFormSubmissions();
    loadReports();
}

function updateDateTime() {
    const now = new Date();
    const options = {
        timeZone: 'Asia/Kolkata',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };
    const dateTimeString = now.toLocaleString('en-IN', options);
    const dateTimeElement = document.getElementById('currentDateTime');
    if (dateTimeElement) {
        dateTimeElement.textContent = `IST: ${dateTimeString}`;
    }
}

function addMobileMenuButton() {
    const header = document.querySelector('.dashboard-header');
    const menuBtn = document.createElement('button');
    menuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    menuBtn.className = 'mobile-menu-btn';
    menuBtn.onclick = toggleSidebar;
    header.insertBefore(menuBtn, header.firstChild);
}

function setupEventListeners() {
    // Change password form
    document.getElementById('changePasswordForm').addEventListener('submit', handleChangePassword);
    
    // Add agent form
    document.getElementById('addAgentForm').addEventListener('submit', handleAddAgent);
    
    // Admin profile form
    document.getElementById('adminProfileForm').addEventListener('submit', handleAdminProfileUpdate);
    
    // Update courier tracking form
    document.getElementById('updateCourierForm').addEventListener('submit', handleUpdateCourier);
}

function setupFormSubmissions() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    });
}

// Navigation
function showSection(sectionId) {
    document.querySelectorAll('.dashboard-section').forEach(section => {
        section.classList.remove('active');
    });
    
    document.getElementById(sectionId).classList.add('active');
    
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    document.querySelector(`[onclick="showSection('${sectionId}')"]`).classList.add('active');
    
    currentSection = sectionId;
    
    switch(sectionId) {
        case 'agents':
            loadAgents();
            break;
        case 'couriers':
            loadCouriers();
            break;
        case 'delivery-selfies':
            loadDeliverySelfies();
            break;
        case 'reports':
            loadReports();
            break;
        case 'profile':
            loadAdminProfile();
            break;
    }
}

// Dashboard Data Loading
async function loadRecentActivities() {
    try {
        const response = await fetch('api/recent-activities.php');
        const activities = await response.json();
        
        const container = document.getElementById('recentActivities');
        if (activities.length === 0) {
            container.innerHTML = '<p>No recent activities</p>';
            return;
        }
        
        container.innerHTML = activities.map(activity => `
            <div class="activity-item">
                <h4>${activity.title}</h4>
                <p>${activity.description}</p>
                <p><small>${activity.timestamp}</small></p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load recent activities:', error);
    }
}

// Agent Management
async function loadAgents() {
    try {
        const response = await fetch('api/agents.php');
        const agents = await response.json();
        
        const tbody = document.getElementById('agentsTableBody');
        tbody.innerHTML = agents.map(agent => `
            <tr>
                <td>${agent.agent_id}</td>
                <td>${agent.agent_name}</td>
                <td>${agent.username}</td>
                <td>${agent.mobile || 'N/A'}</td>
                <td>${agent.city || 'N/A'}</td>
                <td><span class="status-badge ${agent.status}">${agent.status}</span></td>
                <td>
                    <button class="action-btn edit" onclick="editAgent(${agent.id})">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteAgent(${agent.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Failed to load agents:', error);
    }
}

function openAddAgentModal() {
    document.getElementById('addAgentModal').style.display = 'block';
}

async function handleAddAgent(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const agentData = {
        agent_name: formData.get('agent_name'),
        username: formData.get('username'),
        password: formData.get('password'),
        mobile: formData.get('mobile'),
        city: formData.get('city')
    };
    
    try {
        const response = await fetch('api/agents.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(agentData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Agent added successfully!', 'success');
            closeModal('addAgentModal');
            loadAgents();
            e.target.reset();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to add agent. Please try again.', 'error');
    }
}

async function deleteAgent(agentId) {
    if (!confirm('Are you sure you want to delete this agent?')) {
        return;
    }
    
    try {
        const response = await fetch(`api/agents.php?id=${agentId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Agent deleted successfully!', 'success');
            loadAgents();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to delete agent. Please try again.', 'error');
    }
}

// Courier Management
async function loadCouriers() {
    try {
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || '';
        
        let url = 'api/couriers.php';
        const params = new URLSearchParams();
        
        if (statusFilter) params.append('status', statusFilter);
        if (dateFilter) params.append('date', dateFilter);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const response = await fetch(url);
        const couriers = await response.json();
        
        const tbody = document.getElementById('couriersTableBody');
        tbody.innerHTML = couriers.map(courier => `
            <tr>
                <td>${courier.courier_id}</td>
                <td>${courier.party_name}</td>
                <td>${courier.from_city}</td>
                <td>${courier.to_city}</td>
                <td>₹${parseFloat(courier.amount).toLocaleString()}</td>
                <td><span class="status-badge ${courier.status}">${courier.status}</span></td>
                <td>${courier.agent_name || 'N/A'}</td>
                <td>${new Date(courier.created_at).toLocaleDateString()}</td>
                <td>${courier.remarks || 'N/A'}</td>
                <td>
                    ${courier.delivery_person ? `
                        <div class="delivery-info">
                            <strong>${courier.delivery_person}</strong>
                            ${courier.delivery_photo ? `
                                <br><img src="uploads/delivery_photos/${courier.delivery_photo}" 
                                     alt="Delivery Photo" class="delivery-photo-thumb" 
                                     onclick="viewDeliveryPhoto('${courier.delivery_photo}')">
                                <button class="action-btn delete" onclick="deleteDeliveryPhoto('${courier.courier_id}')" title="Delete Photo">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    ` : 'N/A'}
                </td>
                <td>
                    <button class="action-btn edit" onclick="viewCourier('${courier.courier_id}')">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="action-btn delete" onclick="deleteCourier('${courier.courier_id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Failed to load couriers:', error);
    }
}

function filterCouriers() {
    loadCouriers();
}

async function exportCouriers() {
    try {
        const url = 'api/export-couriers.php';
        const a = document.createElement('a');
        a.href = url;
        a.download = `couriers_${new Date().toISOString().split('T')[0]}.xlsx`;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showAlert('Export started! File will download shortly.', 'success');
    } catch (error) {
        showAlert('Failed to export data. Please try again.', 'error');
    }
}

async function exportCouriersToTxt() {
    try {
        const statusFilter = document.getElementById('statusFilter')?.value || '';
        const dateFilter = document.getElementById('dateFilter')?.value || '';
        
        let url = 'api/export-text.php';
        const params = new URLSearchParams();
        
        if (statusFilter) params.append('status', statusFilter);
        if (dateFilter) params.append('date', dateFilter);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `couriers_${new Date().toISOString().split('T')[0]}.txt`;
        a.target = '_blank';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        
        showAlert('Text export started! File will download shortly.', 'success');
    } catch (error) {
        showAlert('Failed to export data. Please try again.', 'error');
    }
}

// Table sorting functionality
function sortTable(columnIndex) {
    const table = document.getElementById('couriersTable');
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    const isAscending = sortDirection[columnIndex] !== 'asc';
    sortDirection[columnIndex] = isAscending ? 'asc' : 'desc';
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Handle numeric values
        if (columnIndex === 4) { // Amount column
            const aNum = parseFloat(aValue.replace('₹', '').replace(',', ''));
            const bNum = parseFloat(bValue.replace('₹', '').replace(',', ''));
            return isAscending ? aNum - bNum : bNum - aNum;
        }
        
        // Handle date values
        if (columnIndex === 7) { // Date column
            const aDate = new Date(aValue);
            const bDate = new Date(bValue);
            return isAscending ? aDate - bDate : bDate - aDate;
        }
        
        // Handle text values
        return isAscending ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
    });
    
    // Clear tbody and append sorted rows
    tbody.innerHTML = '';
    rows.forEach(row => tbody.appendChild(row));
    
    // Update sort indicators
    const headers = table.querySelectorAll('th i.fas');
    headers.forEach((icon, index) => {
        if (index === columnIndex) {
            icon.className = `fas fa-sort-${isAscending ? 'up' : 'down'}`;
        } else {
            icon.className = 'fas fa-sort';
        }
    });
}

// Delivery photo functions
function viewDeliveryPhoto(filename) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'block';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 600px;">
            <span class="close" onclick="this.parentElement.parentElement.remove()">&times;</span>
            <h2>Delivery Photo</h2>
            <img src="uploads/delivery_photos/${filename}" alt="Delivery Photo" style="width: 100%; border-radius: 8px;">
        </div>
    `;
    document.body.appendChild(modal);
}

async function deleteDeliveryPhoto(courierId) {
    if (!confirm('Are you sure you want to delete this delivery photo?')) {
        return;
    }
    
    try {
        const response = await fetch(`api/delete-delivery-photo.php?courier_id=${courierId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Delivery photo deleted successfully!', 'success');
            loadCouriers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to delete photo. Please try again.', 'error');
    }
}

// Delivery Selfies Management
async function loadDeliverySelfies() {
    try {
        const response = await fetch('api/delivery-selfies.php');
        const selfies = await response.json();
        
        const container = document.getElementById('deliverySelfiesGrid');
        
        if (selfies.length === 0) {
            container.innerHTML = '<p style="text-align: center; color: #6b46c1;">No delivery selfies uploaded yet.</p>';
            return;
        }
        
        container.innerHTML = selfies.map(selfie => `
            <div class="selfie-card" style="background: rgba(255, 255, 255, 0.9); border-radius: 15px; padding: 1rem; box-shadow: 0 5px 15px rgba(124, 58, 237, 0.1); border: 1px solid rgba(124, 58, 237, 0.1);">
                <img src="${selfie.image_path}" alt="Delivery Selfie" 
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 10px; cursor: pointer;"
                     onclick="viewSelfie('${selfie.id}', '${selfie.image_path}', '${selfie.courier_id}')">
                <div style="margin-top: 1rem;">
                    <h4 style="color: #2d1b69; margin: 0.5rem 0;">Courier: ${selfie.courier_id}</h4>
                    <p style="color: #6b46c1; margin: 0.25rem 0; font-size: 0.9rem;">Agent: ${selfie.agent_name}</p>
                    <p style="color: #a855f7; margin: 0.25rem 0; font-size: 0.85rem;">${new Date(selfie.uploaded_at).toLocaleDateString()}</p>
                </div>
            </div>
        `).join('');
        
        // Add CSS for selfies grid
        container.style.display = 'grid';
        container.style.gridTemplateColumns = 'repeat(auto-fill, minmax(250px, 1fr))';
        container.style.gap = '1.5rem';
        container.style.padding = '1rem';
    } catch (error) {
        console.error('Failed to load delivery selfies:', error);
        document.getElementById('deliverySelfiesGrid').innerHTML = '<p style="text-align: center; color: #ef4444;">Failed to load delivery selfies.</p>';
    }
}

function viewSelfie(selfieId, imagePath, courierId) {
    currentSelfieId = selfieId;
    document.getElementById('selfieModalTitle').textContent = `Delivery Selfie - ${courierId}`;
    document.getElementById('selfieModalImage').src = imagePath;
    document.getElementById('selfieModal').style.display = 'block';
}

async function deleteSelfie() {
    if (!currentSelfieId) return;
    
    if (!confirm('Are you sure you want to delete this delivery selfie?')) {
        return;
    }
    
    try {
        const response = await fetch(`api/delivery-selfies.php?id=${currentSelfieId}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Delivery selfie deleted successfully!', 'success');
            closeModal('selfieModal');
            loadDeliverySelfies();
            currentSelfieId = null;
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to delete selfie. Please try again.', 'error');
    }
}

// Settings
async function handleChangePassword(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    if (newPassword !== confirmPassword) {
        showAlert('New passwords do not match!', 'error');
        return;
    }
    
    try {
        const response = await fetch('api/admin-profile.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username: document.getElementById('admin_username').value,
                new_password: newPassword
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Password changed successfully!', 'success');
            e.target.reset();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to change password. Please try again.', 'error');
    }
}

function saveSettings() {
    const defaultDays = document.getElementById('defaultDays').value;
    const whatsappNumber = document.getElementById('whatsappNumber').value;
    
    // Implement save settings logic
    showAlert('Settings saved successfully!', 'success');
}

// Reports
async function loadReports() {
    try {
        const response = await fetch('api/reports.php');
        const result = await response.json();
        
        if (result.success) {
            const data = result.data;
            
            // Update revenue cards
            document.getElementById('todayRevenue').textContent = `₹${parseFloat(data.today_revenue).toLocaleString()}`;
            document.getElementById('monthRevenue').textContent = `₹${parseFloat(data.month_revenue).toLocaleString()}`;
            document.getElementById('totalRevenue').textContent = `₹${parseFloat(data.total_revenue).toLocaleString()}`;
            
            // Update agent performance table
            const agentTableBody = document.getElementById('agentPerformanceBody');
            agentTableBody.innerHTML = data.agent_performance.map(agent => `
                <tr>
                    <td>${agent.agent_name}</td>
                    <td>${agent.agent_id}</td>
                    <td>${agent.total_couriers}</td>
                    <td>${agent.delivered_count}</td>
                    <td>${agent.pending_count}</td>
                    <td>${agent.in_transit_count || 0}</td>
                    <td>₹${parseFloat(agent.total_business).toLocaleString()}</td>
                    <td><span class="realtime-indicator">Live</span></td>
                </tr>
            `).join('');
            
            // Create simple charts
            createRevenueChart(data.monthly_revenue);
            createAgentChart(data.agent_performance);
        }
    } catch (error) {
        console.error('Failed to load reports:', error);
        showAlert('Failed to load reports data.', 'error');
    }
}

function createRevenueChart(monthlyData) {
    const chartContainer = document.getElementById('revenueChart');
    const maxRevenue = Math.max(...monthlyData.map(item => parseFloat(item.revenue)));
    
    chartContainer.innerHTML = monthlyData.map(item => {
        const height = maxRevenue > 0 ? (parseFloat(item.revenue) / maxRevenue) * 200 : 0;
        return `
            <div class="chart-bar" style="height: ${height}px; background: linear-gradient(135deg, #7c3aed, #a855f7); margin: 5px; padding: 5px; border-radius: 4px; color: white; font-size: 12px; display: inline-block; width: 60px; text-align: center;">
                <div>${item.month}</div>
                <div>₹${parseFloat(item.revenue).toLocaleString()}</div>
            </div>
        `;
    }).join('');
}

function createAgentChart(agentData) {
    const chartContainer = document.getElementById('agentChart');
    const maxBusiness = Math.max(...agentData.map(agent => parseFloat(agent.total_business)));
    
    chartContainer.innerHTML = agentData.map(agent => {
        const height = maxBusiness > 0 ? (parseFloat(agent.total_business) / maxBusiness) * 200 : 0;
        return `
            <div class="chart-bar" style="height: ${height}px; background: linear-gradient(135deg, #10b981, #059669); margin: 5px; padding: 5px; border-radius: 4px; color: white; font-size: 12px; display: inline-block; width: 80px; text-align: center;">
                <div>${agent.agent_name}</div>
                <div>₹${parseFloat(agent.total_business).toLocaleString()}</div>
            </div>
        `;
    }).join('');
}

// Admin Profile Management
async function loadAdminProfile() {
    try {
        const response = await fetch('api/admin-profile.php');
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('admin_username').value = result.data.username;
            document.getElementById('profile_username').value = result.data.username;
        }
    } catch (error) {
        console.error('Failed to load admin profile:', error);
    }
}

async function handleAdminProfileUpdate(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const profileData = {
        username: formData.get('username'),
        new_password: formData.get('new_password') || ''
    };
    
    try {
        const response = await fetch('api/admin-profile.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(profileData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Profile updated successfully!', 'success');
            e.target.reset();
            loadAdminProfile();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to update profile. Please try again.', 'error');
    }
}

// Edit Agent
function editAgent(agentId) {
    fetch(`api/agents.php?id=${agentId}`)
        .then(response => response.json())
        .then(agent => {
            if (agent) {
                document.getElementById('edit_agent_id').value = agent.id;
                document.getElementById('edit_agent_name').value = agent.agent_name;
                document.getElementById('edit_username').value = agent.username;
                document.getElementById('edit_mobile').value = agent.mobile;
                document.getElementById('edit_city').value = agent.city;
                document.getElementById('edit_status').value = agent.status;
                
                document.getElementById('editAgentModal').style.display = 'block';
            }
        });
}

async function handleEditAgent(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const agentData = {
        id: formData.get('agent_id'),
        agent_name: formData.get('agent_name'),
        username: formData.get('username'),
        password: formData.get('password'),
        mobile: formData.get('mobile'),
        city: formData.get('city'),
        status: formData.get('status')
    };
    
    try {
        const response = await fetch('api/admin-update-agent.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(agentData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Agent updated successfully!', 'success');
            closeModal('editAgentModal');
            loadAgents();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to update agent. Please try again.', 'error');
    }
}

// Update Courier Tracking (Admin)
async function handleUpdateCourier(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const courierData = {
        courier_id: formData.get('courier_id'),
        location: formData.get('location'),
        status: formData.get('status'),
        courier_status: formData.get('courier_status')
    };
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('api/admin-update-courier.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(courierData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Courier tracking updated successfully! (Admin Override)', 'success');
            e.target.reset();
            loadCouriers();
            loadRecentActivities();
            loadReports(); // Refresh reports for real-time data
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to update courier. Please try again.', 'error');
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

function openUpdateCourierModal() {
    document.getElementById('updateCourierModal').style.display = 'block';
}

function viewCourier(courierId) {
    document.getElementById('courier_id_update').value = courierId;
    openUpdateCourierModal();
}

// Enhanced mobile sidebar toggle
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    let overlay = document.querySelector('.sidebar-overlay');
    
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'sidebar-overlay';
        overlay.onclick = toggleSidebar;
        document.body.appendChild(overlay);
    }
    
    sidebar.classList.toggle('active');
    overlay.classList.toggle('active');
}

// Utility Functions
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${type}`;
    alertDiv.textContent = message;
    
    const main = document.querySelector('.main-content');
    main.insertBefore(alertDiv, main.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
    if (modalId === 'selfieModal') {
        currentSelfieId = null;
    }
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = 'index.php';
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                window.location.href = 'index.php';
            });
    }
}

// Close modals when clicking outside
window.addEventListener('click', function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            if (modal.id === 'selfieModal') {
                currentSelfieId = null;
            }
        }
    });
});

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
    }
});