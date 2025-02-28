<?php
session_start();
require_once 'database/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new Database();
    $db = $database->getConnection();

    try {
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Prepare query
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $user['password'])) {
                // Password is correct, create session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                // Redirect to dashboard or home page
                header("Location: dashboard.php");
                exit();
            } else {
                header("Location: index.php?error=invalid_credentials");
                exit();
            }
        } else {
            header("Location: index.php?error=invalid_credentials");
            exit();
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>