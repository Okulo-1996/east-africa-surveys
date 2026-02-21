<?php
session_start();
require_once 'config.php';
require_once 'db_connect.php';
require_once 'functions.php';

// ✅ FIXED: Redirect to login if NOT logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Rest of your dashboard code...
$user = getCurrentUser($pdo);
// ... etc

$vote_history = getUserVoteHistory($pdo, $user['id']);

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as total_votes FROM answers WHERE user_id = ?");
$stmt->execute([$user['id']]);
$total_votes = $stmt->fetch()['total_votes'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - East Africa Surveys</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin: 40px 0;
        }
        
        .profile-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 20px;
            padding: 30px;
        }
        
        .history-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .history-table th {
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .history-table td {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .welcome-badge {
            background: rgba(255,255,255,0.2);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1>East Africa <span>Surveys</span></h1>
            <p>Welcome back, <?php echo htmlspecialchars($user['username']); ?>! 🇰🇪 🇺🇬 🇹🇿</p>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="results.php"><i class="fas fa-chart-bar"></i> Results</a>
            <a href="dashboard.php" class="active"><i class="fas fa-user"></i> Dashboard</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>

        <div class="dashboard-grid">
            <div class="profile-card">
                <h2 style="color: var(--primary); margin-bottom: 20px;">
                    <i class="fas fa-user-circle"></i> My Profile
                </h2>
                
                <div style="display: grid; gap: 15px;">
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8f9fa; border-radius: 10px;">
                        <i class="fas fa-user" style="color: var(--secondary); width: 30px;"></i>
                        <strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8f9fa; border-radius: 10px;">
                        <i class="fas fa-envelope" style="color: var(--secondary); width: 30px;"></i>
                        <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                        <?php if ($user['is_verified']): ?>
                            <span style="background: #27AE60; color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.8em;">Verified</span>
                        <?php else: ?>
                            <span style="background: #E74C3C; color: white; padding: 3px 10px; border-radius: 20px; font-size: 0.8em;">Not Verified</span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($user['country']): ?>
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8f9fa; border-radius: 10px;">
                        <i class="fas fa-flag" style="color: var(--secondary); width: 30px;"></i>
                        <strong>Country:</strong> <?php echo htmlspecialchars($user['country']); ?>
                    </div>
                    <?php endif; ?>
                    
                    <div style="display: flex; align-items: center; gap: 10px; padding: 10px; background: #f8f9fa; border-radius: 10px;">
                        <i class="fas fa-calendar" style="color: var(--secondary); width: 30px;"></i>
                        <strong>Member since:</strong> <?php echo date('F j, Y', strtotime($user['created_at'])); ?>
                    </div>
                </div>
                
                <div style="margin-top: 30px;">
                    <a href="edit-profile.php" class="btn btn-outline" style="margin-right: 10px;">
                        <i class="fas fa-edit"></i> Edit Profile
                    </a>
                    <a href="change-password.php" class="btn btn-outline">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>
            </div>
            
            <div class="stats-card">
                <h3 style="color: white; margin-bottom: 20px;">
                    <i class="fas fa-chart-line"></i> My Statistics
                </h3>
                
                <div class="welcome-badge">
                    <div style="font-size: 3em; font-weight: bold;"><?php echo $total_votes; ?></div>
                    <div>Total Votes Cast</div>
                </div>
                
                <div style="margin-top: 30px;">
                    <h4 style="color: white; margin-bottom: 15px;">Achievements</h4>
                    <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 5px; margin-bottom: 10px;">
                        <i class="fas fa-medal"></i> 
                        <?php if ($total_votes >= 100): ?>
                            🏆 Elite Voter (100+ votes)
                        <?php elseif ($total_votes >= 50): ?>
                            🥇 Gold Voter (50+ votes)
                        <?php elseif ($total_votes >= 25): ?>
                            🥈 Silver Voter (25+ votes)
                        <?php elseif ($total_votes >= 10): ?>
                            🥉 Bronze Voter (10+ votes)
                        <?php else: ?>
                            🌱 New Voter (Keep voting!)
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Recent Voting History -->
        <div style="background: white; border-radius: 20px; padding: 30px; margin: 40px 0;">
            <h3 style="color: var(--primary); margin-bottom: 20px;">
                <i class="fas fa-history"></i> Recent Voting History
            </h3>
            
            <?php if (empty($vote_history)): ?>
                <p style="text-align: center; padding: 40px; color: #666;">
                    <i class="fas fa-inbox" style="font-size: 3em; margin-bottom: 10px; display: block;"></i>
                    No voting history yet. <a href="index.php">Take today's survey!</a>
                </p>
            <?php else: ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Question</th>
                            <th>Your Vote</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($vote_history as $vote): ?>
                        <tr>
                            <td><?php echo date('M j, Y', strtotime($vote['submitted_at'])); ?></td>
                            <td><?php echo htmlspecialchars($vote['question_text']); ?></td>
                            <td>
                                <?php if ($vote['response_value'] == 'Yes'): ?>
                                    <span style="color: #27AE60;">👍 Yes</span>
                                <?php elseif ($vote['response_value'] == 'No'): ?>
                                    <span style="color: #E74C3C;">👎 No</span>
                                <?php else: ?>
                                    <span style="color: #F39C12;">🤔 Not Sure</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 East Africa Surveys | Your voice matters</p>
            </div>
        </div>
    </footer>
</body>
</html>
?>