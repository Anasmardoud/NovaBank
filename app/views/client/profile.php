<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /NovaBank/public/login');
    exit();
}
$currentPage = 'profile';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Profile Settings - Nova Bank</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-university"></i> Nova Bank</h2>
            <nav>
                <ul>
                    <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                    </li>
                    <li class="<?php echo $currentPage === 'accounts' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/accounts"><i class="fas fa-briefcase"></i> Accounts</a>
                    </li>
                    <li class="<?php echo $currentPage === 'transaction' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/transaction"><i class="fas fa-exchange-alt"></i> Transaction</a>
                    </li>
                    <li class="<?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/profile"><i class="fas fa-user"></i> Profile</a>
                    </li>
                    <li class="<?php echo $currentPage === 'loans' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/loans"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                    </li>
                    <li>
                        <a href="/NovaBank/public/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>
        <main class="nova-main-content">
            <header class="header">
                <div class="user-info">
                    <?php if ($profilePicture): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-picture"></i>
                    <?php endif; ?>
                    <h1>Profile Settings, <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </div>
                <span class="date">Today: <?= date('F j, Y') ?></span>
            </header>
            <div class="profile-settings-container">
                <div class="profile-settings-card green-theme">
                    <h2>Edit Profile Information</h2>
                    <form action="/NovaBank/public/client/editProfile" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to do this changes?');">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" value="<?= htmlspecialchars($client['username']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" value="<?= htmlspecialchars($client['phone_number']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars($client['address']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">New Password (leave blank to keep current):</label>
                            <div class="password-input-container">
                                <input type="password" id="password" name="password">
                                <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('password')"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password:</label>
                            <div class="password-input-container">
                                <input type="password" id="confirm_password" name="confirm_password">
                                <i class="fas fa-eye password-toggle" onclick="togglePasswordVisibility('confirm_password')"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="profile_picture">Profile Picture:</label>
                            <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
                        </div>
                        <button type="submit" class="nb-save-btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <!-- Toast Container -->
    <div id="toast-container">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="toast success">
                <span class="toast-message"><?= $_SESSION['success'] ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="toast error">
                <span class="toast-message"><?= $_SESSION['error'] ?></span>
                <button class="toast-close" onclick="this.parentElement.remove()">×</button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <script src="/NovaBank/public/assets/js/client.js"></script>
</body>

</html>