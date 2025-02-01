<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'create_account';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Create Account - Nova Bank</title>
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
                    <li class="<?php echo $currentPage === 'create_account' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/admin/create_account"><i class="fas fa-user-plus"></i> Create Account</a>
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
                    <li>
                        <a href="/PHPLearning/NovaBank/public/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <h1>Create Account</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Inside the form in create_clients.php -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Create a New Client</h2>
                    <form action="/PHPLearning/NovaBank/public/admin/create-client-algorithm" method="POST" onsubmit="return validateAddClientForm()">
                        <!-- Username -->
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" required>
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required>
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <div class="password-container">
                                <input type="password" id="password" name="password" required>
                                <span class="toggle-password" onclick="togglePasswordVisibility('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="form-group">
                            <label for="phone_number">Phone Number:</label>
                            <input type="text" id="phone_number" name="phone_number" required>
                        </div>

                        <!-- Address -->
                        <div class="form-group">
                            <label for="address">Address:</label>
                            <textarea id="address" name="address" required></textarea>
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status">Status:</label>
                            <select id="status" name="status" required>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Add Accounts Section -->
                        <div class="form-group">
                            <label>Add Accounts:</label>
                            <!-- Checking Account -->
                            <div>
                                <input type="checkbox" id="add_checking_account" name="add_checking_account" value="1" onchange="toggleCheckingAccount()">
                                <label for="add_checking_account">Add Checking Account</label>
                                <div id="checking_balance_group" style="display: none;">
                                    <label for="checking_balance">Checking Balance:</label>
                                    <input type="number" id="checking_balance" name="checking_balance" step="0.01" min="0.00" required>
                                </div>
                            </div>

                            <!-- Savings Account -->
                            <div>
                                <input type="checkbox" id="add_savings_account" name="add_savings_account" value="1" onchange="toggleSavingsAccount()">
                                <label for="add_savings_account">Add Savings Account</label>
                                <div id="savings_balance_group" style="display: none;">
                                    <label for="savings_balance">Savings Balance:</label>
                                    <input type="number" id="savings_balance" name="savings_balance" step="0.01" min="0.00" required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <button type="submit" class="btn">Create Client</button>
                            </div>
                    </form>
                </div>
            </div>

        </main> <!-- Create Account Form -->

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

    <script src="/PHPLearning/NovaBank/public/assets/js/admin.js"></script>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>

</html>