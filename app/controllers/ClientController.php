<?php
class ClientController
{
    private $clientModel;
    private $transactionModel;
    private $loanModel;

    /**
     * Constructor to initialize models.
     */
    public function __construct($clientModel, $transactionModel, $loanModel)
    {
        $this->clientModel = $clientModel;
        $this->transactionModel = $transactionModel;
        $this->loanModel = $loanModel;
    }

    /**
     * Render the client dashboard.
     */
    public function dashboard()
    {
        $this->ensureClientLoggedIn();

        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $accounts = $this->clientModel->getAccounts($clientId);

        // Fetch transactions for the client (assuming getByClientId exists in Transaction model)
        $transactions = $this->transactionModel->getByClientId($clientId);
        // Fetch income and expenses
        $incomeAndExpenses = $this->clientModel->getIncomeAndExpenses($clientId);
        // Fetch profile picture (assuming getProfilePicture exists in Client model)
        $profilePicture = $this->clientModel->getProfilePicture($clientId);

        include __DIR__ . '/../views/client/dashboard.php';
    }

    /**
     * Render the client profile page.
     */
    public function profileAction()
    {
        $this->ensureClientLoggedIn();

        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $profilePicture = $this->clientModel->getProfilePicture($clientId);

        include __DIR__ . '/../views/client/profile.php';
    }

    /**
     * Render the client accounts page.
     */
    public function accountsAction()
    {
        $this->ensureClientLoggedIn();

        $clientId = $_SESSION['user_id'];
        $accounts = $this->clientModel->getAccounts($clientId);
        $profilePicture = $this->clientModel->getProfilePicture($clientId);

        include __DIR__ . '/../views/client/accounts.php';
    }

    /**
     * Handle fund transfers.
     */
    public function transferFunds()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->ensureClientLoggedIn();

            $senderAccountId = $_POST['sender_account_id'];
            $recipientAccountId = $_POST['recipient_account_id'];
            $amount = $_POST['transfer_amount'];

            // Validate inputs
            if (empty($senderAccountId) || empty($recipientAccountId) || empty($amount) || $amount <= 0) {
                $this->setSessionError("Invalid input. Please try again.");
                $this->redirect('/NovaBank/public/client/accounts');
            }

            // Transfer funds
            if ($this->transactionModel->transferFunds($senderAccountId, $recipientAccountId, $amount)) {
                $this->setSessionSuccess("Funds transferred successfully!");
            } else {
                $this->setSessionError("Failed to transfer funds. Please try again.");
            }

