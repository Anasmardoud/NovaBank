-- Table for ADMIN
CREATE TABLE ADMIN (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table for CLIENT
CREATE TABLE CLIENT (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Active', 'Inactive') DEFAULT 'Active',
    address TEXT,
    FOREIGN KEY (admin_id) REFERENCES ADMIN(admin_id) ON DELETE SET NULL
);

-- Table for ACCOUNT
CREATE TABLE ACCOUNT (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    created_by_admin INT,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    account_type ENUM('Checking', 'Savings') NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    transaction_limit DECIMAL(15, 2),
    status ENUM('Active', 'Closed') DEFAULT 'Active',
	currency ENUM('USD', 'EUR', 'LL') DEFAULT 'USD',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES CLIENT(client_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by_admin) REFERENCES ADMIN(admin_id) ON DELETE SET NULL
);

-- Table for LOAN
CREATE TABLE LOAN (
    loan_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    admin_id INT,
    amount DECIMAL(15, 2) NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    term_months INT NOT NULL,
    monthly_payment DECIMAL(15, 2) NOT NULL,
    loan_type ENUM('Personal', 'Home', 'Car') NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    start_date DATE,
	ALTER TABLE LOAN ADD COLUMN remaining_balance DECIMAL(15, 2) DEFAULT 0.00;
    end_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES CLIENT(client_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES ADMIN(admin_id) ON DELETE SET NULL
);

-- Table for IMAGE
CREATE TABLE IMAGE (
    image_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    file_path VARCHAR(255) NOT NULL,
    type ENUM('Profile Picture', 'ID Document') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES CLIENT(client_id) ON DELETE CASCADE
);

-- Table for TRANSACTION
CREATE TABLE TRANSACTION (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_account_id INT,
    receiver_account_id INT,
    amount DECIMAL(15, 2) NOT NULL,
    transaction_type ENUM('Deposit', 'Withdrawal', 'Transfer') NOT NULL,
    message TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Success', 'Failed') DEFAULT 'Success',
    FOREIGN KEY (sender_account_id) REFERENCES ACCOUNT(account_id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_account_id) REFERENCES ACCOUNT(account_id) ON DELETE CASCADE
);

-- Table for NOTIFICATION
CREATE TABLE NOTIFICATION (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    message TEXT NOT NULL,
    type ENUM('Alert', 'Reminder') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Read', 'Unread') DEFAULT 'Unread',
    FOREIGN KEY (client_id) REFERENCES CLIENT(client_id) ON DELETE CASCADE
);
--Table for Deposit
CREATE TABLE DEPOSIT (
    deposit_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,                 
    admin_id INT NOT NULL,                     
    amount DECIMAL(10, 2) NOT NULL,           
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP, 
    status ENUM('completed', 'failed') DEFAULT 'completed', 
    FOREIGN KEY (account_id) REFERENCES ACCOUNT(account_id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES ADMIN(admin_id) ON DELETE CASCADE
);

--- set pointer of the auto incremant 

ALTER TABLE ADMIN AUTO_INCREMENT = 1;
ALTER TABLE CLIENT AUTO_INCREMENT = 1;
ALTER TABLE ACCOUNT AUTO_INCREMENT = 1;
ALTER TABLE LOAN AUTO_INCREMENT = 1;
ALTER TABLE IMAGE AUTO_INCREMENT = 1;
ALTER TABLE TRANSACTION AUTO_INCREMENT = 1;
ALTER TABLE NOTIFICATION AUTO_INCREMENT = 1;
ALTER TABLE DEPOSIT AUTO_INCREMENT = 1;
