<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

// Enable CORS for AJAX requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Log function
function logError($message, $data = []) {
    error_log(sprintf(
        "[Face Verification Error] %s | Data: %s",
        $message,
        json_encode($data)
    ));
}

try {
    // Check session
    if (!isset($_SESSION['temp_uid'])) {
        logError("No temp_uid in session");
        throw new Exception('Session expired. Please login again.');
    }

    // Validate user_id parameter
    if (!isset($_GET['user_id'])) {
        logError("No user_id provided");
        throw new Exception('User ID is required');
    }

    $userId = intval($_GET['user_id']);
    
    // Validate user_id matches session
    if ($userId !== intval($_SESSION['temp_uid'])) {
        logError("User ID mismatch", [
            'session_uid' => $_SESSION['temp_uid'],
            'request_uid' => $userId
        ]);
        throw new Exception('Invalid user ID');
    }

    // First, check if user exists in tbluser
    $stmt = $conn->prepare("SELECT ID FROM tbluser WHERE ID = ?");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        throw new Exception('User not found');
    }

    // Get verified face images for the user
    $stmt = $conn->prepare("
        SELECT face_image 
        FROM tblfaceverification 
        WHERE user_id = ? 
        AND verification_status = 'verified'
        ORDER BY verification_date DESC 
        LIMIT 5
    ");
    $stmt->execute([$userId]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($results)) {
        logError("No verified face images found", ['user_id' => $userId]);
        throw new Exception('No verified face images found. Please complete face registration first.');
    }

    // Validate image data
    $validImages = array_filter($results, function($row) {
        return !empty($row['face_image']) && 
               base64_decode($row['face_image'], true) !== false;
    });

    if (empty($validImages)) {
        logError("No valid face images found", ['user_id' => $userId]);
        throw new Exception('No valid face images found');
    }

    // Return all face images for client-side comparison
    echo json_encode([
        'success' => true,
        'images' => array_map(function($row) {
            return $row['face_image'];
        }, $validImages)
    ]);

} catch (Exception $e) {
    logError($e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} 