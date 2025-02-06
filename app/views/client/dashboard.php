<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}
$currentPage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Client Dashboard - Nova Bank</title>
</head>
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2><i class="fas fa-university"></i> Nova Bank</h2>
        <nav>
            <ul>
                <li class="<?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                    <a href="/PHPLearning/NovaBank/public/client/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="<?php echo $currentPage === 'accounts' ? 'active' : ''; ?>">
                    <a href="/PHPLearning/NovaBank/public/client/accounts"><i class="fas fa-briefcase"></i> Accounts</a>
                </li>
                <li class="<?php echo $currentPage === 'transaction' ? 'active' : ''; ?>">
                    <a href="/PHPLearning/NovaBank/public/client/transaction"><i class="fas fa-exchange-alt"></i> Transaction</a>
                </li>
                <li class="<?php echo $currentPage === 'Profile' ? 'active' : ''; ?>">
                    <a href="/PHPLearning/NovaBank/public/client/profile"><i class="fas fa-user"></i>Profile</a>
                </li>
                <li class="<?php echo $currentPage === 'loans' ? 'active' : ''; ?>">
                    <a href="/PHPLearning/NovaBank/public/client/loans"><i class="fas fa-hand-holding-usd"></i> Loans</a>
                </li>
                <li>
                    <a href="/PHPLearning/NovaBank/public/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
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

        <!-- Latest Transactions -->
        <div class="transactions-section">
            <h2>Latest Transactions</h2>
            <ul>
                <?php foreach ($transactions as $transaction): ?>
                    <li class="<?= $transaction['receiver'] === $_SESSION['username'] ? 'incoming' : 'outgoing' ?>">
                        <i class="fas fa-<?= $transaction['receiver'] === $_SESSION['username'] ? 'arrow-down' : 'arrow-up' ?> transaction-icon"></i>
                        <span class="date"><?= htmlspecialchars($transaction['created_at']) ?></span>
                        <span class="from"><?= htmlspecialchars($transaction['sender']) ?></span>
                        <span class="to"><?= htmlspecialchars($transaction['receiver']) ?></span>
                        <span class="amount">$<?= number_format($transaction['amount'], 2) ?></span>
                        <i class="fas fa-bell notification-icon"></i>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

    </main>
</div>

<!-- Toast Container -->
<div id="toast-container"></div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>

<script src="/PHPLearning/NovaBank/public/assets/js/client.js"></script>
</body>

</html>