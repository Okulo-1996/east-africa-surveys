<?php
// test_db.php - Show detailed database connection errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Supabase connection details
$supabase_host = 'aws-1-eu-west-1.pooler.supabase.com';
$supabase_port = '5432';
$supabase_db = 'postgres';
$supabase_user = 'postgres.hdeexaxpqlzzfouyqdut';
$supabase_password = 'YOUR-ACTUAL-SUPABASE-PASSWORD-HERE'; // Put your real password here

echo "<h1>🔍 Database Connection Test</h1>";

try {
    echo "<p>Attempting to connect to: <strong>$supabase_host:$supabase_port</strong></p>";
    
    $dsn = "pgsql:host=$supabase_host;port=$supabase_port;dbname=$supabase_db;";
    $pdo = new PDO($dsn, $supabase_user, $supabase_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    echo "<p style='color: green;'>✅ Connected successfully!</p>";
    
    // Check if tables exist
    $result = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema='public'");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables in database:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
} catch(PDOException $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Connection failed!</p>";
    echo "<p><strong>Error message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Error code:</strong> " . $e->getCode() . "</p>";
    
    // Helpful suggestions based on error
    $msg = $e->getMessage();
    if (strpos($msg, 'password') !== false) {
        echo "<p style='color: orange;'>⚠️ This looks like a password issue. Double-check your Supabase password.</p>";
    } elseif (strpos($msg, 'timeout') !== false || strpos($msg, 'network') !== false) {
        echo "<p style='color: orange;'>⚠️ This looks like a network issue. The pooler might not be reachable.</p>";
    } elseif (strpos($msg, 'database') !== false) {
        echo "<p style='color: orange;'>⚠️ Database name might be wrong. Should be 'postgres'?</p>";
    }
}

echo "<hr>";
echo "<p>Debug info:</p>";
echo "<ul>";
echo "<li>Host: $supabase_host</li>";
echo "<li>Port: $supabase_port</li>";
echo "<li>Database: $supabase_db</li>";
echo "<li>User: $supabase_user</li>";
echo "<li>Password length: " . strlen($supabase_password) . " characters</li>";
echo "</ul>";
?>