<?php
// contact.php - Contact page with email sending
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Check if user is logged in (for navigation)
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';

require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php'; // We need this for sendEmail()

$message_sent = false;
$message_error = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $country = htmlspecialchars($_POST['country'] ?? '');
    $message_content = htmlspecialchars($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message_content)) {
        
        // Prepare email content
        $to = 'info.eastafricasurveys@gmail.com'; // Send to yourself
        $subject = "Contact Form Message from $name ($country)";
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #2C3E50; color: white; padding: 20px; text-align: center; }
                .content { padding: 30px; background: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #2C3E50; }
                .value { padding: 8px; background: white; border-radius: 5px; }
                .footer { text-align: center; padding: 20px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌍 East Africa Surveys</h1>
                    <h2>New Contact Form Message</h2>
                </div>
                <div class='content'>
                    <div class='field'>
                        <div class='label'>Name:</div>
                        <div class='value'>$name</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email:</div>
                        <div class='value'>$email</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Country:</div>
                        <div class='value'>$country</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Message:</div>
                        <div class='value' style='white-space: pre-line;'>$message_content</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>Sent from East Africa Surveys Contact Form</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        // Send the email using your working Brevo function
        if (sendEmail($to, $subject, $message)) {
            $message_sent = true;
            
            // Optional: Save to database
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, country, message) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $email, $country, $message_content]);
            } catch (PDOException $e) {
                // Just log error, don't show to user
                error_log("Failed to save contact message: " . $e->getMessage());
            }
            
        } else {
            $message_error = true;
            error_log("Failed to send contact form email from $email");
        }
    } else {
        $message_error = true;
    }
}

