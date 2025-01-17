<?php
session_start();
require_once 'dbconfig.php';

// Check if admin or user is logged in
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['uid'])) {
    header("HTTP/1.1 403 Forbidden");
    exit('Access denied');
}

// Validate file parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    header("HTTP/1.1 400 Bad Request");
    exit('File parameter is required');
}

// Validate file type
if (!isset($_GET['type']) || empty($_GET['type'])) {
    header("HTTP/1.1 400 Bad Request");
    exit('Type parameter is required');
}

$file = $_GET['file'];
$type = $_GET['type'];

// Prevent directory traversal
$file = basename($file);

// Define allowed file types and their directories
$allowedTypes = [
    'hospital' => 'uploads/hospital_papers/',
    'nid' => 'uploads/nid_documents/',
    'parents' => 'uploads/parents_documents/',
    'death' => 'uploads/death_documents/'
];

// Validate file type
if (!array_key_exists($type, $allowedTypes)) {
    header("HTTP/1.1 400 Bad Request");
    exit('Invalid file type');
}

// Construct the file path
$filePath = $allowedTypes[$type] . $file;

// Check if file exists
if (!file_exists($filePath)) {
    header("HTTP/1.1 404 Not Found");
    exit('File not found');
}

// Get file mime type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $filePath);
finfo_close($finfo);

// Set headers for file download
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $file . '"');
header('Content-Length: ' . filesize($filePath));
header('Cache-Control: public, must-revalidate, max-age=0');
header('Pragma: public');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');

// Output file
readfile($filePath);
exit(); 