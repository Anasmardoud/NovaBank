<?php
require_once __DIR__ . '/Database.php';

class Transaction
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
     * Fetch all deposits.
     *
     * @return array List of deposits.
     */
    public function getDeposits()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM DEPOSIT ORDER BY created_at DESC");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch deposits: " . $e->getMessage(), 0);
            return [];
        }
    }

    /**
     * Transfer funds between accounts using the FundTransfer stored procedure.
     *
     * @param int $senderAccountId The ID of the sender account.
     * @param int $recipientAccountId The ID of the recipient account.
     * @param float $amount The amount to transfer.
     * @return bool True if the transfer was successful, false otherwise.
     */
    public function transferFunds($senderAccountId, $recipientAccountId, $amount)
    {
        try {
            // Prepare and execute the stored procedure
            $stmt = $this->prepareStatement("CALL FundTransfer(?, ?, ?)");
            $stmt->bind_param("iid", $senderAccountId, $recipientAccountId, $amount);
            $this->executeStatement($stmt);

            error_log("Funds transferred from account $senderAccountId to account $recipientAccountId", 0);
            return true;
        } catch (Exception $e) {
            error_log("Fund transfer failed: " . $e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Generate an account statement using the GenerateAccountStatement stored procedure.
     *
     * @param int $accountId The ID of the account.
     * @param string $startDate The start date of the statement.
     * @param string $endDate The end date of the statement.
     * @return array Account statement data.
     */
    public function getAccountStatement($accountId, $startDate, $endDate)
    {
        try {
            // Prepare and execute the stored procedure
            $stmt = $this->prepareStatement("CALL GenerateAccountStatement(?, ?, ?)");
            $stmt->bind_param("iss", $accountId, $startDate, $endDate);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to generate account statement: " . $e->getMessage(), 0);
            return [];
        }
    }

    /**
     * Fetch all transactions for a specific client.
     *
     * @param int $clientId The ID of the client.
     * @return array List of transactions.
     */
    public function getByClientId($clientId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("
                SELECT * FROM TRANSACTION
                WHERE sender_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
                OR receiver_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
                ORDER BY created_at DESC
            ");
            $stmt->bind_param("ii", $clientId, $clientId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch transactions by client ID: " . $e->getMessage(), 0);
            return [];
        }
    }

    /**
     * Fetch a transaction by its ID.
     *
     * @param int $transactionId The ID of the transaction.
     * @return array|null Transaction details if found, null otherwise.
     */
    public function findById($transactionId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM TRANSACTION WHERE transaction_id = ?");
            $stmt->bind_param("i", $transactionId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Failed to fetch transaction by ID: " . $e->getMessage(), 0);
            return null;
        }
    }

    /**
     * Delete a transaction by its ID.
     *
     * @param int $transactionId The ID of the transaction.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete($transactionId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("DELETE FROM TRANSACTION WHERE transaction_id = ?");
            $stmt->bind_param("i", $transactionId);
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Failed to delete transaction: " . $e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Prepare a SQL statement.
     *
     * @param string $sql The SQL query.
     * @return mysqli_stmt The prepared statement.
     * @throws Exception If the statement preparation fails.
     */
    private function prepareStatement($sql)
    {
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Failed to prepare statement: " . $this->conn->error);
        }
        return $stmt;
    }

    /**
     * Execute a prepared statement.
     *
     * @param mysqli_stmt $stmt The prepared statement.
     * @return bool True if execution is successful, false otherwise.
     * @throws Exception If the execution fails.
     */
    private function executeStatement($stmt)
    {
        if (!$stmt->execute()) {
            throw new Exception("Failed to execute statement: " . $stmt->error);
        }
        return true;
    }
}
