<?php
session_start();
require_once '../db_connect.php';

// Simple login check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$message = '';
$error = '';

// Handle question update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_today'])) {
        // Delete today's old questions
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("DELETE FROM questions WHERE active_date = ?");
        $stmt->execute([$today]);
        
        // Insert new questions for today
        $success = true;
        for ($i = 1; $i <= 5; $i++) {
            if (!empty($_POST["q$i"])) {
                $stmt = $pdo->prepare("INSERT INTO questions (question_text, active_date, display_order) VALUES (?, ?, ?)");
                $result = $stmt->execute([$_POST["q$i"], $today, $i]);
                if (!$result) $success = false;
            }
        }
        
        if ($success) {
            $message = "✅ Today's questions updated successfully!";
        } else {
            $error = "❌ Some questions couldn't be saved.";
        }
    }
    
    if (isset($_POST['preview_date'])) {
        $preview_date = $_POST['preview_date'];
        $stmt = $pdo->prepare("SELECT * FROM questions WHERE active_date = ? ORDER BY display_order");
        $stmt->execute([$preview_date]);
        $preview_questions = $stmt->fetchAll();
    }
}

// Get recent questions for quick copy
$stmt = $pdo->query("SELECT DISTINCT active_date FROM questions ORDER BY active_date DESC LIMIT 10");
$recent_dates = $stmt->fetchAll();

