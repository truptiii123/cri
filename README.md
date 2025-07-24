# FastTrack Courier Services - Complete System Setup Guide

## 🚀 NEW FEATURES IMPLEMENTED

### ✅ Database & Infrastructure
- **Complete SQL Database**: Comprehensive database with 500+ Indian cities across all states
- **Admin & Agent Authentication**: Sample login credentials with bypass option for admin
- **Hostinger Compatible**: Optimized for Hostinger/cPanel hosting with phpMyAdmin support

### ✅ Agent Features
- **Courier ID Generation**: Auto-generates IDs using client name (first 2 words) + 4 random digits
- **City Selection**: Searchable dropdown with 500+ Indian cities organized by state
- **Delivery Photo Upload**: Mandatory selfie upload (max 1MB) when marking courier as delivered
- **Cross-Agent Tracking**: Any agent can update tracking for any courier
- **Receipt Generation**: Download receipt in text format with party details
- **Data Export**: Export own courier data to text/notepad file

### ✅ Admin Features
- **Bypass Login**: Direct admin access without password (admin-bypass.php)
- **Password Management**: Change admin password without requiring old password
- **Unlimited Agent Creation**: Create, modify, and manage unlimited agents
- **Complete Courier Management**: Update any courier tracking from admin panel
- **Data Export**: Export all courier data to text/notepad file
- **Recent Activity Tracking**: Real-time updates when agents create/update couriers
- **Delivery Photo Management**: View and delete delivery photos uploaded by agents

### ✅ Website Features
- **Professional Design**: Modern dark/light mix with animated elements
- **Animated Logo**: Floating and spinning logo with gradient effects
- **Announcement Bar**: Scrolling announcement text at top with slow speed
- **WhatsApp Integration**: Customer care button with redirect to mobile number
- **Request Pickup**: Direct WhatsApp link for pickup requests
- **Package Tracking**: Public courier tracking using Courier ID
- **Why Choose Us**: Benefits section with courier company advantages
- **Fleet Showcase**: Visual representation of delivery vehicles
- **Terms & Conditions**: Professional terms section
- **Responsive Design**: Mobile, tablet, and desktop optimized

### ✅ Technical Features
- **Real-time Updates**: Live activity feed and status updates
- **File Upload System**: Secure photo upload with validation
- **Export Functionality**: Text file exports for data portability
- **Cross-platform Compatibility**: Works on all devices and browsers
- **Security Features**: Input validation, file type checking, access controls

## 📋 INSTALLATION STEPS

### Step 1: Hostinger Setup
1. **Login to Hostinger Account**
   - Access your hosting control panel (hPanel)
   - Navigate to your domain's file manager

2. **Database Creation**
   - Go to "MySQL Databases" in hPanel
   - Create database: `courier_services`
   - Create database user with full privileges
   - Note down database credentials

### Step 2: File Upload
1. **Upload All Files**
   - Upload all project files to `public_html` folder
   - Maintain the directory structure as provided
   - Ensure all API files are in the `api/` folder

2. **Set Permissions**
   - Set folder permissions to 755
   - Set PHP file permissions to 644
   - Create `uploads/delivery_photos/` folder with 777 permissions

### Step 3: Database Setup
1. **Import Database**
   - Open phpMyAdmin from hPanel
   - Select your `courier_services` database
   - Import the `database-complete.sql` file
   - Verify all tables are created successfully

2. **Update Configuration**
   - Edit `config.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'your_database_name';
   $username = 'your_database_username';
   $password = 'your_database_password';
   ```

### Step 4: Testing & Configuration
1. **Test Website**
   - Visit your domain to see the homepage
   - Test package tracking with sample courier IDs
   - Verify WhatsApp integration works

2. **Admin Access**
   - **Method 1**: Use bypass login at `yourdomain.com/admin-bypass.php`
   - **Method 2**: Regular login with username: `admin`, password: `admin123`

3. **Agent Access**
   - Username: `agent1`, Password: `admin123`
   - Username: `agent2`, Password: `admin123`
   - Username: `agent3`, Password: `admin123`

## 🔧 CONFIGURATION OPTIONS

### WhatsApp Number Setup
- Edit phone number in `script.js` (openWhatsApp and requestPickup functions)
- Update contact information in `index.php`
- Default: +919876543210

### Announcement Text
- Edit announcement text in `index.php`
- Modify scrolling speed by adjusting CSS animation duration
- Located in the announcement-bar section

