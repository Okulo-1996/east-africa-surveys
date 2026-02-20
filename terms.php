<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();
require_once('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms of Service - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Additional styling for legal pages */
        .legal-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            margin: 40px 0;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .legal-container h1 {
            color: var(--primary);
            font-size: 2.5em;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .legal-container h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
        }
        
        .legal-container h2 {
            color: var(--primary);
            font-size: 1.5em;
            margin: 30px 0 15px;
        }
        
        .legal-container h3 {
            color: var(--secondary);
            font-size: 1.2em;
            margin: 20px 0 10px;
        }
        
        .legal-container p {
            margin-bottom: 15px;
            line-height: 1.8;
            color: #444;
        }
        
        .legal-container ul, .legal-container ol {
            margin: 15px 0 15px 30px;
            line-height: 1.8;
        }
        
        .legal-container li {
            margin-bottom: 10px;
            color: #444;
        }
        
        .legal-container .highlight {
            background: #f0f9f0;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid var(--accent);
            margin: 20px 0;
        }
        
        .legal-container .date-badge {
            background: #e9ecef;
            padding: 8px 15px;
            border-radius: 30px;
            display: inline-block;
            font-size: 0.9em;
            margin-bottom: 20px;
        }
        
        .legal-container table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .legal-container th {
            background: var(--primary);
            color: white;
            padding: 12px;
            text-align: left;
        }
        
        .legal-container td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .legal-container tr:hover {
            background: #f5f5f5;
        }
        
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--secondary);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.3);
            transition: all 0.3s;
            opacity: 0.7;
        }
        
        .back-to-top:hover {
            opacity: 1;
            transform: translateY(-3px);
        }
    </style>
