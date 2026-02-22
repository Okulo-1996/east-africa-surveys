<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                if ($user['is_verified']) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    logUserLogin($pdo, $user['id']);
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = "Please verify your email before logging in. <a href='resend-verification.php?email=" . urlencode($user['email']) . "' style='color: var(--secondary);'>Resend verification email</a>";
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Your Voice Matters — Kenya, Uganda & Tanzania</p>
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

        <div class="auth-container">
            <div class="auth-header">
                <i class="fas fa-user-circle" style="font-size: 3.5em; color: var(--secondary); margin-bottom: 15px;"></i>
                <h2>Welcome Back</h2>
                <p>Login to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" name="username" placeholder="Enter your username or email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-forgot">
                    <label>
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Don't have an account?</p>
                <a href="register.php">Create new account →</a>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>🌍 East Africa Surveys</h3>
                    <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys.</p>
                    <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="index.php">🏠 Home</a></p>
                    <p><a href="results.php">📊 Results</a></p>
                    <p><a href="privacy.php">🔒 Privacy</a></p>
                    <p><a href="terms.php">📝 Terms</a></p>
                </div>
                <div class="footer-section">
                    <h3>Our Region</h3>
                    <p><span style="font-size: 1.5em;">🇰🇪</span> Kenya</p>
                    <p><span style="font-size: 1.5em;">🇺🇬</span> Uganda</p>
                    <p><span style="font-size: 1.5em;">🇹🇿</span> Tanzania</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys. All rights reserved.</p>
                <p style="margin-top: 10px;">🇰🇪 🇺🇬 🇹🇿 Your voice matters across East Africa</p>
            </div>
        </div>
    </footer>
</body>
</html>