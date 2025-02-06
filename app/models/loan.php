<?php
require_once __DIR__ . '/Database.php';

class Loan
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
     * Create a new loan.
     *
     * @param int $clientId The ID of the client.
     * @param int $adminId The ID of the admin.
     * @param float $amount The loan amount.
     * @param float $interestRate The interest rate.
     * @param int $termMonths The loan term in months.
     * @param string $loanType The type of loan.
     * @param string $message Additional message (optional).
     * @return bool True if the loan was created successfully, false otherwise.
     */
    public function create($clientId, $adminId, $amount, $interestRate, $termMonths, $loanType, $message = '')
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("
                INSERT INTO LOAN (client_id, admin_id, amount, interest_rate, term_months, loan_type, status, created_at, message, monthly_payment)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending', NOW(), ?, 0)
            ");
            $stmt->bind_param("iiddiss", $clientId, $adminId, $amount, $interestRate, $termMonths, $loanType, $message);
            $this->executeStatement($stmt);

            // Calculate loan details
            $loanId = $stmt->insert_id;
            $this->calculateLoanDetails($loanId);

            return true;
        } catch (Exception $e) {
            error_log("Error creating loan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch the last five loans.
     *
     * @return array List of the last five loans.
     */
    public function getLastFiveLoans()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM LOAN ORDER BY created_at DESC LIMIT 5");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch last five loans: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch loans by admin ID.
     *
     * @param int $adminId The ID of the admin.
     * @return array List of loans.
     */
    public function getLoansByAdminId($adminId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("
                SELECT l.*, c.username 
                FROM LOAN l
                JOIN CLIENT c ON l.client_id = c.client_id
                WHERE l.admin_id = ?
                ORDER BY l.created_at DESC
            ");
            $stmt->bind_param("i", $adminId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
        } catch (Exception $e) {
            error_log("Failed to fetch loans by admin ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch loans with pagination.
     *
     * @param int $offset The offset for pagination.
     * @param int $limit The limit for pagination.
     * @return array List of loans.
     */
    public function getLoansPaginated($offset, $limit)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM LOAN ORDER BY created_at DESC LIMIT ?, ?");
            $stmt->bind_param("ii", $offset, $limit);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch paginated loans: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch the total number of loans.
     *
     * @return int Total number of loans.
     */
    public function getTotalLoansCount()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT COUNT(*) as total FROM LOAN");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_assoc()['total'];
        } catch (Exception $e) {
            error_log("Failed to fetch total loans count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Fetch loans by client ID.
     *
     * @param int $clientId The ID of the client.
     * @return array List of loans.
     */
    public function getByClientId($clientId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM LOAN WHERE client_id = ? ORDER BY created_at DESC");
            $stmt->bind_param("i", $clientId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch loans by client ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Fetch all loans.
     *
     * @return array List of all loans.
     */
    public function getAll()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM LOAN ORDER BY created_at DESC");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch all loans: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Approve a loan.
     *
     * @param int $loanId The ID of the loan.
     * @return bool True if the loan was approved successfully, false otherwise.
     */
    public function approve($loanId)
    {
        // Start a transaction
        $this->conn->begin_transaction();

        try {
            // Update loan status to "Approved" and set the start_date to NOW()
            $stmt = $this->prepareStatement("UPDATE LOAN SET status = 'Approved', start_date = NOW() WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            $this->executeStatement($stmt);

            // Calculate loan details
            $this->calculateLoanDetails($loanId);

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->conn->rollback();
            error_log("Error approving loan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reject a loan.
     *
     * @param int $loanId The ID of the loan.
     * @return bool True if the loan was rejected successfully, false otherwise.
     */
    public function reject($loanId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("UPDATE LOAN SET status = 'Rejected' WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Error rejecting loan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch all loans with client details.
     *
     * @return array List of loans with client details.
     */
    public function getAllLoansWithClients()
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("
                SELECT l.*, c.username 
                FROM LOAN l
                JOIN CLIENT c ON l.client_id = c.client_id
                ORDER BY l.created_at DESC
            ");
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Failed to fetch loans with clients: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update and calculate loan details.
     *
     * @param int $loanId The ID of the loan.
     * @param float $amount The new loan amount.
     * @param float $interestRate The new interest rate.
     * @param int $termMonths The new loan term in months.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateAndCalculateLoan($loanId, $amount, $interestRate, $termMonths)
    {
        // Start a transaction
        $this->conn->begin_transaction();

        try {
            // Update loan details
            $stmt = $this->prepareStatement("
                UPDATE LOAN 
                SET amount = ?, interest_rate = ?, term_months = ?, start_date = NOW()
                WHERE loan_id = ?
            ");
            $stmt->bind_param("ddii", $amount, $interestRate, $termMonths, $loanId);
            $this->executeStatement($stmt);

            // Calculate loan details
            $this->calculateLoanDetails($loanId);

            // Commit the transaction
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            // Rollback the transaction on error
            $this->conn->rollback();
            error_log("Error updating and calculating loan: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate loan details (monthly payment, total interest, etc.).
     *
     * @param int $loanId The ID of the loan.
     * @return bool True if the calculation was successful, false otherwise.
     */
    private function calculateLoanDetails($loanId)
    {
        try {
            // Fetch loan details
            $stmt = $this->prepareStatement("SELECT amount, interest_rate, term_months FROM LOAN WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            $this->executeStatement($stmt);
            $stmt->bind_result($amount, $interestRate, $termMonths);
            $stmt->fetch();
            $stmt->close();

            // Validate loan details
            if (empty($amount) || empty($interestRate) || empty($termMonths)) {
                throw new Exception("Invalid loan details retrieved for loan_id: $loanId");
            }

            // Initialize calculated values
            $monthlyPayment = 0;
            $totalInterest = 0;
            $endDate = date('Y-m-d'); // Default to current date if term_months is 0

            // Perform calculations if term_months is greater than 0
            if ($termMonths > 0) {
                // Convert annual interest rate to monthly and decimal format
                $monthlyInterestRate = ($interestRate / 100) / 12;

                // Calculate monthly payment using the loan formula
                $monthlyPayment = ($amount * $monthlyInterestRate) / (1 - pow(1 + $monthlyInterestRate, -$termMonths));

                // Calculate total interest
                $totalInterest = ($monthlyPayment * $termMonths) - $amount;

                // Calculate end date
                $endDate = date('Y-m-d', strtotime("+$termMonths months"));
            }

            // Update the LOAN table with calculated values
            $stmt = $this->prepareStatement("
                UPDATE LOAN 
                SET 
                    monthly_payment = ?, 
                    total_interest = ?, 
                    remaining_balance = ?, 
                    end_date = ?
                WHERE loan_id = ?
            ");
            $stmt->bind_param(
                "dddsi",
                $monthlyPayment,
                $totalInterest,
                $amount, // Remaining balance is the same as the loan amount initially
                $endDate,
                $loanId
            );
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Error calculating loan details: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch a loan by ID.
     *
     * @param int $loanId The ID of the loan.
     * @return array|null Loan details if found, null otherwise.
     */
    public function getLoanById($loanId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT * FROM LOAN WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_assoc();
        } catch (Exception $e) {
            error_log("Failed to fetch loan by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Count the number of loans for a specific client.
     *
     * @param int $clientId The ID of the client.
     * @return int Number of loans.
     */
    public function countLoansByClientId($clientId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("SELECT COUNT(*) as total FROM LOAN WHERE client_id = ?");
            $stmt->bind_param("i", $clientId);
            $this->executeStatement($stmt);

            // Fetch the result
            return $stmt->get_result()->fetch_assoc()['total'];
        } catch (Exception $e) {
            error_log("Failed to count loans by client ID: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Delete a loan by ID.
     *
     * @param int $loanId The ID of the loan.
     * @return bool True if the deletion was successful, false otherwise.
     */
    public function delete($loanId)
    {
        try {
            // Prepare and execute the query
            $stmt = $this->prepareStatement("DELETE FROM LOAN WHERE loan_id = ?");
            $stmt->bind_param("i", $loanId);
            return $this->executeStatement($stmt);
        } catch (Exception $e) {
            error_log("Failed to delete loan: " . $e->getMessage());
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
