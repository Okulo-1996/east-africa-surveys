<?php
session_start();
require_once('db_connect.php');

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$today = date('Y-m-d');

// Check if user already voted today (using cookie)
if (isset($_COOKIE['voted_' . str_replace('-', '_', $today)])) {
    $_SESSION['error'] = "You've already voted today. Come back tomorrow!";
    header('Location: index.php');
    exit();
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    $success = true;
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'q') === 0) {
            $question_id = str_replace('q', '', $key);
            
            $stmt = $pdo->prepare("INSERT INTO answers (question_id, response_value) VALUES (?, ?)");
            $result = $stmt->execute([$question_id, $value]);
            if (!$result) $success = false;
        }
    }
    
    if ($success) {
        $pdo->commit();
        
        // Set cookie to prevent double voting (expires at midnight)
        $expire = strtotime('tomorrow midnight') - time();
        setcookie('voted_' . str_replace('-', '_', $today), '1', time() + $expire, '/');
        
        $_SESSION['success'] = "Thank you for voting!";
        header('Location: results.php?voted=1');
        exit();
    } else {
        throw new Exception("Failed to save answers");
    }
    
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Sorry, something went wrong. Please try again.";
    header('Location: index.php');
    exit();
}
?>