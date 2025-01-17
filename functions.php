<?php
require_once 'config.php';

/**
 * Database helper functions
 */
function fetchOne($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database error in fetchOne(): " . $e->getMessage());
        throw new Exception("Database error occurred");
    }
}

function fetchSingle($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchSingle(): " . $e->getMessage());
        throw new Exception("Database error occurred");
    }
}

function fetchAll($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error in fetchAll(): " . $e->getMessage());
        throw new Exception("Database error occurred");
    }
}

function execute($query, $params = []) {
    global $conn;
    try {
        $stmt = $conn->prepare($query);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Database error in execute(): " . $e->getMessage());
        throw new Exception("Database error occurred");
    }
}

/**
 * Security helper functions
 */
function generateToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || 
        !isset($_SESSION['token_time']) || 
        (time() - $_SESSION['token_time']) > CSRF_TIMEOUT) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        $_SESSION['token_time'] = time();
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function validateToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && 
           isset($_SESSION['token_time']) && 
           (time() - $_SESSION['token_time']) <= CSRF_TIMEOUT && 
           hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function sanitizeInput($input, $type = 'string') {
    if (is_array($input)) {
        return array_map(function($item) use ($type) {
            return sanitizeInput($item, $type);
        }, $input);
    }
    
    $input = trim($input);
    
    switch ($type) {
        case 'email':
            $email = filter_var($input, FILTER_SANITIZE_EMAIL);
            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
        case 'int':
            return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
        case 'float':
            return filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        case 'url':
            $url = filter_var($input, FILTER_SANITIZE_URL);
            return filter_var($url, FILTER_VALIDATE_URL) ? $url : false;
        case 'html':
            return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        default:
            return htmlspecialchars(strip_tags($input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < PASSWORD_MIN_LENGTH) {
        $errors[] = "Password must be at least " . PASSWORD_MIN_LENGTH . " characters long";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $errors[] = "Password must contain at least one special character";
    }
    
    return empty($errors) ? true : $errors;
}

function validateFile($file, $allowedTypes = ALLOWED_EXTENSIONS, $maxSize = UPLOAD_MAX_SIZE) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return "Invalid file parameters";
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return "File size exceeds limit";
            case UPLOAD_ERR_PARTIAL:
                return "File was only partially uploaded";
            case UPLOAD_ERR_NO_FILE:
                return "No file was uploaded";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Missing temporary folder";
            case UPLOAD_ERR_CANT_WRITE:
                return "Failed to write file to disk";
            case UPLOAD_ERR_EXTENSION:
                return "File upload stopped by extension";
            default:
                return "Unknown upload error";
        }
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    $allowedMimes = [
        'image/jpeg' => ['jpg', 'jpeg'],
        'image/png' => ['png'],
        'application/pdf' => ['pdf']
    ];
    
    $isAllowedMime = false;
    foreach ($allowedMimes as $mime => $exts) {
        if ($mimeType === $mime && array_intersect($exts, $allowedTypes)) {
            $isAllowedMime = true;
            break;
        }
    }
    
    if (!$isAllowedMime) {
        return "Invalid file type. Allowed types: " . implode(', ', $allowedTypes);
    }
    
    if ($file['size'] > $maxSize) {
        return "File size exceeds limit of " . ($maxSize / 1048576) . "MB";
    }
    
    return true;
}

function generateApplicationID($type = 'birth') {
    $prefix = $type === 'birth' ? 'BRN' : 'DTH';
    $timestamp = date('YmdHis');
    $random = bin2hex(random_bytes(4));
    return $prefix . $timestamp . $random;
}

/**
 * Session helper functions
 */
function checkSession() {
    if (!isset($_SESSION['last_activity']) || !isset($_SESSION['created'])) {
        return false;
    }
    
    if (time() - $_SESSION['created'] > SESSION_LIFETIME) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
    
    if (time() - $_SESSION['last_activity'] > SESSION_LIFETIME) {
        session_unset();
        session_destroy();
        return false;
    }
    
    if (!isset($_SESSION['user_ip']) || $_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_unset();
        session_destroy();
        return false;
    }
    
    if (!isset($_SESSION['user_agent']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

function logActivity($userId, $action, $details = '') {
    try {
        execute(
            "INSERT INTO activity_logs (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)",
            [$userId, $action, $details, $_SERVER['REMOTE_ADDR']]
        );
    } catch (Exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
    }
}

/**
 * Application helper functions
 */
function formatDate($date, $format = 'Y-m-d') {
    return date($format, strtotime($date));
}

function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'pending':
            return 'badge-warning';
        case 'approved':
            return 'badge-success';
        case 'rejected':
            return 'badge-danger';
        default:
            return 'badge-secondary';
    }
}

function redirectTo($path) {
    if (!headers_sent()) {
        header("Location: $path");
        exit;
    }
    echo '<script type="text/javascript">';
    echo 'window.location.href="' . $path . '";';
    echo '</script>';
    echo '<noscript>';
    echo '<meta http-equiv="refresh" content="0;url=' . $path . '" />';
    echo '</noscript>';
    exit;
}

function displayAlert($message, $type = 'info') {
    $message = sanitizeInput($message, 'html');
    $type = sanitizeInput($type, 'string');
    return "<div class='alert alert-$type' role='alert'>$message</div>";
}

function isAjax() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function sendJsonResponse($data, $statusCode = 200) {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        header('X-Content-Type-Options: nosniff');
    }
    echo json_encode($data);
    exit;
}

/**
 * Email helper functions
 */
function sendEmail($to, $subject, $body) {
    require_once 'vendor/autoload.php';
    
    try {
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USERNAME;
        $mail->Password = SMTP_PASSWORD;
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = SMTP_PORT;
        
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
} 