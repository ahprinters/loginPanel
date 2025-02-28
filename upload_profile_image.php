<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database/config.php';
require_once 'classes/User.php';

if (!isset($_SESSION['user_id'])) {
    die('Unauthorized');
}

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($user->updateProfile($_POST, $_FILES)) {
            header('Location: dashboard.php?success=profile_updated');
        } else {
            header('Location: dashboard.php?error=update_failed');
        }
    } catch (Exception $e) {
        header('Location: dashboard.php?error=' . urlencode($e->getMessage()));
    }
    exit();
}
?> 