<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

$message = '';
$error = '';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    
    // Find user with this email
    $stmt = $pdo->prepare("SELECT id, username, verification_token FROM users WHERE email = ? AND is_verified = FALSE");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Generate new token if needed
        if (empty($user['verification_token'])) {
            $token = generateToken();
            $stmt = $pdo->prepare("UPDATE users SET verification_token = ? WHERE id = ?");
            $stmt->execute([$token, $user['id']]);
        } else {
            $token = $user['verification_token'];
        }
        
        // Send verification email
        if (sendVerificationEmail($email, $token, $user['username'])) {
            $message = "Verification email has been resent. Please check your inbox.";
        } else {
            $error = "Failed to send verification email. Please try again later.";
        }
    } else {
        $error = "No unverified account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: 100px auto;">
        <div style="background: white; padding: 40px; border-radius: 20px;">
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 30px; text-align: center;">
                    <i class="fas fa-envelope" style="font-size: 3em; margin-bottom: 15px;"></i>
                    <h2>Email Sent!</h2>
                    <p><?php echo $message; ?></p>
                    <a href="login.php" class="btn" style="margin-top: 20px;">Back to Login</a>
                </div>
            <?php elseif ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 30px; text-align: center;">
                    <i class="fas fa-exclamation-circle" style="font-size: 3em; margin-bottom: 15px;"></i>
                    <h2>Error</h2>
                    <p><?php echo $error; ?></p>
                    <a href="login.php" class="btn" style="margin-top: 20px;">Back to Login</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>