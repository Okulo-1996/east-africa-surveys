<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'functions.php';

echo "<h1>📧 Gmail SMTP Test</h1>";

$test_email = 'YOUR-PERSONAL-EMAIL@gmail.com'; // Send to yourself!
$subject = '✅ Gmail is WORKING! - East Africa Surveys';
$message = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial; text-align: center; padding: 40px; }
        .success { color: #27AE60; font-size: 24px; }
        .flag { font-size: 32px; }
    </style>
</head>
<body>
    <div class="flag">🇰🇪 🇺🇬 🇹🇿</div>
    <h1 class="success">✅ Gmail SMTP is WORKING!</h1>
    <p>Your email configuration is successful.</p>
    <p>East Africa Surveys can now send verification emails!</p>
    <hr>
    <p><small>Sent via: info.eastafricasurveys@gmail.com</small></p>
</body>
</html>
';

echo "<p>Attempting to send email to: <strong>$test_email</strong></p>";

if (sendEmail($test_email, $subject, $message)) {
    echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Check your email inbox (and spam folder).</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ FAILED. Let's debug...</p>";
    
    // Debug mode - show what's happening
    echo "<h3>Debug Info:</h3>";
    echo "<ul>";
    echo "<li>PHPMailer files exist: " . (file_exists(__DIR__ . '/PHPMailer/src/PHPMailer.php') ? '✅ Yes' : '❌ No') . "</li>";
    echo "<li>Username: info.eastafricasurveys@gmail.com</li>";
    echo "<li>Password length: " . strlen('YOUR-16-CHAR-APP-PASSWORD') . " characters</li>";
    echo "<li>Host: smtp.gmail.com</li>";
    echo "<li>Port: 587</li>";
    echo "</ul>";
    
    echo "<p><strong>Did you remember to:</strong></p>";
    echo "<ol>";
    echo "<li>Enable 2FA on your Gmail account?</li>";
    echo "<li>Generate an App Password (16 characters)?</li>";
    echo "<li>Replace 'YOUR-16-CHAR-APP-PASSWORD' with the actual App Password?</li>";
    echo "</ol>";
}
?>