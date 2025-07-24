<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <title>FastTrack Courier Services - Professional Delivery Solutions</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="announcement-content">
            <span class="announcement-text">🚚 Welcome to FastTrack Courier Services - Fast, Reliable & Secure Delivery Solutions Across India 📦 Same Day Delivery Available 🚀 Track Your Package in Real-Time</span>
        </div>
    </div>

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
                    <li><a href="#why-choose-us">Why Choose Us</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li><a href="#" class="help-btn" onclick="openWhatsApp()">
                        <i class="fab fa-whatsapp"></i> Customer Care
                    </a></li>
                </ul>
                <div class="auth-buttons">
                    <button class="pickup-btn" onclick="requestPickup()">
                        <i class="fas fa-truck"></i> Request Pickup
                    </button>
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
                <h1>FastTrack Courier Services</h1>
                <p>Fast, Reliable & Secure Delivery Solutions Across India</p>
                <div class="hero-buttons">
                    <button class="cta-btn" onclick="scrollToSection('tracking')">
                        <i class="fas fa-search"></i> Track Package
                    </button>
                    <button class="cta-btn secondary" onclick="requestPickup()">
                        <i class="fas fa-truck"></i> Request Pickup
                    </button>
                </div>
            </div>
            <div class="hero-bg">
                <div class="floating-elements">
                    <i class="fas fa-box"></i>
                    <i class="fas fa-truck"></i>
                    <i class="fas fa-paper-plane"></i>
                    <i class="fas fa-shipping-fast"></i>
                    <i class="fas fa-globe"></i>
                </div>
            </div>
        </section>

        <section id="tracking" class="tracking-section">
            <div class="container">
                <h2>Track Your Package</h2>
                <div class="tracking-form">
                    <input type="text" id="trackingId" placeholder="Enter Courier ID (e.g., RAJE1001)" required autocomplete="off">
                    <button onclick="trackPackage()">
                        <i class="fas fa-search"></i> Track Now
                    </button>
                </div>
                <div id="trackingResult" class="tracking-result"></div>
            </div>
        </section>

        <section id="services" class="services-section" style="text-align: center; padding: 40px 0;">
    <div class="container">
        <h2>Our Services</h2>
        <div class="services-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; justify-items: center; margin-top: 30px;">
            <div class="service-card">
                <i class="fas fa-shipping-fast fa-2x"></i>
                <h3>Express Delivery</h3>
                <p>Same day and next day delivery options for urgent shipments</p>
            </div>  
            <div class="service-card">
                <i class="fas fa-shield-alt fa-2x"></i>
                <h3>Secure Transport</h3>
                <p>Insurance coverage and real-time tracking for valuable items</p>
            </div>
            <div class="service-card">
                <i class="fas fa-globe fa-2x"></i>
                <h3>Nationwide Coverage</h3>
                <p>Delivery to 500+ cities and towns across India</p>
            </div>
            <div class="service-card">
                <i class="fas fa-mobile-alt fa-2x"></i>
                <h3>Real-time Tracking</h3>
                <p>Live updates and notifications on your package status</p>
            </div>
            <div class="service-card">
                <i class="fas fa-clock fa-2x"></i>
                <h3>Time Guarantee</h3>
                <p>On-time delivery with money-back guarantee</p>
            </div>
            <div class="service-card">
                <i class="fas fa-headset fa-2x"></i>
                <h3>24/7 Support</h3>
                <p>Round-the-clock customer support via WhatsApp</p>
            </div>
        </div>
    </div>
</section>

<section id="why-choose-us" class="why-choose-section" style="text-align: center; padding: 40px 0;">
    <div class="container">
        <h2>Why Choose FastTrack?</h2>
        <div class="benefits-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; justify-items: center; margin-top: 30px;">
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-rocket fa-2x"></i>
                </div>
                <h3>Lightning Fast</h3>
                <p>Express delivery in 24-48 hours to major cities</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-rupee-sign fa-2x"></i>
                </div>
                <h3>Best Prices</h3>
                <p>Competitive rates with no hidden charges</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-award fa-2x"></i>
                </div>
                <h3>Trusted Service</h3>
                <p>10+ years of reliable courier services</p>
            </div>
            <div class="benefit-card">
                <div class="benefit-icon">
                    <i class="fas fa-map-marked-alt fa-2x"></i>
                </div>
                <h3>Wide Network</h3>
                <p>500+ pickup and delivery locations</p>
            </div>
        </div>

        <div class="courier-images" style="margin-top: 40px;">
            <h3>Our Fleet</h3>
            <div class="fleet-showcase" style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; margin-top: 20px;">
                <div class="fleet-item" style="display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-motorcycle fa-2x"></i>
                    <span>Bikes for City Delivery</span>
                </div>
                <div class="fleet-item" style="display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-truck fa-2x"></i>
                    <span>Trucks for Long Distance</span>
                </div>
                <div class="fleet-item" style="display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-plane fa-2x"></i>
                    <span>Air Cargo for Express</span>
                </div>
                <div class="fleet-item" style="display: flex; flex-direction: column; align-items: center;">
                    <i class="fas fa-ship fa-2x"></i>
                    <span>Sea Freight for Bulk</span>
                </div>
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
                            <span>123 Business District, Mumbai, India</span>
                        </div>
                        <div class="contact-item">
                            <i class="fab fa-whatsapp"></i>
                            <span>WhatsApp: +91 98765 43210</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="terms-section">
            <div class="container">
                <h3>Terms & Conditions</h3>
                <div class="terms-content">
                    <p><strong>Delivery Terms:</strong> Standard delivery within 3-5 business days. Express delivery available for urgent shipments.</p>
                    <p><strong>Insurance:</strong> All packages are insured up to ₹10,000. Additional insurance available for valuable items.</p>
                    <p><strong>Prohibited Items:</strong> We do not transport hazardous materials, perishable goods, or illegal items.</p>
                    <p><strong>Liability:</strong> FastTrack Courier Services is liable for lost or damaged packages as per our insurance policy.</p>
                    <p><strong>Payment:</strong> Cash on delivery and online payment options available. Corporate accounts with credit terms.</p>
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
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <!-- Agent Login Modal -->
    <div id="agentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('agentModal')">&times;</span>
            <h2>Agent Login</h2>
            <form id="agentLoginForm">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>FastTrack Courier</h4>
                    <p>Your trusted partner for fast and reliable delivery services across India.</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#tracking">Track Package</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <li><a href="#" onclick="requestPickup()">Request Pickup</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Contact Info</h4>
                    <p><i class="fas fa-phone"></i> +91 98765 43210</p>
                    <p><i class="fas fa-envelope"></i> info@fasttrack.com</p>
                    <p><i class="fab fa-whatsapp"></i> WhatsApp Support</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 FastTrack Courier Services. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>