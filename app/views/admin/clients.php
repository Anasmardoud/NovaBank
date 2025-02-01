<?php
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

// Set the current page
$currentPage = 'clients';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/admin.css">
    <link rel="stylesheet" href="/PHPLearning/NovaBank/public/assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Clients - Nova Bank</title>
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
                <h1>Clients</h1>
                <div class="user-info">
                    <i class="fas fa-user-circle"></i>
                </div>
            </header>

            <!-- Clients Table -->
            <div class="content-section">
                <div class="dash-border">
                    <h2>All Clients</h2>
                    <table class="clients-table">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone Number</th>
                                <th>Address</th>
                                <th>Status</th>
                                <th>Accounts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td><?= htmlspecialchars($client['username']) ?></td>
                                    <td><?= htmlspecialchars($client['email']) ?></td>
                                    <td><?= htmlspecialchars($client['phone_number']) ?></td>
                                    <td><?= htmlspecialchars($client['address']) ?></td>
                                    <td><?= htmlspecialchars($client['status']) ?></td>
                                    <td>
                                        <?= $this->clientModel->getFormattedAccounts($client['client_id']) ?>
                                    </td>
                                    <td>
                                        <button onclick="openEditClientModal(
                                            '<?= $client['client_id'] ?>',
                                            '<?= htmlspecialchars($client['username']) ?>',
                                            '<?= htmlspecialchars($client['email']) ?>',
                                            '<?= htmlspecialchars($client['phone_number']) ?>',
                                            '<?= htmlspecialchars($client['address']) ?>',
                                            '<?= htmlspecialchars($client['status']) ?>',
                                            '<?= $client['checking_balance'] ?? 0 ?>', 
                                            '<?= $client['savings_balance'] ?? 0 ?>'  
                                        )" class="btn btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                            <form action="/PHPLearning/NovaBank/public/admin/delete-client" method="POST" onsubmit="return confirm('Are you sure you want to delete this client?');">
                                                <input type="hidden" name="client_id" value="<?= $client['client_id'] ?>">
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
    <div id="editClientModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditClientModal()">&times;</span>
            <h2>Edit Client</h2>
            <form action="/PHPLearning/NovaBank/public/admin/edit-account" method="POST">
                <!-- Hidden field for client ID -->
                <input type="hidden" id="edit_client_id" name="client_id">

                <!-- Username -->
                <div class="form-group">
                    <label for="edit_username">Username:</label>
                    <input type="text" id="edit_username" name="username" required>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="edit_email">Email:</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="edit_phone_number">Phone Number:</label>
                    <input type="text" id="edit_phone_number" name="phone_number" required>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label for="edit_address">Address:</label>
                    <textarea id="edit_address" name="address" required></textarea>
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="edit_status">Status:</label>
                    <select id="edit_status" name="status" required>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>

                <!-- Checking Account -->
                <div class="form-group">
                    <label for="edit_checking_balance">Checking Account Balance:</label>
                    <input type="number" id="edit_checking_balance" name="checking_balance" step="0.01" min="0">
                </div>

                <!-- Savings Account -->
                <div class="form-group">
                    <label for="edit_savings_balance">Savings Account Balance:</label>
                    <input type="number" id="edit_savings_balance" name="savings_balance" step="0.01" min="0">
                </div>

                <!-- Submit Button -->
                <div class="form-group">
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </form>
        </div>
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

    <script src="/PHPLearning/NovaBank/public/assets/js/admin.js"></script>

</body>

</html>