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
$userData = $user->getUserById($_SESSION['user_id']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $user->updateProfile($_POST, $_FILES);
        header("Location: profile.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <style>
        .profile-cover {
            height: 200px;
            background-color: #2c3e50;
            background-image: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
        }
        .profile-image-container {
            margin-top: -75px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border: 5px solid #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .profile-stats {
            border-right: 1px solid #dee2e6;
        }
        .profile-stats:last-child {
            border-right: none;
        }
    </style>
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
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h1 class="h3 mb-0 text-gray-800">Profile Settings</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Profile</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="dashboard.php" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                        <i class="fas fa-tachometer-alt fa-sm me-2"></i>Back to Dashboard
                    </a>
                </div>

                <!-- Dashboard Button (mobile only) -->
                <div class="d-sm-none mb-4">
                    <a href="dashboard.php" class="btn btn-primary w-100">
                        <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                    </a>
                </div>

                <!-- Profile Cover -->
                <div class="profile-cover"></div>

                <!-- Profile Information -->
                <div class="container">
                    <div class="row">
                        <div class="col-lg-8 mx-auto">
                            <!-- Profile Image and Basic Info -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-body text-center">
                                    <div class="profile-image-container">
                                        <img src="<?php echo !empty($userData['profile_image']) ? htmlspecialchars($userData['profile_image']) : 'uploads/default-avatar.png'; ?>" 
                                             class="profile-image rounded-circle mb-3"
                                             alt="Profile Image">
                                        <button class="btn btn-primary btn-sm position-absolute" 
                                                style="bottom: 0; right: 0;"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageUploadModal">
                                            <i class="fas fa-camera"></i>
                                        </button>
                                    </div>
                                    <h4 class="mb-0"><?php echo htmlspecialchars($userData['fullname']); ?></h4>
                                    <p class="text-muted"><?php echo htmlspecialchars($userData['role']); ?></p>
                                    
                                    <!-- Profile Stats -->
                                    <div class="row mt-4">
                                        <div class="col-4 profile-stats">
                                            <h6>Role</h6>
                                            <p class="text-muted mb-0"><?php echo ucfirst(htmlspecialchars($userData['role'])); ?></p>
                                        </div>
                                        <div class="col-4 profile-stats">
                                            <h6>Joined</h6>
                                            <p class="text-muted mb-0"><?php echo date('M Y', strtotime($userData['created_at'])); ?></p>
                                        </div>
                                        <div class="col-4 profile-stats">
                                            <h6>Status</h6>
                                            <p class="text-muted mb-0">Active</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Details Form -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold">Profile Information</h6>
                                    <a href="dashboard.php" class="btn btn-primary btn-sm">
                                        <i class="fas fa-tachometer-alt me-2"></i>Back to Dashboard
                                    </a>
                                </div>
                                <div class="card-body">
                                    <?php if (isset($_GET['success'])): ?>
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            Profile updated successfully!
                                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                        </div>
                                    <?php endif; ?>

                                    <form action="" method="POST" class="needs-validation" novalidate>
                                        <input type="hidden" name="update_profile" value="1">
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       name="fullname" 
                                                       value="<?php echo htmlspecialchars($userData['fullname']); ?>" 
                                                       required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Username</label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       value="<?php echo htmlspecialchars($userData['username']); ?>" 
                                                       readonly>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" 
                                                   class="form-control" 
                                                   name="email" 
                                                   value="<?php echo htmlspecialchars($userData['email']); ?>" 
                                                   required>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label">Bio</label>
                                            <textarea class="form-control" 
                                                      name="bio" 
                                                      rows="3"><?php echo htmlspecialchars($userData['bio'] ?? ''); ?></textarea>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Change Password Card -->
                            <div class="card shadow-sm">
                                <div class="card-header bg-white py-3">
                                    <h6 class="m-0 font-weight-bold">Change Password</h6>
                                </div>
                                <div class="card-body">
                                    <form action="" method="POST" class="needs-validation" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Current Password</label>
                                            <input type="password" class="form-control" name="current_password" required>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label">New Password</label>
                                                <input type="password" class="form-control" name="new_password" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm New Password</label>
                                                <input type="password" class="form-control" name="confirm_password" required>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-key me-2"></i>Change Password
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Upload Modal -->
    <div class="modal fade" id="imageUploadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Profile Picture</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="upload_profile_image.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Choose Image</label>
                            <input type="file" class="form-control" name="profile_image" accept="image/*" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload me-2"></i>Upload Image
                        </button>
                    </form>
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