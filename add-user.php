<?php
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Include database connection and User class
require_once 'database/config.php';
require_once 'classes/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $userData = [
            'fullname' => $_POST['fullname'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password'],
            'role' => $_POST['role'] ?? 'user'
        ];

        // Validate passwords match
        if ($userData['password'] !== $userData['confirm_password']) {
            throw new Exception("Passwords do not match");
        }

        // Create new user
        if ($user->createUser($userData)) {
            $success = "User created successfully!";
        } else {
            throw new Exception("Error creating user");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get current user data for navbar/sidebar
$userData = $user->getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <?php include 'includes/navbar.php'; ?>

            <!-- Main Content -->
            <div class="container-fluid py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold">Add New User</h6>
                                    <a href="users-list.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Users
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
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

                                <form action="" method="POST" class="needs-validation" novalidate>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="fullname" class="form-label">Full Name</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="fullname" 
                                                   name="fullname" 
                                                   required>
                                            <div class="invalid-feedback">
                                                Please enter full name
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="username" 
                                                   name="username" 
                                                   required>
                                            <div class="invalid-feedback">
                                                Please enter username
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               required>
                                        <div class="invalid-feedback">
                                            Please enter a valid email
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password" 
                                                   required>
                                            <div class="invalid-feedback">
                                                Please enter password
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="confirm_password" 
                                                   name="confirm_password" 
                                                   required>
                                            <div class="invalid-feedback">
                                                Please confirm password
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="user">User</option>
                                            <option value="admin">Admin</option>
                                            <option value="editor">Editor</option>
                                        </select>
                                        <div class="invalid-feedback">
                                            Please select a role
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-user-plus me-2"></i>Create User
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html> 