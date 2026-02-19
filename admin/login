<?php
session_start();
$admin_password = "EastAfrica2026"; // CHANGE THIS TO YOUR SECRET PASSWORD!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: update.php');
        exit();
    } else {
        $error = "Wrong password. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - East Africa Surveys</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .login-box {
            max-width: 400px;
            margin: 100px auto;
            padding: 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .login-icon {
            font-size: 4em;
            color: var(--sunset-orange);
            margin-bottom: 20px;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="login-box">
        <div class="login-icon">
            <i class="fas fa-lock"></i>
        </div>
        
        <h1 style="color: var(--savanna-green); margin-bottom: 10px;">Admin Login</h1>
        <p style="color: #666; margin-bottom: 30px;">East Africa Surveys</p>
        
        <?php if (isset($_GET['logged_out'])): ?>
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                ✅ You've been logged out successfully
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <input type="password" name="password" placeholder="Enter admin password" 
                       style="width: 100%; padding: 15px; border: 2px solid #e0e0e0; border-radius: 10px; font-size: 1.1em;"
                       required autofocus>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> Login to Dashboard
            </button>
        </form>
        
        <div style="margin-top: 30px; font-size: 0.9em; color: #999;">
            <i class="fas fa-shield-alt"></i> Secure admin area
        </div>
        
        <div style="margin-top: 20px;">
            <a href="../index.php" style="color: var(--sunset-orange); text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Homepage
            </a>
        </div>
    </div>
    
    <!-- Simple footer -->
    <div style="text-align: center; color: white; margin-top: 20px; opacity: 0.8;">
        <p>© 2026 East Africa Surveys | info.eastafricasurveys@gmail.com</p>
    </div>
</body>
</html>
