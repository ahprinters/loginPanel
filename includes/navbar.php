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