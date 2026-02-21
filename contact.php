<?php
// contact.php - Contact page for East Africa Surveys
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';

require_once 'config.php';
require_once 'db_connect.php';

$message_sent = false;
$message_error = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $country = htmlspecialchars($_POST['country'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Here you can add code to save to database or send email
        $message_sent = true;
    } else {
        $message_error = true;
    }
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
        }
        .form-status.error {
            background: #f8d7da;
            color: #721c24;
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
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        </nav>

        <main>
            <?php if ($message_sent): ?>
                <div class="form-status success">
                    <i class="fas fa-check-circle"></i>
                    <h3>Message Sent Successfully!</h3>
                    <p>Thank you for contacting us. We'll get back to you soon.</p>
                </div>
            <?php endif; ?>

            <?php if ($message_error): ?>
                <div class="form-status error">
                    <i class="fas fa-exclamation-circle"></i>
                    <h3>Message Failed to Send</h3>
                    <p>Please fill in all fields.</p>
                </div>
            <?php endif; ?>

            <div class="contact-section">
                <!-- Contact Information -->
                <div class="contact-info">
                    <h3 style="margin-bottom: 30px;"><i class="fas fa-address-card"></i> Get in Touch</h3>
                    
                    <div class="contact-detail">
                        <i class="fas fa-envelope" style="font-size: 1.5em;"></i>
                        <div>
                            <strong>Email</strong><br>
                            <a href="mailto:info.eastafricasurveys@gmail.com" style="color: white;">
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
                    
                    <div style="margin-top: 40px; text-align: center; font-size: 2.5em;">
                        🇰🇪 🇺🇬 🇹🇿
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="contact-form">
                    <h2 style="color: #2C3E50; margin-bottom: 30px;">
                        <i class="fas fa-paper-plane"></i> Send a Message
                    </h2>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" name="name" placeholder="e.g., John from Nairobi" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Your Email</label>
                            <input type="email" name="email" placeholder="your.email@example.com" required>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-flag"></i> Country</label>
                            <select name="country" required>
                                <option value="">Select your country</option>
                                <option value="Kenya">🇰🇪 Kenya</option>
                                <option value="Uganda">🇺🇬 Uganda</option>
                                <option value="Tanzania">🇹🇿 Tanzania</option>
                                <option value="Other">🌍 Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Your Message</label>
                            <textarea name="message" rows="5" placeholder="What would you like to tell us?" required></textarea>
                        </div>
                        
                        <button type="submit" name="submit_message" class="btn" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Alternative Contact Methods -->
            <div style="background: white; padding: 40px; border-radius: 20px; margin: 40px 0; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                <h2 style="color: #2C3E50; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-envelope-open-text"></i> Alternative Ways to Reach Us
                </h2>
                
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px;">
                    <div style="text-align: center; padding: 20px;">
                        <i class="fas fa-envelope" style="font-size: 3em; color: #E67E22; margin-bottom: 15px;"></i>
                        <h3>Direct Email</h3>
                        <p>Send us an email directly:</p>
                        <p><strong>info.eastafricasurveys@gmail.com</strong></p>
                        <a href="mailto:info.eastafricasurveys@gmail.com" style="display: inline-block; padding: 10px 20px; background: #E67E22; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px;">
                            <i class="fas fa-envelope"></i> Open in Email App
                        </a>
                    </div>
                    
                    <div style="text-align: center; padding: 20px;">
                        <i class="fab fa-google" style="font-size: 3em; color: #DB4437; margin-bottom: 15px;"></i>
                        <h3>Gmail Users</h3>
                        <p>Compose directly in Gmail:</p>
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to=info.eastafricasurveys@gmail.com" 
                           target="_blank" style="display: inline-block; padding: 10px 20px; background: #DB4437; color: white; text-decoration: none; border-radius: 5px;">
                            <i class="fab fa-google"></i> Open in Gmail
                        </a>
                    </div>
                    
                    <div style="text-align: center; padding: 20px;">
                        <i class="fab fa-yahoo" style="font-size: 3em; color: #6001D2; margin-bottom: 15px;"></i>
                        <h3>Yahoo Users</h3>
                        <p>Compose in Yahoo Mail:</p>
                        <a href="https://compose.mail.yahoo.com/?to=info.eastafricasurveys@gmail.com" 
                           target="_blank" style="display: inline-block; padding: 10px 20px; background: #6001D2; color: white; text-decoration: none; border-radius: 5px;">
                            <i class="fab fa-yahoo"></i> Open in Yahoo
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
                    <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="index.php">Home</a></p>
                    <p><a href="results.php">Results</a></p>
                    <p><a href="privacy.php">Privacy</a></p>
                    <p><a href="terms.php">Terms</a></p>
                </div>
                <div class="footer-section">
                    <h3>Connect With Us</h3>
                    <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> East Africa</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>