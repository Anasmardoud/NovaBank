<?php
require_once __DIR__ . '/Database.php';

class Admin
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }
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
    public function create($username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO Admin (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $hashedPassword);
        return $stmt->execute();
    }

    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT admin_id, username, password, created_at FROM Admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
    public function findById($adminId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Admin WHERE admin_id = ?");
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function updatePassword($adminId, $hashedPassword)
    {
        $stmt = $this->conn->prepare("UPDATE Admin SET password = ? WHERE admin_id = ?");
        $stmt->bind_param("si", $hashedPassword, $adminId);
        return $stmt->execute();
    }
}
