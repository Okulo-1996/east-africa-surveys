<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } else {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Generate reset token
            $token = generateToken();
            $expires = date('Y-m-d H:i:s', strtotime('+' . TOKEN_EXPIRY . ' hours'));
            
            // Save token to database
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
            
            // Send reset email
            if (sendResetEmail($email, $token, $user['username'])) {
                $message = "Password reset instructions have been sent to your email.";
            } else {
                $error = "Failed to send email. Please try again later.";
            }
        } else {
            // Don't reveal that email doesn't exist (security)
            $message = "If your email is registered, you will receive reset instructions.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container" style="max-width: 500px; margin: 100px auto;">
        <div style="background: white; padding: 40px; border-radius: 20px;">
            <h2 style="color: var(--primary);">Reset Password</h2>
            
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit" class="btn">Send Reset Instructions</button>
            </form>
            
            <p style="margin-top: 20px;"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</body>
</html>