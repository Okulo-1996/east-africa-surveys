<?php
require_once 'functions.php';
require_once 'config.php';

echo "<h1>📧 Testing Brevo SMTP</h1>";

// Show config
testEmailConfig();

// Send test
$to = 'okuloisaac46@gmail.com';
$subject = 'Brevo Test - ' . date('Y-m-d H:i:s');
$message = '<h2>Working!</h2><p>Brevo SMTP is configured correctly.</p>';

echo "<p>Sending test email to: $to</p>";

if (sendEmail($to, $subject, $message)) {
    echo "<p style='color: green;'>✅ Email sent! Check your inbox.</p>";
} else {
    echo "<p style='color: red;'>❌ Failed. Check your SMTP key.</p>";
}
?>