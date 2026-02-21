<?php
// test_errors.php - Show all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>🔍 PHP Error Test</h1>";
echo "<p>If you see this, PHP is working.</p>";

// Check PHP version
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check if files exist
$files = [
    'config.php',
    'db_connect.php',
    'functions.php',
    'PHPMailer/src/PHPMailer.php'
];

echo "<h2>Checking required files:</h2>";
echo "<ul>";
foreach ($files as $file) {
    if (file_exists($file)) {
        echo "<li style='color: green;'>✅ $file - FOUND</li>";
    } else {
        echo "<li style='color: red;'>❌ $file - MISSING</li>";
    }
}
echo "</ul>";

// Try to load files
echo "<h2>Loading files:</h2>";
require_once 'config.php';
echo "<p style='color: green;'>✅ config.php loaded</p>";

require_once 'db_connect.php';
echo "<p style='color: green;'>✅ db_connect.php loaded</p>";

require_once 'functions.php';
echo "<p style='color: green;'>✅ functions.php loaded</p>";

echo "<h2>✅ All files loaded successfully!</h2>";
echo "<p><a href='final_test.php'>Now try final_test.php</a></p>";
?>