<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /NovaBank/public/login');
    exit();
}

// Fetch loan details by ID
$loanId = $_GET['loan_id'] ?? null;
$loan = $this->loanModel->getLoanById($loanId);

if (!$loan) {
    $_SESSION['error'] = 'Loan not found.';
    header('Location: /NovaBank/public/admin/loans');
    exit();
}

// Set the current page
$currentPage = 'loans';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Calculate Loan - Nova Bank</title>
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
                <h1>Calculate Loan</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Calculate Loan Form -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Loan Details</h2>
                    <form action="/NovaBank/public/admin/calculate-loan" method="POST">
                        <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">

                        <label for="amount">Loan Amount:</label>
                        <input type="number" id="amount" name="amount" value="<?= $loan['amount'] ?>" step="0.01" required>

                        <label for="interest_rate">Interest Rate (%):</label>
                        <input type="number" id="interest_rate" name="interest_rate" value="<?= $loan['interest_rate'] ?>" step="0.01" required>

                        <label for="term_months">Term (Months):</label>
                        <input type="number" id="term_months" name="term_months" value="<?= $loan['term_months'] ?>" required>

                        <label>Monthly Payment:</label>
                        <span id="monthly_payment"><?= $loan['monthly_payment'] ?? '-' ?></span>

                        <label>Total Interest:</label>
                        <span id="total_interest"><?= $loan['total_interest'] ?? '-' ?></span>

                        <label>End Date:</label>
                        <span id="end_date"><?= $loan['end_date'] ?? '-' ?></span>

                        <button type="submit" class="btn">Calculate</button>
                        <a href="/NovaBank/public/admin/loans" class="btn btn-delete">Cancel</a>
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
    <!-- Footer -->
    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="/NovaBank/public/assets/js/admin.js"></script>
</body>

</html>