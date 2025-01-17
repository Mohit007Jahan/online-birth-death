<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

header('Content-Type: text/plain');

echo "=== Session Info ===\n";
echo "temp_uid: " . ($_SESSION['temp_uid'] ?? 'not set') . "\n";
echo "uid: " . ($_SESSION['uid'] ?? 'not set') . "\n\n";

echo "=== Database Connection ===\n";
try {
    $conn->query("SELECT 1");
    echo "Database connection: OK\n\n";
} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "\n\n";
}

echo "=== Face Verification Table ===\n";
try {
    $result = $conn->query("SHOW TABLES LIKE 'tblfaceverification'");
    if ($result->rowCount() > 0) {
        echo "Table exists: Yes\n";
        
        // Check structure
        $cols = $conn->query("SHOW COLUMNS FROM tblfaceverification");
        echo "\nColumns:\n";
        while ($col = $cols->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$col['Field']}: {$col['Type']}\n";
        }
        
        // Check records
        $stmt = $conn->query("SELECT COUNT(*) as total FROM tblfaceverification");
        $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        echo "\nTotal records: $total\n";
        
        // Check verified records
        $stmt = $conn->query("SELECT COUNT(*) as verified FROM tblfaceverification WHERE verification_status = 'verified'");
        $verified = $stmt->fetch(PDO::FETCH_ASSOC)['verified'];
        echo "Verified records: $verified\n";
        
        if (isset($_SESSION['temp_uid'])) {
            $stmt = $conn->prepare("
                SELECT id, verification_status, LENGTH(face_image) as img_size, 
                       verification_date
                FROM tblfaceverification 
                WHERE user_id = ?
                ORDER BY verification_date DESC
            ");
            $stmt->execute([$_SESSION['temp_uid']]);
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nUser's face records:\n";
            foreach ($records as $record) {
                echo "- ID: {$record['id']}\n";
                echo "  Status: {$record['verification_status']}\n";
                echo "  Image size: {$record['img_size']} bytes\n";
                echo "  Date: {$record['verification_date']}\n\n";
            }
        }
    } else {
        echo "Table exists: No\n";
    }
} catch (Exception $e) {
    echo "Error checking table: " . $e->getMessage() . "\n";
}

echo "\n=== PHP Info ===\n";
echo "PHP version: " . phpversion() . "\n";
echo "Memory limit: " . ini_get('memory_limit') . "\n";
echo "Max execution time: " . ini_get('max_execution_time') . "s\n";
echo "Upload max filesize: " . ini_get('upload_max_filesize') . "\n";
echo "Post max size: " . ini_get('post_max_size') . "\n"; 