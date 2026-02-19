<?php
// Free database on Render.com
$host = 'your-render-db-host';  // Get this from Render dashboard
$dbname = 'east_africa_surveys';
$username = 'your-username';
$password = 'your-password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist (runs once)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_text TEXT NOT NULL,
            active_date DATE NOT NULL,
            display_order INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_date (active_date)
        );
        
        CREATE TABLE IF NOT EXISTS answers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            response_value VARCHAR(50) NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
            INDEX idx_question (question_id)
        );
    ");
    
} catch(PDOException $e) {
    // Simple error page for beginners
    die("
        <div style='text-align: center; padding: 50px; font-family: Arial;'>
            <h1 style='color: #F39C12;'>🌍 East Africa Surveys</h1>
            <p>Sorry, we're having technical difficulties.</p>
            <p>Please try again in a few minutes.</p>
            <p style='color: #666;'>🇰🇪 🇺🇬 🇹🇿</p>
        </div>
    ");
}
?>
