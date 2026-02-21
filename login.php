<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
$debug_info = '';

// Check if this is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $debug_info .= "✅ POST request detected<br>";
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    $debug_info .= "Username entered: " . htmlspecialchars($username) . "<br>";
    $debug_info .= "Password entered: " . (empty($password) ? 'EMPTY' : 'PROVIDED') . "<br>";
    
    if (empty($username) || empty($password)) {
        $error = "Please enter username and password";
        $debug_info .= "❌ Validation failed: Empty fields<br>";
    } else {
        try {
            $debug_info .= "✅ Attempting database query...<br>";
            
            // Get user from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user) {
                $debug_info .= "✅ User found in database: " . htmlspecialchars($user['username']) . "<br>";
                $debug_info .= "Stored hash: " . substr($user['password_hash'], 0, 10) . "...<br>";
                
                if (password_verify($password, $user['password_hash'])) {
                    $debug_info .= "✅ Password verified successfully<br>";
                    
                    if ($user['is_verified']) {
                        $debug_info .= "✅ User is verified<br>";
                        
                        // Login successful
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        
                        // Log login time
                        logUserLogin($pdo, $user['id']);
                        
                        $debug_info .= "✅ Session set, redirecting to dashboard...<br>";
                        
                        // IMPORTANT: This header must execute
                        header('Location: dashboard.php');
                        exit();
                        
                    } else {
                        $error = "Please verify your email before logging in.";
                        $debug_info .= "❌ User not verified<br>";
                    }
                } else {
                    $error = "Invalid password";
                    $debug_info .= "❌ Password verification failed<br>";
                }
            } else {
                $error = "User not found";
                $debug_info .= "❌ No user found with that username/email<br>";
            }
        } catch (PDOException $e) {
            error_log("Login error: " . $e->getMessage());
            $error = "Database error. Please try again.";
            $debug_info .= "❌ Database error: " . $e->getMessage() . "<br>";
        }
    }
}

// Check for URL parameters
if (isset($_GET['registered'])) {
    $success = "Registration successful! Please check your email to verify your account.";
}

if (isset($_GET['verified'])) {
    $success = "Email verified successfully! You can now login.";
}

if (isset($_GET['logged_out'])) {
    $success = "You have been logged out successfully.";
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
    <style>
        .auth-container {
            max-width: 450px;
            margin: 60px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
        .debug-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
            max-height: 300px;
            overflow: auto;
        }
    </style>
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

        <div class="auth-container">
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
            
            <!-- DEBUG INFO - Remove after fixing -->
            <?php if (!empty($debug_info)): ?>
                <div class="debug-box">
                    <strong>🔍 Debug Info:</strong><br>
                    <?php echo $debug_info; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username or Email</label>
                    <input type="text" name="username" placeholder="Enter username or email" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="remember-forgot">
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
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>🌍 East Africa Surveys</h3>
                    <p>Your voice matters across Kenya, Uganda & Tanzania</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="login.php">Login</a></p>
                    <p><a href="register.php">Register</a></p>
                    <p><a href="privacy.php">Privacy</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your voice matters</p>
            </div>
        </div>
    </footer>
</body>
</html>