// Get today's questions if they exist
$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM questions WHERE active_date = ? ORDER BY display_order");
$stmt->execute([$today]);
$today_questions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Update Daily Questions | East Africa Surveys</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .question-input {
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 5px solid var(--sunset-orange);
        }
        
        .question-input label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--savanna-green);
        }
        
        .question-input input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        .question-input input:focus {
            outline: none;
            border-color: var(--sunset-orange);
        }
        
        .question-preview {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 10px;
            margin: 10px 0;
        }
        
        .stats-box {
            background: linear-gradient(135deg, var(--sunset-orange), var(--savanna-green));
            color: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
        }
        
        .btn-danger {
            background: #e74c3c;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
        }
        
        .btn-danger:hover {
            background: #c0392b;
        }
        
        .btn-secondary {
            background: #7f8c8d;
            box-shadow: 0 4px 15px rgba(127, 140, 141, 0.3);
        }
        
        .quick-copy-btn {
            background: none;
            border: 1px solid var(--savanna-green);
            color: var(--savanna-green);
            padding: 5px 10px;
            border-radius: 15px;
            cursor: pointer;
            font-size: 0.8em;
            margin-left: 10px;
        }
        
        .quick-copy-btn:hover {
            background: var(--savanna-green);
            color: white;
        }
        
        .suggestion-box {
            background: #fff9e6;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            border: 1px dashed var(--sunset-orange);
        }
        
        .suggestion-item {
            padding: 8px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }
        
        .suggestion-item:hover {
            background: #f0f0f0;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>🌍 East Africa Surveys</h1>
            <p>Admin Dashboard - Update Daily Questions</p>
            <div class="email-contact">
                <i class="fas fa-envelope"></i> info.eastafricasurveys@gmail.com
            </div>
        </div>
    </header>

    <div class="container">
        <nav>
            <a href="../index.php">🏠 Home</a>
            <a href="../results.php">📊 Results</a>
            <a href="update.php" class="active">✏️ Update Questions</a>
            <a href="logout.php" style="background: #e74c3c; color: white;">🚪 Logout</a>
        </nav>

        <div class="admin-container">
            <!-- Stats Box -->
            <div class="stats-box">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h2 style="margin: 0;">📊 Quick Stats</h2>
                        <p style="margin: 10px 0 0 0;">Today: <?php echo date('l, F j, Y'); ?></p>
                    </div>
                    <div style="text-align: right;">
                        <?php
                        // Get total votes today
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM answers a JOIN questions q ON a.question_id = q.id WHERE q.active_date = ?");
                        $stmt->execute([$today]);
                        $total_votes = $stmt->fetchColumn();
                        ?>
                        <p style="margin: 0; font-size: 2em;"><?php echo $total_votes; ?></p>
                        <p style="margin: 0;">votes today</p>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($message): ?>
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Main Update Form -->
            <h2 style="color: var(--savanna-green); margin-bottom: 20px;">
                <i class="fas fa-pen"></i> Set Today's 5 Questions
            </h2>
            
            <form method="POST">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <div class="question-input">
                        <label>
                            Question <?php echo $i; ?> 
                            <span style="font-weight: normal; color: #666; font-size: 0.9em;">
                                (Yes/No/Maybe format)
                            </span>
                        </label>
                        <input type="text" 
                               name="q<?php echo $i; ?>" 
                               placeholder="e.g., Should Kenya lower taxes on mobile money?" 
                               value="<?php echo isset($today_questions[$i-1]) ? htmlspecialchars($today_questions[$i-1]['question_text']) : ''; ?>"
                               required>
                    </div>
                <?php endfor; ?>
                
                <div style="display: flex; gap: 15px; margin: 30px 0;">
                    <button type="submit" name="update_today" class="btn" style="flex: 2;">
                        <i class="fas fa-save"></i> Update Today's Questions
                    </button>
                    <button type="button" class="btn btn-secondary" style="flex: 1;" onclick="clearForm()">
                        <i class="fas fa-eraser"></i> Clear
                    </button>
                </div>
            </form>

            <!-- Quick Copy from Previous Days -->
            <div class="suggestion-box">
                <h3 style="color: var(--sunset-orange); margin-bottom: 15px;">
                    <i class="fas fa-copy"></i> Quick Copy from Previous Days
                </h3>
                <p style="font-size: 0.9em; margin-bottom: 15px;">Click a date to load those questions:</p>
                
                <?php foreach ($recent_dates as $date): ?>
                    <?php if ($date['active_date'] != $today): ?>
                        <form method="POST" style="display: inline-block; margin-right: 10px; margin-bottom: 10px;">
                            <input type="hidden" name="preview_date" value="<?php echo $date['active_date']; ?>">
                            <button type="submit" class="quick-copy-btn">
                                <?php echo date('M j', strtotime($date['active_date'])); ?>
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <!-- Preview Section -->
            <?php if (isset($preview_questions) && !empty($preview_questions)): ?>
                <div style="margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 10px;">
                    <h3 style="color: var(--savanna-green); margin-bottom: 15px;">
                        <i class="fas fa-eye"></i> Preview: <?php echo date('F j, Y', strtotime($preview_date)); ?>
                    </h3>
                    
                    <?php foreach ($preview_questions as $index => $q): ?>
                        <div class="question-preview">
                            <strong>Q<?php echo $index + 1; ?>:</strong> 
                            <?php echo htmlspecialchars($q['question_text']); ?>
                            
                            <!-- Quick copy button for each question -->
                            <button class="quick-copy-btn" style="float: right;" 
                                    onclick="copyToForm(<?php echo $index + 1; ?>, '<?php echo htmlspecialchars($q['question_text'], ENT_QUOTES); ?>')">
                                <i class="fas fa-copy"></i> Copy to Q<?php echo $index + 1; ?>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Question Ideas Bank (Free suggestions) -->
            <div style="margin-top: 40px;">
                <h3 style="color: var(--savanna-green); margin-bottom: 15px;">
                    <i class="fas fa-lightbulb"></i> East Africa Question Ideas
                </h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🇰🇪 Should Kenya lower fuel prices?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🇺🇬 Is Uganda's economy improving?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🇹🇿 Should Tanzania invest more in tourism?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🌍 Is East African unity working?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        📱 Should mobile money fees be reduced?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🏥 Is healthcare affordable in your country?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🎓 Are schools preparing students for jobs?
                    </div>
                    <div class="suggestion-item" onclick="fillQuestionSuggestion(this.innerText)">
                        🌧️ Is climate change affecting your farming?
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>🌍 East Africa Surveys</h3>
                <p>Admin access only - please keep questions relevant to East Africa.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <p><a href="../privacy.php" style="color: white;">🔒 Privacy Policy</a></p>
                <p><a href="logout.php" style="color: white;">🚪 Logout</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2026 East Africa Surveys | Admin Dashboard</p>
        </div>
    </footer>

    <script>
        // Clear all form inputs
        function clearForm() {
            if (confirm('Clear all questions?')) {
                for (let i = 1; i <= 5; i++) {
                    document.getElementsByName('q' + i)[0].value = '';
                }
            }
        }
        
        // Copy a question to a specific field
        function copyToForm(questionNum, questionText) {
            document.getElementsByName('q' + questionNum)[0].value = questionText;
        }
        
        // Fill the first empty question with a suggestion
        function fillQuestionSuggestion(text) {
            for (let i = 1; i <= 5; i++) {
                let input = document.getElementsByName('q' + i)[0];
                if (input.value === '') {
                    input.value = text;
                    break;
                }
            }
        }
        
        // Auto-save warning
        window.addEventListener('beforeunload', function (e) {
            let unsaved = false;
            for (let i = 1; i <= 5; i++) {
                if (document.getElementsByName('q' + i)[0].value !== '') {
                    unsaved = true;
                    break;
                }
            }
            if (unsaved) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Leave anyway?';
            }
        });
    </script>
</body>
</html>