// Create contact_messages table if it doesn't exist
try {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS contact_messages (
            id SERIAL PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            country VARCHAR(50),
            message TEXT NOT NULL,
            created_at TIMESTAMPTZ DEFAULT NOW(),
            is_read BOOLEAN DEFAULT FALSE
        );
    ");
} catch (PDOException $e) {
    // Table might already exist, ignore error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .contact-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 40px;
            margin: 40px 0;
        }
        .contact-info {
            background: #2C3E50;
            color: white;
            padding: 40px;
            border-radius: 20px;
        }
        .contact-detail {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
            padding: 15px;
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
        }
        .contact-form {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        .form-status {
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .form-status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .email-badge {
            background: rgba(255,255,255,0.15);
            padding: 12px 25px;
            border-radius: 50px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Get in Touch With Us</p>
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
            <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a>
            <?php if ($logged_in): ?>
                <a href="dashboard.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?></a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else: ?>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>

        <main>
            <?php if ($message_sent): ?>
                <div class="form-status success">
                    <i class="fas fa-check-circle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h3>✅ Message Sent Successfully!</h3>
                    <p>Thank you for contacting us, <?php echo htmlspecialchars($name); ?>! We'll get back to you within 24-48 hours.</p>
                    <p style="margin-top: 10px;">🇰🇪 🇺🇬 🇹🇿</p>
                </div>
            <?php endif; ?>

            <?php if ($message_error): ?>
                <div class="form-status error">
                    <i class="fas fa-exclamation-circle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h3>❌ Message Failed to Send</h3>
                    <p>Please fill in all fields correctly and try again.</p>
                    <p>If the problem persists, email us directly at info.eastafricasurveys@gmail.com</p>
                </div>
            <?php endif; ?>

            <div class="contact-section">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h3 style="margin-bottom: 30px; font-size: 1.8em;">
                        <i class="fas fa-address-card"></i> Get in Touch
                    </h3>
                    
                    <div class="contact-detail">
                        <i class="fas fa-envelope" style="font-size: 1.5em;"></i>
                        <div>
                            <strong>Email</strong><br>
                            <a href="mailto:info.eastafricasurveys@gmail.com" style="color: white; text-decoration: none;">
                                info.eastafricasurveys@gmail.com
                            </a>
                        </div>
                    </div>
                    
                    <div class="contact-detail">
                        <i class="fas fa-map-marker-alt" style="font-size: 1.5em;"></i>
                        <div>
                            <strong>Region</strong><br>
                            Kenya | Uganda | Tanzania
                        </div>
                    </div>
                    
                    <div class="contact-detail">
                        <i class="fas fa-clock" style="font-size: 1.5em;"></i>
                        <div>
                            <strong>Response Time</strong><br>
                            Within 24-48 hours
                        </div>
                    </div>
                    
                    <div style="margin-top: 40px; text-align: center;">
                        <p style="font-size: 2.5em; margin: 0;">🇰🇪 🇺🇬 🇹🇿</p>
                        <p style="margin-top: 10px;"><strong>Your voice matters across East Africa!</strong></p>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form">
                    <h2 style="color: #2C3E50; margin-bottom: 30px; font-size: 1.8em;">
                        <i class="fas fa-paper-plane"></i> Send a Message
                    </h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Your Full Name</label>
                            <input type="text" name="name" placeholder="e.g., John Odhiambo" required 
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Your Email</label>
                            <input type="email" name="email" placeholder="you@example.com" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-flag"></i> Your Country</label>
                            <select name="country" required>
                                <option value="">-- Select your country --</option>
                                <option value="Kenya" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Kenya') ? 'selected' : ''; ?>>🇰🇪 Kenya</option>
                                <option value="Uganda" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Uganda') ? 'selected' : ''; ?>>🇺🇬 Uganda</option>
                                <option value="Tanzania" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Tanzania') ? 'selected' : ''; ?>>🇹🇿 Tanzania</option>
                                <option value="Other" <?php echo (isset($_POST['country']) && $_POST['country'] == 'Other') ? 'selected' : ''; ?>>🌍 Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Your Message</label>
                            <textarea name="message" rows="6" placeholder="How can we help you? What would you like to tell us?" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" name="submit_message" class="btn" style="width: 100%; padding: 15px; font-size: 1.2em;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                        
                        <p style="text-align: center; margin-top: 15px; font-size: 0.9em; color: #666;">
                            <i class="fas fa-lock"></i> We respect your privacy. Your information is safe with us.
                        </p>
                    </form>
                </div>
            </div>

            <!-- Alternative Contact Methods -->
            <div style="background: white; padding: 40px; border-radius: 20px; margin: 40px 0; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h2 style="color: #2C3E50; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-envelope-open-text"></i> Other Ways to Reach Us
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-envelope" style="font-size: 3em; color: #E67E22; margin-bottom: 15px;"></i>
                        <h3>Direct Email</h3>
                        <p>Send us an email directly using your preferred email client:</p>
                        <p style="background: #f0f0f0; padding: 10px; border-radius: 5px; font-weight: bold;">
                            info.eastafricasurveys@gmail.com
                        </p>
                        <a href="mailto:info.eastafricasurveys@gmail.com" class="btn btn-outline" style="margin-top: 10px; display: inline-block;">
                            <i class="fas fa-envelope"></i> Open Email App
                        </a>
                    </div>
                    
                    <div style="text-align: center; padding: 20px;">
                        <i class="fab fa-google" style="font-size: 3em; color: #DB4437; margin-bottom: 15px;"></i>
                        <h3>Gmail</h3>
                        <p>Compose a message directly in Gmail:</p>
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=info.eastafricasurveys@gmail.com" 
                           target="_blank" class="btn" style="background: #DB4437; display: inline-block;">
                            <i class="fab fa-google"></i> Open Gmail
                        </a>
                    </div>
                    
                    <div style="text-align: center; padding: 20px;">
                        <i class="fab fa-yahoo" style="font-size: 3em; color: #6001D2; margin-bottom: 15px;"></i>
                        <h3>Yahoo Mail</h3>
                        <p>Compose a message directly in Yahoo Mail:</p>
                        <a href="https://compose.mail.yahoo.com/?to=info.eastafricasurveys@gmail.com" 
                           target="_blank" class="btn" style="background: #6001D2; display: inline-block;">
                            <i class="fab fa-yahoo"></i> Open Yahoo
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>🌍 East Africa Surveys</h3>
                    <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys. Your opinion matters!</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="index.php">🏠 Home</a></p>
                    <p><a href="results.php">📊 Results</a></p>
                    <p><a href="privacy.php">🔒 Privacy</a></p>
                    <p><a href="terms.php">📝 Terms</a></p>
                </div>
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> East Africa</p>
                    <p><i class="fas fa-globe"></i> Kenya | Uganda | Tanzania</p>
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