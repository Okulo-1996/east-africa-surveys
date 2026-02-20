<?php
// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
    <div class="container header-content">
        <h1>East Africa <span>Surveys</span></h1>
        <p>Your Voice Matters — Daily Polls from Kenya, Uganda & Tanzania</p>
        <div class="email-badge">
            <i class="fas fa-envelope"></i>
            info.eastafricasurveys@gmail.com
        </div>
    </div>
</header>

    <div class="container">
        <nav>
    <a href="index.php">🏠 Home</a>
    <a href="results.php">📊 Results</a>
    <a href="about.php">📖 About</a>
    <a href="contact.php">📞 Contact</a>
    
    <?php if ($logged_in): ?>
        <a href="dashboard.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    <?php else: ?>
        <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
        <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
    <?php endif; ?>
</nav>

        <main>
            <div class="about-section">
                <h2 style="color: var(--savanna-green);">🔒 Simple & Clear Privacy</h2>
                
                <div style="margin: 30px 0;">
                    <h3 style="color: var(--sunset-orange);">📝 What We Collect</h3>
                    <ul style="margin: 15px 0 15px 40px;">
                        <li>Your answers to our daily surveys</li>
                        <li>The time you voted</li>
                        <li>Country you select (optional)</li>
                        <li>Nothing else - no names, no emails, no phone numbers</li>
                    </ul>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="color: var(--sunset-orange);">🙅 What We DON'T Collect</h3>
                    <ul style="margin: 15px 0 15px 40px;">
                        <li>❌ Your name</li>
                        <li>❌ Your email address</li>
                        <li>❌ Your phone number</li>
                        <li>❌ Your exact location</li>
                        <li>❌ Your IP address (we removed this)</li>
                    </ul>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="color: var(--sunset-orange);">📊 How We Use Data</h3>
                    <p>We show the total results to everyone. We never sell your individual answers. We never share data with third parties. Everything is 100% anonymous.</p>
                </div>

                <div style="margin: 30px 0;">
                    <h3 style="color: var(--sunset-orange);">🍪 Cookies</h3>
                    <p>We use one simple cookie to know if you've voted today (so you don't vote twice). That's it. No tracking, no advertising cookies.</p>
                </div>

                <div style="background: #e8f4f8; padding: 20px; border-radius: 10px; margin-top: 30px;">
                    <p style="text-align: center;">
                        <i class="fas fa-shield-alt" style="color: var(--savanna-green);"></i>
                        Questions about privacy? Email us: info.eastafricasurveys@gmail.com
                    </p>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <!-- Same footer -->
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Your privacy matters. Always free, always anonymous.</p>
                <p><a href="terms.php"><i class="fas fa-chevron-right"></i> Terms of Service</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 East Africa Surveys | Privacy by design</p>
        </div>
    </footer>
</body>
</html>
