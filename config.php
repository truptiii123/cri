<?php
// Database configuration
$host = 'localhost';
$dbname = 'u261459251_courier_ser';
$username = 'u261459251_courier_nsk';
$password = 'Vishraj@9884';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    // Set timezone to Indian time
    $pdo->exec("SET time_zone = '+05:30'");
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    
    // Set session cookie parameters for better security
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
}

// Set timezone to Indian time
date_default_timezone_set('Asia/Kolkata');

// Helper functions
function generateCourierId($partyName) {
    $prefix = strtoupper(substr($partyName, 0, 3));
    $randomNumber = rand(1000, 9999);
    return $prefix . $randomNumber;
}

function generateAgentId($agentName) {
    $prefix = strtoupper(substr($agentName, 0, 3));
    $randomNumber = rand(100, 999);
    return $prefix . $randomNumber;
}

function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function isAgentLoggedIn() {
    return isset($_SESSION['agent_logged_in']) && $_SESSION['agent_logged_in'] === true;
}

function calculateDeliveryDays($fromCity, $toCity) {
    // Simple calculation based on distance approximation (in kilometers)
    $distances = [
        'Mumbai' => ['Delhi' => 1400, 'Bangalore' => 980, 'Chennai' => 1340, 'Kolkata' => 1650, 'Hyderabad' => 710],
        'Delhi' => ['Mumbai' => 1400, 'Bangalore' => 2150, 'Chennai' => 2180, 'Kolkata' => 1470, 'Hyderabad' => 1570],
        'Bangalore' => ['Mumbai' => 980, 'Delhi' => 2150, 'Chennai' => 350, 'Kolkata' => 1870, 'Hyderabad' => 570],
        'Chennai' => ['Mumbai' => 1340, 'Delhi' => 2180, 'Bangalore' => 350, 'Kolkata' => 1670, 'Hyderabad' => 630],
        'Kolkata' => ['Mumbai' => 1650, 'Delhi' => 1470, 'Bangalore' => 1870, 'Chennai' => 1670, 'Hyderabad' => 1270],
        'Hyderabad' => ['Mumbai' => 710, 'Delhi' => 1570, 'Bangalore' => 570, 'Chennai' => 630, 'Kolkata' => 1270]
    ];
    
    $distance = $distances[$fromCity][$toCity] ?? 800; // Default 800km if not found
    return ceil($distance / 400); // Assuming 400km per day average speed
}
?>