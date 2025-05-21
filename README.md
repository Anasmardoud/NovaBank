# Nova Bank 💳

A secure and scalable **online banking system** built with **PHP, MySQL, HTML, CSS, and JavaScript**. Nova Bank provides role-based access for clients and admins, enabling core banking features like fund transfers, deposit management, loan applications, and transaction tracking.

---

## 🔐 Features

### 👤 **Client Side**
- User registration & secure login
- Edit profile and change password
- View account overview
- Transfer funds between accounts
- View transaction history
- Apply for loans

### 🛠️ **Admin Side**
- Admin login
- Create and manage client accounts
- Process deposits (admin-only)
- Approve or reject loans
- View all transactions and client details
- Generate account statements

---

## 🧠 Tech Stack

- **Backend:** PHP (Procedural)
- **Database:** MySQL (Triggers, Stored Procedures, Indexes)
- **Frontend:** HTML, CSS, JavaScript
- **Server:** XAMPP / Apache (Localhost)
- **Security:** Password hashing, session management, input validation

---

## 🗃️ Database Highlights

- ER model includes `Admin`, `Client`, `Account`, `Loan`, `Transaction`, `Deposit`, `Image`
- Stored procedures for:
  - Fund Transfers
  - Validating foreign keys
  - Generating account statements
- Triggers for data integrity
- Indexes for optimized query performance

---
