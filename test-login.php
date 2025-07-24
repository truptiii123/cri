<?php
// Test file to check login functionality
require_once 'config.php';

echo "<h2>Database Connection Test</h2>";

try {
    // Test database connection
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection: SUCCESS<br>";
    
    // Test admin table
    $stmt = $pdo->query("SELECT * FROM admin");
    $admins = $stmt->fetchAll();
    echo "✅ Admin table: " . count($admins) . " records found<br>";
    
    foreach ($admins as $admin) {
        echo "   - Username: " . $admin['username'] . "<br>";
    }
    
    // Test agents table
    $stmt = $pdo->query("SELECT * FROM agents");
    $agents = $stmt->fetchAll();
    echo "✅ Agents table: " . count($agents) . " records found<br>";
    
    foreach ($agents as $agent) {
        echo "   - Username: " . $agent['username'] . ", Agent ID: " . $agent['agent_id'] . "<br>";
    }
    
    echo "<h3>Login Credentials for Testing:</h3>";
    echo "<strong>Admin Login:</strong><br>";
    echo "Username: admin<br>";
    echo "Password: admin123<br><br>";
    
    echo "<strong>Agent Login:</strong><br>";
    echo "Username: agent1<br>";
    echo "Password: admin123<br><br>";
    
    echo "Username: agent2<br>";
    echo "Password: admin123<br><br>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>