            $this->redirect('/NovaBank/public/client/accounts');
        }
    }

    /**
     * Handle profile updates.
     */
    public function editProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->ensureClientLoggedIn();

            $clientId = $_SESSION['user_id'];
            $username = $this->sanitizeInput($_POST['username']);
            $email = $this->sanitizeInput($_POST['email']);
            $phoneNumber = $this->sanitizeInput($_POST['phone_number']);
            $address = $this->sanitizeInput($_POST['address']);
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $confirmPassword = !empty($_POST['confirm_password']) ? $_POST['confirm_password'] : null;

            // Validate password and confirmation
            if ($password !== null && $password !== $confirmPassword) {
                $this->setSessionError("Passwords do not match.");
                $this->redirect('/NovaBank/public/client/profile');
            }

            // Handle profile picture upload
            $profilePicturePath = $this->handleProfilePictureUpload();

            // Update client profile
            if ($this->clientModel->updateProfile($clientId, $username, $email, $phoneNumber, $address, 'Active', $password, $profilePicturePath)) {
                $this->setSessionSuccess("Profile updated successfully!");
            } else {
                $this->setSessionError("Failed to update profile.");
            }

            $this->redirect('/NovaBank/public/client/profile');
        }
    }

    /**
     * Render the transaction page.
     */
    public function transactionHome()
    {
        $this->ensureClientLoggedIn();

        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $accounts = $this->clientModel->getAccounts($clientId);

        // Fetch recent transactions (assuming getByClientId exists in Transaction model)
        $recentTransactions = $this->transactionModel->getByClientId($clientId);

        // Fetch account statements for the last 30 days
        $startDate = date('Y-m-d', strtotime('-30 days'));
        $endDate = date('Y-m-d');
        $accountStatements = [];
        foreach ($accounts as $account) {
            $accountStatements[$account['account_id']] = $this->transactionModel->getAccountStatement(
                $account['account_id'],
                $startDate,
                $endDate
            );
        }

        $profilePicture = $this->clientModel->getProfilePicture($clientId);

        include __DIR__ . '/../views/client/transaction.php';
    }

    /**
     * Handle fund transfers.
     */
    public function transactionAlgorithm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->ensureClientLoggedIn();

            $senderAccountNumber = $_POST['sender_account_number'];
            $recipientAccountNumber = $_POST['recipient_account_number'];
            $amount = $_POST['amount'];

            // Validate inputs
            if (empty($senderAccountNumber) || empty($recipientAccountNumber) || empty($amount) || $amount <= 0) {
                $this->setSessionError("Invalid input. Please try again.");
                $this->redirect('/NovaBank/public/client/transaction');
            }

            // Fetch sender and recipient account IDs using account numbers
            $senderAccount = $this->clientModel->getAccountByNumber($senderAccountNumber);
            $recipientAccount = $this->clientModel->getAccountByNumber($recipientAccountNumber);

            if (!$senderAccount || !$recipientAccount) {
                $this->setSessionError("Invalid sender or recipient account number.");
                $this->redirect('/NovaBank/public/client/transaction');
            }

            // Ensure the sender account belongs to the logged-in client
            if ($senderAccount['client_id'] !== $_SESSION['user_id']) {
                $this->setSessionError("You can only transfer from your own accounts.");
                $this->redirect('/NovaBank/public/client/transaction');
            }

            // Call the stored procedure to transfer funds
            $result = $this->transactionModel->transferFunds(
                $senderAccount['account_id'],
                $recipientAccount['account_id'],
                $amount
            );

            // Display the result
            if ($result) {
                $this->setSessionSuccess("Funds transferred successfully!");
            } else {
                $this->setSessionError("Failed to transfer funds. Please try again.");
            }

            $this->redirect('/NovaBank/public/client/transaction');
        } else {
            $this->redirect('/NovaBank/public/client/transaction');
        }
    }

    /**
     * Render the loans page.
     */
    public function loansAction()
    {
        $this->ensureClientLoggedIn();

        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $accounts = $this->clientModel->getAccounts($clientId);
        $profilePicture = $this->clientModel->getProfilePicture($clientId);

        // Pagination logic
        $limit = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        // Fetch the client's loan requests with pagination
        $loans = $this->loanModel->getByClientId($clientId, $limit, $offset);
        $totalLoans = $this->loanModel->countLoansByClientId($clientId);
        $totalPages = ceil($totalLoans / $limit);

        include __DIR__ . '/../views/client/loans.php';
    }
    /**
     * Handle loan requests.
     */
    public function requestLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->ensureClientLoggedIn();

            $clientId = $_SESSION['user_id'];
            $amount = $_POST['amount'];
            $interestRate = $_POST['interest_rate'];
            $termMonths = $_POST['term_months'];
            $loanType = $_POST['loan_type'];
            $message = $_POST['message'] ?? '';
            $admin_id = $this->clientModel->getAdminId($clientId);

            // Validate inputs
            if (empty($amount) || empty($interestRate) || empty($termMonths) || empty($loanType)) {
                $this->setSessionError("All fields are required.");
                $this->redirect('/NovaBank/public/client/loans');
            }

            if ($amount <= 0 || $interestRate <= 0 || $termMonths <= 0) {
                $this->setSessionError("Amount, interest rate, and term must be greater than zero.");
                $this->redirect('/NovaBank/public/client/loans');
            }

            // Create the loan request
            if ($this->loanModel->create($clientId, $admin_id, $amount, $interestRate, $termMonths, $loanType, $message)) {
                $this->setSessionSuccess("Loan request submitted successfully!");
            } else {
                $this->setSessionError("Failed to submit loan request. Please try again.");
            }

            $this->redirect('/NovaBank/public/client/loans');
        }
    }

    /**
     * Ensure the user is logged in and is a client.
     */
    private function ensureClientLoggedIn()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            $this->redirect('/NovaBank/public/login');
        }
    }

    /**
     * Handle profile picture upload.
     *
     * @return string|null The file path if uploaded, null otherwise.
     */
    private function handleProfilePictureUpload()
    {
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/assets/images/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $profilePicturePath = $uploadDir . basename($_FILES['profile_picture']['name']);
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profilePicturePath)) {
                return '/NovaBank/public/assets/images/profile_pictures/' . basename($_FILES['profile_picture']['name']);
            } else {
                $this->setSessionError("Failed to upload profile picture.");
                $this->redirect('/NovaBank/public/client/profile');
            }
        }
        return null;
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
