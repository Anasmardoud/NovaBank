<?php
require_once __DIR__ . '/Database.php';

class Deposit
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    // Create a new deposit using the CreateDeposit procedure
    public function create($accountId, $adminId, $amount)
    {
        $stmt = $this->conn->prepare("CALL CreateDeposit(?, ?, ?, @status_message)");
        $stmt->bind_param("iid", $accountId, $adminId, $amount);

        try {
            $stmt->execute();
            $result = $this->conn->query("SELECT @status_message AS status_message");
            $statusMessage = $result->fetch_assoc()['status_message'];
            error_log("Deposit created: $statusMessage", 0);
            return $statusMessage;
        } catch (mysqli_sql_exception $e) {
            error_log("Deposit creation failed: " . $e->getMessage(), 0);
            return "Deposit creation failed.";
        }
    }

    // Fetch deposit history using the ShowDepositHistory procedure
    public function getDepositHistory($accountNumber)
    {
        $stmt = $this->conn->prepare("CALL ShowDepositHistory(?)");
        $stmt->bind_param("i", $accountNumber);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            error_log("Failed to fetch deposit history: " . $e->getMessage(), 0);
            return [];
        }
    }

    // Get a single deposit by ID
    public function getDepositById($depositId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM DEPOSIT WHERE deposit_id = ?");
        $stmt->bind_param("i", $depositId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Delete a deposit
    public function delete($depositId)
    {
        $stmt = $this->conn->prepare("DELETE FROM DEPOSIT WHERE deposit_id = ?");
        $stmt->bind_param("i", $depositId);
        return $stmt->execute();
    }
}
