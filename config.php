<?php
// Debug mode
define('DEBUG_MODE', false);

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', DEBUG_MODE ? 1 : 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'birth_death_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session security
define('SESSION_LIFETIME', 1800);  // 30 minutes
define('CSRF_TOKEN_NAME', 'bdc_csrf_token');
define('CSRF_TIMEOUT', 3600);  // 1 hour

// Application settings
define('SITE_NAME', 'Birth & Death Certificate System');
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);
define('MAX_FILE_SIZE', 5 * 1024 * 1024);  // 5MB
define('UPLOAD_MAX_SIZE', MAX_FILE_SIZE);

// Security settings
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOGIN_LOCKOUT_TIME', 900);  // 15 minutes
define('PASSWORD_RESET_TIMEOUT', 3600);  // 1 hour
define('VERIFICATION_CODE_TIMEOUT', 600);  // 10 minutes

// Email settings
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-specific-password');
define('SMTP_FROM_EMAIL', 'noreply@birthdeath.com');
define('SMTP_FROM_NAME', SITE_NAME);

// Time zone
date_default_timezone_set('Asia/Dhaka'); 