<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Nova Bank</title>
    <link rel="stylesheet" href="/NovaBank/public/assets/css/login.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <!-- Include Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php
    $currentPage = 'login'; // Set the current page
    include __DIR__ . '/../layouts/header.php';
    ?>

    <!-- Rest of the login page content -->
    <main>
        <div class="login-grid">
            <!-- Left Column: Image -->
            <div class="login-image">
                <img src="/NovaBank/public/assets/images/login-image.jpg" alt="Login Image">
            </div>

            <!-- Right Column: Login Form -->
            <div class="login-form">
                <h2>Login to Nova Bank</h2>
                <form action="/NovaBank/public/login_algorithm" method="POST">
                    <!-- Account Type Dropdown -->
                    <div class="form-group">
                        <label for="account-type">Choose Your Account Type:</label>
                        <select id="account-type" name="account-type" required>
                            <option value="admin">Admin</option>
                            <option value="client">Client</option>
                        </select>
                    </div>

                    <!-- Username Field -->
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group password-container">
                        <label for="client-password">Password:</label>
                        <div class="password-input">
                            <input type="password" id="client-password" name="client-password" placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePasswordVisibility('client-password')">
                                <i class="eye-icon fas fa-eye-slash"></i> <!-- Default: closed eye -->
                            </span>
                        </div>
                    </div>

                    <!-- Login Button -->
                    <div class="form-group">
                        <button type="submit" class="login-button">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
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
    <script src="/NovaBank/public/assets/js/login.js"></script>
</body>

</html>