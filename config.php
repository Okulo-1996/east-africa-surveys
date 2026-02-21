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
define('SMTP_PASS', 'YOUR_APP_PASSWORD'); // Your Gmail App Password
define('SMTP_FROM', 'info.eastafricasurveys@gmail.com');
define('SMTP_FROM_NAME', 'East Africa Surveys');

// Security
define('SALT', 'east-africa-2026-secure-salt');
define('TOKEN_EXPIRY', 24); // Hours until verification token expires

// DO NOT start sessions here! Sessions should be started in individual pages
// Session configuration will be handled in each page that needs it
?>