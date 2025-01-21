<?php
class Transaction
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Transfer funds between accounts using the FundTransfer procedure
    public function transferFunds($senderAccountId, $recipientAccountId, $amount)
    {
        $stmt = $this->db->prepare("CALL FundTransfer(?, ?, ?)");
        $stmt->bind_param("iid", $senderAccountId, $recipientAccountId, $amount);

        try {
            $stmt->execute();
            Helper::log("Funds transferred from account $senderAccountId to account $recipientAccountId", 'INFO');
            return true;
        } catch (mysqli_sql_exception $e) {
            Helper::log("Fund transfer failed: " . $e->getMessage(), 'ERROR');
            return false;
        }
    }

    // Generate account statement using the GenerateAccountStatement procedure
    public function getAccountStatement($accountId, $startDate, $endDate)
    {
        $stmt = $this->db->prepare("CALL GenerateAccountStatement(?, ?, ?)");
        $stmt->bind_param("iss", $accountId, $startDate, $endDate);

        try {
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (mysqli_sql_exception $e) {
            Helper::log("Failed to generate account statement: " . $e->getMessage(), 'ERROR');
            return [];
        }
    }

    // Get all transactions for a client
    public function getByClientId($clientId)
    {
        $stmt = $this->db->prepare("
SELECT * FROM TRANSACTION
WHERE sender_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
OR receiver_account_id IN (SELECT account_id FROM ACCOUNT WHERE client_id = ?)
ORDER BY created_at DESC
");
        $stmt->bind_param("ii", $clientId, $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get transaction by ID
    public function findById($transactionId)
    {
        $stmt = $this->db->prepare("SELECT * FROM TRANSACTION WHERE transaction_id = ?");
        $stmt->bind_param("i", $transactionId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Delete a transaction
    public function delete($transactionId)
    {
        $stmt = $this->db->prepare("DELETE FROM TRANSACTION WHERE transaction_id = ?");
        $stmt->bind_param("i", $transactionId);
        return $stmt->execute();
    }
}
