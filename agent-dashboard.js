// Agent Dashboard JavaScript - Enhanced Version
let currentSection = 'dashboard';
let cities = [];
let lastCourierId = null;

document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadCities();
    loadRecentCouriers();
    setupEventListeners();
    updateDateTime();
    
    // Update time every second
    setInterval(updateDateTime, 1000);
    
    // Auto-refresh data every 30 seconds
    setInterval(function() {
        if (currentSection === 'dashboard') {
            loadRecentCouriers();
        } else if (currentSection === 'my-couriers') {
            loadMyCouriers();
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
    // Add courier form
    document.getElementById('addCourierForm').addEventListener('submit', handleAddCourier);
    
    // Update tracking form
    document.getElementById('updateTrackingForm').addEventListener('submit', handleUpdateTracking);
    
    // City search inputs
    setupCitySearch('from_city');
    setupCitySearch('to_city');
    
    // Tracking status change
    document.getElementById('tracking_status').addEventListener('change', handleTrackingStatusChange);
}

function setupFormSubmissions() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
        });
    });
}

async function loadCities() {
    try {
        const response = await fetch('api/cities.php');
        cities = await response.json();
        
        // Populate city dropdowns
        populateCityDropdown('from_city');
        populateCityDropdown('to_city');
    } catch (error) {
        console.error('Failed to load cities:', error);
    }
}

function populateCityDropdown(selectId) {
    const select = document.getElementById(selectId);
    if (!select) return;
    
    select.innerHTML = '<option value="">Select City</option>';
    
    // Group cities by state
    const stateGroups = {};
    cities.forEach(city => {
        if (!stateGroups[city.state]) {
            stateGroups[city.state] = [];
        }
        stateGroups[city.state].push(city);
    });
    
    // Add options grouped by state
    Object.keys(stateGroups).sort().forEach(state => {
        const optgroup = document.createElement('optgroup');
        optgroup.label = state;
        
        stateGroups[state].forEach(city => {
            const option = document.createElement('option');
            option.value = city.city_name;
            option.textContent = city.city_name;
            optgroup.appendChild(option);
        });
        
        select.appendChild(optgroup);
    });
}

function setupCitySearch(inputId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    // Convert select to searchable input
    const select = input;
    const wrapper = document.createElement('div');
    wrapper.className = 'searchable-select';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'city-search-input';
    searchInput.placeholder = 'Search and select city...';
    searchInput.name = select.name;
    searchInput.required = select.required;
    
    const dropdown = document.createElement('div');
    dropdown.className = 'city-dropdown';
    dropdown.style.display = 'none';
    
    select.parentNode.insertBefore(wrapper, select);
    wrapper.appendChild(searchInput);
    wrapper.appendChild(dropdown);
    select.remove();
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const filteredCities = cities.filter(city => 
            city.city_name.toLowerCase().includes(query) ||
            city.state.toLowerCase().includes(query)
        );
        
        if (query.length > 0 && filteredCities.length > 0) {
            dropdown.innerHTML = filteredCities.slice(0, 10).map(city => 
                `<div class="city-option" onclick="selectCity('${inputId}', '${city.city_name}')">${city.city_name}, ${city.state}</div>`
            ).join('');
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });
    
    searchInput.addEventListener('blur', function() {
        setTimeout(() => dropdown.style.display = 'none', 200);
    });
}

function selectCity(inputId, cityName) {
    const wrapper = document.querySelector(`#${inputId}`).closest('.searchable-select');
    const input = wrapper.querySelector('.city-search-input');
    const dropdown = wrapper.querySelector('.city-dropdown');
    
    input.value = cityName;
    dropdown.style.display = 'none';
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
        case 'my-couriers':
            loadMyCouriers();
            break;
    }
}

