<?php
require_once 'database/config.php';

$database = new Database();
$db = $database->getConnection();

try {
    $sql = "ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT 'uploads/default-avatar.png'";
    $db->exec($sql);
    echo "Column added successfully";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 