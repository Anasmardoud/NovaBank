<?php
class Deposit
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create a new deposit using the CreateDeposit procedure
    public function create($accountId, $adminId, $amount)
    {
        $stmt = $this->db->prepare("CALL CreateDeposit(?, ?, ?, @status_message)");
        $stmt->bind_param("iid", $accountId, $adminId, $amount);

        try {
            $stmt->execute();
            $result = $this->db->query("SELECT @status_message AS status_message");
            $statusMessage = $result->fetch_assoc()['status_message'];
            Helper::log("Deposit created: $statusMessage", 'INFO');
            return $statusMessage;
        } catch (mysqli_sql_exception $e) {
            Helper::log("Deposit creation failed: " . $e->getMessage(), 'ERROR');
            return "Deposit creation failed.";
        }
    }

    // Fetch deposit history using the ShowDepositHistory procedure
    public function getDepositHistory($accountNumber)
    {
        $stmt = $this->db->prepare("CALL ShowDepositHistory(?)");
        $stmt->bind_param("i", $accountNumber);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            Helper::log("Failed to fetch deposit history: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    // Get a single deposit by ID
    public function getDepositById($depositId)
    {
        $stmt = $this->db->prepare("SELECT * FROM DEPOSIT WHERE deposit_id = ?");
        $stmt->bind_param("i", $depositId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Delete a deposit
    public function delete($depositId)
    {
        $stmt = $this->db->prepare("DELETE FROM DEPOSIT WHERE deposit_id = ?");
        $stmt->bind_param("i", $depositId);
        return $stmt->execute();
    }
}
