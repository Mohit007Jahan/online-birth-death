<?php
require_once 'config.php';
require_once 'dbconfig.php';

try {
    // Add face_embedding column if it doesn't exist
    $sql = "ALTER TABLE tblfaceverification 
            ADD COLUMN IF NOT EXISTS face_embedding LONGTEXT AFTER face_image";
    $conn->exec($sql);
    
    // Check if primary key exists
    $result = $conn->query("SHOW KEYS FROM tblfaceverification WHERE Key_name = 'PRIMARY'");
    if ($result->rowCount() == 0) {
        $conn->exec("ALTER TABLE tblfaceverification 
                    MODIFY id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY");
    }
    
    // Check if foreign key exists
    $result = $conn->query("SELECT * FROM information_schema.TABLE_CONSTRAINTS 
                           WHERE CONSTRAINT_TYPE = 'FOREIGN KEY' 
                           AND TABLE_NAME = 'tblfaceverification'");
    if ($result->rowCount() == 0) {
        $conn->exec("ALTER TABLE tblfaceverification 
                    ADD CONSTRAINT tblfaceverification_ibfk_1 
                    FOREIGN KEY (user_id) REFERENCES tbluser (ID) ON DELETE CASCADE");
    }
    
    echo "Database updated successfully";
} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage();
}
?> 