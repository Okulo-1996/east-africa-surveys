<?php
require_once 'config.php';
require_once 'db_connect.php';

$message = '';
$error = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Find user with this token
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE verification_token = ? AND is_verified = FALSE");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Verify user
        $stmt = $pdo->prepare("UPDATE users SET is_verified = TRUE, verification_token = NULL WHERE id = ?");
        if ($stmt->execute([$user['id']])) {
            $message = "Email verified successfully! You can now login.";
        } else {
            $error = "Verification failed. Please try again.";
        }
    } else {
        $error = "Invalid or expired verification token.";
    }
} else {
    $error = "No verification token provided.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <header>
        <h1>🌍 East Africa Surveys</h1>
    </header>
    
    <div class="container" style="max-width: 600px; margin: 50px auto;">
        <?php if ($message): ?>
            <div style="background: #d4edda; color: #155724; padding: 30px; border-radius: 10px; text-align: center;">
                <i class="fas fa-check-circle" style="font-size: 4em; margin-bottom: 20px;"></i>
                <h2>Success!</h2>
                <p><?php echo $message; ?></p>
                <a href="login.php" class="btn" style="margin-top: 20px;">Login Now</a>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 30px; border-radius: 10px; text-align: center;">
                <i class="fas fa-exclamation-circle" style="font-size: 4em; margin-bottom: 20px;"></i>
                <h2>Verification Failed</h2>
                <p><?php echo $error; ?></p>
                <a href="register.php" class="btn" style="margin-top: 20px;">Register Again</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>