### Company Information
- Update company details in footer section
- Modify contact information throughout the site
- Customize terms and conditions as needed

## 📱 FEATURES WALKTHROUGH

### For Agents:
1. **Login**: Use provided credentials or create new agent via admin
2. **Add Courier**: 
   - Fill party details with searchable city selection
   - System auto-generates courier ID (e.g., RAJE1001)
   - Download receipt immediately after creation
3. **Update Tracking**: 
   - Select any courier ID (cross-agent functionality)
   - When marking "Delivered", upload delivery person photo (mandatory)
   - System validates photo size (max 1MB) and format
4. **Export Data**: Download all your courier entries as text file

### For Admin:
1. **Access**: Use bypass login or regular credentials
2. **Agent Management**: Create unlimited agents, modify existing ones
3. **Courier Oversight**: Update any courier tracking, view delivery photos
4. **Data Management**: Export all system data, view comprehensive reports
5. **Settings**: Change password without old password requirement

### For Customers:
1. **Package Tracking**: Enter courier ID on homepage
2. **Request Services**: Use WhatsApp buttons for customer care or pickup requests
3. **Information Access**: View company benefits, fleet information, terms

## 🛠️ TROUBLESHOOTING

### Common Issues:
1. **Database Connection Error**
   - Verify credentials in `config.php`
   - Check database exists and user has permissions
   - Contact Hostinger support if needed

2. **File Upload Issues**
   - Ensure `uploads/delivery_photos/` folder exists
   - Set correct permissions (777 for uploads folder)
   - Check PHP upload limits in hosting settings

3. **Login Problems**
   - Use admin bypass link: `yourdomain.com/admin-bypass.php`
   - Verify database was imported correctly
   - Check if admin table has default user

4. **WhatsApp Not Working**
   - Update phone numbers in `script.js`
   - Ensure numbers include country code (+91 for India)
   - Test links manually

### Performance Optimization:
- Enable gzip compression in hosting settings
- Use Hostinger's caching features
- Optimize images if adding custom content
- Regular database cleanup for better performance

## 📊 SAMPLE DATA

### Default Login Credentials:
- **Admin**: username: `admin`, password: `admin123`
- **Agent 1**: username: `agent1`, password: `admin123`
- **Agent 2**: username: `agent2`, password: `admin123`
- **Agent 3**: username: `agent3`, password: `admin123`

### Sample Courier IDs for Testing:
- `RAJE1001` - Mumbai to Delhi
- `PRIY2001` - Delhi to Bangalore  
- `AMIT3001` - Bangalore to Chennai

### Test Cities Available:
- 500+ cities across all Indian states
- Major metros: Mumbai, Delhi, Bangalore, Chennai, Kolkata, Hyderabad
- Tier-2 cities: Pune, Ahmedabad, Jaipur, Lucknow, Kanpur
- Tier-3 cities and towns across all states

## 🔒 SECURITY FEATURES

- Input validation on all forms
- File upload restrictions (type, size)
- SQL injection prevention
- XSS protection
- Secure session management
- Access control for admin/agent functions

## 📞 SUPPORT

### For Technical Issues:
1. Check Hostinger documentation
2. Verify file permissions and database connectivity
3. Review PHP error logs in hPanel
4. Ensure all files uploaded correctly

### For Customization:
- Modify CSS in `styles.css` for design changes
- Update JavaScript in `script.js` for functionality
- Edit PHP files for backend modifications
- Database schema can be extended as needed

## 🎯 PRODUCTION CHECKLIST

- [ ] Database imported successfully
- [ ] All files uploaded with correct permissions
- [ ] Config.php updated with correct credentials
- [ ] WhatsApp numbers updated
- [ ] Admin password changed from default
- [ ] SSL certificate enabled (recommended)
- [ ] Backup system configured
- [ ] Contact information updated throughout site
- [ ] Terms and conditions customized
- [ ] Testing completed on all devices

## 🚀 GOING LIVE

Your FastTrack Courier Services website is now ready for production use! The system includes:

- Professional courier website with modern design
- Complete admin and agent management system
- Real-time package tracking
- Delivery photo verification system
- Comprehensive data export capabilities
- Mobile-responsive design
- WhatsApp integration for customer support

**Your website is now live and fully functional!**

---

*FastTrack Courier Services - Professional delivery solutions across India*
*Contact: +91 98765 43210 | Email: info@fasttrack.com*