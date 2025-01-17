<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect to MySQL without selecting a database
    $conn = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Create database if it doesn't exist
    $dbname = "birth_death_db";
    $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database created or already exists<br>";

    // Select the database
    $conn->exec("USE `$dbname`");

    // Read and execute the SQL file
    $sql = file_get_contents('fix_database.sql');
    if ($sql === false) {
        throw new Exception("Error reading SQL file");
    }

    // Split SQL file into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (!empty($statement)) {
            $conn->exec($statement);
            echo "Executed: " . substr($statement, 0, 50) . "...<br>";
        }
    }

    echo "Database setup completed successfully!<br>";
    echo "<a href='admin_login.php'>Go to Admin Login</a>";

} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?> 