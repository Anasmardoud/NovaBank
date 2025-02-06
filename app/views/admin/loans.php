<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'loans';

// Fetch the admin ID from the session
$adminId = $_SESSION['user_id'];

// Fetch all loans for this admin
$allLoans = $this->loanModel->getLoansByAdminId($adminId);

// Fetch the last 5 loans for this admin
$lastFiveLoans = array_slice($allLoans, 0, 5);

// Pagination for all loans
$limit = 10; // Number of loans per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$totalLoans = count($allLoans);
$totalPages = ceil($totalLoans / $limit);
$paginatedLoans = array_slice($allLoans, $offset, $limit);
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

            <!-- Last 5 Loans Section -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>Last 5 Loans</h2>
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Interest Rate</th>
                                <th>Term (Months)</th>
                                <th>Monthly Payment</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Total Interest</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($loans) && !empty($loans)): ?>
                                <?php foreach ($loans as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['created_at'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['username'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['amount'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['interest_rate'] ?? 'N/A') ?>%</td>
                                        <td><?= htmlspecialchars($loan['term_months'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['monthly_payment'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['loan_type'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['status'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['message'] ?? 'N/A') ?></td>
                                        <td><?= htmlspecialchars($loan['total_interest'] ?? 'N/A') ?>$</td>
                                        <td><?= htmlspecialchars($loan['start_date'] ?? '--') ?></td>
                                        <td><?= htmlspecialchars($loan['end_date'] ?? '--') ?></td>
                                        <!-- Column for Calculate and Delete -->
                                        <td class="actions-container-edit">
                                            <a href="/PHPLearning/NovaBank/public/admin/calculate-loan-page?loan_id=<?= $loan['loan_id'] ?>" class="btn btn-calculate">
                                                <i class="fas fa-calculator"></i> Calculate
                                            </a>
                                            <form action="/PHPLearning/NovaBank/public/admin/delete-loan" method="POST" onsubmit="return confirm('Are you sure you want to delete this loan?');" style="display: inline;">
                                                <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                <button type="submit" class="btn btn-delete">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                        <!-- Column for Approve and Reject -->
                                        <td class="actions-container">
                                            <form action="/PHPLearning/NovaBank/public/admin/approve-loan" method="POST" style="display: inline;">
                                                <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                <button type="submit" class="btn btn-edit" <?= $loan['status'] === 'Approved' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <form action="/PHPLearning/NovaBank/public/admin/reject-loan" method="POST" style="display: inline;">
                                                <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                <button type="submit" class="btn btn-delete" <?= $loan['status'] === 'Rejected' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" style="text-align: center;">No loans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- All Loans Section -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>All Loans</h2>
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Client</th>
                                <th>Amount</th>
                                <th>Interest Rate</th>
                                <th>Term (Months)</th>
                                <th>Monthly Payment</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Message</th>
                                <th>Total Interest</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($paginatedLoans) && !empty($paginatedLoans)): ?>
                                <?php foreach ($paginatedLoans as $loan): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($loan['created_at']) ?></td>
                                        <td><?= htmlspecialchars($loan['username']) ?></td>
                                        <td><?= htmlspecialchars($loan['amount']) ?></td>
                                        <td><?= htmlspecialchars($loan['interest_rate']) ?>%</td>
                                        <td><?= htmlspecialchars($loan['term_months']) ?></td>
                                        <td><?= htmlspecialchars($loan['monthly_payment']) ?></td>
                                        <td><?= htmlspecialchars($loan['loan_type']) ?></td>
                                        <td><?= htmlspecialchars($loan['status']) ?></td>
                                        <td><?= htmlspecialchars($loan['message']) ?></td>
                                        <td><?= htmlspecialchars($loan['total_interest']) ?>$</td>
                                        <td><?= htmlspecialchars($loan['start_date'] ?? '--') ?></td>
                                        <td><?= htmlspecialchars($loan['end_date']) ?></td>
                                        <!-- Column for Calculate and Delete -->
                                        <td class="actions-container-edit">
                                            <!-- Calculate Loan -->
                                            <a href="/PHPLearning/NovaBank/public/admin/calculate-loan-page?loan_id=<?= $loan['loan_id'] ?>" class="btn btn-calculate">
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

                                        <!-- Column for Approve and Reject -->
                                        <td class="actions-container">
                                            <!-- Approve Loan -->
                                            <form action="/PHPLearning/NovaBank/public/admin/approve-loan" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to Approve this Loan?');">
                                                <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                <button type="submit" class="btn btn-edit" <?= $loan['status'] === 'Approved' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-check"></i> Approve
                                                </button>
                                            </form>
                                            <!-- Reject Loan -->
                                            <form action="/PHPLearning/NovaBank/public/admin/reject-loan" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to Reject this Loan?');">
                                                <input type="hidden" name="loan_id" value="<?= $loan['loan_id'] ?>">
                                                <button type="submit" class="btn btn-delete" <?= $loan['status'] === 'Rejected' ? 'disabled' : '' ?>>
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="14" style="text-align: center;">No loans found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?= $page - 1 ?>">Previous</a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i ?>" class="<?= $page === $i ? 'active' : '' ?>"><?= $i ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?= $page + 1 ?>">Next</a>
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

    <script src="/PHPLearning/NovaBank/public/assets/js/admin.js"></script>
</body>

</html>