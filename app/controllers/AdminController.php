<?php
require_once __DIR__ . '/../models/Client.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/Loan.php';
require_once __DIR__ . '/../models/Deposit.php';

class AdminController
{
    private $clientModel;
    private $loanModel;
    private $adminModel;
    private $depositModel;

    /**
     * Constructor to initialize models.
     */
    public function  __construct()
    {
        $this->clientModel = new Client();
        $this->loanModel = new Loan();
        $this->adminModel = new Admin();
        $this->depositModel = new Deposit();
    }

    /**
     * Render the admin dashboard.
     */
    public function dashboard()
    {
        $currentPage = 'dashboard';
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Render the create admin page.
     */
    public function createAdminPage()
    {
        $currentPage = 'create_admin';
        include __DIR__ . '/../views/admin/create_admin.php';
    }
    /**
     * Create Admin Algo.
     */

    public function createAdmin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['password']);
            $confirmPassword = htmlspecialchars($_POST['confirm_password']);

            // Validate inputs
            if (empty($username) || empty($password) || empty($confirmPassword)) {
                $_SESSION['error'] = 'All fields are required.';
                header('Location: /NovaBank/public/admin/create-admin');
                exit();
            }

            if ($password !== $confirmPassword) {
                $_SESSION['error'] = 'Passwords do not match.';
                header('Location: /NovaBank/public/admin/create-admin');
                exit();
            }

            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Create the admin
            if ($this->adminModel->create($username, $hashedPassword)) {
                $_SESSION['success'] = 'Admin created successfully.';
            } else {
                $_SESSION['error'] = 'Failed to create admin.';
            }

