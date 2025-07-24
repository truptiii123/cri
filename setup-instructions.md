# FastTrack Courier Services - Setup Instructions

## Prerequisites
- Web hosting account with PHP and MySQL support (Hostinger recommended)
- Basic understanding of file uploads and database management

## Step 1: Hostinger Account Setup

1. **Sign up for Hostinger** (if you don't have an account)
   - Go to [Hostinger](https://www.hostinger.com)
   - Choose a hosting plan (Premium or Business plan recommended)
   - Complete the signup process

2. **Access your hosting control panel**
   - Login to your Hostinger account
   - Go to your hosting dashboard
   - Look for "hPanel" or "Control Panel"

## Step 2: Database Setup

1. **Create MySQL Database**
   - In hPanel, find "MySQL Databases" section
   - Click "Create Database"
   - Database name: `courier_services`
   - Username: Create a database user (remember these credentials)
   - Password: Create a strong password
   - Click "Create"

2. **Import Database Schema**
   - Go to "phpMyAdmin" in your control panel
   - Select your `courier_services` database
   - Click "Import" tab
   - Upload the `database.sql` file
   - Click "Go" to execute

## Step 3: Upload Files

1. **Access File Manager**
   - In hPanel, find "File Manager"
   - Navigate to `public_html` folder
   - Delete any existing files (like index.html)

2. **Upload Website Files**
   - Upload ALL project files to `public_html` folder
   - Make sure the file structure is maintained:
     ```
     public_html/
     ├── index.php
     ├── config.php
     ├── styles.css
     ├── script.js
     ├── auth.php
     ├── track.php
     ├── admin-dashboard.php
     ├── dashboard.css
     ├── admin-dashboard.js
     ├── api/
     │   ├── agents.php
     │   ├── couriers.php
     │   ├── recent-activities.php
     │   └── change-password.php
     └── other files...
     ```

## Step 4: Configure Database Connection

1. **Edit config.php**
   - In File Manager, find and edit `config.php`
   - Update the database connection details:
     ```php
     $host = 'localhost';
     $dbname = 'your_database_name';
     $username = 'your_database_username';
     $password = 'your_database_password';
     ```
   - Save the file

## Step 5: Set File Permissions

1. **Set Correct Permissions**
   - Right-click on folders and select "Permissions"
   - Set folders to `755`
   - Set PHP files to `644`
   - Make sure the `api` folder has proper permissions

## Step 6: Test Your Website

1. **Access Your Website**
   - Go to your domain name (e.g., `yoursite.com`)
   - You should see the FastTrack Courier homepage

2. **Test Admin Login**
   - Click "Admin" button
   - Default credentials:
     - Username: `admin`
     - Password: `admin123`
   - **IMPORTANT:** Change this password immediately after first login!

## Step 7: Initial Setup

1. **Change Admin Password**
   - Login to admin panel
   - Go to Settings
   - Change the default password

2. **Create Your First Agent**
   - In admin panel, go to "Agents"
   - Click "Add Agent"
   - Fill in the details
   - The agent can now login and add couriers

3. **Update WhatsApp Number**
   - In `script.js`, find the `openWhatsApp()` function
   - Update the phone number to your business WhatsApp number
   - Or update it in the Settings section

## Step 8: SSL Certificate (Recommended)

1. **Enable SSL**
   - In hPanel, find "SSL" section
   - Enable "Free SSL Certificate"
   - This ensures your website is secure (https://)

## Important Security Notes

1. **Change Default Passwords**
   - Change the default admin password immediately
   - Use strong passwords for all accounts

2. **Regular Backups**
   - Hostinger provides backup options
   - Set up automatic backups for your database and files

3. **Keep Software Updated**
   - Regularly check for any security updates
   - Monitor your website for any issues

## Troubleshooting Common Issues

### Database Connection Error
- Check if database credentials in `config.php` are correct
- Ensure database exists and user has proper permissions
- Contact Hostinger support if needed

### Files Not Loading
- Check file permissions (folders: 755, files: 644)
- Ensure all files are uploaded to `public_html`
- Clear browser cache

### Admin Login Not Working
- Check if database was imported correctly
- Verify the admin table has the default user
- Check PHP error logs in hPanel

### WhatsApp Button Not Working
- Update the phone number in `script.js`
- Ensure the number includes country code (+91 for India)

## Support

If you encounter any issues:
1. Check Hostinger's documentation
2. Contact Hostinger support
3. Check PHP error logs in your hosting control panel
4. Ensure all files are uploaded and have correct permissions

## Default Login Credentials

**Admin:**
- Username: `admin`
- Password: `admin123`

**Remember to change these immediately after first login!**

## Features Overview

### Admin Panel Features:
- Dashboard with statistics
- Agent management (add, edit, delete)
- Courier management and tracking
- Reports and analytics
- Settings and password management
- Excel export functionality

### Agent Panel Features:
- Add new couriers
- View own courier entries
- Update courier status
- Track deliveries
- View business statistics

### Public Features:
- Package tracking
- Professional homepage
- WhatsApp integration
- Mobile responsive design

Your FastTrack Courier Services website is now ready to use!