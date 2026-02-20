<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session for messages
session_start();
require_once('db_connect.php');

// Get today's date or selected date
$today = date('Y-m-d');
$selected_date = isset($_GET['date']) ? $_GET['date'] : $today;

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
        }
    }

    // Calculate total votes
    $total_votes = 0;
    foreach ($questions as $question) {
        $total_votes += array_sum($question['votes']);
    }

} catch(PDOException $e) {
    // Log error but don't show to users
    error_log("Results page error: " . $e->getMessage());
    $error = "Unable to load results. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="results.php" class="active"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="about.php"><i class="fas fa-info-circle"></i> About Us</a>
            <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            <a href="terms.php"><i class="fas fa-file-contract"></i> Terms</a>
        </nav>

        <main>
            <!-- Success Message (if just voted) -->
            <?php if (isset($_GET['voted'])): ?>
                <div style="background: #d4edda; color: #155724; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: center; animation: fadeIn 0.5s;">
                    <i class="fas fa-check-circle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h2>Thank You for Voting! 🇰🇪 🇺🇬 🇹🇿</h2>
                    <p>Your voice has been counted. Here's what East Africa thinks today:</p>
                </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (isset($error)): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h3><?php echo $error; ?></h3>
                </div>
            <?php endif; ?>

            <!-- Date Selector -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <div>
                    <h2 style="color: var(--primary); margin: 0;">
                        <i class="fas fa-calendar-alt"></i> 
                        <?php echo date('F j, Y', strtotime($selected_date)); ?>
                    </h2>
                </div>
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="date" name="date" value="<?php echo $selected_date; ?>" 
                           style="padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-family: inherit;">
                    <button type="submit" class="btn" style="padding: 10px 20px; font-size: 1em;">
                        <i class="fas fa-search"></i> View
                    </button>
                </form>
            </div>

            <!-- Stats Summary Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($questions); ?></div>
                    <div class="stat-label">Questions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_votes; ?></div>
                    <div class="stat-label">Total Votes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">3</div>
                    <div class="stat-label">Countries</div>
                </div>
            </div>

            <?php if (empty($questions)): ?>
                <!-- No Results Message -->
                <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.05);">
                    <i class="fas fa-chart-pie" style="font-size: 5em; color: #ccc; margin-bottom: 20px;"></i>
                    <h2 style="color: var(--primary); margin-bottom: 15px;">No Results Available</h2>
                    <p style="color: #666; margin-bottom: 30px; font-size: 1.2em;">
                        There are no votes for this date yet.
                    </p>
                    <?php if ($selected_date == $today): ?>
                        <a href="index.php" class="btn">
                            <i class="fas fa-pencil-alt"></i> Be the First to Vote
                        </a>
                    <?php else: ?>
                        <a href="results.php" class="btn btn-outline">
                            <i class="fas fa-calendar-day"></i> View Today's Results
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- Results for Each Question -->
                <?php foreach ($questions as $qid => $question): 
                    $total = array_sum($question['votes']);
                    $yes = isset($question['votes']['Yes']) ? $question['votes']['Yes'] : 0;
                    $no = isset($question['votes']['No']) ? $question['votes']['No'] : 0;
                    $maybe = isset($question['votes']['Maybe']) ? $question['votes']['Maybe'] : 0;
                    
                    $yes_percent = $total > 0 ? round(($yes / $total) * 100) : 0;
                    $no_percent = $total > 0 ? round(($no / $total) * 100) : 0;
                    $maybe_percent = $total > 0 ? round(($maybe / $total) * 100) : 0;
                ?>
                    <div class="chart-container">
                        <h3 class="chart-title">
                            <?php echo htmlspecialchars($question['text']); ?>
                            <span style="float: right; font-size: 0.8em; color: #666;">
                                <?php echo $total; ?> votes
                            </span>
                        </h3>
                        
                        <!-- Visual Results -->
                        <div style="margin: 25px 0;">
                            <!-- Yes Bar -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: 600; color: #27AE60;">
                                        <i class="fas fa-check-circle"></i> Yes
                                    </span>
                                    <span><?php echo $yes; ?> votes (<?php echo $yes_percent; ?>%)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-yes" style="width: <?php echo $yes_percent; ?>%;">
                                        <?php echo $yes_percent; ?>%
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No Bar -->
                            <div style="margin-bottom: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: 600; color: #E74C3C;">
                                        <i class="fas fa-times-circle"></i> No
                                    </span>
                                    <span><?php echo $no; ?> votes (<?php echo $no_percent; ?>%)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-no" style="width: <?php echo $no_percent; ?>%;">
                                        <?php echo $no_percent; ?>%
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Maybe Bar -->
                            <div style="margin-bottom: 10px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                    <span style="font-weight: 600; color: #F39C12;">
                                        <i class="fas fa-question-circle"></i> Not Sure
                                    </span>
                                    <span><?php echo $maybe; ?> votes (<?php echo $maybe_percent; ?>%)</span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-maybe" style="width: <?php echo $maybe_percent; ?>%;">
                                        <?php echo $maybe_percent; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Chart.js Pie Chart (hidden on mobile, shows on desktop) -->
                        <div style="max-width: 300px; margin: 30px auto;">
                            <canvas id="chart-<?php echo $qid; ?>"></canvas>
                        </div>
                    </div>
                    
                    <script>
                        // Create pie chart for this question
                        new Chart(document.getElementById('chart-<?php echo $qid; ?>'), {
                            type: 'doughnut',
                            data: {
                                labels: ['Yes', 'No', 'Not Sure'],
                                datasets: [{
                                    data: [<?php echo $yes; ?>, <?php echo $no; ?>, <?php echo $maybe; ?>],
                                    backgroundColor: ['#27AE60', '#E74C3C', '#F39C12'],
                                    borderWidth: 0,
                                    hoverOffset: 10
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                        labels: { 
                                            font: { size: 12, family: "'Inter', sans-serif" },
                                            padding: 15
                                        }
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                const label = context.label || '';
                                                const value = context.raw || 0;
                                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                                const percentage = Math.round((value / total) * 100);
                                                return `${label}: ${value} votes (${percentage}%)`;
                                            }
                                        }
                                    }
                                },
                                cutout: '60%'
                            }
                        });
                    </script>
                <?php endforeach; ?>

                <!-- Share Results Section -->
                <div style="text-align: center; margin: 50px 0; padding: 30px; background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%); border-radius: 20px;">
                    <h3 style="color: var(--primary); margin-bottom: 20px;">Share These Results</h3>
                    <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                        <a href="https://wa.me/?text=Check%20out%20today's%20East%20Africa%20Survey%20results!%20https://east-africa-surveys.onrender.com/results.php" 
                           target="_blank" class="btn btn-outline" style="background: #25D366; color: white; border: none;">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                        <a href="https://twitter.com/intent/tweet?text=See%20what%20East%20Africa%20thinks!%20Daily%20survey%20results%20from%20Kenya,%20Uganda%20%26%20Tanzania&url=https://east-africa-surveys.onrender.com" 
                           target="_blank" class="btn btn-outline" style="background: #1DA1F2; color: white; border: none;">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=https://east-africa-surveys.onrender.com" 
                           target="_blank" class="btn btn-outline" style="background: #4267B2; color: white; border: none;">
                            <i class="fab fa-facebook"></i> Facebook
                        </a>
                    </div>
                </div>

                <!-- Call to Action -->
                <div style="text-align: center; margin: 40px 0;">
                    <p style="font-size: 1.2em; color: #666; margin-bottom: 20px;">
                        Want to influence tomorrow's results?
                    </p>
                    <a href="index.php" class="btn">
                        <i class="fas fa-vote-yea"></i> Vote in Today's Survey
                    </a>
                </div>
            <?php endif; ?>
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

    <!-- Add animation styles if not in CSS -->
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</body>
</html>
