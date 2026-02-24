<?php
// results.php - Modern Results Page with Beautiful Visualizations
session_start();
require_once 'db_connect.php';

$today = date('Y-m-d');
$selected_date = $_GET['date'] ?? $today;

try {
    // Get questions and vote counts for selected date
    $sql = "SELECT 
                q.id,
                q.question_text,
                a.response_value,
                COUNT(a.id) as vote_count
            FROM questions q
            LEFT JOIN answers a ON q.id = a.question_id
            WHERE q.active_date = :selected_date
            GROUP BY q.id, q.question_text, a.response_value
            ORDER BY q.display_order, a.response_value";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':selected_date' => $selected_date]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reorganize data by question
    $questions = [];
    $total_votes = 0;
    foreach ($results as $row) {
        $qid = $row['id'];
        if (!isset($questions[$qid])) {
            $questions[$qid] = [
                'text' => $row['question_text'],
                'votes' => []
            ];
        }
        if ($row['response_value']) {
            $questions[$qid]['votes'][$row['response_value']] = (int)$row['vote_count'];
            $total_votes += (int)$row['vote_count'];
        }
    }

    // Get date range for navigation
    $stmt = $pdo->query("SELECT DISTINCT active_date FROM questions WHERE active_date <= CURRENT_DATE ORDER BY active_date DESC LIMIT 7");
    $available_dates = $stmt->fetchAll(PDO::FETCH_COLUMN);

} catch(PDOException $e) {
    error_log("Results page error: " . $e->getMessage());
    $error = "Unable to load results. Please try again later.";
}

