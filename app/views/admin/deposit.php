<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'deposit';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Deposit Management - Nova Bank</title>
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
                    <li class="<?php echo $currentPage === 'create_account' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/admin/client-creation-homepage"><i class="fas fa-user-plus"></i> Create Account</a>
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
                <h1>Deposit Management</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>
            <div class="content-section">
                <div class="dash-border">
                    <h2>Deposit History</h2>
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Account Type</th>
                                <th>Admin Name</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($deposits as $deposit): ?>
                                <tr>
                                    <td><?= htmlspecialchars($deposit['client_name']) ?></td>
                                    <td><?= htmlspecialchars($deposit['account_type']) ?></td>
                                    <td><?= htmlspecialchars($deposit['admin_name']) ?></td>
                                    <td><?= htmlspecialchars($deposit['amount']) ?></td>
                                    <td><?= htmlspecialchars($deposit['created_at']) ?></td>
                                    <td><?= htmlspecialchars($deposit['status']) ?></td>
                                    <td class="actions-container">
                                        <!-- Edit Form -->
                                        <form action="/NovaBank/public/admin/edit-deposit" method="POST" style="display: inline;">
                                            <input type="hidden" name="deposit_id" value="<?= $deposit['deposit_id'] ?>">
                                            <button type="submit" class="btn btn-edit">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </form>

                                        <!-- Delete Form -->
                                        <form action="/NovaBank/public/admin/delete-deposit" method="POST" onsubmit="return confirm('Are you sure you want to delete this deposit?');" style="display: inline;">
                                            <input type="hidden" name="deposit_id" value="<?= $deposit['deposit_id'] ?>">
                                            <button type="submit" class="btn btn-delete">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Create Deposit Form -->
            <div class="content-section">
                <div class="create-deposit-form">
                    <h2>Create New Deposit</h2>
                    <form action="/NovaBank/public/admin/create-deposit" method="POST" onsubmit="return validateDepositForm()">
                        <div class="form-group">
                            <label for="account_id">Account:</label>
                            <select id="account_id" name="account_id" required>
                                <option value="">Select an account</option>
                                <?php foreach ($activeAccounts as $account): ?>
                                    <option value="<?= $account['account_id'] ?>">
                                        <?= htmlspecialchars($account['account_number']) ?> - <?= htmlspecialchars($account['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="number" id="amount" name="amount" step="0.01" min="0" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn">Create Deposit</button>
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

    </div>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
    <script src="/NovaBank/public/assets/js/admin.js"></script>
</body>

</html>