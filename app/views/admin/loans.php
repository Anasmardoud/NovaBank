<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'loans';

// Fetch all loans with client details
$loans = $this->loanModel->getAllLoansWithClients();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Loan Management - Nova Bank</title>
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
                        <a href="/PHPLearning/NovaBank/public/admin/client-creation-homepage"><i class="fas fa-user-plus"></i> Create Account</a>
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
                <h1>Loan Management</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Loan Table -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>All Loans</h2>
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>Loan ID</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Interest Rate</th>
                                <th>Term (Months)</th>
                                <th>Monthly Payment</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td><?= htmlspecialchars($loan['loan_id']) ?></td>
                                    <td><?= htmlspecialchars($loan['username']) ?></td>
                                    <td><?= htmlspecialchars($loan['amount']) ?></td>
                                    <td><?= htmlspecialchars($loan['interest_rate']) ?>%</td>
                                    <td><?= htmlspecialchars($loan['term_months']) ?></td>
                                    <td><?= htmlspecialchars($loan['monthly_payment']) ?></td>
                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                    <td><?= htmlspecialchars($loan['status']) ?></td>
                                    <td>
                                        <!-- Approve Loan -->
                                        <form action="/PHPLearning/NovaBank/public/admin/approve-loan" method="POST" style="display: inline;">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                            <button type="submit" class="btn btn-edit" <?= $loan['status'] === 'Approved' ? 'disabled' : '' ?>>
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        </form>

                                        <!-- Reject Loan -->
                                        <form action="/PHPLearning/NovaBank/public/admin/reject-loan" method="POST" style="display: inline;">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                            <button type="submit" class="btn btn-delete" <?= $loan['status'] === 'Rejected' ? 'disabled' : '' ?>>
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </form>

                                        <!-- Calculate Loan -->
                                        <a href="/PHPLearning/NovaBank/public/admin/calculate-loan-page?loan_id=<?= $loan['loan_id'] ?>" class="btn">
                                            <i class="fas fa-calculator"></i> Calculate
                                        </a>

                                        <!-- Delete Loan -->
                                        <form action="/PHPLearning/NovaBank/public/admin/delete-loan" method="POST" onsubmit="return confirm('Are you sure you want to delete this loan?');" style="display: inline;">
                                            <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
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

    </div>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="/PHPLearning/NovaBank/public/assets/js/admin.js"></script>
</body>

</html>