// Check if user is logged in
$logged_in = isset($_SESSION['user_id']);
$username = $logged_in ? $_SESSION['username'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Results - East Africa Surveys | Real-Time Public Opinion</title>
    <meta name="description" content="See real-time survey results from Kenya, Uganda, and Tanzania. Watch how East Africans are voting on today's most important questions.">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* Results Page Specific Styles */
        .results-hero {
            background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
            color: white;
            padding: 60px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .results-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 50%);
            animation: rotate 30s linear infinite;
        }
        
        .results-hero-content {
            position: relative;
            z-index: 2;
        }
        
        .results-hero h1 {
            font-size: clamp(2rem, 6vw, 3rem);
            font-weight: 800;
            margin-bottom: 10px;
        }
        
        .results-hero p {
            font-size: 1.2rem;
            opacity: 0.95;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .date-navigation {
            background: white;
            padding: 20px 24px;
            border-bottom: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .date-picker {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-picker label {
            color: #64748B;
            font-weight: 500;
        }
        
        .date-picker select {
            padding: 12px 20px;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            font-size: 1rem;
            background: white;
            cursor: pointer;
            min-width: 200px;
        }
        
        .date-picker button {
            padding: 12px 24px;
            background: #2563EB;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .date-picker button:hover {
            background: #1D4ED8;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        .summary-card {
            background: white;
            border-radius: 20px;
            padding: 30px 20px;
            text-align: center;
            border: 1px solid #E2E8F0;
            transition: all 0.3s;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: #2563EB;
        }
        
        .summary-icon {
            font-size: 2.5rem;
            color: #2563EB;
            margin-bottom: 15px;
        }
        
        .summary-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: #1E293B;
            line-height: 1;
            margin-bottom: 8px;
        }
        
        .summary-label {
            color: #64748B;
            font-size: 1rem;
            font-weight: 500;
        }
        
        .questions-container {
            padding: 40px 24px;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .question-card {
            background: white;
            border-radius: 24px;
            padding: 30px;
            margin-bottom: 30px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
            transition: all 0.3s;
        }
        
        .question-card:hover {
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border-color: #2563EB;
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .question-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1E293B;
            line-height: 1.5;
            flex: 1;
        }
        
        .vote-badge {
            background: #F1F5F9;
            padding: 8px 16px;
            border-radius: 30px;
            color: #2563EB;
            font-weight: 600;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-top: 20px;
        }
        
        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }
        
        .stats-bars {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .stat-bar-item {
            width: 100%;
        }
        
        .stat-bar-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .stat-bar-label {
            font-weight: 600;
            color: #1E293B;
        }
        
        .stat-bar-value {
            color: #64748B;
        }
        
        .stat-bar-bg {
            width: 100%;
            height: 40px;
            background: #F1F5F9;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .stat-bar-fill {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            transition: width 1s ease;
        }
        
        .fill-yes { background: #10B981; }
        .fill-no { background: #EF4444; }
        .fill-maybe { background: #F59E0B; }
        
        .share-section {
            background: linear-gradient(135deg, #1E293B 0%, #0F172A 100%);
            color: white;
            padding: 60px 24px;
            text-align: center;
        }
        
        .share-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .share-description {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .share-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .share-btn {
            padding: 16px 32px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .share-whatsapp {
            background: #25D366;
            color: white;
        }
        
        .share-twitter {
            background: #1DA1F2;
            color: white;
        }
        
        .share-facebook {
            background: #4267B2;
            color: white;
        }
        
        .share-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 24px;
            background: #F8FAFC;
        }
        
        .empty-icon {
            font-size: 5rem;
            color: #94A3B8;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 2rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 10px;
        }
        
        .empty-text {
            color: #64748B;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
        
        .trending-tags {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }
        
        .trending-tag {
            background: #F1F5F9;
            padding: 8px 16px;
            border-radius: 30px;
            color: #2563EB;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .trending-tag:hover {
            background: #2563EB;
            color: white;
            cursor: pointer;
        }
        
        .floating-stats {
            position: fixed;
            bottom: 100px;
            right: 24px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid #E2E8F0;
            text-align: center;
            min-width: 150px;
            z-index: 100;
        }
        
        .floating-stats-number {
            font-size: 2rem;
            font-weight: 800;
            color: #2563EB;
        }
        
        .floating-stats-label {
            color: #64748B;
            font-size: 0.9rem;
        }
        
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .question-card {
            animation: slideIn 0.5s ease forwards;
        }
        
        @media (max-width: 768px) {
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .chart-container {
                height: 200px;
            }
            
            .date-navigation {
                flex-direction: column;
                align-items: stretch;
            }
            
            .date-picker {
                flex-direction: column;
            }
            
            .date-picker select {
                width: 100%;
            }
            
            .floating-stats {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Sticky Header -->
    <header>
        <div class="header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p class="tagline">Real-Time Public Opinion from Kenya, Uganda & Tanzania</p>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <a href="results.php" class="active"><i class="fas fa-chart-bar"></i> Results</a>
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
    <section class="results-hero">
        <div class="results-hero-content">
            <h1>📊 Live Results</h1>
            <p>See what East Africa is thinking in real-time</p>
        </div>
    </section>

    <!-- Date Navigation -->
    <div class="date-navigation">
        <div class="date-picker">
            <label for="date"><i class="fas fa-calendar-alt"></i> Select Date:</label>
            <select id="date" name="date" onchange="window.location.href='?date=' + this.value">
                <?php foreach ($available_dates as $date): ?>
                    <option value="<?php echo $date; ?>" <?php echo $selected_date == $date ? 'selected' : ''; ?>>
                        <?php echo date('F j, Y', strtotime($date)); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <span style="color: #64748B;"><i class="fas fa-sync-alt"></i> Updated in real-time</span>
        </div>
    </div>

    <?php if (isset($_GET['voted'])): ?>
        <div style="background: #10B981; color: white; padding: 16px; text-align: center;">
            <i class="fas fa-check-circle"></i> Thank you for voting! Your voice has been counted.
        </div>
    <?php endif; ?>

    <!-- Summary Stats -->
    <?php if (!empty($questions)): ?>
    <div class="summary-stats">
        <div class="summary-card">
            <i class="fas fa-poll summary-icon"></i>
            <div class="summary-number"><?php echo count($questions); ?></div>
            <div class="summary-label">Questions Today</div>
        </div>
        <div class="summary-card">
            <i class="fas fa-users summary-icon"></i>
            <div class="summary-number"><?php echo number_format($total_votes); ?></div>
            <div class="summary-label">Total Votes</div>
        </div>
        <div class="summary-card">
            <i class="fas fa-globe-africa summary-icon"></i>
            <div class="summary-number">3</div>
            <div class="summary-label">Countries</div>
        </div>
    </div>

    <!-- Questions Results -->
    <div class="questions-container">
        <?php foreach ($questions as $qid => $question): 
            $total = array_sum($question['votes']);
            $yes = isset($question['votes']['Yes']) ? $question['votes']['Yes'] : 0;
            $no = isset($question['votes']['No']) ? $question['votes']['No'] : 0;
            $maybe = isset($question['votes']['Maybe']) ? $question['votes']['Maybe'] : 0;
            
            $yes_percent = $total > 0 ? round(($yes / $total) * 100) : 0;
            $no_percent = $total > 0 ? round(($no / $total) * 100) : 0;
            $maybe_percent = $total > 0 ? round(($maybe / $total) * 100) : 0;
        ?>
            <div class="question-card">
                <div class="question-header">
                    <div class="question-title">
                        <?php echo htmlspecialchars($question['text']); ?>
                    </div>
                    <div class="vote-badge">
                        <i class="fas fa-users"></i>
                        <?php echo $total; ?> votes
                    </div>
                </div>

                <div class="results-grid">
                    <!-- Chart Container -->
                    <div class="chart-container">
                        <canvas id="chart-<?php echo $qid; ?>"></canvas>
                    </div>

                    <!-- Stats Bars -->
                    <div class="stats-bars">
                        <div class="stat-bar-item">
                            <div class="stat-bar-header">
                                <span class="stat-bar-label">👍 Yes</span>
                                <span class="stat-bar-value"><?php echo $yes; ?> (<?php echo $yes_percent; ?>%)</span>
                            </div>
                            <div class="stat-bar-bg">
                                <div class="stat-bar-fill fill-yes" style="width: <?php echo $yes_percent; ?>%;">
                                    <?php echo $yes_percent; ?>%
                                </div>
                            </div>
                        </div>

                        <div class="stat-bar-item">
                            <div class="stat-bar-header">
                                <span class="stat-bar-label">👎 No</span>
                                <span class="stat-bar-value"><?php echo $no; ?> (<?php echo $no_percent; ?>%)</span>
                            </div>
                            <div class="stat-bar-bg">
                                <div class="stat-bar-fill fill-no" style="width: <?php echo $no_percent; ?>%;">
                                    <?php echo $no_percent; ?>%
                                </div>
                            </div>
                        </div>

                        <div class="stat-bar-item">
                            <div class="stat-bar-header">
                                <span class="stat-bar-label">🤔 Not Sure</span>
                                <span class="stat-bar-value"><?php echo $maybe; ?> (<?php echo $maybe_percent; ?>%)</span>
                            </div>
                            <div class="stat-bar-bg">
                                <div class="stat-bar-fill fill-maybe" style="width: <?php echo $maybe_percent; ?>%;">
                                    <?php echo $maybe_percent; ?>%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                (function() {
                    const ctx = document.getElementById('chart-<?php echo $qid; ?>').getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Yes', 'No', 'Not Sure'],
                            datasets: [{
                                data: [<?php echo $yes; ?>, <?php echo $no; ?>, <?php echo $maybe; ?>],
                                backgroundColor: ['#10B981', '#EF4444', '#F59E0B'],
                                borderWidth: 0,
                                hoverOffset: 10
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { size: 12, family: 'Inter' } }
                                }
                            }
                        }
                    });
                })();
            </script>
        <?php endforeach; ?>
    </div>

    <!-- Trending Tags -->
    <div style="padding: 20px 24px; text-align: center;">
        <div class="trending-tags">
            <span class="trending-tag">#KenyaDecides</span>
            <span class="trending-tag">#UgandaOpinion</span>
            <span class="trending-tag">#TanzaniaPolls</span>
            <span class="trending-tag">#EastAfricaVoice</span>
            <span class="trending-tag">#DailySurvey</span>
        </div>
    </div>

    <!-- Share Section -->
    <section class="share-section">
        <h2 class="share-title">Share These Results</h2>
        <p class="share-description">Help us reach more East Africans. Share today's poll results!</p>
        <div class="share-buttons">
            <a href="https://wa.me/?text=Check%20out%20today's%20East%20Africa%20Survey%20results!%20https://east-africa-surveys.onrender.com/results.php" 
               target="_blank" class="share-btn share-whatsapp">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
            <a href="https://twitter.com/intent/tweet?text=See%20what%20East%20Africa%20thinks!%20Daily%20survey%20results%20from%20Kenya,%20Uganda%20%26%20Tanzania&url=https://east-africa-surveys.onrender.com" 
               target="_blank" class="share-btn share-twitter">
                <i class="fab fa-twitter"></i> Twitter
            </a>
            <a href="https://www.facebook.com/sharer/sharer.php?u=https://east-africa-surveys.onrender.com" 
               target="_blank" class="share-btn share-facebook">
                <i class="fab fa-facebook"></i> Facebook
            </a>
        </div>
    </section>

    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-chart-pie empty-icon"></i>
            <h2 class="empty-title">No Results Yet</h2>
            <p class="empty-text">Be the first to vote in today's survey and see what East Africa thinks!</p>
            <a href="index.php" class="hero-button hero-button-primary" style="display: inline-block;">
                <i class="fas fa-poll"></i> Take Today's Survey
            </a>
        </div>
    <?php endif; ?>

    <!-- Floating Stats (Visible on Desktop) -->
    <?php if (!empty($questions)): ?>
    <div class="floating-stats">
        <div class="floating-stats-number"><?php echo number_format($total_votes); ?></div>
        <div class="floating-stats-label">Total Votes Today</div>
        <div style="font-size: 0.8rem; color: #94A3B8; margin-top: 5px;">
            <i class="fas fa-sync-alt fa-spin"></i> Live
        </div>
    </div>
    <?php endif; ?>

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

        document.querySelectorAll('.question-card, .summary-card').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        // Auto-refresh data every 30 seconds (optional)
        setTimeout(() => {
            window.location.reload();
        }, 30000); // Refresh every 30 seconds to show new votes
    </script>
</body>
</html>