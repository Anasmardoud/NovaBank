<?php
require_once __DIR__ . '/Database.php';

class Deposit
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
     * Create a new deposit using the CreateDeposit stored procedure.
     *
     * @param int $accountId The account ID.
     * @param int $adminId The admin ID.
     * @param float $amount The deposit amount.
     * @return string Status message (success or error).
     */
    public function create($accountId, $adminId, $amount)
    {
        try {
            // Prepare and execute the stored procedure
            $stmt = $this->prepareStatement("CALL CreateDeposit(?, ?, ?, @status_message)");
            $stmt->bind_param("iid", $adminId, $accountId, $amount);
            $this->executeStatement($stmt);

            // Fetch the status message
            $statusMessage = $this->fetchStatusMessage();
            error_log("Deposit created: $statusMessage", 0);

            return $statusMessage;
        } catch (Exception $e) {
            error_log("Deposit creation failed: " . $e->getMessage(), 0);
            return "Deposit creation failed: " . $e->getMessage();
        }
    }

    /**
     * Fetch deposit history for a specific account using the ShowDepositHistory stored procedure.
     *
     * @param int $accountNumber The account number.
     * @return array Deposit history.
     */
    public function getDepositHistory($accountNumber)
    {
        try {
            // Prepare and execute the stored procedure
            $stmt = $this->prepareStatement("CALL ShowDepositHistory(?)");
            $stmt->bind_param("i", $accountNumber);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch deposit history: " . $e->getMessage(), 0);
            return [];
        }
    }

    /**
     * Get a single deposit by ID.
     *
     * @param int $depositId The deposit ID.
     * @return array|null Deposit details or null if not found.
     */
    public function getDepositById($depositId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM DEPOSIT WHERE deposit_id = ?");
            $stmt->bind_param("i", $depositId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Failed to fetch deposit by ID: " . $e->getMessage(), 0);
            return null;
        }
    }

    /**
     * Fetch all deposits.
     *
     * @return array List of all deposits.
     */
    public function getAll()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM DEPOSIT ORDER BY created_at DESC");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch all deposits: " . $e->getMessage(), 0);
            return [];
        }
    }

    /**
     * Delete a deposit by ID.
     *
     * @param int $depositId The deposit ID.
     * @return bool True if successful, false otherwise.
     */
    public function delete($depositId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("DELETE FROM DEPOSIT WHERE deposit_id = ?");
            $stmt->bind_param("i", $depositId);
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Failed to delete deposit: " . $e->getMessage(), 0);
            return false;
        }
    }

    /**
     * Update a deposit by ID.
     *
     * @param int $depositId The deposit ID.
     * @param float $amount The new deposit amount.
     * @param string $status The new deposit status.
     * @return bool True if successful, false otherwise.
     */
    public function update($depositId, $amount, $status)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("
                UPDATE DEPOSIT
                SET amount = ?, status = ?
                WHERE deposit_id = ?
            ");
            $stmt->bind_param("dsi", $amount, $status, $depositId);
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Error updating deposit: " . $e->getMessage(), 0);
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

    /**
     * Fetch the status message after executing a stored procedure.
     *
     * @return string The status message.
     * @throws Exception If fetching the status message fails.
     */
    private function fetchStatusMessage()
    {
        $result = $this->conn->query("SELECT @status_message AS status_message");
        if (!$result) {
            throw new Exception("Failed to fetch status message: " . $this->conn->error);
        }
        return $result->fetch_assoc()['status_message'];
    }
}
