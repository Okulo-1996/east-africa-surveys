<?php
// final_test.php - Complete email test
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'db_connect.php';

// PHPMailer includes
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

echo "<!DOCTYPE html>
<html>
<head>
    <title>Final Email Test - East Africa Surveys</title>
    <style>
        body { font-family: Arial; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; }
        .info { background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>";

echo "<h1>📧 Final Email Test</h1>";

// Your test email - CHANGE THIS!
$test_email = 'okuloisaac46@gmail.com'; // Put your email here

echo "<div class='info'>";
echo "<p><strong>Testing email to:</strong> $test_email</p>";
echo "<p><strong>Using password:</strong> " . substr('srjbpbfooktiwkie', 0, 4) . "..." . substr('srjbpbfooktiwkie', -4) . "</p>";
echo "</div>";

// Test function directly
function testEmailDirect($to) {
    $mail = new PHPMailer(true);
    
    try {
        echo "<h3>📤 Attempting to send...</h3>";
        
        $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Show debug output
        $mail->Debugoutput = function($str, $level) {
            echo "<pre>DEBUG: $str</pre>";
        };
        
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info.eastafricasurveys@gmail.com';
        $mail->Password   = 'srjbpbfooktiwkie'; // Your App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        $mail->setFrom('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        $mail->addAddress($to);
        $mail->addReplyTo('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        
        $mail->Subject = 'Test Email from East Africa Surveys';
        $mail->Body    = '<h1>Test</h1><p>If you see this, email is working!</p>';
        $mail->AltBody = 'Test email - plain text version';
        
        if ($mail->send()) {
            echo "<div class='success'>✅ Email sent successfully!</div>";
            return true;
        } else {
            echo "<div class='error'>❌ Send returned false</div>";
            return false;
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>";
        echo "<h3>❌ Exception caught:</h3>";
        echo "<pre>" . $e->getMessage() . "</pre>";
        echo "<h4>Mailer Error:</h4>";
        echo "<pre>" . $mail->ErrorInfo . "</pre>";
        echo "</div>";
        return false;
    }
}

// Run the test
testEmailDirect($test_email);

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ul>";
echo "<li>If successful: Check your email inbox and spam folder</li>";
echo "<li>If failed: Copy the error message above and share it with me</li>";
echo "</ul>";

echo "<p><a href='register.php'>Try Registration</a> | <a href='index.php'>Home</a></p>";

echo "</body></html>";
?>