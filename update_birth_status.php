<?php
session_start();
require_once 'dbconfig.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if required parameters are present
if (!isset($_POST['id']) || !isset($_POST['status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$id = $_POST['id'];
$status = $_POST['status'];

// Validate status
$valid_statuses = ['Pending', 'Approved', 'Rejected'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // First verify if the application exists
    $check_stmt = $conn->prepare("SELECT id FROM tblbirthapplications WHERE id = ?");
    $check_stmt->execute([$id]);
    
    if (!$check_stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Application not found']);
        exit();
    }

    // Update the status - removed updated_at since it doesn't exist in the table
    $stmt = $conn->prepare("UPDATE tblbirthapplications SET status = ? WHERE id = ?");
    $result = $stmt->execute([$status, $id]);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update status']);
    }
} catch (PDOException $e) {
    error_log("Error updating birth application status: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} 