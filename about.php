<?php
// about.php - Modern About Page with Storytelling
session_start();

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>About Us - East Africa Surveys | Our Mission & Story</title>
    <meta name="description" content="Learn about East Africa Surveys - our mission to give a voice to Kenya, Uganda, and Tanzania through daily public opinion polls.">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* About Page Specific Styles */
        .about-hero {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 80px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .about-hero::before {
            content: '🇰🇪 🇺🇬 🇹🇿';
            position: absolute;
            bottom: 20px;
            right: 30px;
            font-size: 80px;
            opacity: 0.1;
            white-space: nowrap;
        }
        
        .about-hero h1 {
            font-size: clamp(2.5rem, 8vw, 4rem);
            font-weight: 800;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }
        
        .about-hero h1 span {
            color: #2563EB;
        }
        
        .about-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .mission-section {
            padding: 60px 24px;
            background: white;
        }
        
        .mission-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            max-width: 1000px;
            margin: 0 auto;
            align-items: center;
        }
        
        .mission-content h2 {
            font-size: 2.2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 20px;
        }
        
        .mission-content h2 span {
            color: #2563EB;
        }
        
        .mission-content p {
            color: #64748B;
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        
        .mission-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }
        
        .mission-stat {
            text-align: center;
            padding: 20px;
            background: #F8FAFC;
            border-radius: 20px;
            border: 1px solid #E2E8F0;
        }
        
        .mission-stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #2563EB;
            margin-bottom: 5px;
        }
        
        .mission-stat-label {
            color: #64748B;
            font-size: 0.9rem;
        }
        
        .mission-image {
            position: relative;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 30px 60px rgba(0,0,0,0.1);
        }
        
        .mission-image img {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s;
        }
        
        .mission-image:hover img {
            transform: scale(1.05);
        }
        
        .values-section {
            background: #F8FAFC;
            padding: 80px 24px;
        }
        
        .section-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 50px;
        }
        
        .values-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .value-card {
            background: white;
            border-radius: 30px;
            padding: 40px 30px;
            text-align: center;
            border: 1px solid #E2E8F0;
            transition: all 0.3s;
        }
        
        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(37,99,235,0.1);
            border-color: #2563EB;
        }
        
        .value-icon {
            width: 80px;
            height: 80px;
            background: rgba(37,99,235,0.1);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 2.5rem;
            color: #2563EB;
            transition: all 0.3s;
        }
        
        .value-card:hover .value-icon {
            background: #2563EB;
            color: white;
            transform: rotate(360deg);
        }
        
        .value-card h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 15px;
        }
        
        .value-card p {
            color: #64748B;
            line-height: 1.7;
        }
        
        .team-section {
            padding: 80px 24px;
            background: white;
        }
        
        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 1000px;
            margin: 40px auto 0;
        }
        
        .team-card {
            text-align: center;
            padding: 30px 20px;
            border-radius: 30px;
            border: 1px solid #E2E8F0;
            transition: all 0.3s;
        }
        
        .team-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: #2563EB;
        }
        
        .team-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #2563EB, #1E40AF);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            font-weight: 600;
        }
        
        .team-card h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 5px;
        }
        
        .team-title {
            color: #2563EB;
            font-weight: 500;
            margin-bottom: 15px;
        }
        
        .team-bio {
            color: #64748B;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .team-social {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        
        .team-social a {
            color: #94A3B8;
            font-size: 1.2rem;
            transition: all 0.2s;
        }
        
        .team-social a:hover {
            color: #2563EB;
            transform: translateY(-2px);
        }
        
        .timeline-section {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 80px 24px;
        }
        
        .timeline-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 50px;
        }
        
        .timeline {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background: rgba(255,255,255,0.2);
        }
        
        .timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            position: relative;
        }
        
        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .timeline-content {
            width: 45%;
            padding: 30px;
            background: rgba(255,255,255,0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        
        .timeline-date {
            display: inline-block;
            padding: 8px 16px;
            background: #2563EB;
            border-radius: 30px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .timeline-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }
        
        .timeline-content p {
            opacity: 0.9;
            line-height: 1.6;
        }
        
        .timeline-dot {
            width: 20px;
            height: 20px;
            background: #2563EB;
            border: 4px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .cta-section {
            padding: 80px 24px;
            text-align: center;
            background: white;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 20px;
        }
        
        .cta-text {
            color: #64748B;
            font-size: 1.2rem;
            max-width: 600px;
            margin: 0 auto 30px;
        }
        
        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .cta-button {
            padding: 18px 40px;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .cta-button-primary {
            background: #2563EB;
            color: white;
            box-shadow: 0 10px 20px rgba(37,99,235,0.3);
        }
        
        .cta-button-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(37,99,235,0.4);
        }
        
        .cta-button-secondary {
            background: #F1F5F9;
            color: #1E293B;
        }
        
        .cta-button-secondary:hover {
            background: #E2E8F0;
            transform: translateY(-2px);
        }
        
        .impact-numbers {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        .impact-card {
            text-align: center;
        }
        
        .impact-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #2563EB;
            margin-bottom: 5px;
        }
        
        .impact-label {
            color: #64748B;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .mission-grid {
                grid-template-columns: 1fr;
            }
            
            .values-grid {
                grid-template-columns: 1fr;
            }
            
            .team-grid {
                grid-template-columns: 1fr;
            }
            
            .timeline::before {
                left: 30px;
            }
            
            .timeline-item {
                flex-direction: column !important;
                align-items: flex-start;
                margin-left: 60px;
            }
            
            .timeline-content {
                width: 100%;
            }
            
            .timeline-dot {
                left: 30px;
            }
            
            .impact-numbers {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <!-- Sticky Header -->
    <header>
        <div class="header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p class="tagline">Your Voice Matters Across Kenya, Uganda & Tanzania</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
        <a href="about.php" class="active"><i class="fas fa-info-circle"></i> About</a>
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
    <section class="about-hero">
        <h1>Our <span>Story</span></h1>
        <p>Building the largest public opinion platform in East Africa</p>
    </section>

    <!-- Mission Section -->
    <section class="mission-section">
        <div class="mission-grid">
            <div class="mission-content">
                <h2>Our <span>Mission</span></h2>
                <p>East Africa Surveys was born from a simple idea: every East African deserves to have their voice heard. We're building a platform where opinions from Kenya, Uganda, and Tanzania can be collected, analyzed, and shared in real-time.</p>
                <p>Whether it's about politics, economy, social issues, or daily life, we believe that understanding what people really think is the first step toward positive change.</p>
                <div class="mission-stats">
                    <div class="mission-stat">
                        <div class="mission-stat-number">10K+</div>
                        <div class="mission-stat-label">Monthly Votes</div>
                    </div>
                    <div class="mission-stat">
                        <div class="mission-stat-number">3</div>
                        <div class="mission-stat-label">Countries</div>
                    </div>
                    <div class="mission-stat">
                        <div class="mission-stat-number">100%</div>
                        <div class="mission-stat-label">Anonymous</div>
                    </div>
                    <div class="mission-stat">
                        <div class="mission-stat-number">24/7</div>
                        <div class="mission-stat-label">Live Results</div>
                    </div>
                </div>
            </div>
            <div class="mission-image">
                <img src="https://images.unsplash.com/photo-1523805009345-7448845a9e53?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" alt="East Africa Map" style="width: 100%; height: auto;">
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="values-section">
        <h2 class="section-title">Our Core Values</h2>
        <div class="values-grid">
            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Inclusivity</h3>
                <p>Every voice matters, regardless of background, location, or status. We're building a platform for all East Africans.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Privacy</h3>
                <p>Your opinions are anonymous. We never share personal data or track individual responses.</p>
            </div>
            <div class="value-card">
                <div class="value-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3>Transparency</h3>
                <p>All results are public and verifiable. See exactly what East Africa thinks in real-time.</p>
            </div>
        </div>
    </section>

    <!-- Impact Numbers -->
    <div class="impact-numbers">
        <div class="impact-card">
            <div class="impact-number">50K+</div>
            <div class="impact-label">Surveys Completed</div>
        </div>
        <div class="impact-card">
            <div class="impact-number">15K+</div>
            <div class="impact-label">Registered Users</div>
        </div>
        <div class="impact-card">
            <div class="impact-number">100+</div>
            <div class="impact-label">Daily Questions</div>
        </div>
        <div class="impact-card">
            <div class="impact-number">3</div>
            <div class="impact-label">Countries</div>
        </div>
    </div>

    <!-- Timeline Section -->
    <section class="timeline-section">
        <h2 class="timeline-title">Our Journey</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">January 2025</span>
                    <h3>The Idea</h3>
                    <p>East Africa Surveys was conceived as a way to give every East African a voice in public opinion.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">March 2025</span>
                    <h3>First Prototype</h3>
                    <p>Built the first version with 5 daily questions and basic results visualization.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">June 2025</span>
                    <h3>Launch in Kenya</h3>
                    <p>Launched in Kenya with 100+ daily users within the first week.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">September 2025</span>
                    <h3>Expansion to Uganda</h3>
                    <p>Added Uganda to our coverage, reaching thousands of new voices.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">January 2026</span>
                    <h3>Tanzania Joins</h3>
                    <p>Completed East African coverage with the addition of Tanzania.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
            
            <div class="timeline-item">
                <div class="timeline-content">
                    <span class="timeline-date">Today</span>
                    <h3>Growing Strong</h3>
                    <p>Thousands of East Africans share their opinions daily. This is just the beginning.</p>
                </div>
                <div class="timeline-dot"></div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team-section">
        <h2 class="section-title">Meet the Team</h2>
        <div class="team-grid">
            <div class="team-card">
                <div class="team-avatar">JM</div>
                <h3>James Mwangi</h3>
                <div class="team-title">Founder & CEO</div>
                <p class="team-bio">Passionate about giving East Africans a voice. Previously led data teams at Safaricom.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">AN</div>
                <h3>Aisha Nakato</h3>
                <div class="team-title">Head of Research</div>
                <p class="team-bio">Data scientist focused on understanding East African public opinion trends.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            
            <div class="team-card">
                <div class="team-avatar">KM</div>
                <h3>Khadija Mussa</h3>
                <div class="team-title">Community Lead</div>
                <p class="team-bio">Building connections across Kenya, Uganda, and Tanzania. Your voice matters.</p>
                <div class="team-social">
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <h2 class="cta-title">Be Part of the Story</h2>
        <p class="cta-text">Join thousands of East Africans sharing their opinions daily. Your voice matters.</p>
        <div class="cta-buttons">
            <a href="register.php" class="cta-button cta-button-primary">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
            <a href="index.php" class="cta-button cta-button-secondary">
                <i class="fas fa-poll"></i> Take Today's Survey
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Your voice matters across Kenya, Uganda & Tanzania. Daily polls, real-time results.</p>
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
                <p style="margin-top: 15px; font-size: 1.5rem;">🇰🇪 🇺🇬 🇹🇿</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 East Africa Surveys. All rights reserved. | Made with <i class="fas fa-heart" style="color: #EF4444;"></i> for East Africa</p>
        </div>
    </footer>

    <script>
        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        });

        document.querySelectorAll('.value-card, .team-card, .timeline-item').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>