<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

$error = '';
$message = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

// Verify token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = "Invalid or expired reset token. Please request a new password reset.";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $token = $_POST['token'];
    
    // Verify token again
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $error = "Invalid or expired reset token. Please request a new password reset.";
    } elseif (empty($password) || empty($confirm_password)) {
        $error = "Please enter and confirm your new password";
    } elseif (!isPasswordStrong($password)) {
        $error = "Password must be at least 8 characters with 1 uppercase, 1 lowercase, and 1 number";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Update password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        
        if ($stmt->execute([$password_hash, $user['id']])) {
            $message = "Password reset successful! You can now login with your new password.";
        } else {
            $error = "Failed to reset password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .reset-container {
            max-width: 500px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .password-hint {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Reset Your Password - Kenya, Uganda & Tanzania</p>
        </div>
    </header>

    <div class="container">
        <div class="reset-container">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-key" style="font-size: 4em; color: var(--secondary);"></i>
                <h2 style="color: var(--primary);">Create New Password</h2>
                <p style="color: #666;">Please enter your new password below</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="login.php" class="btn">Login Now</a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (empty($message) && !empty($token) && !$error): ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> New Password</label>
                        <input type="password" name="password" required placeholder="Enter new password">
                        <div class="password-hint">
                            <i class="fas fa-info-circle"></i> 8+ characters, 1 uppercase, 1 lowercase, 1 number
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" name="confirm_password" required placeholder="Confirm new password">
                    </div>
                    
                    <button type="submit" class="btn" style="width: 100%;">
                        <i class="fas fa-save"></i> Reset Password
                    </button>
                </form>
            <?php endif; ?>
            
            <?php if (empty($token)): ?>
                <div style="text-align: center;">
                    <p>No reset token provided.</p>
                    <a href="forgot-password.php" class="btn">Request Password Reset</a>
                </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" style="color: var(--secondary);">Back to Login</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your security matters</p>
            </div>
        </div>
    </footer>
</body>
</html>