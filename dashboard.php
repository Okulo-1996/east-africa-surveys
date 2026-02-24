<?php
// dashboard.php - Modern User Dashboard
session_start();
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user = getCurrentUser($pdo);
$vote_history = getUserVoteHistory($pdo, $user['id'], 20);

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_votes FROM answers WHERE user_id = ?");
$stmt->execute([$user['id']]);
$total_votes = $stmt->fetch()['total_votes'];

// Get user's rank based on votes
$stmt = $pdo->query("SELECT COUNT(*) as user_count FROM users");
$total_users = $stmt->fetch()['user_count'];

// Get recent activity
$stmt = $pdo->prepare("
    SELECT q.question_text, a.response_value, a.submitted_at 
    FROM answers a
    JOIN questions q ON a.question_id = q.id
    WHERE a.user_id = ?
    ORDER BY a.submitted_at DESC
    LIMIT 5
");
$stmt->execute([$user['id']]);
$recent_activity = $stmt->fetchAll();

// Get achievement badges
$achievements = [];

if ($total_votes >= 1) $achievements[] = ['name' => 'First Vote', 'icon' => '🎯', 'date' => 'Just now'];
if ($total_votes >= 10) $achievements[] = ['name' => 'Getting Started', 'icon' => '🌱', 'date' => 'Recent'];
if ($total_votes >= 25) $achievements[] = ['name' => 'Regular Voter', 'icon' => '📊', 'date' => 'Active'];
if ($total_votes >= 50) $achievements[] = ['name' => 'Voice of East Africa', 'icon' => '🗣️', 'date' => 'Dedicated'];
if ($total_votes >= 100) $achievements[] = ['name' => 'Elite Contributor', 'icon' => '🏆', 'date' => 'Elite'];

// Calculate next achievement
$next_achievement = '';
$votes_needed = 0;
if ($total_votes < 10) {
    $next_achievement = 'Getting Started';
    $votes_needed = 10 - $total_votes;
} elseif ($total_votes < 25) {
    $next_achievement = 'Regular Voter';
    $votes_needed = 25 - $total_votes;
} elseif ($total_votes < 50) {
    $next_achievement = 'Voice of East Africa';
    $votes_needed = 50 - $total_votes;
} elseif ($total_votes < 100) {
    $next_achievement = 'Elite Contributor';
    $votes_needed = 100 - $total_votes;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <title>Dashboard - East Africa Surveys | Your Voice Matters</title>
    <meta name="description" content="Your personal dashboard - track your voting history, achievements, and impact on East African public opinion.">
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/style.css">
    
    <style>
        /* Dashboard Specific Styles */
        .dashboard-hero {
            background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
            color: white;
            padding: 40px 24px;
            position: relative;
            overflow: hidden;
        }
        
        .dashboard-hero::before {
            content: '👤';
            position: absolute;
            right: 30px;
            bottom: 20px;
            font-size: 100px;
            opacity: 0.1;
        }
        
        .welcome-message {
            position: relative;
            z-index: 2;
        }
        
        .welcome-message h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .welcome-message p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .member-badge {
            display: inline-block;
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 30px;
            margin-top: 15px;
            backdrop-filter: blur(10px);
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        /* Profile Card */
        .profile-card {
            background: white;
            border-radius: 30px;
            padding: 30px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #2563EB, #1E40AF);
            border-radius: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: white;
            font-weight: 600;
        }
        
        .profile-info h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 5px;
        }
        
        .profile-info .email {
            color: #64748B;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .profile-stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .profile-stat {
            background: #F8FAFC;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
        }
        
        .profile-stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #2563EB;
            margin-bottom: 5px;
        }
        
        .profile-stat-label {
            color: #64748B;
            font-size: 0.9rem;
        }
        
        .profile-actions {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .profile-btn {
            flex: 1;
            padding: 14px 20px;
            border-radius: 15px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s;
        }
        
        .profile-btn-primary {
            background: #2563EB;
            color: white;
        }
        
        .profile-btn-primary:hover {
            background: #1D4ED8;
            transform: translateY(-2px);
        }
        
        .profile-btn-secondary {
            background: #F1F5F9;
            color: #1E293B;
        }
        
        .profile-btn-secondary:hover {
            background: #E2E8F0;
            transform: translateY(-2px);
        }
        
        /* Stats Card */
        .stats-card {
            background: white;
            border-radius: 30px;
            padding: 30px;
            border: 1px solid #E2E8F0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        
        .stats-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .stats-header h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1E293B;
        }
        
        .stats-header i {
            color: #2563EB;
            font-size: 1.5rem;
        }
        
        .progress-circle {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }
        
        .progress-circle svg {
            width: 120px;
            height: 120px;
            transform: rotate(-90deg);
        }
        
        .progress-circle circle {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
        }
        
        .progress-circle-bg {
            stroke: #F1F5F9;
        }
        
        .progress-circle-fill {
            stroke: #2563EB;
            stroke-dasharray: 314;
            stroke-dashoffset: <?php echo 314 - (314 * ($total_votes / 100)); ?>;
            transition: stroke-dashoffset 1s;
        }
        
        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }
        
        .progress-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2563EB;
            line-height: 1;
        }
        
        .progress-label {
            font-size: 0.8rem;
            color: #64748B;
        }
        
        .achievement-list {
            margin-top: 25px;
        }
        
        .achievement-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            background: #F8FAFC;
            border-radius: 15px;
            margin-bottom: 10px;
        }
        
        .achievement-icon {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .achievement-info {
            flex: 1;
        }
        
        .achievement-name {
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 2px;
        }
        
        .achievement-date {
            font-size: 0.8rem;
            color: #64748B;
        }
        
        /* Activity Section */
        .activity-section {
            padding: 40px 24px;
            background: white;
        }
        
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1E293B;
            margin-bottom: 30px;
        }
        
        .activity-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .activity-card {
            background: #F8FAFC;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.2s;
        }
        
        .activity-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }
        
        .activity-question {
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 12px;
            line-height: 1.5;
        }
        
        .activity-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .activity-vote {
            padding: 5px 15px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .vote-yes {
            background: rgba(16, 185, 129, 0.1);
            color: #10B981;
        }
        
        .vote-no {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
        }
        
        .vote-maybe {
            background: rgba(245, 158, 11, 0.1);
            color: #F59E0B;
        }
        
        .activity-date {
            color: #64748B;
            font-size: 0.9rem;
        }
        
        .empty-activity {
            text-align: center;
            padding: 60px 20px;
            background: #F8FAFC;
            border-radius: 30px;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #94A3B8;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1E293B;
            margin-bottom: 10px;
        }
        
        .empty-text {
            color: #64748B;
            margin-bottom: 20px;
        }
        
        .badges-section {
            padding: 40px 24px;
            background: #F8FAFC;
        }
        
        .badges-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-top: 30px;
        }
        
        .badge-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            text-align: center;
            border: 1px solid #E2E8F0;
            transition: all 0.2s;
            opacity: 0.5;
        }
        
        .badge-card.unlocked {
            opacity: 1;
            border-color: #2563EB;
            box-shadow: 0 10px 20px rgba(37,99,235,0.1);
        }
        
        .badge-card.unlocked:hover {
            transform: translateY(-5px);
        }
        
        .badge-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .badge-name {
            font-weight: 600;
            color: #1E293B;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        
        .badge-desc {
            font-size: 0.8rem;
            color: #64748B;
        }
        
        .next-goal {
            background: linear-gradient(135deg, #2563EB 0%, #1E40AF 100%);
            color: white;
            border-radius: 20px;
            padding: 20px;
            margin-top: 30px;
        }
        
        .next-goal h4 {
            font-size: 1.1rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .next-goal-progress {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .progress-bar-goal {
            flex: 1;
            height: 10px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        
        .progress-fill-goal {
            height: 100%;
            background: white;
            width: <?php echo $votes_needed ? min(100, (($total_votes % 100) / 100) * 100) : 0; ?>%;
            border-radius: 10px;
        }
        
        .next-goal-text {
            font-weight: 600;
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            
            .badges-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-actions {
                flex-direction: column;
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
        <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
        <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
        <a href="dashboard.php" class="active"><i class="fas fa-user"></i> Dashboard</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>

    <!-- Dashboard Hero -->
    <section class="dashboard-hero">
        <div class="welcome-message">
            <h1>Welcome back, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            <p>Your voice is shaping East African opinion</p>
            <div class="member-badge">
                <i class="fas fa-calendar-alt"></i> Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
            </div>
        </div>
    </section>

    <!-- Main Dashboard Grid -->
    <div class="dashboard-grid">
        <!-- Left Column - Profile -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <div class="email">
                        <i class="fas fa-envelope"></i>
                        <?php echo htmlspecialchars($user['email']); ?>
                        <?php if ($user['is_verified']): ?>
                            <span style="color: #10B981;"><i class="fas fa-check-circle"></i> Verified</span>
                        <?php else: ?>
                            <span style="color: #F59E0B;"><i class="fas fa-exclamation-triangle"></i> Not Verified</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="profile-stats-grid">
                <div class="profile-stat">
                    <div class="profile-stat-value"><?php echo $total_votes; ?></div>
                    <div class="profile-stat-label">Total Votes</div>
                </div>
                <div class="profile-stat">
                    <div class="profile-stat-value"><?php echo $total_users; ?></div>
                    <div class="profile-stat-label">Community Rank</div>
                </div>
                <?php if ($user['country']): ?>
                <div class="profile-stat">
                    <div class="profile-stat-value">🇰🇪</div>
                    <div class="profile-stat-label"><?php echo htmlspecialchars($user['country']); ?></div>
                </div>
                <?php endif; ?>
                <div class="profile-stat">
                    <div class="profile-stat-value"><?php echo $user['age_range'] ?? 'All'; ?></div>
                    <div class="profile-stat-label">Age Group</div>
                </div>
            </div>

            <div class="profile-actions">
                <a href="edit-profile.php" class="profile-btn profile-btn-primary">
                    <i class="fas fa-user-edit"></i> Edit Profile
                </a>
                <a href="change-password.php" class="profile-btn profile-btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </a>
            </div>
        </div>

        <!-- Right Column - Stats & Achievements -->
        <div class="stats-card">
            <div class="stats-header">
                <h3>Your Impact</h3>
                <i class="fas fa-chart-line"></i>
            </div>

            <!-- Progress Circle -->
            <div class="progress-circle">
                <svg viewBox="0 0 120 120">
                    <circle class="progress-circle-bg" cx="60" cy="60" r="50"></circle>
                    <circle class="progress-circle-fill" cx="60" cy="60" r="50"></circle>
                </svg>
                <div class="progress-text">
                    <div class="progress-number"><?php echo min(100, $total_votes); ?>%</div>
                    <div class="progress-label">to Elite</div>
                </div>
            </div>

            <!-- Achievements -->
            <div class="achievement-list">
                <?php foreach ($achievements as $achievement): ?>
                <div class="achievement-item">
                    <div class="achievement-icon"><?php echo $achievement['icon']; ?></div>
                    <div class="achievement-info">
                        <div class="achievement-name"><?php echo $achievement['name']; ?></div>
                        <div class="achievement-date"><?php echo $achievement['date']; ?></div>
                    </div>
                    <i class="fas fa-check-circle" style="color: #10B981;"></i>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Next Goal -->
            <?php if ($votes_needed > 0): ?>
            <div class="next-goal">
                <h4><i class="fas fa-flag-checkered"></i> Next Goal: <?php echo $next_achievement; ?></h4>
                <div class="next-goal-progress">
                    <div class="progress-bar-goal">
                        <div class="progress-fill-goal"></div>
                    </div>
                    <div class="next-goal-text"><?php echo $votes_needed; ?> more votes</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Badges Section -->
    <section class="badges-section">
        <h2 class="section-title">Your Badges</h2>
        <div class="badges-grid">
            <div class="badge-card <?php echo $total_votes >= 1 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🎯</div>
                <div class="badge-name">First Vote</div>
                <div class="badge-desc">Cast your first vote</div>
            </div>
            <div class="badge-card <?php echo $total_votes >= 10 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🌱</div>
                <div class="badge-name">Getting Started</div>
                <div class="badge-desc">10 votes cast</div>
            </div>
            <div class="badge-card <?php echo $total_votes >= 25 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">📊</div>
                <div class="badge-name">Regular Voter</div>
                <div class="badge-desc">25 votes cast</div>
            </div>
            <div class="badge-card <?php echo $total_votes >= 50 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🗣️</div>
                <div class="badge-name">Voice of East Africa</div>
                <div class="badge-desc">50 votes cast</div>
            </div>
            <div class="badge-card <?php echo $total_votes >= 100 ? 'unlocked' : ''; ?>">
                <div class="badge-icon">🏆</div>
                <div class="badge-name">Elite Contributor</div>
                <div class="badge-desc">100+ votes cast</div>
            </div>
        </div>
    </section>

    <!-- Recent Activity -->
    <section class="activity-section">
        <h2 class="section-title">Recent Activity</h2>
        
        <?php if (empty($recent_activity)): ?>
            <div class="empty-activity">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-title">No Activity Yet</h3>
                <p class="empty-text">Start voting to see your activity here!</p>
                <a href="index.php" class="profile-btn profile-btn-primary" style="display: inline-block; width: auto; padding: 12px 30px;">
                    <i class="fas fa-poll"></i> Take Today's Survey
                </a>
            </div>
        <?php else: ?>
            <div class="activity-grid">
                <?php foreach ($recent_activity as $activity): ?>
                    <div class="activity-card">
                        <div class="activity-question">
                            <?php echo htmlspecialchars($activity['question_text']); ?>
                        </div>
                        <div class="activity-meta">
                            <span class="activity-vote vote-<?php echo strtolower($activity['response_value']); ?>">
                                <?php echo $activity['response_value'] == 'Yes' ? '👍' : ($activity['response_value'] == 'No' ? '👎' : '🤔'); ?>
                                <?php echo $activity['response_value']; ?>
                            </span>
                            <span class="activity-date">
                                <i class="fas fa-clock"></i>
                                <?php echo date('M j, H:i', strtotime($activity['submitted_at'])); ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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

        document.querySelectorAll('.profile-stat, .activity-card, .badge-card').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });
    </script>
</body>
</html>