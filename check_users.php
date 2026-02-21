<?php
require_once 'config.php';
require_once 'db_connect.php';

echo "<h1>🔍 User Database Check</h1>";

try {
    // Count total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $count = $stmt->fetchColumn();
    echo "<p>Total users in database: <strong>$count</strong></p>";
    
    // Show first 5 users (if any)
    $stmt = $pdo->query("SELECT id, username, email, created_at FROM users LIMIT 5");
    $users = $stmt->fetchAll();
    
    if ($users) {
        echo "<h3>Existing users:</h3>";
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Created</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . $user['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: green;'>✅ Database is empty - ready for new registrations!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>