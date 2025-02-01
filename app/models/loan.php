<?php
require_once __DIR__ . '/Database.php';

class Loan
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function create($clientId, $amount, $interestRate, $termMonths, $loanType, $message = '')
    {
        $stmt = $this->conn->prepare("
            INSERT INTO LOAN (client_id, amount, interest_rate, term_months, loan_type, status, created_at)
            VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
        ");
        $stmt->bind_param("iddis", $clientId, $amount, $interestRate, $termMonths, $loanType);
        return $stmt->execute();
    }

    public function getByClientId($clientId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM LOAN WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM LOAN ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function approve($loanId)
    {
        $stmt = $this->conn->prepare("UPDATE LOAN SET status = 'Approved' WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }

    public function reject($loanId)
    {
        $stmt = $this->conn->prepare("UPDATE LOAN SET status = 'Rejected' WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }

    public function delete($loanId)
    {
        $stmt = $this->conn->prepare("DELETE FROM LOAN WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }
}
