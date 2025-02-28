<?php
// Include database connection
require_once 'database/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get database connection
    $database = new Database();
    $db = $database->getConnection();

    try {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Validate passwords match
        if ($password !== $confirm_password) {
            die("Passwords do not match!");
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if username or email already exists
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            die("Username or email already exists!");
        }

        // Insert new user
        $sql = "INSERT INTO users (fullname, email, username, password) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$fullname, $email, $username, $hashed_password]);

        // Redirect to login page
        header("Location: index.php?registration=success");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?> 