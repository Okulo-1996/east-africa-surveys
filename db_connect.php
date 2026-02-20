<?php
// db_connection.php - Supabase with Connection Pooler
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Supabase connection pooler details - from your connection string
$supabase_host = 'aws-1-eu-west-1.pooler.supabase.com';
$supabase_port = '5432';  // Using 5432 as specified in your string
$supabase_db = 'postgres';
$supabase_user = 'postgres.hdeexaxpqlzzfouyqdut';  // Your username with project ref
$supabase_password = 'Okulo@1996$28;  // CHANGE THIS TO YOUR ACTUAL PASSWORD!

try {
    // Create DSN string
    $dsn = "pgsql:host=$supabase_host;port=$supabase_port;dbname=$supabase_db;";
    
    // Create PDO connection
    $pdo = new PDO($dsn, $supabase_user, $supabase_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 10,
        PDO::ATTR_PERSISTENT => false
    ]);
    
    // Test connection
    $pdo->query("SELECT 1");
    
} catch(PDOException $e) {
    // Log error for debugging
    error_log("Supabase connection failed: " . $e->getMessage());
    
    // Show user-friendly error
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial; max-width: 600px; margin: 0 auto;'>
            <h1 style='color: #F39C12;'>🌍 East Africa Surveys</h1>
            <p>We're having trouble connecting to our database. Our team has been notified.</p>
            <p style='color: #666;'>Please try again in a few minutes.</p>
            <p style='margin-top: 30px;'>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</p>
            <p style='font-size: 0.8em; color: #999; margin-top: 20px;'>Error reference: " . substr(md5($e->getMessage()), 0, 8) . "</p>
        </div>
    ");
}
?>