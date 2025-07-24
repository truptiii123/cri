<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FastTrack Courier Services</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo">
                    <i class="fas fa-shipping-fast"></i>
                    <span>FastTrack</span>
                </div>
                <ul class="nav-menu">
                    <li><a href="#home">Home</a></li>
                    <li><a href="#services">Services</a></li>
                    <li><a href="#tracking">Track</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#" class="help-btn" onclick="openWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Help
                    </a></li>
                </ul>
                <div class="auth-buttons">
                    <button class="admin-btn" onclick="openAdminLogin()">
                        <i class="fas fa-user-shield"></i> Admin
                    </button>
                    <button class="agent-btn" onclick="openAgentLogin()">
                        <i class="fas fa-user"></i> Agent
                    </button>
                </div>
                <div class="hamburger">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section id="home" class="hero">
            <div class="hero-content">
                <h1>Professional Courier Services</h1>
                <p>Fast, Reliable, and Secure Delivery Solutions Across India</p>
                <div class="hero-buttons">
                    <button class="cta-btn" onclick="scrollToSection('tracking')">
                        <i class="fas fa-search"></i> Track Package
                    </button>
                    <button class="cta-btn secondary" onclick="scrollToSection('services')">
                        <i class="fas fa-info-circle"></i> Our Services
                    </button>
                </div>
            </div>
            <div class="hero-bg">
                <div class="floating-elements">
                    <i class="fas fa-box"></i>
                    <i class="fas fa-truck"></i>
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
        </section>

        <section id="tracking" class="tracking-section">
            <div class="container">
                <h2>Track Your Package</h2>
                <div class="tracking-form">
                    <input type="text" id="trackingId" placeholder="Enter Tracking ID" required>
                    <button onclick="trackPackage()">
                        <i class="fas fa-search"></i> Track Now
                    </button>
                </div>
                <div id="trackingResult" class="tracking-result"></div>
            </div>
        </section>

        <section id="services" class="services-section">
            <div class="container">
                <h2>Our Services</h2>
                <div class="services-grid">
                    <div class="service-card">
                        <i class="fas fa-shipping-fast"></i>
                        <h3>Express Delivery</h3>
                        <p>Same day and next day delivery options</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-shield-alt"></i>
                        <h3>Secure Transport</h3>
                        <p>Insurance and tracking for valuable items</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-globe"></i>
                        <h3>Nationwide Coverage</h3>
                        <p>Delivery to all major cities and towns</p>
                    </div>
                    <div class="service-card">
                        <i class="fas fa-mobile-alt"></i>
                        <h3>Real-time Tracking</h3>
                        <p>Live updates on your package status</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="contact" class="contact-section">
            <div class="container">
                <h2>Contact Us</h2>
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <span>+91 98765 43210</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <span>info@fasttrack.com</span>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>123 Business District, Mumbai</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Admin Login Modal -->
    <div id="adminModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('adminModal')">&times;</span>
            <h2>Admin Login</h2>
            <form id="adminLoginForm">
                <input type="text" name="username" placeholder="Enter: admin" value="admin" required>
                <button type="submit">Login</button>
                <p style="color: #a1a1aa; font-size: 0.9rem; margin-top: 1rem;">
                    Just enter "admin" and click Login
                </p>
            </form>
        </div>
    </div>

    <!-- Agent Login Modal -->
    <div id="agentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('agentModal')">&times;</span>
            <h2>Agent Login</h2>
            <form id="agentLoginForm">
                <input type="text" name="username" placeholder="Enter: agent1 or agent2" required>
                <button type="submit">Login</button>
                <p style="color: #a1a1aa; font-size: 0.9rem; margin-top: 1rem;">
                    Enter "agent1" or "agent2" and click Login
                </p>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 FastTrack Courier Services. All rights reserved.</p>
        </div>
    </footer>

    <script src="script-no-password.js"></script>
</body>
</html>