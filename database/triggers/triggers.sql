/*==============================================================*/
/* DBMS name:               MYSQL                               */
/* Created on:              1/13/2025                           */
/*==============================================================*/
------------------------------------------------------------------
/*          Triggers for Nova Bank                               */
------------------------------------------------------------------

-- Trigger for INSERT on ACCOUNT
DELIMITER $$
CREATE TRIGGER ti_account
BEFORE INSERT ON ACCOUNT
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when inserting into ACCOUNT
    IF NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in CLIENT. Cannot create child in ACCOUNT.';
    END IF;
END$$

-- Trigger for UPDATE on ACCOUNT
CREATE TRIGGER tu_account
BEFORE UPDATE ON ACCOUNT
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when updating ACCOUNT
    IF NEW.client_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent CLIENT does not exist. Cannot modify child in ACCOUNT.';
    END IF;
END$$
----------------------------------------------------------------
-- Trigger for INSERT on CLIENT
CREATE TRIGGER ti_client
BEFORE INSERT ON CLIENT
FOR EACH ROW
BEGIN
    -- Parent ADMIN must exist when inserting into CLIENT
    IF NOT EXISTS (SELECT 1 FROM ADMIN WHERE admin_id = NEW.admin_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in ADMIN. Cannot create child in CLIENT.';
    END IF;
END$$

-- Trigger for UPDATE on CLIENT
CREATE TRIGGER tu_client
BEFORE UPDATE ON CLIENT
FOR EACH ROW
BEGIN
    -- Parent ADMIN must exist when updating CLIENT
    IF NEW.admin_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM ADMIN WHERE admin_id = NEW.admin_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent ADMIN does not exist. Cannot modify child in CLIENT.';
    END IF;
END$$
----------------------------------------------------------------
-- Trigger for INSERT on IMAGE
CREATE TRIGGER ti_image
BEFORE INSERT ON IMAGE
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when inserting into IMAGE
    IF NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in CLIENT. Cannot create child in IMAGE.';
    END IF;
END$$

-- Trigger for UPDATE on IMAGE
CREATE TRIGGER tu_image
BEFORE UPDATE ON IMAGE
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when updating IMAGE
    IF NEW.client_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent CLIENT does not exist. Cannot modify child in IMAGE.';
    END IF;
END$$
------------------------------------------------------------------------------------------------
-- Trigger for INSERT on LOAN
CREATE TRIGGER ti_loan
BEFORE INSERT ON LOAN
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when inserting into LOAN
    IF NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in CLIENT. Cannot create child in LOAN.';
    END IF;

    -- Parent ADMIN must exist when inserting into LOAN
    IF NOT EXISTS (SELECT 1 FROM ADMIN WHERE admin_id = NEW.admin_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in ADMIN. Cannot create child in LOAN.';
    END IF;
END$$

-- Trigger for UPDATE on LOAN
CREATE TRIGGER tu_loan
BEFORE UPDATE ON LOAN
FOR EACH ROW
BEGIN
    -- Parent CLIENT must exist when updating LOAN
    IF NEW.client_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM CLIENT WHERE client_id = NEW.client_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent CLIENT does not exist. Cannot modify child in LOAN.';
    END IF;

    -- Parent ADMIN must exist when updating LOAN
    IF NEW.admin_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM ADMIN WHERE admin_id = NEW.admin_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent ADMIN does not exist. Cannot modify child in LOAN.';
    END IF;
END$$
-------------------------------------------------------------------------------------------------------------
-- Trigger for INSERT on TRANSACTION
CREATE TRIGGER ti_transaction
BEFORE INSERT ON TRANSACTION
FOR EACH ROW
BEGIN
    -- Parent ACCOUNT must exist when inserting into TRANSACTION
    IF NOT EXISTS (SELECT 1 FROM ACCOUNT WHERE account_id = NEW.sender_account_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent does not exist in ACCOUNT. Cannot create child in TRANSACTION.';
    END IF;
END$$

-- Trigger for UPDATE on TRANSACTION
CREATE TRIGGER tu_transaction
BEFORE UPDATE ON TRANSACTION
FOR EACH ROW
BEGIN
    -- Parent ACCOUNT must exist when updating TRANSACTION
    IF NEW.sender_account_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM ACCOUNT WHERE account_id = NEW.sender_account_id) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Parent ACCOUNT does not exist. Cannot modify child in TRANSACTION.';
    END IF;
END$$

DELIMITER ;
-------------------------------------------------------------------------------------
-- Trigger for INSERT on DEPOSIT
DELIMITER $$
CREATE TRIGGER before_deposit_insert
BEFORE INSERT ON DEPOSIT
FOR EACH ROW
BEGIN
    -- Ensure the account is active
    DECLARE account_status VARCHAR(10);

    SELECT status INTO account_status
    FROM ACCOUNT
    WHERE account_id = NEW.account_id;

    IF account_status != 'active' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Cannot make a deposit to an inactive account.';
    END IF;

    -- Ensure the deposit amount is greater than 0
    IF NEW.amount <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Deposit amount must be greater than zero.';
    END IF;
END;
DELIMITER ;
----------------------------------------------------------------------------------------------------
