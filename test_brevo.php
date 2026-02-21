<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'functions.php';
require_once 'config.php';

echo "<h1>🔍 Detailed Brevo Debug</h1>";

// Show environment variable status
$brevo_key = getenv('YOUR_BREVO_SMTP_KEY');
echo "<h3>Environment Variable:</h3>";
echo "<p>YOUR_BREVO_SMTP_KEY exists: " . ($brevo_key ? '✅ YES' : '❌ NO') . "</p>";
echo "<p>YOUR_BREVO_SMTP_KEY length: " . strlen($brevo_key) . " characters</p>";
echo "<p>First 10 chars: " . substr($brevo_key, 0, 10) . "...</p>";

// Test function with detailed output
function debugSendEmail($to, $subject, $message) {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    
    try {
        echo "<h3>📤 Attempting to send with detailed debug...</h3>";
        
        // Enable detailed debug output
        $mail->SMTPDebug = PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
        $mail->Debugoutput = function($str, $level) {
            echo "<pre style='background: #f4f4f4; padding: 5px; margin: 2px 0;'>DEBUG: $str</pre>";
        };
        
        echo "<p>Setting up SMTP...</p>";
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'a2ff87001@smtp-brevo.com';
        $mail->Password   = $brevo_key;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 2525;
        
        echo "<p>Setting recipients...</p>";
        $mail->setFrom('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        $mail->addAddress($to);
        $mail->addReplyTo('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        
        echo "<p>Setting content...</p>";
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags($message);
        
        echo "<p>Sending...</p>";
        $mail->send();
        echo "<p style='color: green; font-weight: bold;'>✅ EMAIL SENT SUCCESSFULLY!</p>";
        return true;
        
    } catch (Exception $e) {
        echo "<p style='color: red; font-weight: bold;'>❌ ERROR: " . $e->getMessage() . "</p>";
        echo "<p>Mailer Error: " . $mail->ErrorInfo . "</p>";
        return false;
    }
}

$test_email = 'okuloisaac46@gmail.com';
$subject = 'Brevo Debug Test - ' . date('Y-m-d H:i:s');
$message = '<h2>Test</h2><p>If you see this, Brevo is working!</p>';

debugSendEmail($test_email, $subject, $message);

echo "<hr>";
echo "<p><a href='index.php'>Home</a></p>";
?>