<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - East Africa Surveys</title>
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
            <a href="about.php">📖 About Us</a>
            <a href="contact.php" class="active">📞 Contact</a>
            <a href="terms.php"><i class="fas fa-file-contract"></i> Terms</a>
        </nav>

        <main>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin: 40px 0;">
                <!-- Contact Information -->
                <div class="about-section">
                    <h2 style="color: var(--savanna-green); margin-bottom: 20px;">📬 Get in Touch</h2>
                    
                    <div style="margin-bottom: 25px;">
                        <i class="fas fa-envelope" style="color: var(--sunset-orange); font-size: 1.5em;"></i>
                        <h3>Email</h3>
                        <p>info.eastafricasurveys@gmail.com</p>
                    </div>
                    
                    <div style="margin-bottom: 25px;">
                        <i class="fas fa-map-marker-alt" style="color: var(--sunset-orange); font-size: 1.5em;"></i>
                        <h3>Our Region</h3>
                        <p>🇰🇪 Nairobi, Kenya<br>
                           🇺🇬 Kampala, Uganda<br>
                           🇹🇿 Dar es Salaam, Tanzania</p>
                    </div>
                    
                    <div>
                        <i class="fas fa-clock" style="color: var(--sunset-orange); font-size: 1.5em;"></i>
                        <h3>Response Time</h3>
                        <p>We reply within 24-48 hours</p>
                    </div>
                </div>

                <!-- Contact Form - Free using FormSubmit -->
                <div class="contact-form">
                    <h2 style="color: var(--savanna-green); margin-bottom: 20px;">✍️ Send a Message</h2>
                    
                    <form action="https://formsubmit.co/info.eastafricasurveys@gmail.com" method="POST">
                        <!-- FormSubmit configuration - all FREE -->
                        <input type="hidden" name="_subject" value="New Contact from East Africa Surveys">
                        <input type="hidden" name="_captcha" value="false">
                        <input type="hidden" name="_next" value="http://eastafricasurveys.com/thankyou.html">
                        <input type="hidden" name="_template" value="table">
                        
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Your Name</label>
                            <input type="text" name="name" required placeholder="e.g., John from Nairobi">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-envelope"></i> Your Email</label>
                            <input type="email" name="email" required placeholder="We'll reply to this email">
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-flag"></i> Country</label>
                            <select name="country" style="width: 100%; padding: 12px; border-radius: 10px; border: 2px solid #e0e0e0;">
                                <option value="Kenya">🇰🇪 Kenya</option>
                                <option value="Uganda">🇺🇬 Uganda</option>
                                <option value="Tanzania">🇹🇿 Tanzania</option>
                                <option value="Other">🌍 Other</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label><i class="fas fa-comment"></i> Your Message</label>
                            <textarea name="message" rows="5" required placeholder="What would you like to tell us?"></textarea>
                        </div>
                        
                        <button type="submit" class="btn" style="width: 100%;">
                            <i class="fas fa-paper-plane"></i> Send Message Free
                        </button>
                        
                        <p style="text-align: center; margin-top: 15px; font-size: 0.8em; color: #666;">
                            <i class="fas fa-lock"></i> No spam. We respect your privacy.
                        </p>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Your voice matters across Kenya, Uganda, and Tanzania.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="privacy.php" style="color: white;">🔒 Privacy</a></p>
                <p><a href="terms.php"><i class="fas fa-chevron-right"></i> Terms of Service</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 East Africa Surveys | Free for all East Africans</p>
        </div>
    </footer>
</body>
</html>
