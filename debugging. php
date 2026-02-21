<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';
require_once 'functions.php';

echo "<h1>🔍 SendEmail Function Debug</h1>";

// Check if function exists
if (!function_exists('sendEmail')) {
    die("<p style='color: red;'>❌ sendEmail() function not found!</p>");
}

echo "<p style='color: green;'>✅ sendEmail() exists</p>";

// Get the function code to check its configuration
$reflection = new ReflectionFunction('sendEmail');
$file = $reflection->getFileName();
$start = $reflection->getStartLine();
$end = $reflection->getEndLine();

echo "<p>Function located in: $file (lines $start - $end)</p>";

// Read the function code
$lines = file($file);
echo "<h3>Function code:</h3>";
echo "<pre style='background: #f4f4f4; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
for ($i = $start - 1; $i < $end; $i++) {
    $line_num = $i + 1;
    $line = htmlspecialchars($lines[$i]);
    
    // Highlight important lines
    if (strpos($line, 'Host') !== false || 
        strpos($line, 'Port') !== false ||
        strpos($line, 'Username') !== false ||
        strpos($line, 'Password') !== false ||
        strpos($line, 'SMTPSecure') !== false) {
        echo "<span style='background: #ffffcc; font-weight: bold;'>$line_num: $line</span>";
    } else {
        echo "$line_num: $line";
    }
}
echo "</pre>";

// Test the function with a real email
echo "<h3>Test sendEmail() with real email:</h3>";
$test_email = 'okuloisaac46@gmail.com';
$subject = 'Debug Test - ' . date('Y-m-d H:i:s');
$message = '<h2>Debug Test</h2><p>If you see this, sendEmail() is working!</p>';

echo "<p>Sending to: $test_email</p>";

// Temporarily override SMTP debug to see output
try {
    // Create a new PHPMailer instance to test independently
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background: #f0f0f0; padding: 3px; margin: 2px 0;'>DEBUG: $str</pre>";
    };
    
    // Get environment variable
    $brevo_key = getenv('YOUR_BREVO_KEY');
    echo "<p>Environment variable YOUR_BREVO_KEY: " . ($brevo_key ? '✅ Found (length: ' . strlen($brevo_key) . ')' : '❌ NOT FOUND') . "</p>";
    
    // Configure SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp-relay.brevo.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'a2ff87001@smtp-brevo.com';
    $mail->Password = $brevo_key;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 2525;
    
    $mail->setFrom('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
    $mail->addAddress($test_email);
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    if ($mail->send()) {
        echo "<p style='color: green; font-weight: bold;'>✅ EMAIL SENT SUCCESSFULLY!</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Send failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ ERROR: " . $e->getMessage() . "</p>";
    echo "<p>Mailer Error: " . $mail->ErrorInfo . "</p>";
}

echo "<hr>";
echo "<p><a href='register.php'>Back to Register</a> | <a href='index.php'>Home</a></p>";
?>