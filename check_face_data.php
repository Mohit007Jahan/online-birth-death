<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

header('Content-Type: text/plain');

echo "Face Verification Debug Report\n";
echo "============================\n\n";

// Check model files
$modelPath = __DIR__ . '/models';
echo "Checking model files in: $modelPath\n";
$requiredModels = [
    'face_recognition_model-weights_manifest.json',
    'face_recognition_model-shard1',
    'face_recognition_model-shard2',
    'face_landmark_68_model-weights_manifest.json',
    'face_landmark_68_model-shard1',
    'tiny_face_detector_model-weights_manifest.json',
    'tiny_face_detector_model-shard1'
];

foreach ($requiredModels as $model) {
    $filePath = $modelPath . '/' . $model;
    if (file_exists($filePath)) {
        echo "✓ Found: $model\n";
    } else {
        echo "✗ Missing: $model\n";
    }
}

echo "\nChecking database tables:\n";
echo "========================\n";

// Check tblfaceverification table
try {
    $stmt = $conn->query("SELECT COUNT(*) FROM tblfaceverification");
    $count = $stmt->fetchColumn();
    echo "Face verification records: $count\n";

    // Check a sample record
    if ($count > 0) {
        $stmt = $conn->query("SELECT * FROM tblfaceverification LIMIT 1");
        $sample = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "\nSample face verification record:\n";
        echo "ID: " . $sample['id'] . "\n";
        echo "User ID: " . $sample['user_id'] . "\n";
        echo "Status: " . $sample['verification_status'] . "\n";
        echo "Face data length: " . strlen($sample['face_image']) . " bytes\n";
    }
} catch (PDOException $e) {
    echo "Error checking tblfaceverification: " . $e->getMessage() . "\n";
}

// Check model directory permissions
echo "\nChecking directory permissions:\n";
echo "==============================\n";
echo "Models directory: " . substr(sprintf('%o', fileperms($modelPath)), -4) . "\n";

// Check PHP configuration
echo "\nPHP Configuration:\n";
echo "=================\n";
echo "Max execution time: " . ini_get('max_execution_time') . "s\n";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post max size: " . ini_get('post_max_size') . "\n";

// Check session data
echo "\nSession Data:\n";
echo "============\n";
echo "Session status: " . session_status() . "\n";
if (isset($_SESSION['temp_uid'])) {
    echo "Temporary User ID: " . $_SESSION['temp_uid'] . "\n";
    
    // Check user data
    try {
        $stmt = $conn->prepare("SELECT * FROM tbluser WHERE ID = ?");
        $stmt->execute([$_SESSION['temp_uid']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            echo "User found:\n";
            echo "Status: " . $user['status'] . "\n";
            echo "Last login: " . ($user['last_login'] ?? 'Never') . "\n";
        } else {
            echo "No user found for ID: " . $_SESSION['temp_uid'] . "\n";
        }
    } catch (PDOException $e) {
        echo "Error checking user data: " . $e->getMessage() . "\n";
    }
} else {
    echo "No temporary user ID in session\n";
}

// Check .htaccess file
echo "\nChecking .htaccess:\n";
echo "=================\n";
$htaccessPath = $modelPath . '/.htaccess';
if (file_exists($htaccessPath)) {
    echo "✓ .htaccess exists in models directory\n";
    echo "Content:\n" . file_get_contents($htaccessPath) . "\n";
} else {
    echo "✗ No .htaccess file in models directory\n";
}

?> 