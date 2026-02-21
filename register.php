<?php
// Start session at the VERY TOP
session_start();

// Check if user is already logged in - redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $country = $_POST['country'] ?? '';
    $age_range = $_POST['age_range'] ?? '';
    $gender = $_POST['gender'] ?? '';

    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format";
    } elseif (!isPasswordStrong($password)) {
        $error = "Password must be at least 8 characters with 1 uppercase, 1 lowercase, and 1 number";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        try {
            // Check if username or email exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);

            if ($stmt->fetch()) {
                $error = "Username or email already exists";
            } else {
                // Create new user
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $verification_token = generateToken();

                // Insert user into database
                $stmt = $pdo->prepare("
                    INSERT INTO users (username, email, password_hash, country, age_range, gender, verification_token, is_verified) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, false)
                ");

                if ($stmt->execute([$username, $email, $password_hash, $country, $age_range, $gender, $verification_token])) {
                    
                    // Send verification email
                    $verify_link = SITE_URL . "/verify.php?token=" . $verification_token;
                    
                    $email_subject = "Verify Your East Africa Surveys Account";
                    $email_message = "
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; }
                                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                .header { background: #2C3E50; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
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
                                    <p>Thank you for registering with East Africa Surveys. Please click the button below to verify your account:</p>
                                    
                                    <div style='text-align: center;'>
                                        <a href='$verify_link' class='button'>Verify My Account</a>
                                    </div>
                                    
                                    <p>Or copy and paste this link into your browser:</p>
                                    <p style='word-break: break-all; background: #eee; padding: 10px; border-radius: 5px;'>$verify_link</p>
                                    
                                    <p>This link will expire in 24 hours.</p>
                                    
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

                    if (sendEmail($email, $email_subject, $email_message)) {
                        $success = "Registration successful! Please check your email to verify your account.";
                    } else {
                        $success = "Registration successful! But we couldn't send verification email. Please contact support.";
                        // Log the error for debugging
                        error_log("Failed to send verification email to: $email");
                    }
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            error_log("Database error in register.php: " . $e->getMessage());
            $error = "A database error occurred. Please try again later.";
        } catch (Exception $e) {
            error_log("General error in register.php: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .auth-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header h2 {
            color: var(--primary);
            font-size: 2em;
            margin-bottom: 10px;
        }

        .auth-header p {
            color: #666;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .password-hint {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }

        .auth-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }

        .auth-footer a {
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Join Our Community - Kenya, Uganda & Tanzania</p>
            <div class="email-badge">
                <i class="fas fa-envelope"></i>
                info.eastafricasurveys@gmail.com
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php" class="active"><i class="fas fa-user-plus"></i> Register</a>
        </nav>

        <div class="auth-container">
            <div class="auth-header">
                <i class="fas fa-user-plus" style="font-size: 3em; color: var(--secondary);"></i>
                <h2>Create an Account</h2>
                <p>Join thousands of East Africans sharing their opinions daily</p>
            </div>

            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <input type="text" name="username" placeholder="Choose a username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" placeholder="your@email.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="Create password" required>
                        <div class="password-hint">
                            <i class="fas fa-info-circle"></i> 8+ chars, 1 uppercase, 1 lowercase, 1 number
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm password" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label><i class="fas fa-flag"></i> Country</label>
                        <select name="country">
                            <option value="">Select country</option>
                            <option value="Kenya" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Kenya') ? 'selected' : ''; ?>>🇰🇪 Kenya</option>
                            <option value="Uganda" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Uganda') ? 'selected' : ''; ?>>🇺🇬 Uganda</option>
                            <option value="Tanzania" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Tanzania') ? 'selected' : ''; ?>>🇹🇿 Tanzania</option>
                            <option value="Other" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Other') ? 'selected' : ''; ?>>🌍 Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-calendar"></i> Age Range</label>
                        <select name="age_range">
                            <option value="">Select age</option>
                            <option value="13-17" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '13-17') ? 'selected' : ''; ?>>13-17</option>
                            <option value="18-24" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '18-24') ? 'selected' : ''; ?>>18-24</option>
                            <option value="25-34" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '25-34') ? 'selected' : ''; ?>>25-34</option>
                            <option value="35-44" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '35-44') ? 'selected' : ''; ?>>35-44</option>
                            <option value="45-54" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '45-54') ? 'selected' : ''; ?>>45-54</option>
                            <option value="55+" <?php echo (isset($_POST['age_range']) && $_POST['age_range'] == '55+') ? 'selected' : ''; ?>>55+</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-venus-mars"></i> Gender</label>
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        <option value="prefer-not" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'prefer-not') ? 'selected' : ''; ?>>Prefer not to say</option>
                    </select>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 10px;">
                        <input type="checkbox" name="terms" required>
                        <span>I agree to the <a href="terms.php" target="_blank">Terms of Service</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></span>
                    </label>
                </div>

                <button type="submit" class="btn" style="width: 100%;">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login here</a></p>
                <p style="margin-top: 10px; font-size: 0.9em;">
                    🇰🇪 🇺🇬 🇹🇿 Your voice matters across East Africa
                </p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>🌍 East Africa Surveys</h3>
                    <p>Your voice matters across Kenya, Uganda & Tanzania</p>
                    <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="login.php">Login</a></p>
                    <p><a href="register.php">Register</a></p>
                    <p><a href="privacy.php">Privacy</a></p>
                    <p><a href="terms.php">Terms</a></p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your voice matters</p>
            </div>
        </div>
    </footer>
</body>
</html>