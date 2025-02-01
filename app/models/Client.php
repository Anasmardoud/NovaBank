<?php
require_once __DIR__ . '/Database.php';

class Client
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }
    public function dashboard()
    {
        // Check if the user is logged in and is a client
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
            header('Location: /login');
            exit();
        }

        // Include the client dashboard view
        include __DIR__ . '/../views/client/dashboard.php';
    }
    public function create($adminId, $username, $email, $password, $phoneNumber, $address, $status)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare("INSERT INTO Client (admin_id, username, email, password, phone_number, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $adminId, $username, $email, $hashedPassword, $phoneNumber, $address, $status);
        return $stmt->execute();
    }

    public function findByUsername($username)
    {
        $stmt = $this->conn->prepare("SELECT client_id, username, password, created_at FROM Client WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function findById($clientId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update($clientId, $username, $email, $phoneNumber, $address, $status)
    {
        $stmt = $this->conn->prepare("UPDATE Client SET username = ?, email = ?, phone_number = ?, address = ?, status = ? WHERE client_id = ?");
        $stmt->bind_param("sssssi", $username, $email, $phoneNumber, $address, $status, $clientId);
        return $stmt->execute();
    }

    public function delete($clientId)
    {
        $stmt = $this->conn->prepare("DELETE FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $clientId);
        return $stmt->execute();
    }

    public function getAll()
    {
        $result = $this->conn->query("SELECT * FROM Client");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
