// Global variables
let currentUser = null;
let currentRole = null;

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
    setupEventListeners();
    setupMobileMenu();
    
    // Add browser compatibility fixes
    addBrowserCompatibilityFixes();
});

function addBrowserCompatibilityFixes() {
    // Fix for older browsers
    if (!window.fetch) {
        console.warn('Fetch API not supported. Please use a modern browser.');
    }
    
    // Fix for iOS Safari
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        document.body.style.webkitOverflowScrolling = 'touch';
    }
    
    // Fix for Internet Explorer
    if (navigator.userAgent.indexOf('MSIE') !== -1 || navigator.appVersion.indexOf('Trident/') > -1) {
        document.body.classList.add('ie-browser');
    }
    
    // Fix viewport for mobile browsers
    const viewport = document.querySelector('meta[name="viewport"]');
    if (!viewport) {
        const meta = document.createElement('meta');
        meta.name = 'viewport';
        meta.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
        document.head.appendChild(meta);
    }
}

function initializeApp() {
    // Check if user is already logged in
    checkLoginStatus();
    
    // Add scroll effect to header
    setupScrollEffect();
    
    // Initialize animations
    initializeAnimations();
}

function setupEventListeners() {
    // Admin login form
    const adminForm = document.getElementById('adminLoginForm');
    if (adminForm) {
        adminForm.addEventListener('submit', handleAdminLogin);
    }
    
    // Agent login form
    const agentForm = document.getElementById('agentLoginForm');
    if (agentForm) {
        agentForm.addEventListener('submit', handleAgentLogin);
    }
    
    // Close modals when clicking outside
    window.addEventListener('click', function(event) {
        const adminModal = document.getElementById('adminModal');
        const agentModal = document.getElementById('agentModal');
        
        if (event.target === adminModal) {
            closeModal('adminModal');
        }
        if (event.target === agentModal) {
            closeModal('agentModal');
        }
    });
}

function setupMobileMenu() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');
    
    if (hamburger && navMenu) {
        hamburger.addEventListener('click', function() {
            hamburger.classList.toggle('active');
            navMenu.classList.toggle('active');
            
            // Prevent body scroll when menu is open
            if (navMenu.classList.contains('active')) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        });
        
        // Close menu when clicking on a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', function() {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
                hamburger.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }
}

function setupScrollEffect() {
    const header = document.querySelector('.header');
    
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                header.style.background = 'rgba(10, 10, 10, 0.98)';
            } else {
                header.style.background = 'rgba(10, 10, 10, 0.95)';
            }
        });
    }
}

function initializeAnimations() {
    // Intersection Observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, observerOptions);
    
    // Observe service cards
    document.querySelectorAll('.service-card').forEach(card => {
        observer.observe(card);
    });
}

// WhatsApp integration
function openWhatsApp() {
    const phoneNumber = '+917767834383';
    const message = 'Hello! I need help with FastTrack courier services.';
    const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

// Request Pickup
function requestPickup() {
    const phoneNumber = '+917767834383';
    const message = 'Hello! I would like to request a pickup for my package from FastTrack Courier Services. Please provide me with the details and schedule.';
    const url = `https://wa.me/${phoneNumber}?text=${encodeURIComponent(message)}`;
    window.open(url, '_blank');
}

// Modal functions
function openAdminLogin() {
    const modal = document.getElementById('adminModal');
    if (modal) {
        modal.style.display = 'block';
        // Clear any previous messages
        clearMessages('adminLoginForm');
    }
}

function openAgentLogin() {
    const modal = document.getElementById('agentModal');
    if (modal) {
        modal.style.display = 'block';
        // Clear any previous messages
        clearMessages('agentLoginForm');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        // Clear form data
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            clearMessages(form.id);
        }
    }
}

function clearMessages(formId) {
    const form = document.getElementById(formId);
    if (form) {
        const messages = form.querySelectorAll('.message');
        messages.forEach(msg => msg.remove());
    }
}

// Login handlers
async function handleAdminLogin(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Logging in...';
    submitBtn.disabled = true;
    
    const formData = new FormData(e.target);
    const loginData = {
        username: formData.get('username'),
        password: formData.get('password'),
        role: 'admin'
    };
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(loginData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentUser = result.user;
            currentRole = 'admin';
            showMessage('Login successful! Redirecting...', 'success', 'adminLoginForm');
            
            setTimeout(() => {
                window.location.href = result.redirect || 'admin-dashboard.php';
            }, 1000);
        } else {
            showMessage(result.message, 'error', 'adminLoginForm');
        }
    } catch (error) {
        console.error('Login error:', error);
        showMessage('Login failed. Please check your connection and try again.', 'error', 'adminLoginForm');
    } finally {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
}

async function handleAgentLogin(e) {
    e.preventDefault();
    
    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    
    // Show loading state
    submitBtn.textContent = 'Logging in...';
    submitBtn.disabled = true;
    
    const formData = new FormData(e.target);
    const loginData = {
        username: formData.get('username'),
        password: formData.get('password'),
        role: 'agent'
    };
    
    try {
        const response = await fetch('auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(loginData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentUser = result.user;
            currentRole = 'agent';
            showMessage('Login successful! Redirecting...', 'success', 'agentLoginForm');
            
            setTimeout(() => {
                window.location.href = result.redirect || 'agent-dashboard.php';
            }, 1000);
        } else {
            showMessage(result.message, 'error', 'agentLoginForm');
        }
    } catch (error) {
        console.error('Login error:', error);
        showMessage('Login failed. Please check your connection and try again.', 'error', 'agentLoginForm');
    } finally {
        // Reset button state
        submitBtn.textContent = originalText;
        submitBtn.disabled = false;
    }
}

