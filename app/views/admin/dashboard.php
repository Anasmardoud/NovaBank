<?php

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

include __DIR__ . '/../layouts/header.php';

// Set the current page
$currentPage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Admin Dashboard</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-university"></i> Nova Bank</h2>
            <nav>
                <ul>
                    <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="<?php echo $currentPage === 'create_client' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/create_client"><i class="fas fa-user-plus"></i> Create Client</a>
                    </li>
                    <li class="<?php echo $currentPage === 'clients' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/clients"><i class="fas fa-users"></i> Clients</a>
                    </li>
                    <li class="<?php echo $currentPage === 'deposit' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/deposit"><i class="fas fa-wallet"></i> Deposit</a>
                    </li>
                    <li class="<?php echo $currentPage === 'loans' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/loans"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                    </li>
                    <li class="<?php echo $currentPage === 'notifications' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/notifications"><i class="fas fa-bell"></i> Notifications</a>
                    </li>
                    <li>
                        <a href="/PHPLearning/NovaBank/public/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Admin Information -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Your Information</h2>
                    <p><strong>Username:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                    <p><strong>Role:</strong> Admin</p>
                    <p><strong>Created At:</strong> <?php echo htmlspecialchars($_SESSION['created_at']); ?></p>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Change Password</h2>
                    <form action="/PHPLearning/NovaBank/public/admin/change_password" method="POST" onsubmit="return validatePassword()">
                        <div class="form-group">
                            <label for="current-password">Current Password:</label>
                            <div class="password-container">
                                <input type="password" id="current-password" name="current_password" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('current-password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-group">
                                <label for="new-password">New Password:</label>
                                <div class="password-container">
                                    <input type="password" id="new-password" name="new_password" required>
                                    <span class="toggle-password" onclick="togglePasswordVisibility('new-password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div id="password-strength-message" class="password-strength-message"></div>
                            </div>
                            <div class="form-group">
                                <label for="confirm-password">Confirm New Password:</label>
                                <div class="password-container">
                                    <input type="password" id="confirm-password" name="confirm_password" required>
                                    <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn">Change Password</button>
                            </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script src="/PHPLearning/NovaBank/public/assets/js/admin.js"></script>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>