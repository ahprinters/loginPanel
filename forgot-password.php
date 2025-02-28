<?php
session_start();
require_once 'database/config.php';
require_once 'classes/User.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $email = $_POST['email'];
    
    // Check if email exists
    if ($user->emailExists($email)) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        if ($user->setResetToken($email, $token, $expiry)) {
            // Send reset email
            $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
            $to = $email;
            $subject = "Password Reset Request";
            $message = "Hello,\n\nYou have requested to reset your password. Click the link below to reset it:\n\n";
            $message .= $resetLink . "\n\n";
            $message .= "This link will expire in 1 hour.\n\n";
            $message .= "If you didn't request this, please ignore this email.\n\n";
            $message .= "Best regards,\nYour Application Team";
            $headers = "From: noreply@yourwebsite.com";

            if (mail($to, $subject, $message, $headers)) {
                $success = "Password reset instructions have been sent to your email.";
            } else {
                $error = "Error sending email. Please try again.";
            }
        } else {
            $error = "Error generating reset token. Please try again.";
        }
    } else {
        // Don't reveal if email exists or not for security
        $success = "If your email exists in our system, you will receive password reset instructions.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                            <h3 class="font-weight-bold">Forgot Password?</h3>
                            <p class="text-muted">Enter your email to reset your password</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>

                        <form action="" method="post">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required 
                                       placeholder="Enter your email">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Reset Password
                            </button>
                            <div class="text-center">
                                <a href="index.php" class="text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 