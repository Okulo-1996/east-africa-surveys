<?php
// db_connect.php - Supabase with Connection Pooler
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Supabase connection pooler details
$supabase_host = 'aws-1-eu-west-1.pooler.supabase.com';
$supabase_port = '5432';
$supabase_db = 'postgres';
$supabase_user = 'postgres.hdeexaxpqlzzfouyqdut';
$supabase_password = 'DtcvA7hdk4sbNGGz0ZhwyXb9aVaFMO49';

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
    // Show user-friendly error
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial; max-width: 600px; margin: 0 auto;'>
            <h1 style='color: #F39C12;'>🌍 East Africa Surveys</h1>
            <p>We're having trouble connecting to our database. Please try again later.</p>
            <p style='color: #666;'>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</p>
        </div>
    ");
}
?>