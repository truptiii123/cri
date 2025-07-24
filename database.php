<?php
// Database configuration for Hostinger
$host = 'localhost'; // Change to your Hostinger database host
$dbname = 'u261459251_courier_ser'; // Change to your database name
$username = 'u261459251_courier_nsk'; // Change to your database username
$password = 'Vishraj@9884'; // Change to your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Set timezone to Indian time
date_default_timezone_set('Asia/Kolkata');
?>