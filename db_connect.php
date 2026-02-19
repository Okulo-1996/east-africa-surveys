<?php
// db_connection.php - Working version for Render PostgreSQL
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Get database connection from environment variable
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        // Fallback to individual variables if DATABASE_URL not set
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        $port = getenv('DB_PORT') ?: '5432';
        
        if ($host && $dbname && $username && $password) {
            $database_url = "pgsql:host=$host;port=$port;dbname=$dbname;";
            $pdo = new PDO($database_url, $username, $password);
        } else {
            throw new Exception("Database configuration missing");
        }
    } else {
        // Parse DATABASE_URL (format: postgresql://user:pass@host:port/dbname)
        $url = parse_url($database_url);
        
        $host = $url['host'];
        $port = $url['port'] ?? '5432';
        $dbname = ltrim($url['path'], '/');
        $username = $url['user'];
        $password = $url['pass'];
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;";
        $pdo = new PDO($dsn, $username, $password);
    }
    
    // Set PDO attributes
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Create tables if they don't exist (PostgreSQL syntax)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS questions (
            id SERIAL PRIMARY KEY,
            question_text TEXT NOT NULL,
            active_date DATE NOT NULL,
            display_order INTEGER DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS answers (
            id SERIAL PRIMARY KEY,
            question_id INTEGER NOT NULL,
            response_value VARCHAR(50) NOT NULL,
            submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
        );
        
        CREATE INDEX IF NOT EXISTS idx_questions_date ON questions(active_date);
        CREATE INDEX IF NOT EXISTS idx_answers_question ON answers(question_id);
    ");
    
    // Test connection with a simple query
    $pdo->query("SELECT 1");
    
} catch(Exception $e) {
    // Detailed error for debugging (remove in production)
    die("
        <div style='text-align: center; padding: 40px; font-family: Arial; max-width: 600px; margin: 50px auto; background: #f8f9fa; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
            <h1 style='color: #F39C12;'>🌍 East Africa Surveys</h1>
            <h2 style='color: #e74c3c;'>Technical Details (Debug Mode)</h2>
            <p style='background: #f8d7da; padding: 15px; border-radius: 5px; text-align: left;'>
                <strong>Error:</strong> " . $e->getMessage() . "
            </p>
            <p><strong>Environment variables check:</strong></p>
            <ul style='text-align: left; background: #e9ecef; padding: 15px; border-radius: 5px;'>
                <li>DATABASE_URL: " . (getenv('DATABASE_URL') ? '✓ Set' : '✗ Missing') . "</li>
                <li>DB_HOST: " . (getenv('DB_HOST') ? '✓ Set' : '✗ Missing') . "</li>
                <li>DB_NAME: " . (getenv('DB_NAME') ? '✓ Set' : '✗ Missing') . "</li>
                <li>DB_USER: " . (getenv('DB_USER') ? '✓ Set' : '✗ Missing') . "</li>
                <li>DB_PASSWORD: " . (getenv('DB_PASSWORD') ? '✓ Set' : '✗ Missing') . "</li>
            </ul>
            <p>Please check your environment variables in Render dashboard.</p>
            <p><small>🇰🇪 Kenya | 🇺🇬 Uganda | 🇹🇿 Tanzania</small></p>
        </div>
    ");
}
?>
