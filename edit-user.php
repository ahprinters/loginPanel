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

// Get current user data for navbar/sidebar
$userData = $user->getUserById($_SESSION['user_id']);

$error = '';
$success = '';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: users-list.php");
    exit();
}

// Get user to edit
$editUser = $user->getUserById($_GET['id']);
if (!$editUser) {
    header("Location: users-list.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $updateData = [
            'id' => $_GET['id'],
            'fullname' => $_POST['fullname'],
            'email' => $_POST['email'],
            'role' => $_POST['role'],
            'username' => $_POST['username']
        ];

        // Only include password if it's being changed
        if (!empty($_POST['password'])) {
            if ($_POST['password'] !== $_POST['confirm_password']) {
                throw new Exception("Passwords do not match");
            }
            $updateData['password'] = $_POST['password'];
        }

        if ($user->updateUser($updateData)) {
            $success = "User updated successfully!";
            $editUser = $user->getUserById($_GET['id']); // Refresh user data
        } else {
            throw new Exception("Error updating user");
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <?php require_once 'includes/navbar.php'; ?>

            <!-- Main Content -->
            <div class="container-fluid py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow">
                            <div class="card-header py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold">Edit User</h6>
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
                                                   value="<?php echo htmlspecialchars($editUser['fullname']); ?>"
                                                   required>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="username" 
                                                   name="username" 
                                                   value="<?php echo htmlspecialchars($editUser['username']); ?>"
                                                   required>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" 
                                               class="form-control" 
                                               id="email" 
                                               name="email" 
                                               value="<?php echo htmlspecialchars($editUser['email']); ?>"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <?php 
                                            // Get current role, default to 'user' if not set
                                            $currentRole = isset($editUser['role']) ? $editUser['role'] : 'user';
                                            ?>
                                            <option value="user" <?php echo $currentRole === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $currentRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            <option value="editor" <?php echo $currentRole === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                        </select>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="password" 
                                                   name="password">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" 
                                                   class="form-control" 
                                                   id="confirm_password" 
                                                   name="confirm_password">
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Changes
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

        // Password confirmation validation
        document.getElementById('password').addEventListener('input', validatePassword);
        document.getElementById('confirm_password').addEventListener('input', validatePassword);

        function validatePassword() {
            const password = document.getElementById('password');
            const confirm = document.getElementById('confirm_password');
            
            if (password.value !== confirm.value) {
                confirm.setCustomValidity("Passwords don't match");
            } else {
                confirm.setCustomValidity('');
            }
        }

        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html> 