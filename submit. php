<?php
session_start();
require_once 'db_connect.php';

$today = date('Y-m-d');
$selected_date = $_GET['date'] ?? $today;

// Get questions and results for selected date
$sql = "SELECT 
            q.id,
            q.question_text,
            a.response_value,
            COUNT(*) as count
        FROM questions q
        LEFT JOIN answers a ON q.id = a.question_id
        WHERE q.active_date = ?
        GROUP BY q.id, a.response_value
        ORDER BY q.display_order, a.response_value";

$stmt = $pdo->prepare($sql);
$stmt->execute([$selected_date]);
$results = $stmt->fetchAll();

// Reorganize data by question
$questions = [];
foreach ($results as $row) {
    $qid = $row['id'];
    if (!isset($questions[$qid])) {
        $questions[$qid] = [
            'text' => $row['question_text'],
            'answers' => []
        ];
    }
    if ($row['response_value']) {
        $questions[$qid]['answers'][$row['response_value']] = $row['count'];
    }
}

// Get total votes for today
$stmt = $pdo->prepare("SELECT COUNT(*) FROM answers a JOIN questions q ON a.question_id = q.id WHERE q.active_date = ?");
$stmt->execute([$today]);
$total_votes_today = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - East Africa Surveys</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            border: 2px solid #c3e6cb;
            animation: slideDown 0.5s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .vote-count {
            background: white;
            border-radius: 30px;
            padding: 5px 15px;
            color: var(--sunset-orange);
            font-weight: bold;
            margin-left: 10px;
        }
        
        .share-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }
        
        .share-btn {
            padding: 10px 20px;
            border-radius: 30px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: transform 0.3s;
        }
        
        .share-btn:hover {
            transform: translateY(-3px);
        }
        
        .share-whatsapp { background: #25D366; }
        .share-twitter { background: #1DA1F2; }
        .share-facebook { background: #4267B2; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>🌍 East Africa Surveys</h1>
            <p>Real-time Results - See What East Africa Thinks!</p>
            <div class="email-contact">
                <i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php">🏠 Home</a>
            <a href="results.php" class="active">📊 Results</a>
            <a href="about.php">📖 About Us</a>
            <a href="contact.php">📞 Contact</a>
        </nav>

        <main>
            <!-- Success message after voting -->
            <?php if (isset($_GET['voted'])): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle" style="font-size: 3em; margin-bottom: 10px;"></i>
                    <h2>Thank You for Voting! 🇰🇪 🇺🇬 🇹🇿</h2>
                    <p style="font-size: 1.2em;">Your voice has been counted. Here's what East Africa thinks today:</p>
                </div>
                <?php 
                // Clear the session message
                unset($_SESSION['success']);
                ?>
            <?php endif; ?>

            <!-- Date selector -->
            <div style="text-align: right; margin-bottom: 20px;">
                <form method="GET" style="display: inline-block;">
                    <label for="date">View results for:</label>
                    <input type="date" id="date" name="date" value="<?php echo $selected_date; ?>" 
                           style="padding: 8px; border-radius: 5px; border: 1px solid #ddd;">
                    <button type="submit" class="btn-small" style="padding: 8px 15px;">Go</button>
                </form>
            </div>

            <!-- Today's stats summary -->
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 30px 0;">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_votes_today; ?></div>
                    <div>Total Votes Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($questions); ?></div>
                    <div>Questions Today</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo date('M j, Y', strtotime($selected_date)); ?></div>
                    <div>Results Date</div>
                </div>
            </div>

            <?php if (empty($questions)): ?>
                <div style="background: #fff3cd; color: #856404; padding: 40px; border-radius: 15px; text-align: center; margin: 40px 0;">
                    <i class="fas fa-chart-bar" style="font-size: 4em; margin-bottom: 20px;"></i>
                    <h2>No Results Yet</h2>
                    <p>Be the first to vote in today's survey!</p>
                    <a href="index.php" class="btn" style="margin-top: 20px;">Take the Survey</a>
                </div>
            <?php else: ?>
                <!-- Results for each question -->
                <?php foreach ($questions as $qid => $question): 
                    $total = array_sum($question['answers']);
                    $yes = $question['answers']['Yes'] ?? 0;
                    $no = $question['answers']['No'] ?? 0;
                    $maybe = $question['answers']['Maybe'] ?? 0;
                    
                    $yes_percent = $total > 0 ? round(($yes / $total) * 100) : 0;
                    $no_percent = $total > 0 ? round(($no / $total) * 100) : 0;
                    $maybe_percent = $total > 0 ? round(($maybe / $total) * 100) : 0;
                ?>
                    <div class="chart-container">
                        <h2 class="chart-title">
                            <?php echo htmlspecialchars($question['text']); ?>
                            <span class="vote-count"><?php echo $total; ?> votes</span>
                        </h2>
                        
                        <!-- Simple bar chart visualization (works without JavaScript) -->
                        <div style="margin: 20px 0;">
                            <!-- Yes bar -->
                            <div style="margin: 10px 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><strong>👍 Yes</strong></span>
                                    <span><?php echo $yes; ?> votes (<?php echo $yes_percent; ?>%)</span>
                                </div>
                                <div style="background: #e0e0e0; height: 30px; border-radius: 15px; overflow: hidden;">
                                    <div style="background: #27AE60; width: <?php echo $yes_percent; ?>%; height: 100%; text-align: center; color: white; line-height: 30px;">
                                        <?php echo $yes_percent; ?>%
                                    </div>
                                </div>
                            </div>
                            
                            <!-- No bar -->
                            <div style="margin: 10px 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><strong>👎 No</strong></span>
                                    <span><?php echo $no; ?> votes (<?php echo $no_percent; ?>%)</span>
                                </div>
                                <div style="background: #e0e0e0; height: 30px; border-radius: 15px; overflow: hidden;">
                                    <div style="background: #E74C3C; width: <?php echo $no_percent; ?>%; height: 100%; text-align: center; color: white; line-height: 30px;">
                                        <?php echo $no_percent; ?>%
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Maybe bar -->
                            <div style="margin: 10px 0;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><strong>🤔 Not Sure</strong></span>
                                    <span><?php echo $maybe; ?> votes (<?php echo $maybe_percent; ?>%)</span>
                                </div>
                                <div style="background: #e0e0e0; height: 30px; border-radius: 15px; overflow: hidden;">
                                    <div style="background: #F39C12; width: <?php echo $maybe_percent; ?>%; height: 100%; text-align: center; color: white; line-height: 30px;">
                                        <?php echo $maybe_percent; ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Canvas for Chart.js (optional) -->
                        <canvas id="chart-<?php echo $qid; ?>" style="max-height: 300px; margin-top: 20px;"></canvas>
                    </div>
                    
                    <script>
                        // Create Chart.js chart
                        (function() {
                            const ctx = document.getElementById('chart-<?php echo $qid; ?>').getContext('2d');
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Yes (👍)', 'No (👎)', 'Not Sure (🤔)'],
                                    datasets: [{
                                        data: [<?php echo $yes; ?>, <?php echo $no; ?>, <?php echo $maybe; ?>],
                                        backgroundColor: ['#27AE60', '#E74C3C', '#F39C12'],
                                        borderWidth: 0
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: true,
                                    plugins: {
                                        legend: {
                                            position: 'bottom',
                                            labels: { font: { size: 12 } }
                                        }
                                    }
                                }
                            });
                        })();
                    </script>
                <?php endforeach; ?>

                <!-- Share Results -->
                <div class="share-buttons">
                    <a href="https://wa.me/?text=Check%20out%20today's%20East%20Africa%20Survey%20results!%20<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/results.php'); ?>" 
                       target="_blank" class="share-btn share-whatsapp">
                        <i class="fab fa-whatsapp"></i> Share on WhatsApp
                    </a>
                    <a href="https://twitter.com/intent/tweet?text=See%20what%20East%20Africa%20thinks!%20Daily%20survey%20results%20from%20Kenya,%20Uganda%20%26%20Tanzania&url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . '/results.php'); ?>" 
                       target="_blank" class="share-btn share-twitter">
                        <i class="fab fa-twitter"></i> Share on Twitter
                    </a>
                </div>

                <!-- Call to action -->
                <div style="text-align: center; margin: 40px 0;">
                    <p style="font-size: 1.2em;">Want to see tomorrow's results?</p>
                    <a href="index.php" class="btn">
                        <i class="fas fa-vote-yea"></i> Vote in Today's Survey
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Real-time opinions from Kenya, Uganda & Tanzania.</p>
                <p><i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="index.php" style="color: white;">🏠 Home</a></p>
                <p><a href="about.php" style="color: white;">📖 About Us</a></p>
                <p><a href="privacy.php" style="color: white;">🔒 Privacy</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 East Africa Surveys | Every voice matters 🇰🇪 🇺🇬 🇹🇿</p>
        </div>
    </footer>
</body>
</html>