            header('Location: /NovaBank/public/admin/create-admin');
            exit();
        }

        // Render the create admin page
        $currentPage = 'create_admin';
        include __DIR__ . '/../views/admin/create_admin.php';
    }
    /**
     * Handle password change for the admin.
     */
    public function changePassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['user_id'];
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            // Fetch the admin's current password
            $admin = $this->adminModel->findById($adminId);

            if (!$admin || !password_verify($currentPassword, $admin['password'])) {
                $this->setSessionError('Current password is incorrect.');
                $this->redirect('/NovaBank/public/admin/dashboard');
            }

            // Validate the new password
            if ($newPassword !== $confirmPassword) {
                $this->setSessionError('New password and confirmation do not match.');
                $this->redirect('/NovaBank/public/admin/dashboard');
            }

            // Update the password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            if ($this->adminModel->updatePassword($adminId, $hashedPassword)) {
                $this->setSessionSuccess('Password changed successfully.');
            } else {
                $this->setSessionError('Failed to change password.');
            }

            $this->redirect('/NovaBank/public/admin/dashboard');
        }
    }

    /**
     * Render the client creation page.
     */
    public function clientCreationHomePage()
    {
        include __DIR__ . '/../views/admin/create_clients.php';
    }

    /**
     * Handle client creation.
     */
    public function createClientAlgorithm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['user_id'];

            // Sanitize inputs
            $username = $this->sanitizeInput($_POST['username']);
            $email = $this->sanitizeInput($_POST['email']);
            $password = $this->sanitizeInput($_POST['password']);
            $phoneNumber = $this->sanitizeInput($_POST['phone_number']);
            $address = $this->sanitizeInput($_POST['address']);
            $status = $this->sanitizeInput($_POST['status']);

            // Validate account types
            $addChecking = isset($_POST['add_checking_account']);
            $addSavings = isset($_POST['add_savings_account']);

            if (!$addChecking && !$addSavings) {
                $this->setSessionError('Please select at least one account type.');
                $this->redirect('/NovaBank/public/admin/client-creation-homepage');
            }

            // Validate balances
            $checkingBalance = $addChecking ? floatval($_POST['checking_balance'] ?? 0) : null;
            $savingsBalance = $addSavings ? floatval($_POST['savings_balance'] ?? 0) : null;

            if ($addChecking && $checkingBalance <= 0) {
                $this->setSessionError('Checking account balance must be greater than 0.');
                $this->redirect('/NovaBank/public/admin/client-creation-homepage');
            }

            if ($addSavings && $savingsBalance <= 0) {
                $this->setSessionError('Savings account balance must be greater than 0.');
                $this->redirect('/NovaBank/public/admin/client-creation-homepage');
            }

            // Create client and accounts
            if ($this->clientModel->create($adminId, $username, $email, $password, $phoneNumber, $address, $status, $checkingBalance, $savingsBalance)) {
                $this->setSessionSuccess('Client and accounts created successfully.');
            } else {
                $this->setSessionError('Failed to create client and accounts.');
            }

            $this->redirect('/NovaBank/public/admin/client-creation-homepage');
        }
    }

    /**
     * Render the clients page.
     */
    public function clients()
    {
        $clients = $this->clientModel->getAllClientsWithAccounts();
        include __DIR__ . '/../views/admin/clients.php';
    }

    /**
     * Handle client updates.
     */
    public function updateClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_POST['client_id'];
            $username = $this->sanitizeInput($_POST['username']);
            $email = $this->sanitizeInput($_POST['email']);
            $phoneNumber = $this->sanitizeInput($_POST['phone_number']);
            $address = $this->sanitizeInput($_POST['address']);
            $status = $this->sanitizeInput($_POST['status']);
            $checkingBalance = isset($_POST['checking_balance']) ? floatval($_POST['checking_balance']) : null;
            $savingsBalance = isset($_POST['savings_balance']) ? floatval($_POST['savings_balance']) : null;

            // Update client information
            if ($this->clientModel->update($clientId, $username, $email, $phoneNumber, $address, $status, $checkingBalance, $savingsBalance)) {
                $this->setSessionSuccess("Client $username updated successfully.");
            } else {
                $this->setSessionError("Failed to update client $username.");
            }

            $this->redirect('/NovaBank/public/admin/clients');
        }
    }

    /**
     * Handle client deletion.
     */
    public function deleteClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_POST['client_id'];

            if (!is_numeric($clientId)) {
                $this->setSessionError('Invalid client ID.');
                $this->redirect('/NovaBank/public/admin/clients');
            }

            if ($this->clientModel->delete($clientId)) {
                $this->setSessionSuccess('Client deleted successfully.');
            } else {
                $this->setSessionError('Failed to delete client.');
            }

            $this->redirect('/NovaBank/public/admin/clients');
        }
    }

    /**
     * Render the deposits page.
     */
    public function deposit()
    {
        $deposits = $this->depositModel->getAll();
        $activeAccounts = $this->clientModel->getActiveAccounts();
        include __DIR__ . '/../views/admin/deposit.php';
    }

    /**
     * Handle deposit creation.
     */
    public function createDeposit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accountId = $_POST['account_id'];
            $amount = $_POST['amount'];
            $adminId = $_SESSION['user_id'];

            // Validate input
            if (!is_numeric($accountId) || !is_numeric($amount) || $amount <= 0) {
                $this->setSessionError('Invalid input. Please check the account ID and amount.');
                $this->redirect('/NovaBank/public/admin/deposit');
            }

            // Create the deposit
            $statusMessage = $this->depositModel->create($accountId, $adminId, $amount);

            if (strpos($statusMessage, 'successfully') !== false) {
                $this->setSessionSuccess($statusMessage);
            } else {
                $this->setSessionError($statusMessage);
            }
            $this->redirect('/NovaBank/public/admin/deposit');
        }
    }


    public function editDeposit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $depositId = $_POST['deposit_id'];

            // Fetch deposit details for editing
            $deposit = $this->depositModel->getDepositById($depositId);
            if ($deposit) {
                // Load the edit deposit view with deposit data
                include __DIR__ . '/../views/admin/edit_deposit.php';
            } else {
                $this->setSessionError('Deposit not found.');
                $this->redirect('/NovaBank/public/admin/deposit');
                exit();
            }
        }
    }
    /**
     * Handle deposit deletion.
     */
    public function deleteDeposit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $depositId = $_POST['deposit_id'];

            if ($this->depositModel->delete($depositId)) {
                $this->setSessionSuccess('Deposit deleted successfully.');
            } else {
                $this->setSessionError('Failed to delete deposit.');
            }

            $this->redirect('/NovaBank/public/admin/deposit');
        }
    }

    /**
     * Handle deposit updates.
     */
    public function updateDeposit()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $depositId = $_POST['deposit_id'];
            $amount = $_POST['amount'];
            $status = $_POST['status'];

            // Validate input
            if (!is_numeric($amount) || $amount <= 0) {
                $this->setSessionError('Invalid amount. Amount must be greater than 0.');
                $this->redirect('/NovaBank/public/admin/deposit');
            }

            // Update the deposit
            if ($this->depositModel->update($depositId, $amount, $status)) {
                $this->setSessionSuccess('Deposit updated successfully.');
            } else {
                $this->setSessionError('Failed to update deposit.');
            }

            $this->redirect('/NovaBank/public/admin/deposit');
        }
    }

    /**
     * Render the loans page.
     */
    public function loans()
    {
        $lastFiveLoans = $this->loanModel->getLastFiveLoans();
        $adminId = $_SESSION['user_id'];
        $loans = $this->loanModel->getLoansByAdminId($adminId);

        // Pagination logic
        $limit = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;
        $totalLoans = $this->loanModel->getTotalLoansCount();
        $totalPages = ceil($totalLoans / $limit);
        $loansHistory = $this->loanModel->getLoansPaginated($offset, $limit);

        include __DIR__ . '/../views/admin/loans.php';
    }

    /**
     * Handle loan approval.
     */
    public function approveLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanId = $_POST['loan_id'];

            if ($this->loanModel->approve($loanId)) {
                $this->setSessionSuccess('Loan approved successfully.');
            } else {
                $this->setSessionError('Failed to approve loan.');
            }

            $this->redirect('/NovaBank/public/admin/loans');
        }
    }

    /**
     * Handle loan rejection.
     */
    public function rejectLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanId = $_POST['loan_id'];

            if ($this->loanModel->reject($loanId)) {
                $this->setSessionSuccess('Loan rejected successfully.');
            } else {
                $this->setSessionError('Failed to reject loan.');
            }

            $this->redirect('/NovaBank/public/admin/loans');
        }
    }

    /**
     * Handle loan calculation.
     */
    public function calculateLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanId = $_POST['loan_id'] ?? null;
            $amount = $_POST['amount'] ?? null;
            $interestRate = $_POST['interest_rate'] ?? null;
            $termMonths = $_POST['term_months'] ?? null;

            if (!$loanId || !$amount || !$interestRate || !$termMonths) {
                $this->setSessionError('All fields are required.');
                $this->redirect('/NovaBank/public/admin/loans');
            }

            // Update and calculate loan
            if ($this->loanModel->updateAndCalculateLoan($loanId, $amount, $interestRate, $termMonths)) {
                $this->setSessionSuccess('Loan updated and calculation completed successfully.');
            } else {
                $this->setSessionError('Failed to update and calculate loan.');
            }

            $this->redirect('/NovaBank/public/admin/loans');
        }
    }

    /**
     * Render the loan calculation page.
     */
    public function calculateLoanPage()
    {
        $loanId = $_GET['loan_id'] ?? null;

        if (!$loanId) {
            $this->setSessionError('Loan ID is required.');
            $this->redirect('/NovaBank/public/admin/loans');
        }

        $loan = $this->loanModel->getLoanById($loanId);

        if (!$loan) {
            $this->setSessionError('Loan not found.');
            $this->redirect('/NovaBank/public/admin/loans');
        }

        include __DIR__ . '/../views/admin/calculate_loan.php';
    }

    /**
     * Handle loan deletion.
     */
    public function deleteLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $loanId = $_POST['loan_id'];

            if ($this->loanModel->delete($loanId)) {
                $this->setSessionSuccess('Loan deleted successfully.');
            } else {
                $this->setSessionError('Failed to delete loan.');
            }

            $this->redirect('/NovaBank/public/admin/loans');
        }
    }

    /**
     * Set a session success message.
     *
     * @param string $message The success message.
     */
    private function setSessionSuccess($message)
    {
        $_SESSION['success'] = $message;
    }

    /**
     * Set a session error message.
     *
     * @param string $message The error message.
     */
    private function setSessionError($message)
    {
        $_SESSION['error'] = $message;
    }

    /**
     * Redirect to a specific URL.
     *
     * @param string $url The URL to redirect to.
     */
    private function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    /**
     * Sanitize input data.
     *
     * @param string $input The input to sanitize.
     * @return string The sanitized input.
     */
    private function sanitizeInput($input)
    {
        return htmlspecialchars($input);
    }
}
