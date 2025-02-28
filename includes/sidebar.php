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
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
            <a href="dashboard.php">
                <i class="fas fa-home"></i> Dashboard
            </a>
        </li>
        <li class="<?php echo basename($_SERVER['PHP_SELF']) === 'profile.php' ? 'active' : ''; ?>">
            <a href="profile.php">
                <i class="fas fa-user"></i> Profile
            </a>
        </li>
        <li>
            <a href="#userSubmenu" data-bs-toggle="collapse" class="dropdown-toggle">
                <i class="fas fa-users"></i> User Management
            </a>
            <ul class="collapse list-unstyled <?php echo in_array(basename($_SERVER['PHP_SELF']), ['users-list.php', 'add-user.php', 'user-roles.php']) ? 'show' : ''; ?>" id="userSubmenu">
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
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav> 