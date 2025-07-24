<?php
require_once 'config.php';

header('Content-Type: application/json');

if (isAdminLoggedIn()) {
    echo json_encode([
        'logged_in' => true,
        'role' => 'admin',
        'user' => [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username']
        ]
    ]);
} elseif (isAgentLoggedIn()) {
    echo json_encode([
        'logged_in' => true,
        'role' => 'agent',
        'user' => [
            'id' => $_SESSION['agent_id'],
            'username' => $_SESSION['agent_username'],
            'agent_id' => $_SESSION['agent_agent_id']
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>