<?php
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($pdo);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $stored_hash = $stmt->fetch()['password_hash'];
    
    if (!password_verify($current_password, $stored_hash)) {
        $error = "Current password is incorrect";
    } elseif (!isPasswordStrong($new_password)) {
        $error = "New password must be at least 8 characters with 1 uppercase, 1 lowercase, and 1 number";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else {
        // Update password
        $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        
        if ($stmt->execute([$password_hash, $user['id']])) {
            $success = "Password changed successfully!";
        } else {
            $error = "Failed to change password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .password-container {
            max-width: 500px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .password-hint {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 0.9em;
        }
        
        .password-hint ul {
            margin: 10px 0 0 20px;
        }
        
        .password-hint li {
            margin-bottom: 5px;
        }
        
        .valid {
            color: #27AE60;
        }
        
        .invalid {
            color: #E74C3C;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Change Your Password - <?php echo htmlspecialchars($user['username']); ?></p>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a>
            <a href="change-password.php" class="active"><i class="fas fa-key"></i> Change Password</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>

        <div class="password-container">
            <div style="text-align: center; margin-bottom: 30px;">
                <i class="fas fa-lock" style="font-size: 4em; color: var(--secondary);"></i>
                <h2 style="color: var(--primary);">Change Password</h2>
                <p style="color: #666;">Ensure your account is secure</p>
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
            
            <div class="password-hint">
                <strong><i class="fas fa-info-circle"></i> Password Requirements:</strong>
                <ul>
                    <li>✓ At least 8 characters long</li>
                    <li>✓ At least 1 uppercase letter (A-Z)</li>
                    <li>✓ At least 1 lowercase letter (a-z)</li>
                    <li>✓ At least 1 number (0-9)</li>
                </ul>
            </div>
            
            <form method="POST" action="" id="passwordForm">
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Current Password</label>
                    <input type="password" name="current_password" required placeholder="Enter current password">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> New Password</label>
                    <input type="password" name="new_password" id="new_password" required placeholder="Enter new password">
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm new password">
                    <div id="password_match" style="font-size: 0.9em; margin-top: 5px;"></div>
                </div>
                
                <button type="submit" class="btn" style="width: 100%;">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </form>
            
            <div style="text-align: center; margin-top: 20px;">
                <a href="dashboard.php" style="color: var(--secondary);">Back to Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        // Real-time password match validation
        document.getElementById('confirm_password').addEventListener('keyup', function() {
            var password = document.getElementById('new_password').value;
            var confirm = this.value;
            var matchDiv = document.getElementById('password_match');
            
            if (password === confirm) {
                matchDiv.innerHTML = '<span style="color: #27AE60;"><i class="fas fa-check"></i> Passwords match</span>';
            } else {
                matchDiv.innerHTML = '<span style="color: #E74C3C;"><i class="fas fa-times"></i> Passwords do not match</span>';
            }
        });
        
        // Password strength indicator
        document.getElementById('new_password').addEventListener('keyup', function() {
            var password = this.value;
            var strength = 0;
            
            if (password.length >= 8) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            
            // You could add a visual strength indicator here
        });
    </script>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Keep your account secure</p>
            </div>
        </div>
    </footer>
</body>
</html>