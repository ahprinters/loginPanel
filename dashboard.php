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

// Get current page for active sidebar item
$currentPage = basename($_SERVER['PHP_SELF']);

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user->updateProfile($_POST, $_FILES);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar" class="active">
            <div class="sidebar-header">
                <div class="d-flex align-items-center">
                    <img src="<?php echo isset($userData['profile_image']) ? $userData['profile_image'] : 'uploads/default-avatar.png'; ?>" 
                         class="rounded-circle profile-image-small" 
                         alt="Profile Image">
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo htmlspecialchars($userData['fullname']); ?></h6>
                        <small class="text-muted">Administrator</small>
                    </div>
                </div>
            </div>

            <ul class="list-unstyled components">
                <li class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                    <a href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>">
                    <a href="profile.php">
                        <i class="fas fa-user"></i> Profile
                    </a>
                </li>
                <li>
                    <a href="#userSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-users"></i> User Management
                    </a>
                    <ul class="collapse list-unstyled" id="userSubmenu">
                        <li>
                            <a href="users-list.php"><i class="fas fa-list"></i> Users List</a>
                        </li>
                        <li>
                            <a href="add-user.php"><i class="fas fa-user-plus"></i> Add User</a>
                        </li>
                        <li>
                            <a href="user-roles.php"><i class="fas fa-user-shield"></i> Roles</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#settingsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <ul class="collapse list-unstyled" id="settingsSubmenu">
                        <li>
                            <a href="general-settings.php"><i class="fas fa-sliders-h"></i> General</a>
                        </li>
                        <li>
                            <a href="security-settings.php"><i class="fas fa-shield-alt"></i> Security</a>
                        </li>
                        <li>
                            <a href="notification-settings.php"><i class="fas fa-bell"></i> Notifications</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="reports.php">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </li>
                <li>
                    <a href="messages.php">
                        <i class="fas fa-envelope"></i> Messages
                        <span class="badge bg-primary rounded-pill ms-2">3</span>
                    </a>
                </li>
                <li>
                    <a href="tasks.php">
                        <i class="fas fa-tasks"></i> Tasks
                        <span class="badge bg-warning rounded-pill ms-2">5</span>
                    </a>
                </li>
                <li>
                    <a href="calendar.php">
                        <i class="fas fa-calendar"></i> Calendar
                    </a>
                </li>
                <li>
                    <a href="help.php">
                        <i class="fas fa-question-circle"></i> Help & Support
                    </a>
                </li>
            </ul>

            <div class="sidebar-footer">
                <a href="logout.php" class="btn btn-danger w-100">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>

        <!-- Page Content -->
        <div id="content">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                <div class="container-fluid">
                    <button type="button" id="sidebarCollapse" class="btn btn-primary">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="d-flex align-items-center">
                        <div class="dropdown">
                            <button class="btn btn-link dropdown-toggle" type="button" id="notificationDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <span class="badge bg-danger rounded-pill">3</span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown">
                                <li><a class="dropdown-item" href="#">New message from John</a></li>
                                <li><a class="dropdown-item" href="#">System update completed</a></li>
                                <li><a class="dropdown-item" href="#">New user registered</a></li>
                            </ul>
                        </div>

                        <div class="dropdown ms-3">
                            <button class="btn btn-link dropdown-toggle d-flex align-items-center" type="button" 
                                    id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="<?php echo isset($userData['profile_image']) ? $userData['profile_image'] : 'uploads/default-avatar.png'; ?>" 
                                     class="rounded-circle profile-image-tiny" alt="Profile">
                                <span class="ms-2"><?php echo htmlspecialchars($userData['username']); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid py-4">
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Total Users</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">250</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Active Tasks</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">5</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-info shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                            Messages</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">3</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-envelope fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Notifications</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-bell fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional content sections can go here -->
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar toggle
            document.getElementById('sidebarCollapse').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });
        });
    </script>
</body>
</html> 