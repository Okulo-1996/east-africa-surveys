<?php
// contact.php - Modern Tech Contact Page
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';

require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

$message_sent = false;
$message_error = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_message'])) {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message_content = htmlspecialchars($_POST['message'] ?? '');
    
    if (!empty($name) && !empty($email) && !empty($message_content)) {
        
        $to = 'info.eastafricasurveys@gmail.com';
        $email_subject = "📬 New Contact: $subject - $name";
        
        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: 'Inter', sans-serif; background: #f8fafc; }
                .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #2563EB, #1E40AF); padding: 40px 30px; text-align: center; }
                .header h1 { color: white; margin: 0; font-size: 28px; }
                .content { padding: 40px 30px; }
                .field { margin-bottom: 20px; }
                .label { color: #64748B; font-size: 14px; margin-bottom: 4px; }
                .value { color: #1E293B; font-size: 16px; font-weight: 500; padding: 12px; background: #F8FAFC; border-radius: 12px; }
                .footer { background: #F8FAFC; padding: 30px; text-align: center; color: #64748B; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌍 East Africa Surveys</h1>
                </div>
                <div class='content'>
                    <h2 style='margin-top: 0; color: #1E293B;'>New Contact Message</h2>
                    <div class='field'>
                        <div class='label'>Name</div>
                        <div class='value'>$name</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Email</div>
                        <div class='value'>$email</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Subject</div>
                        <div class='value'>$subject</div>
                    </div>
                    <div class='field'>
                        <div class='label'>Message</div>
                        <div class='value' style='white-space: pre-line;'>$message_content</div>
                    </div>
                </div>
                <div class='footer'>
                    <p>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        if (sendEmail($to, $email_subject, $message)) {
            $message_sent = true;
        } else {
            $message_error = true;
        }
    } else {
        $message_error = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Contact Us - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Modern Contact Page Specific Styles */
        .contact-hero {
            background: linear-gradient(135deg, #2563EB, #1E40AF);
            padding: 60px 24px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .contact-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }
        
        .contact-hero h1 {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 12px;
            position: relative;
        }
        
        .contact-hero p {
            font-size: 1.1em;
            opacity: 0.95;
            max-width: 500px;
            margin: 0 auto;
            position: relative;
        }
        
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            padding: 24px;
            background: #F8FAFC;
        }
        
        .contact-info-card {
            background: white;
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid #E2E8F0;
            transition: transform 0.3s ease;
        }
        
        .contact-info-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        }
        
        .info-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        
        .info-header i {
            font-size: 28px;
            color: #2563EB;
            background: rgba(37, 99, 235, 0.1);
            padding: 12px;
            border-radius: 16px;
        }
        
        .info-header h3 {
            font-size: 1.2em;
            font-weight: 600;
            color: #1E293B;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #F8FAFC;
            border-radius: 16px;
            margin-bottom: 12px;
            transition: all 0.2s;
        }
        
        .info-item:hover {
            background: #F1F5F9;
        }
        
        .info-item i {
            font-size: 20px;
            color: #2563EB;
        }
        
        .info-item-content {
            flex: 1;
        }
        
        .info-item-content .label {
            font-size: 0.85em;
            color: #64748B;
            margin-bottom: 2px;
        }
        
        .info-item-content .value {
            font-weight: 600;
            color: #1E293B;
        }
        
        .info-item-content .value a {
            color: #2563EB;
            text-decoration: none;
        }
        
        .social-links {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        
        .social-link {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: #F8FAFC;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748B;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .social-link:hover {
            background: #2563EB;
            color: white;
            transform: translateY(-2px);
        }
        
        .contact-form-card {
            background: white;
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid #E2E8F0;
        }
        
        .form-header {
            margin-bottom: 24px;
        }
        
        .form-header h2 {
            font-size: 1.8em;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 8px;
        }
        
        .form-header p {
            color: #64748B;
            font-size: 1em;
        }
        
        .modern-input-group {
            margin-bottom: 20px;
        }
        
        .modern-input-group label {
            display: block;
            margin-bottom: 8px;
            color: #1E293B;
            font-weight: 500;
            font-size: 0.95em;
        }
        
        .modern-input {
            width: 100%;
            padding: 16px 18px;
            background: #F8FAFC;
            border: 2px solid transparent;
            border-radius: 16px;
            font-size: 1em;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        
        .modern-input:focus {
            outline: none;
            border-color: #2563EB;
            background: white;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
        }
        
        .modern-textarea {
            min-height: 140px;
            resize: vertical;
        }
        
        .modern-button {
            width: 100%;
            padding: 18px 24px;
            background: linear-gradient(135deg, #2563EB, #1E40AF);
            color: white;
            border: none;
            border-radius: 16px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }
        
        .modern-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.3);
        }
        
        .modern-button i {
            font-size: 1.2em;
        }
        
        .quick-reach {
            padding: 24px;
            background: linear-gradient(135deg, #F8FAFC, #FFFFFF);
        }
        
        .quick-reach h3 {
            font-size: 1.2em;
            color: #1E293B;
            margin-bottom: 16px;
        }
        
        .quick-reach-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
        }
        
        .quick-reach-item {
            background: white;
            padding: 20px 12px;
            border-radius: 20px;
            text-align: center;
            border: 1px solid #E2E8F0;
            transition: all 0.2s;
        }
        
        .quick-reach-item:hover {
            border-color: #2563EB;
            transform: translateY(-2px);
        }
        
        .quick-reach-item i {
            font-size: 24px;
            color: #2563EB;
            margin-bottom: 8px;
        }
        
        .quick-reach-item span {
            display: block;
            font-size: 0.9em;
            color: #64748B;
        }
        
        .map-section {
            height: 200px;
            background: linear-gradient(135deg, #1E293B, #0F172A);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }
        
        .map-section i {
            font-size: 48px;
            margin-bottom: 12px;
            color: #2563EB;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 768px) {
            .contact-grid {
                grid-template-columns: 1fr;
                padding: 16px;
            }
            
            .contact-hero h1 {
                font-size: 2em;
            }
            
            .quick-reach-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        .floating-badge {
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: white;
            padding: 16px 24px;
            border-radius: 60px;
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid #E2E8F0;
            animation: float 3s ease-in-out infinite;
            z-index: 100;
        }
        
        .floating-badge i {
            color: #2563EB;
            font-size: 20px;
        }
        
        .floating-badge span {
            font-weight: 600;
            color: #1E293B;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <!-- Modern Hero Section -->
    <div class="contact-hero">
        <h1>Let's Connect</h1>
        <p>We're here to listen, help, and build something amazing together</p>
    </div>

    <!-- Navigation -->
    <nav>
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
        <a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a>
        <?php if ($logged_in): ?>
            <a href="dashboard.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        <?php endif; ?>
    </nav>

    <!-- Success/Error Messages -->
    <?php if ($message_sent): ?>
        <div style="padding: 16px; background: #10B981; color: white; text-align: center; animation: slideDown 0.3s;">
            <i class="fas fa-check-circle"></i> Message sent! We'll get back to you within 24 hours.
        </div>
    <?php endif; ?>

    <?php if ($message_error): ?>
        <div style="padding: 16px; background: #EF4444; color: white; text-align: center; animation: slideDown 0.3s;">
            <i class="fas fa-exclamation-circle"></i> Please fill all fields correctly.
        </div>
    <?php endif; ?>

    <!-- Main Contact Grid -->
    <div class="contact-grid">
        <!-- Left Column - Contact Info -->
        <div class="contact-info-card">
            <div class="info-header">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Visit Our Hub</h3>
            </div>
            
            <div class="info-item">
                <i class="fas fa-map-pin"></i>
                <div class="info-item-content">
                    <div class="label">Main Office</div>
                    <div class="value">Nairobi, Kenya</div>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fas fa-globe-africa"></i>
                <div class="info-item-content">
                    <div class="label">Regional Presence</div>
                    <div class="value">Kenya · Uganda · Tanzania</div>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fas fa-clock"></i>
                <div class="info-item-content">
                    <div class="label">Response Time</div>
                    <div class="value">< 24 hours · Mon-Fri</div>
                </div>
            </div>
            
            <div class="info-item" style="margin-top: 20px;">
                <i class="fas fa-envelope"></i>
                <div class="info-item-content">
                    <div class="label">Email</div>
                    <div class="value"><a href="mailto:info.eastafricasurveys@gmail.com">info.eastafricasurveys@gmail.com</a></div>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fas fa-phone-alt"></i>
                <div class="info-item-content">
                    <div class="label">Phone (Coming Soon)</div>
                    <div class="value" style="color: #94A3B8;">+254 ...</div>
                </div>
            </div>
            
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-link"><i class="fab fa-github"></i></a>
                <a href="#" class="social-link"><i class="fab fa-whatsapp"></i></a>
            </div>
        </div>

        <!-- Right Column - Contact Form -->
        <div class="contact-form-card">
            <div class="form-header">
                <h2>Send a Message</h2>
                <p>We'd love to hear from you</p>
            </div>
            
            <form method="POST" action="">
                <div class="modern-input-group">
                    <label><i class="fas fa-user" style="margin-right: 8px;"></i> Full Name</label>
                    <input type="text" name="name" class="modern-input" placeholder="John M." required>
                </div>
                
                <div class="modern-input-group">
                    <label><i class="fas fa-envelope" style="margin-right: 8px;"></i> Email Address</label>
                    <input type="email" name="email" class="modern-input" placeholder="you@example.com" required>
                </div>
                
                <div class="modern-input-group">
                    <label><i class="fas fa-tag" style="margin-right: 8px;"></i> Subject</label>
                    <input type="text" name="subject" class="modern-input" placeholder="What's this about?">
                </div>
                
                <div class="modern-input-group">
                    <label><i class="fas fa-comment" style="margin-right: 8px;"></i> Message</label>
                    <textarea name="message" class="modern-input modern-textarea" placeholder="Tell us everything..." required></textarea>
                </div>
                
                <button type="submit" name="submit_message" class="modern-button">
                    <i class="fas fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Reach Section -->
    <div class="quick-reach">
        <h3 style="text-align: center;">Quick Contact Options</h3>
        <div class="quick-reach-grid">
            <div class="quick-reach-item">
                <i class="fas fa-envelope-open-text"></i>
                <span>Email Direct</span>
            </div>
            <div class="quick-reach-item">
                <i class="fab fa-whatsapp"></i>
                <span>WhatsApp</span>
            </div>
            <div class="quick-reach-item">
                <i class="fas fa-headset"></i>
                <span>Live Chat</span>
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="map-section">
        <div>
            <i class="fas fa-map-marked-alt"></i>
            <h3>Serving East Africa</h3>
            <p>🇰🇪 Nairobi · 🇺🇬 Kampala · 🇹🇿 Dar es Salaam</p>
        </div>
    </div>

    <!-- Floating Contact Badge -->
    <div class="floating-badge">
        <i class="fas fa-headset"></i>
        <span>24/7 Support</span>
        <i class="fas fa-chevron-right" style="font-size: 14px;"></i>
    </div>

    <footer>
        <div class="footer-grid">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Your voice matters across Kenya, Uganda & Tanzania</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php">Home</a></p>
                <p><a href="privacy.php">Privacy</a></p>
                <p><a href="terms.php">Terms</a></p>
            </div>
            <div class="footer-section">
                <h3>Connect</h3>
                <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 East Africa Surveys</p>
        </div>
    </footer>

    <script>
        // Smooth scroll to top
        document.querySelector('.floating-badge').addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
        
        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });
        
        document.querySelectorAll('.contact-info-card, .contact-form-card, .quick-reach-item').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>