// Package tracking
async function trackPackage() {
    const trackingId = document.getElementById('trackingId').value.trim();
    const resultDiv = document.getElementById('trackingResult');
    
    if (!trackingId) {
        showMessage('Please enter a tracking ID', 'error');
        return;
    }
    
    // Show loading
    resultDiv.innerHTML = '<div class="loading"></div><p>Tracking package...</p>';
    resultDiv.classList.add('show');
    
    try {
        const response = await fetch(`track.php?id=${encodeURIComponent(trackingId)}`);
        const result = await response.json();
        
        if (result.success) {
            displayTrackingResult(result.data);
        } else {
            resultDiv.innerHTML = `<p class="message error">${result.message}</p>`;
        }
    } catch (error) {
        console.error('Tracking error:', error);
        resultDiv.innerHTML = '<p class="message error">Failed to track package. Please try again.</p>';
    }
}

function displayTrackingResult(data) {
    const resultDiv = document.getElementById('trackingResult');
    
    const html = `
        <div class="tracking-info">
            <h3>Tracking Information</h3>
            <div class="tracking-details">
                <p><strong>Courier ID:</strong> ${data.courier_id}</p>
                <p><strong>Party Name:</strong> ${data.party_name}</p>
                <p><strong>From:</strong> ${data.from_city}</p>
                <p><strong>To:</strong> ${data.to_city}</p>
                <p><strong>Status:</strong> <span class="status ${data.status}">${data.status.toUpperCase()}</span></p>
                <p><strong>Expected Delivery:</strong> ${data.delivery_date}</p>
                ${data.delivery_person ? `<p><strong>Delivery Person:</strong> ${data.delivery_person}</p>` : ''}
            </div>
            <div class="tracking-history">
                <h4>Tracking History</h4>
                <div class="timeline">
                    ${data.tracking_history.map(item => `
                        <div class="timeline-item">
                            <div class="timeline-dot"></div>
                            <div class="timeline-content">
                                <p><strong>${item.location}</strong></p>
                                <p>${item.status}</p>
                                <small>${item.timestamp}</small>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        </div>
    `;
    
    resultDiv.innerHTML = html;
}

// Utility functions
function showMessage(message, type, formId = null) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `message ${type}`;
    messageDiv.textContent = message;
    
    let targetElement;
    
    if (formId) {
        const form = document.getElementById(formId);
        if (form) {
            // Remove any existing messages
            const existingMessages = form.querySelectorAll('.message');
            existingMessages.forEach(msg => msg.remove());
            
            // Add new message at the top of the form
            form.insertBefore(messageDiv, form.firstChild);
            targetElement = form;
        }
    } else {
        // Find the active modal or use body
        const activeModal = document.querySelector('.modal[style*="block"]');
        if (activeModal) {
            const form = activeModal.querySelector('form');
            if (form) {
                form.appendChild(messageDiv);
                targetElement = form;
            }
        } else {
            document.body.appendChild(messageDiv);
            targetElement = document.body;
        }
    }
    
    // Remove message after 5 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 5000);
}

function scrollToSection(sectionId) {
    const element = document.getElementById(sectionId);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth' });
    }
}

function checkLoginStatus() {
    // Check if user is logged in and redirect accordingly
    fetch('check-session.php')
        .then(response => response.json())
        .then(data => {
            if (data.logged_in) {
                currentUser = data.user;
                currentRole = data.role;
                updateUIForLoggedInUser();
            }
        })
        .catch(error => {
            console.log('Session check failed:', error);
        });
}

function updateUIForLoggedInUser() {
    // Update UI elements for logged-in user
    const authButtons = document.querySelector('.auth-buttons');
    if (authButtons) {
        if (currentRole === 'admin') {
            authButtons.innerHTML = `
                <a href="admin-dashboard.php" class="admin-btn">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <button onclick="logout()" class="agent-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            `;
        } else if (currentRole === 'agent') {
            authButtons.innerHTML = `
                <a href="agent-dashboard.php" class="agent-btn">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <button onclick="logout()" class="admin-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            `;
        }
    }
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        fetch('logout.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentUser = null;
                    currentRole = null;
                    window.location.href = 'index.php';
                }
            })
            .catch(error => {
                console.error('Logout error:', error);
                // Force redirect even if logout fails
                window.location.href = 'index.php';
            });
    }
}

// Add CSS for timeline and tracking styles
const additionalStyles = `
    .tracking-info {
        text-align: left;
    }
    
    .tracking-details {
        margin: 1rem 0;
        padding: 1rem;
        background: rgba(248, 250, 252, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 10px;
        border: 1px solid rgba(102, 126, 234, 0.2);
    }
    
    .tracking-details p {
        margin: 0.5rem 0;
        color: #1f2937;
    }
    
    .status {
        padding: 0.25rem 0.5rem;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .status.pending {
        background: #f59e0b;
        color: #fff;
    }
    
    .status.in_transit {
        background: #3b82f6;
        color: #fff;
    }
    
    .status.delivered {
        background: #10b981;
        color: #fff;
    }
    
    .timeline {
        position: relative;
        padding-left: 2rem;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        left: 0.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #667eea, #764ba2);
    }
    
    .timeline-item {
        position: relative;
        margin: 1rem 0;
    }
    
    .timeline-dot {
        position: absolute;
        left: -2rem;
        top: 0.5rem;
        width: 1rem;
        height: 1rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .timeline-content {
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border-radius: 8px;
        margin-left: 0.5rem;
        border: 1px solid rgba(102, 126, 234, 0.1);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .timeline-content p {
        color: #1f2937;
        margin: 0.25rem 0;
    }
    
    .timeline-content small {
        color: #6b7280;
    }
`;

// Add the styles to the page
const styleSheet = document.createElement('style');
styleSheet.textContent = additionalStyles;
document.head.appendChild(styleSheet);