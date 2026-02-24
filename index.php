<?php
// index.php - Modern Homepage for East Africa Surveys
session_start();
require_once 'db_connect.php';

// Get today's questions
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM questions WHERE active_date = ? ORDER BY display_order");
$stmt->execute([$today]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user already voted today
$voted_today = isset($_COOKIE['voted_' . str_replace('-', '_', $today)]);

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>East Africa Surveys - Daily Public Opinion from Kenya, Uganda & Tanzania</title>
    <meta name="description" content="Join thousands of East Africans sharing their opinions daily. Free, anonymous surveys on topics that matter to Kenya, Uganda, and Tanzania.">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* Modern Homepage Specific Styles */
        .hero-section {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 80px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(37,99,235,0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
        }
        
        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-title {
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        
        .hero-title span {
            color: #2563EB;
            display: inline-block;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .hero-subtitle {
            font-size: clamp(1.1rem, 4vw, 1.3rem);
            opacity: 0.9;
            margin-bottom: 30px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-buttons {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .hero-button {
            padding: 16px 32px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
        }
        
        .hero-button-primary {
            background: #2563EB;
            color: white;
            box-shadow: 0 10px 20px rgba(37,99,235,0.3);
        }
        
        .hero-button-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37,99,235,0.4);
        }
        
        .hero-button-secondary {
            background: rgba(255,255,255,0.1);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .hero-button-secondary:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }
        
        .stats-grid-modern {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        .stat-card-modern {
            background: white;
            border-radius: 24px;
            padding: 32px 24px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            border: 1px solid #E2E8F0;
            transition: all 0.3s;
        }
        
        .stat-card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: #2563EB;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: #2563EB;
            margin-bottom: 16px;
        }
        
        .stat-number-modern {
            font-size: 2.8rem;
            font-weight: 800;
            color: #1E293B;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .stat-label-modern {
            color: #64748B;
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .how-it-works {
            padding: 60px 24px;
            background: white;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 40px;
        }
        
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 900px;
            margin: 0 auto;
        }
        
        .step-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 20px;
            background: #F8FAFC;
            transition: all 0.3s;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            background: #2563EB;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.5rem;
            margin: 0 auto 20px;
        }
        
        .step-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: #1E293B;
        }
        
        .step-card p {
            color: #64748B;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .survey-section {
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        .survey-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .survey-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 8px;
        }
        
        .survey-header p {
            color: #64748B;
            font-size: 1.1rem;
        }
        
        .no-polls-card {
            background: white;
            border-radius: 24px;
            padding: 60px 40px;
            text-align: center;
            border: 1px solid #E2E8F0;
            box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        
        .no-polls-icon {
            font-size: 5rem;
            color: #94A3B8;
            margin-bottom: 20px;
        }
        
        .no-polls-card h3 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 12px;
        }
        
        .no-polls-card p {
            color: #64748B;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .trust-badges {
            display: flex;
            justify-content: center;
            gap: 40px;
            padding: 40px 24px;
            background: white;
            flex-wrap: wrap;
        }
        
        .trust-badge {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #64748B;
        }
        
        .trust-badge i {
            font-size: 1.5rem;
            color: #2563EB;
        }
        
        .testimonials {
            padding: 60px 24px;
            background: #F8FAFC;
        }
        
        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .testimonial-card {
            background: white;
            border-radius: 20px;
            padding: 24px;
            border: 1px solid #E2E8F0;
        }
        
        .testimonial-text {
            color: #1E293B;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 16px;
            font-style: italic;
        }
        
        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .author-avatar {
            width: 40px;
            height: 40px;
            background: #2563EB;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        
        .author-info {
            font-size: 0.9rem;
        }
        
        .author-name {
            font-weight: 600;
            color: #1E293B;
        }
        
        .author-location {
            color: #64748B;
            font-size: 0.8rem;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        @media (max-width: 768px) {
            .stats-grid-modern {
                grid-template-columns: 1fr;
                gap: 16px;
            }
            
            .steps-grid {
                grid-template-columns: 1fr;
            }
            
            .testimonials-grid {
                grid-template-columns: 1fr;
            }
            
            .hero-buttons {
                flex-direction: column;
            }
            
            .hero-button {
                width: 100%;
                justify-content: center;
            }
        }
        
        .floating-chat {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 60px;
            height: 60px;
            background: #2563EB;
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            box-shadow: 0 10px 25px rgba(37,99,235,0.3);
            cursor: pointer;
            transition: all 0.3s;
            z-index: 1000;
        }
        
        .floating-chat:hover {
            transform: scale(1.1);
            background: #1D4ED8;
        }
        
        .floating-chat i {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <!-- Sticky Header -->
    <header>
        <div class="header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p class="tagline">Daily Public Opinion from Kenya, Uganda & Tanzania</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <a href="index.php" class="active"><i class="fas fa-home"></i> Home</a>
        <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
        <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
        <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        <?php if ($logged_in): ?>
            <a href="dashboard.php"><i class="fas fa-user"></i> <?php echo htmlspecialchars($username); ?></a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php" class="btn-primary-nav"><i class="fas fa-user-plus"></i> Register</a>
        <?php endif; ?>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">
                Your Voice <span>Matters</span>
            </h1>
            <p class="hero-subtitle">
                Join thousands of East Africans sharing their opinions daily. Free, anonymous, and impactful.
            </p>
            <div class="hero-buttons">
                <a href="#survey" class="hero-button hero-button-primary">
                    <i class="fas fa-poll"></i> Take Today's Poll
                </a>
                <a href="results.php" class="hero-button hero-button-secondary">
                    <i class="fas fa-chart-line"></i> View Results
                </a>
            </div>
        </div>
    </section>

    <!-- Modern Stats Cards -->
    <section class="stats-grid-modern">
        <div class="stat-card-modern">
            <i class="fas fa-users stat-icon"></i>
            <div class="stat-number-modern">1,234+</div>
            <div class="stat-label-modern">Daily Voters</div>
        </div>
        <div class="stat-card-modern">
            <i class="fas fa-globe-africa stat-icon"></i>
            <div class="stat-number-modern">3</div>
            <div class="stat-label-modern">Countries</div>
        </div>
        <div class="stat-card-modern">
            <i class="fas fa-lock stat-icon"></i>
            <div class="stat-number-modern">100%</div>
            <div class="stat-label-modern">Free & Anonymous</div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <h2 class="section-title">How It Works</h2>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-number">1</div>
                <h3>Answer 5 Questions</h3>
                <p>Daily polls on topics that matter to East Africa. Takes just 2 minutes.</p>
            </div>
            <div class="step-card">
                <div class="step-number">2</div>
                <h3>See Real-Time Results</h3>
                <p>Watch opinions unfold across Kenya, Uganda, and Tanzania instantly.</p>
            </div>
            <div class="step-card">
                <div class="step-number">3</div>
                <h3>Make Your Voice Heard</h3>
                <p>Your opinion contributes to understanding what East Africa really thinks.</p>
            </div>
        </div>
    </section>

    <!-- Survey Section -->
    <section id="survey" class="survey-section">
        <div class="survey-header">
            <h2>🗓️ Today's Polls</h2>
            <p><?php echo date('l, F j, Y'); ?></p>
        </div>

        <?php if ($voted_today): ?>
            <div class="alert alert-success" style="text-align: center;">
                <i class="fas fa-check-circle"></i>
                <h3>Thank you for voting today!</h3>
                <p>Come back tomorrow for new questions.</p>
                <a href="results.php" class="hero-button hero-button-primary" style="display: inline-block; margin-top: 15px;">View Results</a>
            </div>
        <?php elseif (count($questions) > 0): ?>
            <form method="POST" action="submit.php" id="surveyForm">
                <?php foreach ($questions as $index => $question): ?>
                    <div class="survey-card">
                        <span class="question-number">Question <?php echo $index + 1; ?> of 5</span>
                        <div class="question-text">
                            <?php echo htmlspecialchars($question['question_text']); ?>
                        </div>
                        <div class="option-group">
                            <label>
                                <input type="radio" name="q<?php echo $question['id']; ?>" value="Yes" required>
                                <span>👍 Yes</span>
                            </label>
                            <label>
                                <input type="radio" name="q<?php echo $question['id']; ?>" value="No">
                                <span>👎 No</span>
                            </label>
                            <label>
                                <input type="radio" name="q<?php echo $question['id']; ?>" value="Maybe">
                                <span>🤔 Not Sure</span>
                            </label>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div style="text-align: center; margin: 40px 0;">
                    <button type="submit" class="hero-button hero-button-primary" style="border: none;">
                        <i class="fas fa-paper-plane"></i> Submit My Answers
                    </button>
                </div>
            </form>
        <?php else: ?>
            <div class="no-polls-card">
                <i class="fas fa-calendar-day no-polls-icon"></i>
                <h3>No Polls Available Today</h3>
                <p>Our team is preparing tomorrow's questions. Check back tomorrow!</p>
                <a href="results.php" class="hero-button hero-button-primary" style="display: inline-block;">
                    <i class="fas fa-chart-bar"></i> View Past Results
                </a>
            </div>
        <?php endif; ?>
    </section>

    <!-- Trust Badges -->
    <section class="trust-badges">
        <div class="trust-badge">
            <i class="fas fa-shield-alt"></i>
            <span>100% Anonymous</span>
        </div>
        <div class="trust-badge">
            <i class="fas fa-clock"></i>
            <span>2-Minute Surveys</span>
        </div>
        <div class="trust-badge">
            <i class="fas fa-chart-pie"></i>
            <span>Real-Time Results</span>
        </div>
        <div class="trust-badge">
            <i class="fas fa-mobile-alt"></i>
            <span>Mobile Friendly</span>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="testimonials">
        <h2 class="section-title">What East Africans Say</h2>
        <div class="testimonials-grid">
            <div class="testimonial-card">
                <p class="testimonial-text">"Finally a platform where my opinion matters. I love seeing how my views compare with others across East Africa."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">JM</div>
                    <div class="author-info">
                        <div class="author-name">James Mwangi</div>
                        <div class="author-location">Nairobi, Kenya</div>
                    </div>
                </div>
            </div>
            <div class="testimonial-card">
                <p class="testimonial-text">"The daily polls are quick and interesting. I've learned so much about what my fellow East Africans think."</p>
                <div class="testimonial-author">
                    <div class="author-avatar">AN</div>
                    <div class="author-info">
                        <div class="author-name">Aisha Nakato</div>
                        <div class="author-location">Kampala, Uganda</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Chat Button -->
    <div class="floating-chat" onclick="window.location.href='contact.php'">
        <i class="fas fa-comment"></i>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys. Your opinion matters!</p>
                <div class="email-badge" style="margin-top: 15px;">
                    <i class="fas fa-envelope"></i>
                    info.eastafricasurveys@gmail.com
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php">🏠 Home</a></p>
                <p><a href="results.php">📊 Results</a></p>
                <p><a href="about.php">📖 About Us</a></p>
                <p><a href="contact.php">📞 Contact</a></p>
            </div>
            <div class="footer-section">
                <h3>Legal</h3>
                <p><a href="privacy.php">🔒 Privacy Policy</a></p>
                <p><a href="terms.php">📝 Terms of Service</a></p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <p><i class="fab fa-twitter"></i> @EastAfricaSurveys</p>
                <p><i class="fab fa-facebook"></i> East Africa Surveys</p>
                <p style="margin-top: 15px;">🇰🇪 🇺🇬 🇹🇿</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 East Africa Surveys. All rights reserved. | Made with <i class="fas fa-heart" style="color: #EF4444;"></i> for East Africa</p>
        </div>
    </footer>

    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Animate stats on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.stat-card-modern, .step-card, .testimonial-card').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        // Handle form submission loading state
        document.getElementById('surveyForm')?.addEventListener('submit', function() {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            button.disabled = true;
        });
    </script>
</body>
</html>