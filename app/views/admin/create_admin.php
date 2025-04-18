<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'create_admin';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Create Admin</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-university"></i> Nova Bank</h2>
            <nav>
                <ul>
                    <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="<?php echo $currentPage === 'create_admin' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/create-admin"><i class="fas fa-user-plus"></i> Create Admin</a>
                    </li>
                    <li class="<?php echo $currentPage === 'create_client' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/client-creation-homepage"><i class="fas fa-user-plus"></i> Create Client</a>
                    </li>
                    <li class="<?php echo $currentPage === 'clients' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/clients"><i class="fas fa-users"></i> Clients</a>
                    </li>
                    <li class="<?php echo $currentPage === 'deposit' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/deposit"><i class="fas fa-wallet"></i> Deposit</a>
                    </li>
                    <li class="<?php echo $currentPage === 'loans' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/loans"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                    </li>
                    <li>
                        <a href="/NovaBank/public/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Create Admin</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Admin Creation Form -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Create a New Admin</h2>
                    <form action="/NovaBank/public/admin/create-admin" method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="password-container">
                                <input type="password" id="new-password" name="password" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <div id="password-strength-message" class="password-strength-message"></div>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password:</label>
                            <div class="password-container">
                                <input type="password" id="confirm-password" name="confirm_password" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">Create Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="toast success">
                <span class="toast-message"><?php echo $_SESSION['success']; ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="toast error">
                <span class="toast-message"><?php echo $_SESSION['error']; ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <script src="/NovaBank/public/assets/js/admin.js"></script>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>