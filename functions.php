<?php
// At the VERY TOP of functions.php
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    // ... rest of your code
}
// functions.php - Helper functions

require_once 'db_connect.php';
require_once 'config.php';

// Generate random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// Send email using Gmail SMTP
function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM . ">" . "\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Send verification email
function sendVerificationEmail($email, $token, $username) {
    $subject = "Activate Your Account - East Africa Surveys";
    
    $verification_link = SITE_URL . "/verify.php?token=" . $token;
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2C3E50; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .button { 
                display: inline-block; 
                padding: 12px 30px; 
                background: #E67E22; 
                color: white; 
                text-decoration: none; 
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🌍 East Africa Surveys</h1>
            </div>
            <div class='content'>
                <h2>Welcome, $username!</h2>
                <p>Thank you for registering with East Africa Surveys. Please click the button below to activate your account:</p>
                
                <a href='$verification_link' class='button'>Activate My Account</a>
                
                <p>Or copy and paste this link into your browser:</p>
                <p style='word-break: break-all;'>$verification_link</p>
                
                <p>This link will expire in " . TOKEN_EXPIRY . " hours.</p>
                
                <p>If you didn't create an account, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>© 2026 East Africa Surveys | Kenya | Uganda | Tanzania</p>
                <p><small>Your voice matters across East Africa</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

// Send password reset email
function sendResetEmail($email, $token, $username) {
    $subject = "Reset Your Password - East Africa Surveys";
    
    $reset_link = SITE_URL . "/reset-password.php?token=" . $token;
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2C3E50; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; background: #f9f9f9; }
            .button { 
                display: inline-block; 
                padding: 12px 30px; 
                background: #E67E22; 
                color: white; 
                text-decoration: none; 
                border-radius: 5px;
                margin: 20px 0;
            }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 0.9em; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🌍 East Africa Surveys</h1>
            </div>
            <div class='content'>
                <h2>Hello, $username!</h2>
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                
                <a href='$reset_link' class='button'>Reset Password</a>
                
                <p>Or copy and paste this link:</p>
                <p style='word-break: break-all;'>$reset_link</p>
                
                <p>This link will expire in " . TOKEN_EXPIRY . " hours.</p>
                
                <p>If you didn't request this, please ignore this email.</p>
            </div>
            <div class='footer'>
                <p>© 2026 East Africa Surveys | Kenya | Uganda | Tanzania</p>
            </div>
        </div>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user data
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    $stmt = $pdo->prepare("SELECT id, username, email, country, age_range, gender, is_verified, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Log user activity
function logUserLogin($pdo, $user_id) {
    $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$user_id]);
}

// Validate password strength
function isPasswordStrong($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// Get user's voting history
function getUserVoteHistory($pdo, $user_id, $limit = 10) {
    $stmt = $pdo->prepare("
        SELECT q.question_text, a.response_value, a.submitted_at 
        FROM answers a
        JOIN questions q ON a.question_id = q.id
        WHERE a.user_id = ?
        ORDER BY a.submitted_at DESC
        LIMIT ?
    ");
    $stmt->execute([$user_id, $limit]);
    return $stmt->fetchAll();
}
?>