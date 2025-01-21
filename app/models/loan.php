<?php
class Loan
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create a new loan request
    public function create($clientId, $amount, $interestRate, $termMonths, $loanType, $message = '')
    {
        $stmt = $this->db->prepare("
INSERT INTO LOAN (client_id, amount, interest_rate, term_months, loan_type, status, created_at)
VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
");
        $stmt->bind_param("iddis", $clientId, $amount, $interestRate, $termMonths, $loanType);
        return $stmt->execute();
    }

    // Get all loans for a client
    public function getByClientId($clientId)
    {
        $stmt = $this->db->prepare("SELECT * FROM LOAN WHERE client_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get all loans (for admin)
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM LOAN ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Approve a loan
    public function approve($loanId)
    {
        $stmt = $this->db->prepare("UPDATE LOAN SET status = 'Approved' WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }

    // Reject a loan
    public function reject($loanId)
    {
        $stmt = $this->db->prepare("UPDATE LOAN SET status = 'Rejected' WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }

    // Delete a loan
    public function delete($loanId)
    {
        $stmt = $this->db->prepare("DELETE FROM LOAN WHERE loan_id = ?");
        $stmt->bind_param("i", $loanId);
        return $stmt->execute();
    }
}