// Add Courier
async function handleAddCourier(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Get city values from searchable inputs
    const fromCityInput = document.querySelector('#from_city').closest('.searchable-select')?.querySelector('.city-search-input');
    const toCityInput = document.querySelector('#to_city').closest('.searchable-select')?.querySelector('.city-search-input');
    
    const courierData = {
        to_party_name: formData.get('to_party_name'),
        from_party_name: formData.get('from_party_name'),
        mobile: formData.get('mobile'),
        address: formData.get('address'),
        from_city: fromCityInput ? fromCityInput.value : formData.get('from_city'),
        to_city: toCityInput ? toCityInput.value : formData.get('to_city'),
        amount: formData.get('amount') || 0,
        delivery_date: formData.get('delivery_date'),
        remarks: formData.get('remarks')
    };
    
    try {
        const response = await fetch('api/agent-couriers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(courierData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Courier added successfully! Courier ID: ' + result.courier_id, 'success');
            lastCourierId = result.courier_id;
            
            // Show download receipt button
            const downloadBtn = document.getElementById('downloadReceiptBtn');
            downloadBtn.style.display = 'inline-flex';
            
            e.target.reset();
            
            // Reset city search inputs
            if (fromCityInput) fromCityInput.value = '';
            if (toCityInput) toCityInput.value = '';
            
            loadRecentCouriers();
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to add courier. Please try again.', 'error');
    }
}

function downloadReceipt() {
    if (!lastCourierId) {
        showAlert('No recent courier to download receipt for.', 'error');
        return;
    }
    
    const url = `api/generate-receipt.php?courier_id=${lastCourierId}`;
    const a = document.createElement('a');
    a.href = url;
    a.download = `receipt_${lastCourierId}.txt`;
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    showAlert('Receipt download started!', 'success');
}

// Load Recent Couriers
async function loadRecentCouriers() {
    try {
        const response = await fetch('api/agent-couriers.php?recent=5');
        const couriers = await response.json();
        
        const container = document.getElementById('recentCouriers');
        if (couriers.length === 0) {
            container.innerHTML = '<p>No recent couriers</p>';
            return;
        }
        
        container.innerHTML = couriers.map(courier => `
            <div class="activity-item">
                <h4>Courier ID: ${courier.courier_id}</h4>
                <p><strong>To Party:</strong> ${courier.to_party_name || courier.party_name}</p>
                <p><strong>From Party:</strong> ${courier.from_party_name || courier.party_name}</p>
                <p><strong>Route:</strong> ${courier.from_city} → ${courier.to_city}</p>
                <p><strong>Status:</strong> <span class="status-badge ${courier.status}">${courier.status}</span></p>
                <p><small>${new Date(courier.created_at).toLocaleDateString()}</small></p>
            </div>
        `).join('');
    } catch (error) {
        console.error('Failed to load recent couriers:', error);
    }
}

