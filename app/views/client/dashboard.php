<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /NovaBank/public/login');
    exit();
}
$currentPage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Client Dashboard - Nova Bank</title>
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
                    <li class="<?php echo $currentPage === 'Profile' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/profile"><i class="fas fa-user"></i>Profile</a>
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
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="header">
                <div class="user-info">
                    <?php if ($profilePicture): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-picture"></i>
                    <?php endif; ?>
                    <h1>Hi, <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </div>
                <span class="date">Today: <?= date('F j, Y') ?></span>
            </header>

            <!-- Accounts Section -->
            <div class="accounts-section">
                <?php foreach ($accounts as $account): ?>
                    <div class="account-card">
                        <i class="fas fa-<?= $account['account_type'] === 'Checking' ? 'wallet' : 'piggy-bank' ?> account-icon"></i>
                        <h3>$<?= number_format($account['balance'], 2) ?></h3>
                        <p>****<?= substr($account['account_number'], -4) ?></p>
                        <p><?= htmlspecialchars($account['account_type']) ?> Account</p>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Income and Expenses Section -->
            <div class="income-expenses-section">
                <h2>Income & Expenses</h2>
                <div class="income-expenses-grid">
                    <div class="income-card">
                        <i class="fas fa-arrow-down income-icon"></i>
                        <h3>Income</h3>
                        <p>$<?= number_format($incomeAndExpenses['income'] ?? 0, 2) ?></p>
                    </div>
                    <div class="expenses-card">
                        <i class="fas fa-arrow-up expenses-icon"></i>
                        <h3>Expenses</h3>
                        <p>$<?= number_format($incomeAndExpenses['expenses'] ?? 0, 2) ?></p>
                    </div>
                </div>
            </div>
            <!-- Latest Transactions -->
            <div class="transactions-section">
                <h2>Latest Transactions</h2>
                <ul>
                    <?php if (!empty($transactions)): ?>
                        <ul class="transaction-list">
                            <?php foreach ($transactions as $transaction): ?>
                                <li class="<?= $transaction['receiver_client_id'] === $clientId ? 'incoming' : 'outgoing' ?>">
                                    <i class="fas fa-<?= $transaction['receiver_client_id'] === $clientId ? 'arrow-down' : 'arrow-up' ?> transaction-icon"></i>
                                    <span class="date"><?= htmlspecialchars($transaction['created_at']) ?></span>
                                    <span class="from">
                                        <?= htmlspecialchars($transaction['sender_username']) ?> (<?= htmlspecialchars($transaction['sender_account_type']) ?>)
                                    </span>
                                    <span class="to">
                                        <?= htmlspecialchars($transaction['receiver_username']) ?> (<?= htmlspecialchars($transaction['receiver_account_type']) ?>)
                                    </span>
                                    <span class="amount">$<?= number_format($transaction['amount'], 2) ?></span>
                                    <i class="fas fa-bell notification-icon"></i>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No transactions found.</p>
                    <?php endif; ?>
                </ul>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container"></div>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="/NovaBank/public/assets/js/client.js"></script>
</body>

</html>