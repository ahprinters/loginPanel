<?php
require_once 'database/config.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Check if role column exists
    $query = "SHOW COLUMNS FROM users LIKE 'role'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        // Add role column if it doesn't exist
        $sql = "ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user'";
        $db->exec($sql);
        
        // Update existing users to have 'user' role
        $sql = "UPDATE users SET role = 'user' WHERE role IS NULL";
        $db->exec($sql);
        
        echo "Column 'role' added successfully and existing users updated";
    } else {
        echo "Column 'role' already exists";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>