// Load My Couriers
async function loadMyCouriers() {
    try {
        const response = await fetch('api/agent-couriers.php');
        const couriers = await response.json();
        
        const tbody = document.getElementById('myCouriersTableBody');
        tbody.innerHTML = couriers.map(courier => `
            <tr>
                <td>${courier.courier_id}</td>
                <td>${courier.to_party_name || courier.party_name}</td>
                <td>${courier.from_party_name || courier.party_name}</td>
                <td>${courier.mobile}</td>
                <td>${courier.from_city}</td>
                <td>${courier.to_city}</td>
                <td>₹${parseFloat(courier.amount).toLocaleString()}</td>
                <td><span class="status-badge ${courier.status}">${courier.status}</span></td>
                <td>${new Date(courier.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="action-btn edit" onclick="fillTrackingForm('${courier.courier_id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="action-btn view" onclick="downloadCourierReceipt('${courier.courier_id}')" title="Download Receipt">
                        <i class="fas fa-download"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    } catch (error) {
        console.error('Failed to load couriers:', error);
    }
}

function fillTrackingForm(courierId) {
    document.getElementById('courier_id_track').value = courierId;
    showSection('tracking');
}

function downloadCourierReceipt(courierId) {
    const url = `api/generate-receipt.php?courier_id=${courierId}`;
    const a = document.createElement('a');
    a.href = url;
    a.download = `receipt_${courierId}.txt`;
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    showAlert('Receipt download started!', 'success');
}

// Update Tracking
function handleTrackingStatusChange() {
    const status = document.getElementById('tracking_status').value;
    const deliverySection = document.getElementById('deliverySection');
    const photoSection = document.getElementById('photoSection');
    
    if (status === 'Delivered') {
        deliverySection.style.display = 'block';
        photoSection.style.display = 'block';
        document.getElementById('delivery_person').required = true;
        document.getElementById('delivery_photo').required = true;
    } else {
        deliverySection.style.display = 'none';
        photoSection.style.display = 'none';
        document.getElementById('delivery_person').required = false;
        document.getElementById('delivery_photo').required = false;
    }
}

async function handleUpdateTracking(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const status = formData.get('status');
    
    // Show loading state
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    // If marking as delivered, upload photo first
    if (status === 'Delivered') {
        const photoFile = formData.get('delivery_photo');
        const deliveryPerson = formData.get('delivery_person');
        
        if (!photoFile || !deliveryPerson) {
            showAlert('Delivery person name and selfie are required for delivered status', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        // Check file size (500KB limit)
        if (photoFile.size > 500 * 1024) {
            showAlert('Photo size must be less than 500KB', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
        
        try {
            // Upload delivery photo
            const photoFormData = new FormData();
            photoFormData.append('courier_id', formData.get('courier_id'));
            photoFormData.append('delivery_person', deliveryPerson);
            photoFormData.append('delivery_photo', photoFile);
            
            const photoResponse = await fetch('api/upload-delivery-photo.php', {
                method: 'POST',
                body: photoFormData
            });
            
            const photoResult = await photoResponse.json();
            
            if (!photoResult.success) {
                showAlert(photoResult.message, 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                return;
            }
        } catch (error) {
            showAlert('Failed to upload delivery photo', 'error');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            return;
        }
    }
    
    // Update tracking
    const trackingData = {
        courier_id: formData.get('courier_id'),
        location: formData.get('location'),
        status: status,
        courier_status: formData.get('courier_status')
    };
    
    try {
        const response = await fetch('api/update-tracking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(trackingData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showAlert('Courier tracking updated successfully! (Cross-agent update)', 'success');
            e.target.reset();
            handleTrackingStatusChange(); // Reset delivery sections
            loadRecentCouriers();
            if (currentSection === 'my-couriers') {
                loadMyCouriers();
            }
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        showAlert('Failed to update tracking. Please try again.', 'error');
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}

// Export Data
function exportMyData() {
    const url = 'api/export-text.php';
    const a = document.createElement('a');
    a.href = url;
    a.download = `my_courier_data_${new Date().toISOString().split('T')[0]}.txt`;
    a.target = '_blank';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    
    showAlert('Export started! File will download shortly.', 'success');
}

// Filter Couriers
function filterMyCouriers() {
    loadMyCouriers();
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

// Add CSS for city search
const citySearchStyles = `
    .searchable-select {
        position: relative;
    }
    
    .city-search-input {
        width: 100%;
        padding: 1rem;
        border: 2px solid rgba(124, 58, 237, 0.2);
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.9);
        color: #2d1b69;
        font-size: 1rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }
    
    .city-search-input:focus {
        outline: none;
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    
    .city-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        border: 2px solid rgba(124, 58, 237, 0.2);
        border-top: none;
        border-radius: 0 0 10px 10px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 25px rgba(124, 58, 237, 0.1);
    }
    
    .city-option {
        padding: 0.75rem;
        cursor: pointer;
        border-bottom: 1px solid rgba(124, 58, 237, 0.1);
        color: #2d1b69;
        transition: background-color 0.2s ease;
        font-weight: 500;
    }
    
    .city-option:hover {
        background: rgba(124, 58, 237, 0.1);
        color: #7c3aed;
    }
    
    .city-option:last-child {
        border-bottom: none;
    }
    
    .receipt-option {
        margin: 1rem 0;
        padding: 1rem;
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid #10b981;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .receipt-option .btn {
        margin-top: 0.5rem;
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = citySearchStyles;
document.head.appendChild(styleSheet);

// Handle window resize
window.addEventListener('resize', function() {
    if (window.innerWidth > 768) {
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
    }
});