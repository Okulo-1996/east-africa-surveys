<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

// ✅ FIXED: Only redirect to dashboard if user IS logged in
// But don't redirect if we're already on login page
if (isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: dashboard.php');
    exit();
}

$error = '';
$success = '';

if (isset($_GET['registered'])) {
    $success = "Registration successful! Please check your email to verify your account.";
}

if (isset($_GET['verified'])) {
    $success = "Email verified successfully! You can now login.";
}

if (isset($_GET['logged_out'])) {
    $success = "You have been logged out successfully.";
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        try {
            // Get user from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['is_verified']) {
                    // Login successful
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Log login time
                    logUserLogin($pdo, $user['id']);
                    
                    // ✅ REDIRECT TO DASHBOARD - THIS SHOULD WORK NOW
                    header('Location: dashboard.php');
                    exit();
                    
                } else {
                    $error = "Please verify your email before logging in. <a href='resend-verification.php?email=" . urlencode($user['email']) . "'>Resend verification email</a>";
                }
            } else {
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Welcome Back! 🇰🇪 🇺🇬 🇹🇿</p>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        </nav>

        <div class="auth-container" style="max-width: 450px; margin: 40px auto; background: white; padding: 40px; border-radius: 20px;">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-user-circle" style="font-size: 4em; color: var(--secondary);"></i>
                <h2 style="color: var(--primary);">Login to Your Account</h2>
                <p style="color: #666;">Welcome back! Please enter your details</p>
            </div>
            
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" name="username" placeholder="Enter username or email" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-forgot" style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
                    <label style="display: flex; align-items: center; gap: 5px;">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php" style="color: var(--secondary); text-decoration: none;">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 30px;">
                <p>Don't have an account? <a href="register.php" style="color: var(--secondary); font-weight: 600;">Register here</a></p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your voice matters</p>
            </div>
        </div>
    </footer>
</body>
</html>