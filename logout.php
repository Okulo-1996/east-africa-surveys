<?php
require_once 'config.php';
require_once 'db_connect.php';

// Destroy session
$_SESSION = array();
session_destroy();

// Delete remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM user_sessions WHERE session_token = ?");
    $stmt->execute([$_COOKIE['remember_token']]);
    
    // Expire cookie
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}

header('Location: login.php?logged_out=1');
exit();
?>