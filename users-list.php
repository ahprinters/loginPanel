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

// Handle user deletion
if (isset($_POST['delete_user'])) {
    $user->deleteUser($_POST['delete_user']);
}

// Get all users
$users = $user->getAllUsers();

// Check if the required directories exist
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Check if default avatar exists
$defaultAvatar = 'uploads/default-avatar.png';
if (!file_exists($defaultAvatar)) {
    // You might want to copy a default avatar image here
    // or use a different default image path
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users List - Admin Panel</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- Include sidebar -->
        <?php require_once 'includes/sidebar.php'; ?>

        <!-- Page Content -->
        <div id="content">
            <!-- Include navbar -->
            <?php require_once 'includes/navbar.php'; ?>

            <!-- Main Content -->
            <div class="container-fluid py-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold">Users List</h6>
                        <a href="add-user.php" class="btn btn-primary btn-sm">
                            <i class="fas fa-user-plus me-2"></i>Add New User
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                User updated successfully!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Profile</th>
                                        <th>Full Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user_data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user_data['id']); ?></td>
                                            <td>
                                                <img src="<?php echo !empty($user_data['profile_image']) ? htmlspecialchars($user_data['profile_image']) : 'uploads/default-avatar.png'; ?>" 
                                                     class="rounded-circle" 
                                                     alt="Profile" 
                                                     width="40" 
                                                     height="40"
                                                     onerror="this.src='uploads/default-avatar.png'">
                                            </td>
                                            <td><?php echo htmlspecialchars($user_data['fullname']); ?></td>
                                            <td><?php echo htmlspecialchars($user_data['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user_data['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user_data['role'] ?? 'user'); ?></td>
                                            <td><?php echo isset($user_data['created_at']) ? date('M d, Y', strtotime($user_data['created_at'])) : 'N/A'; ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="edit-user.php?id=<?php echo $user_data['id']; ?>" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?php if ($user_data['id'] != $_SESSION['user_id']): ?>
                                                        <button type="button" 
                                                                class="btn btn-danger btn-sm" 
                                                                title="Delete"
                                                                onclick="confirmDelete(<?php echo $user_data['id']; ?>, '<?php echo htmlspecialchars($user_data['username']); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete user: <strong id="deleteUserName"></strong>?</p>
                    <p class="text-danger mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        This action cannot be undone!
                    </p>
                </div>
                <div class="modal-footer">
                    <form action="" method="POST">
                        <input type="hidden" name="delete_user" id="deleteUserId">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Delete User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#usersTable').DataTable({
                "order": [[0, "desc"]],
                "pageLength": 10,
                "language": {
                    "search": "Search users:",
                    "lengthMenu": "Show _MENU_ users per page",
                }
            });
        });

        function confirmDelete(userId, username) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserName').textContent = username;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        // Add success toast notification after delete
        <?php if (isset($_POST['delete_user'])): ?>
            const toast = new bootstrap.Toast(document.createElement('div'));
            toast.innerHTML = `
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div class="toast show bg-success text-white" role="alert">
                        <div class="toast-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong class="me-auto">Success</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                        </div>
                        <div class="toast-body">
                            User has been deleted successfully!
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        <?php endif; ?>

        // Sidebar toggle
        document.getElementById('sidebarCollapse').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
</body>
</html> 