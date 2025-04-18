<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /NovaBank/public/login');
    exit();
}
$currentPage = 'transaction';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Transaction - Nova Bank</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-university"></i> Nova Bank</h2>
            <nav>
                <ul>
                    <li class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                        <a href="/NovaBank/public/client/dashboard"><i class="fas fa-home"></i> Home</a>
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
        <main class="Nova-main-content">
            <header class="header">
                <div class="user-info">
                    <?php if ($profilePicture): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-picture"></i>
                    <?php endif; ?>
                    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </div>
                <span class="date">Today: <?= date('F j, Y') ?></span>
            </header>
            <div class="home-container">
                <!-- Account Balances -->
                <div class="account-balances green-theme">
                    <h2>Your Accounts</h2>
                    <div class="accounts-grid">
                        <?php foreach ($accounts as $account): ?>
                            <div class="account-card">
                                <h3><?= htmlspecialchars($account['account_type']) ?> Account</h3>
                                <p>Balance: $<?= number_format($account['balance'], 2) ?></p>
                                <p>Account Number: <?= htmlspecialchars($account['account_number']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quick Transfer Form -->
                <div class="quick-transfer green-theme">
                    <h2>Transfer Funds</h2>
                    <form action="/NovaBank/public/client/transactionAlgorithm" method="POST" onsubmit="return confirm('Are you sure you want to make this transfer?');">
                        <div class="form-group">
                            <label for="sender_account_number">From Account Number:</label>
                            <input type="text" id="sender_account_number" name="sender_account_number" required>
                        </div>
                        <div class="form-group">
                            <label for="recipient_account_number">To Account Number:</label>
                            <input type="text" id="recipient_account_number" name="recipient_account_number" required>
                        </div>
                        <div class="form-group">
                            <label for="amount">Amount:</label>
                            <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
                        </div>
                        <button type="submit" class="nb-transfer-btn">
                            <i class="fas fa-paper-plane"></i> Transfer Funds
                        </button>
                    </form>
                </div>
                <!-- Recent Transactions -->
                <div class="recent-transactions green-theme">
                    <h2>Recent Transactions</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentTransactions as $transaction): ?>
                                <tr>
                                    <td><?= htmlspecialchars($transaction['created_at']) ?></td>
                                    <td><?= htmlspecialchars($transaction['transaction_type']) ?></td>
                                    <td>$<?= number_format($transaction['amount'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
</body>

</html>