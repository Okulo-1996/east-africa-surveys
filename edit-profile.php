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
    $country = $_POST['country'];
    $age_range = $_POST['age_range'];
    $gender = $_POST['gender'];
    
    // Update profile
    $stmt = $pdo->prepare("UPDATE users SET country = ?, age_range = ?, gender = ? WHERE id = ?");
    
    if ($stmt->execute([$country, $age_range, $gender, $user['id']])) {
        $success = "Profile updated successfully!";
        // Refresh user data
        $user = getCurrentUser($pdo);
    } else {
        $error = "Failed to update profile. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .profile-header i {
            font-size: 4em;
            color: var(--secondary);
            margin-bottom: 15px;
        }
        
        .email-display {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .email-display i {
            color: var(--secondary);
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Edit Your Profile - <?php echo htmlspecialchars($user['username']); ?></p>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="dashboard.php"><i class="fas fa-user"></i> Dashboard</a>
            <a href="edit-profile.php" class="active"><i class="fas fa-edit"></i> Edit Profile</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>

        <div class="profile-container">
            <div class="profile-header">
                <i class="fas fa-user-circle"></i>
                <h2 style="color: var(--primary);">Edit Profile</h2>
                <p>Update your personal information</p>
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
            
            <div class="email-display">
                <i class="fas fa-envelope"></i>
                <div>
                    <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                    <div style="font-size: 0.9em; color: #666;">(Email cannot be changed)</div>
                </div>
            </div>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label><i class="fas fa-flag"></i> Country</label>
                    <select name="country">
                        <option value="">Select country</option>
                        <option value="Kenya" <?php echo ($user['country'] == 'Kenya') ? 'selected' : ''; ?>>🇰🇪 Kenya</option>
                        <option value="Uganda" <?php echo ($user['country'] == 'Uganda') ? 'selected' : ''; ?>>🇺🇬 Uganda</option>
                        <option value="Tanzania" <?php echo ($user['country'] == 'Tanzania') ? 'selected' : ''; ?>>🇹🇿 Tanzania</option>
                        <option value="Other" <?php echo ($user['country'] == 'Other') ? 'selected' : ''; ?>>🌍 Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-calendar"></i> Age Range</label>
                    <select name="age_range">
                        <option value="">Select age range</option>
                        <option value="13-17" <?php echo ($user['age_range'] == '13-17') ? 'selected' : ''; ?>>13-17</option>
                        <option value="18-24" <?php echo ($user['age_range'] == '18-24') ? 'selected' : ''; ?>>18-24</option>
                        <option value="25-34" <?php echo ($user['age_range'] == '25-34') ? 'selected' : ''; ?>>25-34</option>
                        <option value="35-44" <?php echo ($user['age_range'] == '35-44') ? 'selected' : ''; ?>>35-44</option>
                        <option value="45-54" <?php echo ($user['age_range'] == '45-54') ? 'selected' : ''; ?>>45-54</option>
                        <option value="55+" <?php echo ($user['age_range'] == '55+') ? 'selected' : ''; ?>>55+</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-venus-mars"></i> Gender</label>
                    <select name="gender">
                        <option value="">Select gender</option>
                        <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                        <option value="other" <?php echo ($user['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                        <option value="prefer-not" <?php echo ($user['gender'] == 'prefer-not') ? 'selected' : ''; ?>>Prefer not to say</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 15px; margin-top: 30px;">
                    <button type="submit" class="btn" style="flex: 2;">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="dashboard.php" class="btn btn-outline" style="flex: 1;">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0; text-align: center;">
                <p>Want to change your password? <a href="change-password.php" style="color: var(--secondary);">Click here</a></p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your privacy matters</p>
            </div>
        </div>
    </footer>
</body>
</html>