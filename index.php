<?php
require_once 'db_connect.php';

// Get today's questions
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM questions WHERE active_date = ? ORDER BY display_order");
$stmt->execute([$today]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if user already voted today (optional - uses simple cookie)
$voted_today = isset($_COOKIE['voted_' . $today]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>East Africa Surveys - Your Voice Matters</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Free icons from Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for results -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="index.php" class="active">🏠 Home</a>
            <a href="results.php">📊 Results</a>
            <a href="about.php">📖 About Us</a>
            <a href="contact.php">📞 Contact</a>
        </nav>

        <main>
            <div style="text-align: center; margin-bottom: 30px;">
                <h2>🗓️ Today's Polls - <?php echo date('l, F j, Y'); ?></h2>
                <p>Answer 5 quick questions and see what East Africa thinks!</p>
            </div>

            <?php if ($voted_today): ?>
                <div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 30px;">
                    <i class="fas fa-check-circle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h3>Thank you for voting today!</h3>
                    <p>You've already shared your opinions today. Come back tomorrow for new questions!</p>
                    <a href="results.php" class="btn btn-small" style="margin-top: 15px;">View Today's Results</a>
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
                        <button type="submit" class="btn">
                            <i class="fas fa-paper-plane"></i> Submit My Answers
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div style="background: #fff3cd; color: #856404; padding: 40px; border-radius: 10px; text-align: center;">
                    <i class="fas fa-clock" style="font-size: 4em; margin-bottom: 20px;"></i>
                    <h3>No Polls Available Today</h3>
                    <p>Our team is preparing tomorrow's questions. Check back tomorrow!</p>
                </div>
            <?php endif; ?>

            <!-- Stats Preview -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 50px 0;">
                <div class="stat-card">
                    <div class="stat-number">1,234+</div>
                    <div>Daily Voters</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3</div>
                    <div>East African Countries</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <div>Free & Anonymous</div>
                </div>
            </div>
        </main>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Giving a voice to Kenya, Uganda, and Tanzania through daily polls and surveys.</p>
                <div style="margin-top: 15px;">
                    <i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="about.php" style="color: white; text-decoration: none;">📖 About Us</a></p>
                <p><a href="privacy.php" style="color: white; text-decoration: none;">🔒 Privacy Policy</a></p>
                <p><a href="contact.php" style="color: white; text-decoration: none;">📞 Contact</a></p>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <p>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</p>
                <p>Coming soon to social media!</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 East Africa Surveys. All rights reserved. | Free for all East Africans</p>
        </div>
    </footer>
</body>
</html>
