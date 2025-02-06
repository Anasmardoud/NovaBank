<?php
require_once __DIR__ . '/Database.php';

class Client
{
    private $conn;

    /**
     * Constructor to initialize the database connection.
     */
    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    /**
     * Render the client dashboard.
     * Redirects to the login page if the user is not logged in or is not a client.
     */
    public function dashboard()
    {
        // Check if the user is logged in and is a client
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            header('Location: /login');
            exit();
        }

        // Include the client dashboard view
        include __DIR__ . '/../views/client/dashboard.php';
    }

    /**
     * Fetch all accounts for a specific client.
     *
     * @param int $clientId The ID of the client.
     * @return array List of accounts.
     */
    public function getAccounts($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT account_id, account_number, account_type, balance, transaction_limit, created_at 
            FROM ACCOUNT 
            WHERE client_id = ?
        ");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Fetch a specific account by account number.
     *
     * @param string $accountNumber The account number.
     * @return array|null Account details if found, null otherwise.
     */
    public function getAccount($accountNumber)
    {
        $stmt = $this->conn->prepare("
            SELECT account_id, account_number, account_type, balance 
            FROM ACCOUNT 
            WHERE account_number = ?
        ");
        $stmt->bind_param("s", $accountNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Fetch the latest transactions for a client.
     *
     * @param int $clientId The ID of the client.
     * @return array List of transactions.
     */
    public function getLatestTransactions($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT t.transaction_id, t.amount, t.created_at, 
                   s.username AS sender, r.username AS receiver
            FROM TRANSACTION t
            JOIN ACCOUNT a ON t.sender_account_id = a.account_id
            JOIN CLIENT s ON a.client_id = s.client_id
            JOIN ACCOUNT b ON t.receiver_account_id = b.account_id
            JOIN CLIENT r ON b.client_id = r.client_id
            WHERE t.sender_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
               OR t.receiver_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
            ORDER BY t.created_at DESC
            LIMIT 5
        ");
        $stmt->bind_param("ii", $clientId, $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Fetch all accounts for a specific client.
     *
     * @param int $clientId The ID of the client.
     * @return array List of accounts.
     */
    public function getFormattedAccounts(int $clientId): string
    {
        $accounts = $this->getAccountsByClientId($clientId);
        return $this->formatAccounts($accounts);
    }
    /**
     * Fetch income and expenses for a client.
     *
     * @param int $clientId The ID of the client.
     * @return array Income and expenses data.
     */
    public function getIncomeAndExpenses($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                SUM(CASE WHEN t.receiver_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?) THEN t.amount ELSE 0 END) AS income,
                SUM(CASE WHEN t.sender_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?) THEN t.amount ELSE 0 END) AS expenses
            FROM TRANSACTION t
        ");
        $stmt->bind_param("ii", $clientId, $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create a new client with optional checking and savings accounts.
     *
     * @param int $adminId The ID of the admin creating the client.
     * @param string $username The username of the client.
     * @param string $email The email of the client.
     * @param string $password The password of the client.
     * @param string $phoneNumber The phone number of the client.
     * @param string $address The address of the client.
     * @param string $status The status of the client.
     * @param float|null $checkingBalance The initial balance for the checking account.
     * @param float|null $savingsBalance The initial balance for the savings account.
     * @param string $currency The currency for the accounts (default: 'USD').
     * @return bool True if the client was created successfully, false otherwise.
     */
    public function create(
        int $adminId,
        string $username,
        string $email,
        string $password,
        string $phoneNumber,
        string $address,
        string $status,
        ?float $checkingBalance = null,
        ?float $savingsBalance = null,
        string $currency = 'USD'
    ): bool {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $this->conn->begin_transaction();

        try {
            // Insert client
            $stmt = $this->conn->prepare("
                INSERT INTO CLIENT (admin_id, username, email, password, phone_number, address, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param(
                "issssss",
                $adminId,
                $username,
                $email,
                $hashedPassword,
                $phoneNumber,
                $address,
                $status
            );
            $stmt->execute();
            $clientId = $stmt->insert_id;

            // Generate account numbers
            $checkingAccountNumber = $checkingBalance !== null ? $this->generateAccountNumber() : null;
            $savingsAccountNumber = $savingsBalance !== null ? $this->generateAccountNumber() : null;

            // Insert checking account
            if ($checkingBalance !== null) {
                $checkingTransactionLimit = $this->calculateTransactionLimit($checkingBalance, 'Checking', $currency);
                $stmt = $this->conn->prepare("
                    INSERT INTO ACCOUNT (
                        client_id, created_by_admin, account_number, account_type, balance, transaction_limit, currency, created_at
                    ) VALUES (?, ?, ?, 'Checking', ?, ?, ?, NOW())
                ");
                $stmt->bind_param(
                    "iisdds",
                    $clientId,
                    $adminId,
                    $checkingAccountNumber,
                    $checkingBalance,
                    $checkingTransactionLimit,
                    $currency
                );
                $stmt->execute();
            }

            // Insert savings account
            if ($savingsBalance !== null) {
                $savingsTransactionLimit = $this->calculateTransactionLimit($savingsBalance, 'Savings', $currency);
                $stmt = $this->conn->prepare("
                    INSERT INTO ACCOUNT (
                        client_id, created_by_admin, account_number, account_type, balance, transaction_limit, currency, created_at
                    ) VALUES (?, ?, ?, 'Savings', ?, ?, ?, NOW())
                ");
                $stmt->bind_param(
                    "iisdds",
                    $clientId,
                    $adminId,
                    $savingsAccountNumber,
                    $savingsBalance,
                    $savingsTransactionLimit,
                    $currency
                );
                $stmt->execute();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error creating client: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate the transaction limit based on balance, account type, and currency.
     *
     * @param float $balance The account balance.
     * @param string $accountType The type of account (e.g., 'Checking', 'Savings').
     * @param string $currency The currency of the account.
     * @return float The calculated transaction limit.
     */
    private function calculateTransactionLimit(float $balance, string $accountType, string $currency): float
    {
        if ($currency === 'USD') {
            if ($accountType === 'Checking') {
                return $balance * 0.4; // 40% for Checking
            } elseif ($accountType === 'Savings') {
                return $balance * 0.3; // 30% for Savings
            }
        } elseif ($currency === 'EUR') {
            return $balance * 0.35; // 35% for EUR
        }
        return 1000.00; // Default limit for other currencies
    }

    /**
     * Generate a unique account number.
     *
     * @return string The generated account number.
     */
    private function generateAccountNumber(): string
    {
        return 'ACC' . str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
    }

    /**
     * Fetch all clients with their associated accounts.
     *
     * @return array List of clients with accounts.
     */
    public function getAllClientsWithAccounts()
    {
        $query = "
            SELECT 
                c.client_id, 
                c.username, 
                c.email, 
                c.phone_number, 
                c.address, 
                c.status, 
                a.account_id, 
                a.account_type, 
                a.balance
            FROM CLIENT c
            LEFT JOIN ACCOUNT a ON c.client_id = a.client_id
            ORDER BY c.client_id ASC
        ";

        $result = $this->conn->query($query);

        if (!$result) {
            error_log("Error fetching clients with accounts: " . $this->conn->error);
            return [];
        }

        // Organize clients and their accounts
        $clients = [];
        while ($row = $result->fetch_assoc()) {
            $clientId = $row['client_id'];

            // Initialize client data if not already set
            if (!isset($clients[$clientId])) {
                $clients[$clientId] = [
                    'client_id' => $row['client_id'],
                    'username' => $row['username'],
                    'email' => $row['email'],
                    'phone_number' => $row['phone_number'],
                    'address' => $row['address'],
                    'status' => $row['status'],
                    'accounts' => []
                ];
            }

            // Add account data if it exists
            if ($row['account_id']) {
                $clients[$clientId]['accounts'][] = [
                    'account_id' => $row['account_id'],
                    'account_type' => $row['account_type'],
                    'balance' => $row['balance']
                ];
            }
        }

        return array_values($clients); // Return as a numerically indexed array
    }

    /**
     * Find a client by their username.
     *
     * @param string $username The username to search for.
     * @return array|null The client data if found, null otherwise.
     */
    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT client_id, username, password, created_at FROM Client WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Find a client by their ID.
     *
     * @param int $clientId The ID of the client.
     * @return array|null The client data if found, null otherwise.
     */
    public function findById($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM CLIENT WHERE client_id = ?
        ");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Fetch all accounts for a specific client.
     *
     * @param int $clientId The ID of the client.
     * @return array List of accounts.
     */
    public function getAccountsByClientId($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM ACCOUNT WHERE client_id = ?
        ");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Update client information and associated accounts.
     *
     * @param int $clientId The ID of the client.
     * @param string $username The new username.
     * @param string $email The new email.
     * @param string $phoneNumber The new phone number.
     * @param string $address The new address.
     * @param string $status The new status.
     * @param float|null $checkingBalance The new balance for the checking account.
     * @param float|null $savingsBalance The new balance for the savings account.
     * @param string $currency The currency for the accounts (default: 'USD').
     * @return bool True if the update was successful, false otherwise.
     */
    public function update(
        int $clientId,
        string $username,
        string $email,
        string $phoneNumber,
        string $address,
        string $status,
        ?float $checkingBalance = null,
        ?float $savingsBalance = null,
        string $currency = 'USD'
    ): bool {
        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Update client information
            $stmt = $this->conn->prepare("
                UPDATE CLIENT 
                SET username = ?, email = ?, phone_number = ?, address = ?, status = ?
                WHERE client_id = ?
            ");
            $stmt->bind_param("sssssi", $username, $email, $phoneNumber, $address, $status, $clientId);
            $stmt->execute();

            // Update or create Checking Account
            if ($checkingBalance !== null) {
                $checkingTransactionLimit = $this->calculateTransactionLimit($checkingBalance, 'Checking', $currency);
                $stmt = $this->conn->prepare("
                    INSERT INTO ACCOUNT (
                        client_id, created_by_admin, account_number, account_type, balance, transaction_limit, currency, created_at
                    ) VALUES (?, ?, ?, 'Checking', ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                        balance = VALUES(balance),
                        transaction_limit = VALUES(transaction_limit),
                        currency = VALUES(currency),
                        created_at = NOW()
                ");
                $accountNumber = $this->generateAccountNumber();
                $stmt->bind_param(
                    "iisdds",
                    $clientId,
                    $_SESSION['user_id'],
                    $accountNumber,
                    $checkingBalance,
                    $checkingTransactionLimit,
                    $currency
                );
                $stmt->execute();
            }

            // Update or create Savings Account
            if ($savingsBalance !== null) {
                $savingsTransactionLimit = $this->calculateTransactionLimit($savingsBalance, 'Savings', $currency);
                $stmt = $this->conn->prepare("
                    INSERT INTO ACCOUNT (
                        client_id, created_by_admin, account_number, account_type, balance, transaction_limit, currency, created_at
                    ) VALUES (?, ?, ?, 'Savings', ?, ?, ?, NOW())
                    ON DUPLICATE KEY UPDATE 
                        balance = VALUES(balance),
                        transaction_limit = VALUES(transaction_limit),
                        currency = VALUES(currency),
                        created_at = NOW()
                ");
                $accountNumber = $this->generateAccountNumber();
                $stmt->bind_param(
                    "iisdds",
                    $clientId,
                    $_SESSION['user_id'],
                    $accountNumber,
                    $savingsBalance,
                    $savingsTransactionLimit,
                    $currency
                );
                $stmt->execute();
            }

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error updating client and accounts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update client profile information.
     *
     * @param int $clientId The ID of the client.
     * @param string $username The new username.
     * @param string $email The new email.
     * @param string $phoneNumber The new phone number.
     * @param string $address The new address.
     * @param string $status The new status.
     * @param string|null $password The new password (optional).
     * @param string|null $profilePicturePath The path to the new profile picture (optional).
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateProfile(
        int $clientId,
        string $username,
        string $email,
        string $phoneNumber,
        string $address,
        string $status,
        ?string $password = null,
        ?string $profilePicturePath = null
    ): bool {
        // Start transaction
        $this->conn->begin_transaction();

        try {
            // Update client information
            $stmt = $this->conn->prepare("
                UPDATE CLIENT 
                SET username = ?, email = ?, phone_number = ?, address = ?, status = ?
                " . ($password !== null ? ", password = ?" : "") . "
                WHERE client_id = ?
            ");
            if ($password !== null) {
                $stmt->bind_param("ssssssi", $username, $email, $phoneNumber, $address, $status, $password, $clientId);
            } else {
                $stmt->bind_param("sssssi", $username, $email, $phoneNumber, $address, $status, $clientId);
            }
            $stmt->execute();

            // Update profile picture if provided
            if ($profilePicturePath !== null) {
                $stmt = $this->conn->prepare("
                    INSERT INTO IMAGE (client_id, file_path, type, created_at)
                    VALUES (?, ?, 'Profile Picture', NOW())
                    ON DUPLICATE KEY UPDATE file_path = VALUES(file_path), created_at = NOW()
                ");
                $stmt->bind_param("is", $clientId, $profilePicturePath);
                $stmt->execute();
            }

            // Commit transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->conn->rollback();
            error_log("Error updating client profile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format accounts for display.
     *
     * @param array $accounts The accounts to format.
     * @return string Formatted account balances.
     */
    public function formatAccounts(array $accounts): string
    {
        // Initialize both balances to 0.00
        $checkingBalance = 0.00;
        $savingsBalance = 0.00;

        // Update balances if accounts exist
        foreach ($accounts as $account) {
            if ($account['account_type'] === 'Checking') {
                $checkingBalance = $account['balance'];
            } elseif ($account['account_type'] === 'Savings') {
                $savingsBalance = $account['balance'];
            }
        }

        // Always show both accounts
        return "Checking: $" . number_format($checkingBalance, 2) . "<br>Savings: $" . number_format($savingsBalance, 2);
    }

    /**
     * Delete a client and all associated records.
     *
     * @param int $clientId The ID of the client.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete(int $clientId): bool
    {
        // Start a transaction to ensure atomicity
        $this->conn->begin_transaction();

        try {
            // Delete images associated with the client
            $stmt = $this->conn->prepare("DELETE FROM IMAGE WHERE client_id = ?");
            $stmt->bind_param("i", $clientId);
            $stmt->execute();

            // Delete accounts associated with the client
            $stmt = $this->conn->prepare("DELETE FROM ACCOUNT WHERE client_id = ?");
            $stmt->bind_param("i", $clientId);
            $stmt->execute();

            // Delete the client record
            $stmt = $this->conn->prepare("DELETE FROM CLIENT WHERE client_id = ?");
            $stmt->bind_param("i", $clientId);
            $stmt->execute();

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->conn->rollback();
            error_log("Error deleting client and related records: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all clients.
     *
     * @return array List of clients.
     */
    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM Client");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Fetch the profile picture for a client.
     *
     * @param int $clientId The ID of the client.
     * @return string|null The file path of the profile picture if found, null otherwise.
     */
    public function getProfilePicture($clientId)
    {
        $stmt = $this->conn->prepare("
            SELECT file_path 
            FROM IMAGE 
            WHERE client_id = ? AND type = 'Profile Picture'
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc()['file_path'];
        }

        return null; // Return null if no profile picture is found
    }

    /**
     * Fetch an account by account number.
     *
     * @param string $accountNumber The account number.
     * @return array|null Account details if found, null otherwise.
     */
    public function getAccountByNumber($accountNumber)
    {
        $stmt = $this->conn->prepare("
            SELECT account_id, client_id, account_number, balance 
            FROM ACCOUNT 
            WHERE account_number = ?
        ");
        $stmt->bind_param("s", $accountNumber);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Fetch the admin ID associated with a client.
     *
     * @param int $clientId The ID of the client.
     * @return int|null The admin ID if found, null otherwise.
     */
    public function getAdminId($clientId)
    {
        $stmt = $this->conn->prepare("SELECT admin_id FROM CLIENT WHERE client_id = ?");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['admin_id'];
    }

    /**
     * Fetch all active accounts.
     *
     * @return array List of active accounts.
     */
    public function getActiveAccounts()
    {
        $query = "
            SELECT a.account_id, a.account_number, c.username 
            FROM ACCOUNT a
            JOIN CLIENT c ON a.client_id = c.client_id
            WHERE a.status = 'Active'
        ";
        $result = $this->conn->query($query);

        if (!$result) {
            error_log("Error fetching active accounts: " . $this->conn->error);
            return [];
        }

        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
