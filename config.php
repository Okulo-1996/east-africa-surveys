<?php
// config.php - Configuration file for East Africa Surveys

// Site configuration
define('SITE_NAME', 'East Africa Surveys');
define('SITE_URL', 'https://east-africa-surveys.onrender.com');
define('ADMIN_EMAIL', 'info.eastafricasurveys@gmail.com');

// Email configuration (for account activation)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'info.eastafricasurveys@gmail.com');
define('SMTP_PASS', 'YOUR_APP_PASSWORD'); // You'll need to create an App Password in Gmail
define('SMTP_FROM', 'info.eastafricasurveys@gmail.com');
define('SMTP_FROM_NAME', 'East Africa Surveys');

// Security
define('SALT', 'east-africa-2026-secure-salt'); // Change this to random string
define('TOKEN_EXPIRY', 24); // Hours until verification token expires

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);
session_start();
?>