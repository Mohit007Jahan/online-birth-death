<?php
session_start();
require_once 'dbconfig.php';

// Check if user is logged in or if it's an admin request with user_id
if (!isset($_SESSION['uid']) && !isset($_GET['user_id']) && !isset($_SESSION['admin_id'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

try {
    // Determine which user_id to use
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['uid'];

    // If it's an admin request with user_id, verify admin is logged in
    if (isset($_GET['user_id']) && !isset($_SESSION['admin_id'])) {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }

    // Get face image
    $stmt = $conn->prepare("
        SELECT face_image 
        FROM tblfaceverification 
        WHERE user_id = ? 
        AND verification_status = 'verified' 
        ORDER BY verification_date DESC 
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $faceData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($faceData && !empty($faceData['face_image'])) {
        // Extract the base64 data
        $imageData = $faceData['face_image'];
        $imageInfo = explode(';base64,', $imageData);
        
        if (count($imageInfo) === 2) {
            $imageType = str_replace('data:', '', $imageInfo[0]);
            $imageContent = base64_decode($imageInfo[1]);
            
            // Set proper headers
            header("Content-Type: $imageType");
            header("Cache-Control: public, max-age=86400");
            echo $imageContent;
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Error fetching profile image: " . $e->getMessage());
}

// If we get here, return a default image
header("Location: avatar.png"); 