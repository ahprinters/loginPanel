<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Login Panel</title>
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-lg">
                    <div class="card-body p-5">
                        <h1 class="text-center mb-4 fw-bold">Welcome Back</h1>
                        
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <!-- Show Dashboard button if user is logged in -->
                            <div class="text-center mb-4">
                                <a href="dashboard.php" class="btn btn-primary w-100">
                                    <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- Show login form if user is not logged in -->
                            <form action="login.php" method="post">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-2 mt-3">Login</button>
                            </form>
                        <?php endif; ?>

                        <div class="text-center mt-4">
                            <a href="forgot-password.php" class="text-decoration-none me-3">Forgot Password?</a>
                            <a href="register.php" class="text-decoration-none">Create Account</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>