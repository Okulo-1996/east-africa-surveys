<?php
// final_test_debug.php - Debug version
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h1>📧 Final Email Test (Debug Mode)</h1>";
echo "<p>Starting test...</p>";

// Check if we can load files
echo "<p>Loading config.php...</p>";
require_once 'config.php';
echo "<p style='color: green;'>✅ config.php loaded</p>";

echo "<p>Loading db_connect.php...</p>";
require_once 'db_connect.php';
echo "<p style='color: green;'>✅ db_connect.php loaded</p>";

echo "<p>Loading functions.php...</p>";
require_once 'functions.php';
echo "<p style='color: green;'>✅ functions.php loaded</p>";

// Check PHPMailer files
echo "<p>Checking PHPMailer files...</p>";
$phpmailer_files = [
    __DIR__ . '/PHPMailer/src/Exception.php',
    __DIR__ . '/PHPMailer/src/PHPMailer.php',
    __DIR__ . '/PHPMailer/src/SMTP.php'
];

foreach ($phpmailer_files as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ Found: " . basename($file) . "</p>";
        require_once $file;
    } else {
        echo "<p style='color: red;'>❌ Missing: " . basename($file) . "</p>";
    }
}

echo "<p style='color: green;'>✅ All PHPMailer files loaded</p>";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

echo "<p style='color: green;'>✅ PHPMailer classes imported</p>";

// Test email address
$test_email = 'okuloisaac46@gmail.com';
echo "<p><strong>Testing email to:</strong> $test_email</p>";

// Create a simple test function
function simpleTest($to) {
    echo "<p>Creating PHPMailer object...</p>";
    $mail = new PHPMailer(true);
    echo "<p style='color: green;'>✅ PHPMailer object created</p>";
    
    try {
        echo "<p>Configuring SMTP...</p>";
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info.eastafricasurveys@gmail.com';
        $mail->Password = 'srjbpbfooktiwkie';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        echo "<p style='color: green;'>✅ SMTP configured</p>";
        
        echo "<p>Setting recipients...</p>";
        $mail->setFrom('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        $mail->addAddress($to);
        $mail->addReplyTo('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        echo "<p style='color: green;'>✅ Recipients set</p>";
        
        echo "<p>Setting content...</p>";
        $mail->Subject = 'Test Email';
        $mail->Body = '<h1>Test</h1>';
        $mail->AltBody = 'Test';
        echo "<p style='color: green;'>✅ Content set</p>";
        
        echo "<p>Sending email...</p>";
        if ($mail->send()) {
            echo "<p style='color: green; font-weight: bold;'>✅ EMAIL SENT SUCCESSFULLY!</p>";
        } else {
            echo "<p style='color: red;'>❌ Send failed</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red; font-weight: bold;'>❌ ERROR: " . $e->getMessage() . "</p>";
        echo "<p>Mailer Error: " . $mail->ErrorInfo . "</p>";
    }
}

// Run the test
simpleTest($test_email);

echo "<p>Test complete.</p>";
echo "<p><a href='final_test.php'>Try original final_test.php</a> | <a href='index.php'>Home</a></p>";
?>