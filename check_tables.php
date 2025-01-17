<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'config.php';
require_once 'dbconfig.php';

echo "<h2>Checking Database Tables</h2>";

try {
    // Check tbluserdetails
    $stmt = $conn->query("SHOW CREATE TABLE tbluserdetails");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h3>tbluserdetails structure:</h3>";
    echo "<pre>" . htmlspecialchars($result['Create Table']) . "</pre>";

    // Check if any records exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbluserdetails");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>Number of records in tbluserdetails: " . $count . "</p>";

    // Check tbluser
    $stmt = $conn->query("SHOW CREATE TABLE tbluser");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<h3>tbluser structure:</h3>";
    echo "<pre>" . htmlspecialchars($result['Create Table']) . "</pre>";

    // Check if any records exist
    $stmt = $conn->query("SELECT COUNT(*) as count FROM tbluser");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo "<p>Number of records in tbluser: " . $count . "</p>";

    // Check for any foreign key constraints
    echo "<h3>Foreign Key Relationships:</h3>";
    $stmt = $conn->query("
        SELECT 
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = '" . DB_NAME . "'
            AND TABLE_NAME IN ('tbluserdetails', 'tbluser')
    ");
    $constraints = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "<pre>" . print_r($constraints, true) . "</pre>";

} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?> 