/*---------------------------------Stored Procedures---------------------------------*/
      --1. Fund Transfer Between Accounts                                            *
      --2. Generate Account Statements                                               *
      --3. Stored Procedure for Creating a Deposit                                   *
      --4. Stored Procedure to Show Deposit History                                  *
      --5. Stored Procedure to Calculate Loan                                        *
      --6. Stored Procedure to check and calculate loans using a cursor              *
/*-------------------------------------------------------------------------------------*/
/*--------------------------------Function---------------------------------------------------*/
  -- Function to calculate interest for a given amount and interest rate                    *
---------------------------------------------------------------------------------------------*/
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
--------------------------------------------------------------------------------
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
        created_at AS transaction_date,
        transaction_type,
        amount,
        (SELECT balance FROM ACCOUNT WHERE account_id = AccountID) AS balance
    FROM TRANSACTION
    WHERE (sender_account_id = AccountID OR receiver_account_id = AccountID)
      AND created_at BETWEEN StartDate AND EndDate
    ORDER BY created_at DESC;
END;

DELIMITER ;
--------------------------------------------------------------------------------
--3.Stored Procedure for Creating a Deposit
--The stored procedure simplifies the deposit process and ensures admin-only access.
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
--------------------------------------------------------------------------------
--4. Stored Procedure to Show Deposit History
-- This procedure fetches deposit history for a specific account number.
-- It joins the DEPOSIT, ACCOUNT, and ADMIN tables to display relevant information.
-- The results are ordered by deposit date in descending order.
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
----------------------------------------------------------------
--1.function to calculate monthly payment for a loan
--The function takes loan amount, interest rate, and term in months as input parameters
CREATE FUNCTION CalculateMonthlyPayment(
    p_loan_amount DECIMAL(15,2),
    p_interest_rate DECIMAL(5,2),
    p_term_months INT
) RETURNS DECIMAL(15,2)
DETERMINISTIC
BEGIN
    DECLARE v_monthly_interest DECIMAL(10,6);
    DECLARE v_monthly_payment DECIMAL(15,2);

    -- Check if term_months is greater than 0 to avoid division by zero
    IF p_term_months > 0 THEN
        SET v_monthly_interest = p_interest_rate / 100 / 12;
        SET v_monthly_payment = (p_loan_amount * v_monthly_interest) / 
                                (1 - POW(1 + v_monthly_interest, -p_term_months));
    ELSE
        SET v_monthly_payment = 0;
    END IF;

    RETURN v_monthly_payment;
END$$

DELIMITER ;
--------------------------------------------------------------------------------
-- 5.Stored Procedure to Calculate Loan
-- This procedure calculates the monthly payment, total interest, and end date for a loan.
-- It updates the LOAN table with the calculated values.
-- The procedure calls the CalculateMonthlyPayment function to calculate the monthly payment.
DELIMITER $$

CREATE PROCEDURE CalculateLoan(IN p_loan_id INT)
BEGIN
    DECLARE v_loan_amount DECIMAL(15,2);
    DECLARE v_interest_rate DECIMAL(5,2);
    DECLARE v_term_months INT;
    DECLARE v_monthly_payment DECIMAL(15,2);
    DECLARE v_total_interest DECIMAL(15,2);
    DECLARE v_start_date DATE;
    DECLARE v_end_date DATE;

    -- Fetch loan details
    SELECT amount, interest_rate, term_months, start_date
    INTO v_loan_amount, v_interest_rate, v_term_months, v_start_date
    FROM LOAN
    WHERE loan_id = p_loan_id;

    -- Calculate monthly payment using the function
    SET v_monthly_payment = CalculateMonthlyPayment(v_loan_amount, v_interest_rate, v_term_months);

    -- Calculate total interest
    SET v_total_interest = (v_monthly_payment * v_term_months) - v_loan_amount;

    -- Calculate end date
    SET v_end_date = DATE_ADD(v_start_date, INTERVAL v_term_months MONTH);

    -- Update the LOAN table with calculated values
    UPDATE LOAN
    SET monthly_payment = v_monthly_payment,
        total_interest = v_total_interest,
        remaining_balance = v_loan_amount,
        end_date = v_end_date
    WHERE loan_id = p_loan_id;

    -- Output success message
    SELECT 'Loan calculation completed successfully.' AS status_message;
END$$

DELIMITER ;
--------------------------------------------------------------------------------
--6. Stored Procedure to check and calculate loans
-- Stored Procedure uses a cursor to iterate through all loans in the LOAN table,
-- checks if monthly_payment is NULL or 0, and if so, calls the CalculateLoan procedure.
-- This procedure updates the LOAN table with the calculated values.
DELIMITER $$

CREATE PROCEDURE CheckAndCalculateLoans()
BEGIN
    DECLARE v_loan_id INT;
    DECLARE done INT DEFAULT 0;

    -- Declare cursor to select loans that need calculation
    DECLARE loan_cursor CURSOR FOR 
    SELECT loan_id FROM LOAN 
    WHERE monthly_payment IS NULL OR monthly_payment = 0;

    -- Declare exit handler for when no more rows are found
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Open cursor
    OPEN loan_cursor;

    loan_loop: LOOP
        -- Fetch loan_id into variable
        FETCH loan_cursor INTO v_loan_id;

        -- Exit loop if no more loans
        IF done = 1 THEN
            LEAVE loan_loop;
        END IF;

        -- Call CalculateLoan procedure
        CALL CalculateLoan(v_loan_id);
    END LOOP;

    -- Close cursor
    CLOSE loan_cursor;

    -- Output message
    SELECT 'Loan calculations checked and updated successfully.' AS status_message;
END$$

DELIMITER ;
