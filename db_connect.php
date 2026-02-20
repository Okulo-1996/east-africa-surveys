<?php
// db_connection.php - Direct Supabase connection
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Supabase connection details - USE YOUR ACTUAL PASSWORD!
$supabase_host = 'db.hdeexaxpqlzzfouyqdut.supabase.co';
$supabase_port = '5432';
$supabase_db = 'postgres';
$supabase_user = 'postgres';
$supabase_password = 'Okulo@1996$28'; // CHANGE THIS!

try {
    // Create DSN string
    $dsn = "pgsql:host=$supabase_host;port=$supabase_port;dbname=$supabase_db;";
    
    // Create PDO connection
    $pdo = new PDO($dsn, $supabase_user, $supabase_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5
    ]);
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch(PDOException $e) {
    // Log error
    error_log("Supabase connection failed: " . $e->getMessage());
    
    // Show user-friendly error
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial;'>
            <h1 style='color: #F39C12;'>🌍 East Africa Surveys</h1>
            <p>Database connection issue. Please try again later.</p>
            <p style='color: #666;'>Error: " . $e->getMessage() . "</p>
            <p>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</p>
        </div>
    ");
}
?>