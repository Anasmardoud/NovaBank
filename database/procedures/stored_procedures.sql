--Some functions and Procedures



--1. Fund Transfer Between Accounts (Stored Procedure)
/*Deduct from sender's account and add to the recipient's account in a single transaction. */

DELIMITER //

CREATE PROCEDURE FundTransfer(
    IN SenderAccountID INT,
    IN RecipientAccountID INT,
    IN Amount DECIMAL(18, 2)
)
BEGIN
    DECLARE CurrentBalance DECIMAL(18, 2);

    -- Start transaction
    START TRANSACTION;

    -- Check sender's balance
    SELECT balance INTO CurrentBalance
    FROM ACCOUNT
    WHERE account_id = SenderAccountID;

    IF CurrentBalance < Amount THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Insufficient balance in sender''s account.';
    ELSE
        -- Deduct from sender
        UPDATE ACCOUNT
        SET balance = balance - Amount
        WHERE account_id = SenderAccountID;

        -- Add to recipient
        UPDATE ACCOUNT
        SET balance = balance + Amount
        WHERE account_id = RecipientAccountID;

        -- Insert transaction for sender
        INSERT INTO TRANSACTION (account_id, transaction_date, transaction_type, amount, balance)
        VALUES (SenderAccountID, NOW(), 'DEBIT', Amount, (SELECT balance FROM ACCOUNT WHERE account_id = SenderAccountID));

        -- Insert transaction for recipient
        INSERT INTO TRANSACTION (account_id, transaction_date, transaction_type, amount, balance)
        VALUES (RecipientAccountID, NOW(), 'CREDIT', Amount, (SELECT balance FROM ACCOUNT WHERE account_id = RecipientAccountID));
    END IF;

    -- Commit transaction
    COMMIT;
END;
//

DELIMITER ;


 -- may be in the future
--2. Generate Account Statements (Stored Procedure)
/*Fetch transactions for a specific account within a given date range and format them. */

DELIMITER //

CREATE PROCEDURE GenerateAccountStatement(
    IN AccountID INT,
    IN StartDate DATE,
    IN EndDate DATE
)
BEGIN
    SELECT 
        transaction_id,
        created_at AS transaction_date, -- Use 'created_at' instead of 'transaction_date'
        transaction_type,
        amount,
        (SELECT balance FROM ACCOUNT WHERE account_id = AccountID) AS balance
    FROM TRANSACTION
    WHERE (sender_account_id = AccountID OR receiver_account_id = AccountID)
      AND created_at BETWEEN StartDate AND EndDate
    ORDER BY created_at DESC;
END;

DELIMITER ;

/*Stored Procedure for Creating a Deposit
The stored procedure simplifies the deposit process and ensures admin-only access.*/
DELIMITER $$

CREATE PROCEDURE CreateDeposit(
    IN p_admin_id INT,
    IN p_account_id INT,
    IN p_amount DECIMAL(10, 2),
    OUT p_status_message VARCHAR(255)
)
BEGIN
    DECLARE account_status VARCHAR(10);

    -- Check if the account exists and get its status
    SELECT status INTO account_status
    FROM ACCOUNT
    WHERE account_id = p_account_id;

    -- Handle account not found or inactive cases
    IF account_status IS NULL THEN
        SET p_status_message = 'Account does not exist. Deposit cannot be made.';
    ELSEIF account_status != 'active' THEN
        SET p_status_message = 'Account is inactive. Deposit cannot be made.';
    ELSEIF p_amount <= 0 THEN
        -- Ensure the deposit amount is positive
        SET p_status_message = 'Deposit amount must be greater than zero.';
    ELSE
        -- Insert the deposit record
        INSERT INTO DEPOSIT (account_id, admin_id, amount, created_at, status)
        VALUES (p_account_id, p_admin_id, p_amount, NOW(), 'completed');

        -- Set success message
        SET p_status_message = 'Deposit successfully created.';
    END IF;
END$$

DELIMITER ;


DELIMITER ;

/*Stored Procedure for show the deposit history */
--i use it
DELIMITER $$

CREATE PROCEDURE ShowDepositHistory(
    IN accountNumber INT
)
BEGIN
    SELECT 
        D.deposit_id AS DepositID,
        A.account_number AS AccountNumber,
        ADM.username AS AdminUsername,
        D.amount AS DepositAmount,
        D.status AS DepositStatus,
        D.created_at AS DepositDate
    FROM 
        DEPOSIT D
    JOIN 
        ACCOUNT A ON D.account_id = A.account_id
    JOIN 
        ADMIN ADM ON D.admin_id = ADM.admin_id
    WHERE 
        A.account_number = accountNumber
    ORDER BY 
        D.created_at DESC;
END$$

DELIMITER ;
DELIMITER $$
DELIMITER $$

CREATE PROCEDURE CalculateLoan(IN p_loan_id INT)
BEGIN
    DECLARE v_loan_amount DECIMAL(15, 2);
    DECLARE v_interest_rate DECIMAL(5, 2);
    DECLARE v_term_months INT;
    DECLARE v_monthly_payment DECIMAL(15, 2);
    DECLARE v_total_interest DECIMAL(15, 2);
    DECLARE v_remaining_balance DECIMAL(15, 2);
    DECLARE v_start_date DATE;
    DECLARE v_end_date DATE;

    -- Fetch loan details
    SELECT
        amount,
        interest_rate,
        term_months,
        start_date
    INTO v_loan_amount, v_interest_rate, v_term_months, v_start_date
    FROM LOAN
    WHERE loan_id = p_loan_id;

    -- Check if term_months is greater than 0 to avoid division by zero
    IF v_term_months > 0 THEN
        -- Calculate monthly interest rate
        SET v_interest_rate = v_interest_rate / 100 / 12;

        -- Calculate monthly payment using the loan formula
        SET v_monthly_payment = (v_loan_amount * v_interest_rate) / (1 - POW(1 + v_interest_rate, -v_term_months));

        -- Calculate total interest
        SET v_total_interest = (v_monthly_payment * v_term_months) - v_loan_amount;

        -- Calculate end date
        SET v_end_date = DATE_ADD(v_start_date, INTERVAL v_term_months MONTH);
    ELSE
        SET v_monthly_payment = 0;  -- Default value to avoid NULL
        SET v_total_interest = 0;   -- Default value
        SET v_end_date = v_start_date;  -- Same start date if no term
    END IF;

    -- Update the LOAN table with calculated values
    UPDATE LOAN
    SET
        monthly_payment = v_monthly_payment,
        total_interest = v_total_interest,
        remaining_balance = v_loan_amount,
        end_date = v_end_date
    WHERE loan_id = p_loan_id;

    -- Output success message
    SELECT 'Loan calculation completed successfully.' AS status_message;
END$$

DELIMITER ;
