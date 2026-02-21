<?php
// functions.php - Helper functions for East Africa Surveys
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHPMailer includes
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// ============================================
// TOKEN GENERATION
// ============================================

/**
 * Generate a random token for verification/password reset
 * @param int $length Length of token (default 32)
 * @return string Random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

// ============================================
// EMAIL FUNCTIONS - BREVO SMTP (PORT 2525)
// ============================================

/**
 * Send email using Brevo SMTP (works on Render free tier - port 2525)
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message HTML message content
 * @return bool Success status
 */
function sendEmail($to, $subject, $message) {
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings - BREVO with PORT 2525 (Works on Render free tier!)
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Set to DEBUG_SERVER for debugging
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'a2ff87001@smtp-brevo.com'; // Your Brevo login
        $mail->Password   = 'YOUR_BREVO_KEY'; // 🔴 PASTE YOUR GENERATED SMTP KEY!
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 'tls'
        $mail->Port       = 2525; // ⭐ CRITICAL - WORKS ON RENDER FREE TIER!
        
        // Recipients - Using verified Gmail sender
        $mail->setFrom('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        $mail->addAddress($to);
        $mail->addReplyTo('info.eastafricasurveys@gmail.com', 'East Africa Surveys');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '</p>', '<br />'], "\n", $message));
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error for debugging
        error_log("Brevo SMTP Error: " . $mail->ErrorInfo);
        return false;
    }
}

// ============================================
// VERIFICATION EMAILS
// ============================================

/**
 * Send account verification email
 * @param string $email User's email
 * @param string $token Verification token
 * @param string $username User's username
 * @return bool Success status
 */
