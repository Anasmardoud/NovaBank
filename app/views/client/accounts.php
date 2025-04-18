<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /NovaBank/public/login');
    exit();
}
$currentPage = 'accounts';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Client Accounts - Nova Bank</title>
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
        <main class="nova-main-content">
            <header class="header">
                <div class="user-info">
                    <?php if ($profilePicture): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-picture"></i>
                    <?php endif; ?>
                    <h1>Accounts of <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </div>
                <span class="date">Today: <?= date('F j, Y') ?></span>
            </header>
            <div class="nb-accounts-grid">
                <?php foreach ($accounts as $account): ?>
                    <div class="nb-account-card green-theme">
                        <div class="nb-account-header">
                            <i class="fas fa-<?= $account['account_type'] === 'Checking' ? 'wallet' : 'piggy-bank' ?> nb-account-icon"></i>
                            <h3><?= htmlspecialchars($account['account_type']) ?> Account</h3>
                        </div>
                        <div class="nb-account-details">
                            <p class="nb-balance-amount">
                                $<?= number_format($account['balance'] ?? 0, 2) ?>
                            </p>
                            <p>
                                <span>Account Number:</span>
                                <span><?= htmlspecialchars($account['account_number'] ?? 'N/A') ?></span>
                            </p>
                            <p>
                                <span>Transaction Limit:</span>
                                <span>$<?= number_format($account['transaction_limit'] ?? 0, 2) ?></span>
                            </p>
                            <p>
                                <span>Currency:</span>
                                <span><?= htmlspecialchars($account['currency'] ?? 'USD') ?></span>
                            </p>
                            <p>
                                <span>Created:</span>
                                <span><?= htmlspecialchars(date('M d, Y', strtotime($account['created_at']))) ?></span>
                            </p>
                        </div>

                        <div class="nb-transfer-section">
                            <h4>Transfer Funds</h4>
                            <form class="nb-transfer-form" action="/NovaBank/public/client/transferFunds" method="POST" required>
                                <input type="hidden" name="sender_account_id" value="<?= $account['account_id'] ?>">
                                <div class="nb-form-group">
                                    <label for="recipient_account_id_<?= $account['account_id'] ?>">Transfer to:</label>
                                    <select id="recipient_account_id_<?= $account['account_id'] ?>" name="recipient_account_id" required>
                                        <?php foreach ($accounts as $recipientAccount): ?>
                                            <?php if ($recipientAccount['account_id'] !== $account['account_id']): ?>
                                                <option value="<?= $recipientAccount['account_id'] ?>">
                                                    <?= htmlspecialchars($recipientAccount['account_type']) ?> Account
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="nb-form-group">
                                    <label for="transfer_amount_<?= $account['account_id'] ?>">Amount:</label>
                                    <input type="number"
                                        id="transfer_amount_<?= $account['account_id'] ?>"
                                        name="transfer_amount"
                                        min="0.01"
                                        max="<?= $account['balance'] ?>"
                                        step="0.01"
                                        required>
                                </div>
                                <button type="submit" class="nb-transfer-btn">
                                    <i class="fas fa-paper-plane"></i>
                                    Transfer Funds
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
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