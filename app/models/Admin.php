<?php
require_once __DIR__ . '/Database.php';

class Admin
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
     * Render the admin dashboard.
     * Redirects to the login page if the user is not logged in or is not an admin.
     */
    public function dashboard()
    {
        // Check if the user is logged in and is an admin
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /login');
            exit();
        }

        // Include the admin dashboard view
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Create a new admin user.
     *
     * @param string $username The username of the admin.
     * @param string $password The password of the admin.
     * @return bool True if the admin was created successfully, false otherwise.
     */
    public function create($username, $hashedPassword)
    {
        $stmt = $this->conn->prepare("INSERT INTO Admin (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $hashedPassword);
        return $stmt->execute();
    }

    /**
     * Find an admin by their username.
     *
     * @param string $username The username to search for.
     * @return array|null The admin data if found, null otherwise.
     */
    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT admin_id, username, password, created_at FROM Admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Find an admin by their ID.
     *
     * @param int $adminId The ID of the admin.
     * @return array|null The admin data if found, null otherwise.
     */
    public function findById($adminId)
    {
        $stmt = $this->conn->prepare("SELECT admin_id, username, password, created_at FROM Admin WHERE admin_id = ?");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    /**
     * Update an admin's password.
     *
     * @param int $adminId The ID of the admin.
     * @param string $hashedPassword The new hashed password.
     * @return bool True if the password was updated successfully, false otherwise.
     */
    public function updatePassword($adminId, $hashedPassword)
    {
        $stmt = $this->conn->prepare("UPDATE Admin SET password = ? WHERE admin_id = ?");
        $stmt->bind_param("si", $hashedPassword, $adminId);
        return $stmt->execute();
    }
}