function sendVerificationEmail($email, $token, $username) {
    $subject = "Activate Your Account - East Africa Surveys";
    
    $verification_link = SITE_URL . "/verify.php?token=" . $token;
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background: #f4f4f4; }
            .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { background: #2C3E50; color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header span { color: #E67E22; }
            .content { padding: 40px 30px; background: white; }
            .content h2 { color: #2C3E50; margin-top: 0; }
            .button { 
                display: inline-block; 
                padding: 14px 35px; 
                background: #E67E22; 
                color: white; 
                text-decoration: none; 
                border-radius: 50px;
                margin: 25px 0;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .button:hover { background: #d35400; }
            .link-box { 
                background: #f8f9fa; 
                padding: 15px; 
                border-radius: 5px; 
                word-break: break-all;
                font-family: monospace;
                border: 1px solid #e0e0e0;
            }
            .footer { 
                background: #f8f9fa; 
                padding: 25px; 
                text-align: center; 
                color: #666; 
                font-size: 0.9em;
                border-top: 1px solid #e0e0e0;
            }
            .footer p { margin: 5px 0; }
            .flag { font-size: 24px; margin: 15px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>East Africa <span>Surveys</span></h1>
            </div>
            <div class='content'>
                <h2>Welcome, $username!</h2>
                <p>Thank you for registering with East Africa Surveys. Your voice matters across Kenya, Uganda, and Tanzania!</p>
                
                <p>Please click the button below to activate your account:</p>
                
                <div style='text-align: center;'>
                    <a href='$verification_link' class='button'>ACTIVATE MY ACCOUNT</a>
                </div>
                
                <p>If the button doesn't work, copy and paste this link into your browser:</p>
                <div class='link-box'>$verification_link</div>
                
                <p><strong>⏰ This link will expire in " . TOKEN_EXPIRY . " hours.</strong></p>
                
                <p>If you didn't create an account with East Africa Surveys, please ignore this email.</p>
                
                <div class='flag'>🇰🇪 🇺🇬 🇹🇿</div>
            </div>
            <div class='footer'>
                <p>© 2026 East Africa Surveys</p>
                <p>Kenya | Uganda | Tanzania</p>
                <p><small>Your voice matters across East Africa</small></p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

/**
 * Send password reset email
 * @param string $email User's email
 * @param string $token Reset token
 * @param string $username User's username
 * @return bool Success status
 */
function sendResetEmail($email, $token, $username) {
    $subject = "Reset Your Password - East Africa Surveys";
    
    $reset_link = SITE_URL . "/reset-password.php?token=" . $token;
    
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background: #f4f4f4; }
            container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
            .header { background: #2C3E50; color: white; padding: 30px 20px; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header span { color: #E67E22; }
            .content { padding: 40px 30px; background: white; }
            .content h2 { color: #2C3E50; margin-top: 0; }
            .button { 
                display: inline-block; 
                padding: 14px 35px; 
                background: #E67E22; 
                color: white; 
                text-decoration: none; 
                border-radius: 50px;
                margin: 25px 0;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .button:hover { background: #d35400; }
            .link-box { 
                background: #f8f9fa; 
                padding: 15px; 
                border-radius: 5px; 
                word-break: break-all;
                font-family: monospace;
                border: 1px solid #e0e0e0;
            }
            .footer { 
                background: #f8f9fa; 
                padding: 25px; 
                text-align: center; 
                color: #666; 
                font-size: 0.9em;
                border-top: 1px solid #e0e0e0;
            }
            .warning { 
                background: #fff3cd; 
                border: 1px solid #ffeeba; 
                color: #856404; 
                padding: 15px; 
                border-radius: 5px; 
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>East Africa <span>Surveys</span></h1>
            </div>
            <div class='content'>
                <h2>Hello, $username!</h2>
                
                <div class='warning'>
                    <strong>⚠️ Password Reset Request</strong>
                </div>
                
                <p>We received a request to reset your password. Click the button below to create a new password:</p>
                
                <div style='text-align: center;'>
                    <a href='$reset_link' class='button'>RESET PASSWORD</a>
                </div>
                
                <p>If the button doesn't work, copy and paste this link:</p>
                <div class='link-box'>$reset_link</div>
                
                <p><strong>⏰ This link will expire in " . TOKEN_EXPIRY . " hours.</strong></p>
                
                <p>If you didn't request a password reset, please ignore this email or contact support if you're concerned.</p>
                
                <div class='flag'>🇰🇪 🇺🇬 🇹🇿</div>
            </div>
            <div class='footer'>
                <p>© 2026 East Africa Surveys</p>
                <p>Kenya | Uganda | Tanzania</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return sendEmail($email, $subject, $message);
}

// ============================================
// USER AUTHENTICATION FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 * @return bool True if logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user data
 * @param PDO $pdo Database connection
 * @return array|null User data or null if not logged in
 */
function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $stmt = $pdo->prepare("SELECT id, username, email, country, age_range, gender, is_verified, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        error_log("getCurrentUser error: " . $e->getMessage());
        return null;
    }
}

/**
 * Log user login activity
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 */
function logUserLogin($pdo, $user_id) {
    try {
        $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$user_id]);
    } catch (PDOException $e) {
        error_log("logUserLogin error: " . $e->getMessage());
    }
}

/**
 * Validate password strength
 * @param string $password Password to check
 * @return bool True if password meets requirements
 */
function isPasswordStrong($password) {
    // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
    return strlen($password) >= 8 && 
           preg_match('/[A-Z]/', $password) && 
           preg_match('/[a-z]/', $password) && 
           preg_match('/[0-9]/', $password);
}

// ============================================
// USER ACTIVITY FUNCTIONS
// ============================================

/**
 * Get user's voting history
 * @param PDO $pdo Database connection
 * @param int $user_id User ID
 * @param int $limit Number of records to return
 * @return array Voting history
 */
function getUserVoteHistory($pdo, $user_id, $limit = 10) {
    try {
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
    } catch (PDOException $e) {
        error_log("getUserVoteHistory error: " . $e->getMessage());
        return [];
    }
}

// ============================================
// DEBUG FUNCTION (remove in production)
// ============================================

/**
 * Debug email configuration - REMOVE AFTER TESTING
 */
function testEmailConfig() {
    echo "<h3>Email Configuration Test</h3>";
    echo "<p>PHPMailer files loaded: " . (class_exists('PHPMailer\PHPMailer\PHPMailer') ? '✅' : '❌') . "</p>";
    echo "<p>Brevo SMTP configured with port 2525</p>";
    echo "<p>From email: info.eastafricasurveys@gmail.com</p>";
}
?>