</head>
<body>
    <!-- Professional Header -->
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

    <!-- Navigation -->
    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="terms.php" class="active"><i class="fas fa-file-contract"></i> Terms</a>
        </nav>

        <main>
            <div class="legal-container">
                <!-- Last Updated Badge -->
                <div class="date-badge">
                    <i class="fas fa-calendar-alt"></i> Last Updated: February 20, 2026
                </div>

                <h1>Terms of Service</h1>
                
                <!-- Table of Contents -->
                <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 30px 0;">
                    <h3 style="margin-top: 0;">Quick Navigation</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px;">
                        <a href="#acceptance" style="color: var(--primary); text-decoration: none;">1. Acceptance</a>
                        <a href="#description" style="color: var(--primary); text-decoration: none;">2. Service Description</a>
                        <a href="#eligibility" style="color: var(--primary); text-decoration: none;">3. Eligibility</a>
                        <a href="#conduct" style="color: var(--primary); text-decoration: none;">4. User Conduct</a>
                        <a href="#privacy" style="color: var(--primary); text-decoration: none;">5. Privacy</a>
                        <a href="#intellectual" style="color: var(--primary); text-decoration: none;">6. Intellectual Property</a>
                        <a href="#disclaimer" style="color: var(--primary); text-decoration: none;">7. Disclaimer</a>
                        <a href="#limitation" style="color: var(--primary); text-decoration: none;">8. Limitation of Liability</a>
                        <a href="#termination" style="color: var(--primary); text-decoration: none;">9. Termination</a>
                        <a href="#changes" style="color: var(--primary); text-decoration: none;">10. Changes to Terms</a>
                        <a href="#contact" style="color: var(--primary); text-decoration: none;">11. Contact Us</a>
                    </div>
                </div>

                <!-- Section 1: Acceptance -->
                <h2 id="acceptance">1. Acceptance of Terms</h2>
                <p>Welcome to East Africa Surveys ("we," "our," "us"). By accessing or using our website at <strong>east-africa-surveys.onrender.com</strong> (the "Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use our Service.</p>
                <div class="highlight">
                    <p><i class="fas fa-gavel"></i> <strong>Important:</strong> By using our Service, you acknowledge that you have read, understood, and agree to be bound by these Terms.</p>
                </div>

                <!-- Section 2: Service Description -->
                <h2 id="description">2. Description of Service</h2>
                <p>East Africa Surveys provides a platform for daily public opinion polls and surveys focused on Kenya, Uganda, and Tanzania. Our Service allows users to:</p>
                <ul>
                    <li>Answer 5 daily survey questions</li>
                    <li>View aggregated, anonymous results</li>
                    <li>Participate in understanding public opinion across East Africa</li>
                </ul>
                <p><strong>Our service is:</strong></p>
                <ul>
                    <li><i class="fas fa-check-circle" style="color: #27AE60;"></i> 100% Free to use</li>
                    <li><i class="fas fa-check-circle" style="color: #27AE60;"></i> Anonymous (no personal information collected)</li>
                    <li><i class="fas fa-check-circle" style="color: #27AE60;"></i> Updated daily with new questions</li>
                </ul>

                <!-- Section 3: Eligibility -->
                <h2 id="eligibility">3. Eligibility</h2>
                <p>By using our Service, you represent and warrant that:</p>
                <ul>
                    <li>You are at least 13 years of age (or the age of digital consent in your country)</li>
                    <li>You have the capacity to enter into a binding agreement</li>
                    <li>You are not located in a country that is subject to trade sanctions</li>
                    <li>You will use the Service in compliance with all applicable laws</li>
                </ul>
                <p>If you are under 13, you may only use the Service with parental consent.</p>

                <!-- Section 4: User Conduct -->
                <h2 id="conduct">4. User Conduct</h2>
                <p>You agree not to:</p>
                <ul>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Attempt to vote multiple times in a single day</li>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Use bots, scripts, or automated methods to submit votes</li>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Manipulate survey results through fraudulent means</li>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Attempt to hack, damage, or disrupt the Service</li>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Use the Service for any illegal purpose</li>
                    <li><i class="fas fa-times-circle" style="color: #E74C3C;"></i> Harass, abuse, or harm other users</li>
                </ul>
                <p>We reserve the right to block any IP address found violating these conduct rules.</p>

                <!-- Section 5: Privacy -->
                <h2 id="privacy">5. Privacy and Data Collection</h2>
                <p>Your privacy is important to us. Our practices are detailed in our <a href="privacy.php" style="color: var(--secondary);">Privacy Policy</a>. By using our Service, you consent to:</p>
                <ul>
                    <li>Collection of your survey responses (anonymously)</li>
                    <li>Use of cookies to prevent duplicate voting</li>
                    <li>Aggregation of data for public results</li>
                </ul>
                <p><strong>We do NOT collect:</strong> names, email addresses, phone numbers, or any personally identifiable information.</p>

                <!-- Section 6: Intellectual Property -->
                <h2 id="intellectual">6. Intellectual Property</h2>
                <p>All content on East Africa Surveys, including but not limited to:</p>
                <ul>
                    <li>Survey questions and results</li>
                    <li>Website design, layout, and code</li>
                    <li>Graphics, logos, and trademarks</li>
                    <li>Written content and documentation</li>
                </ul>
                <p>is the property of East Africa Surveys and is protected by copyright, trademark, and other intellectual property laws.</p>
                <p>You may not reproduce, distribute, or create derivative works without our express written permission.</p>

                <!-- Section 7: Disclaimer -->
                <h2 id="disclaimer">7. Disclaimer of Warranties</h2>
                <p>THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED. TO THE FULLEST EXTENT PERMITTED BY LAW, WE DISCLAIM ALL WARRANTIES, INCLUDING:</p>
                <ul>
                    <li>Implied warranties of merchantability and fitness for a particular purpose</li>
                    <li>Warranties of accuracy, reliability, or completeness of results</li>
                    <li>Warranties that the Service will be uninterrupted, secure, or error-free</li>
                </ul>
                <p>Survey results reflect the opinions of participants and may not represent the views of the general population.</p>

                <!-- Section 8: Limitation of Liability -->
                <h2 id="limitation">8. Limitation of Liability</h2>
                <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, EAST AFRICA SURVEYS SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING:</p>
                <ul>
                    <li>Loss of profits, data, or goodwill</li>
                    <li>Service interruptions or errors</li>
                    <li>Any actions taken based on survey results</li>
                </ul>
                <p>Our total liability shall not exceed the amount you paid us (which is zero, as our service is free).</p>

                <!-- Section 9: Termination -->
                <h2 id="termination">9. Termination</h2>
                <p>We reserve the right to:</p>
                <ul>
                    <li>Suspend or terminate access to users who violate these Terms</li>
                    <li>Modify or discontinue the Service at any time without notice</li>
                    <li>Block IP addresses engaged in fraudulent voting</li>
                </ul>
                <p>Upon termination, your right to use the Service will immediately cease.</p>

                <!-- Section 10: Changes to Terms -->
                <h2 id="changes">10. Changes to Terms</h2>
                <p>We may update these Terms from time to time. When we do, we will:</p>
                <ul>
                    <li>Update the "Last Updated" date at the top</li>
                    <li>Post the new Terms on this page</li>
                    <li>For significant changes, provide notice on our homepage</li>
                </ul>
                <p>Your continued use of the Service after changes constitutes acceptance of the new Terms.</p>

                <!-- Section 11: Governing Law -->
                <h2 id="governing">11. Governing Law</h2>
                <p>These Terms shall be governed by the laws of the Republic of Kenya, without regard to its conflict of law provisions. Any disputes arising under these Terms shall be resolved in the courts of Nairobi, Kenya.</p>

                <!-- Section 12: Contact Information -->
                <h2 id="contact">12. Contact Us</h2>
                <p>If you have any questions about these Terms, please contact us:</p>
                <div style="background: #f8f9fa; padding: 30px; border-radius: 15px; margin: 20px 0;">
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
                        <i class="fas fa-envelope" style="font-size: 2em; color: var(--secondary);"></i>
                        <div>
                            <strong>Email:</strong><br>
                            <a href="mailto:info.eastafricasurveys@gmail.com" style="color: var(--primary); text-decoration: none;">info.eastafricasurveys@gmail.com</a>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 15px;">
                        <i class="fas fa-globe" style="font-size: 2em; color: var(--secondary);"></i>
                        <div>
                            <strong>Website:</strong><br>
                            <a href="https://east-africa-surveys.onrender.com" style="color: var(--primary); text-decoration: none;">east-africa-surveys.onrender.com</a>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        <i class="fas fa-map-marker-alt" style="font-size: 2em; color: var(--secondary);"></i>
                        <div>
                            <strong>Region:</strong><br>
                            Kenya | Uganda | Tanzania
                        </div>
                    </div>
                </div>

                <!-- Acknowledgment -->
                <div style="margin-top: 40px; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%); border-radius: 10px; text-align: center;">
                    <i class="fas fa-check-circle" style="font-size: 3em; color: var(--accent); margin-bottom: 15px;"></i>
                    <h3 style="color: var(--primary);">By using East Africa Surveys, you acknowledge that you have read and understood these Terms of Service.</h3>
                    <div style="margin-top: 20px; display: flex; gap: 20px; justify-content: center;">
                        <span style="background: white; padding: 10px 20px; border-radius: 30px;">🇰🇪 Kenya</span>
                        <span style="background: white; padding: 10px 20px; border-radius: 30px;">🇺🇬 Uganda</span>
                        <span style="background: white; padding: 10px 20px; border-radius: 30px;">🇹🇿 Tanzania</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Professional Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3><i class="fas fa-globe-africa"></i> East Africa Surveys</h3>
                    <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys. Your opinion matters.</p>
                    <div style="margin-top: 20px;">
                        <i class="fas fa-envelope" style="color: var(--secondary);"></i>
                        info.eastafricasurveys@gmail.com
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <p><a href="index.php"><i class="fas fa-chevron-right"></i> Today's Survey</a></p>
                    <p><a href="results.php"><i class="fas fa-chevron-right"></i> View Results</a></p>
                    <p><a href="about.php"><i class="fas fa-chevron-right"></i> About Us</a></p>
                    <p><a href="privacy.php"><i class="fas fa-chevron-right"></i> Privacy Policy</a></p>
                    <p><a href="terms.php"><i class="fas fa-chevron-right"></i> Terms of Service</a></p>
                </div>
                <div class="footer-section">
                    <h3>Our Region</h3>
                    <p><span style="font-size: 1.5em;">🇰🇪</span> Kenya</p>
                    <p><span style="font-size: 1.5em;">🇺🇬</span> Uganda</p>
                    <p><span style="font-size: 1.5em;">🇹🇿</span> Tanzania</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys. All rights reserved. | Free for all East Africans</p>
                <p style="margin-top: 10px; font-size: 0.9em;">Made with <i class="fas fa-heart" style="color: #E74C3C;"></i> for East Africa</p>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" title="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </a>

    <!-- Smooth scroll for back to top -->
    <script>
        document.querySelector('.back-to-top').addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>