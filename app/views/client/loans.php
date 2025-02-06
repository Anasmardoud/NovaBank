<?php
// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}
$currentPage = 'loans';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/client.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Loans - Nova Bank</title>
</head>

<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <h2><i class="fas fa-university"></i> Nova Bank</h2>
            <nav>
                <ul>
                    <li class="<?php echo $currentPage === 'home' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/client/home"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="<?php echo $currentPage === 'accounts' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/client/accounts"><i class="fas fa-briefcase"></i> Accounts</a>
                    </li>
                    <li class="<?php echo $currentPage === 'transaction' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/client/transaction"><i class="fas fa-exchange-alt"></i> Transaction</a>
                    </li>
                    <li class="<?php echo $currentPage === 'profile' ? 'active' : ''; ?>">
                        <a href="/PHPLearning/NovaBank/public/client/profile"><i class="fas fa-user"></i> Profile</a>
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
        <main class="nova-main-content">
            <header class="header">
                <div class="user-info">
                    <?php if ($profilePicture): ?>
                        <img src="<?= htmlspecialchars($profilePicture) ?>" alt="Profile Picture" class="profile-picture">
                    <?php else: ?>
                        <i class="fas fa-user-circle default-profile-picture"></i>
                    <?php endif; ?>
                    <h1>Loans, <?= htmlspecialchars($_SESSION['username']) ?></h1>
                </div>
                <span class="date">Today: <?= date('F j, Y') ?></span>
            </header>
            <div class="loans-container">
                <!-- Loan Request Form -->
                <div class="loan-request green-theme">
                    <h2>Request a Loan</h2>
                    <form action="/PHPLearning/NovaBank/public/client/requestLoan" method="POST">
                        <div class="form-group">
                            <label for="amount">Loan Amount:</label>
                            <input type="number" id="amount" name="amount" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="interest_rate">Interest Rate (%):</label>
                            <input type="number" id="interest_rate" name="interest_rate" min="0.01" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="term_months">Loan Term (Months):</label>
                            <input type="number" id="term_months" name="term_months" min="1" required>
                        </div>
                        <div class="form-group">
                            <label for="loan_type">Loan Type:</label>
                            <select id="loan_type" name="loan_type" required>
                                <option value="Personal">Personal</option>
                                <option value="Home">Home</option>
                                <option value="Car">Car</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="message">Message:</label>
                            <textarea id="message" name="message" rows="3"></textarea>
                        </div>
                        <button type="submit" class="nb-request-btn">
                            <i class="fas fa-paper-plane"></i> Request Loan
                        </button>
                    </form>
                </div>
                <!-- Loan History -->
                <div class="loan-history green-theme">
                    <h2>Loan History</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Interest Rate</th>
                                <th>Term (Months)</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($loans as $loan): ?>
                                <tr>
                                    <td>$<?= number_format($loan['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($loan['interest_rate']) ?>%</td>
                                    <td><?= htmlspecialchars($loan['term_months']) ?></td>
                                    <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                    <td><?= htmlspecialchars($loan['status']) ?></td>
                                    <td><?= htmlspecialchars($loan['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <!-- Pagination -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>" class="pagination-link">Previous</a>
                        <?php endif; ?>

                        <span>Page <?= $page ?> of <?= $totalPages ?></span>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>" class="pagination-link">Next</a>
                        <?php endif; ?>
                    </div>
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