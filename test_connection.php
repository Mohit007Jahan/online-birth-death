<?php
// Enable error reporting for testing
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Try to include config file
try {
    require_once 'config.php';
    echo "Config file loaded successfully<br>";
} catch (Exception $e) {
    die("Error loading config file: " . $e->getMessage());
}

// Display configuration (without passwords)
echo "Database Host: " . DB_HOST . "<br>";
echo "Database Name: " . DB_NAME . "<br>";

// Try database connection
try {
    $conn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    echo "Database connection successful<br>";

    // Test query
    $stmt = $conn->query("SHOW TABLES");
    echo "Tables in database:<br>";
    while ($row = $stmt->fetch()) {
        echo "- " . $row[0] . "<br>";
    }
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if required tables exist
$requiredTables = ['tbluser', 'tbladmin', 'tblapplication', 'tbldeathapplication'];
foreach ($requiredTables as $table) {
    $stmt = $conn->query("SHOW TABLES LIKE '$table'");
    if ($stmt->rowCount() > 0) {
        echo "Table $table exists<br>";
    } else {
        echo "Table $table does not exist<br>";
    }
}
?> 