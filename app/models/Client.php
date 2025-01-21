<?php
class Client
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    // Create a new client
    public function create($adminId, $username, $email, $password, $phoneNumber, $address, $status)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Client (admin_id, username, email, password, phone_number, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $adminId, $username, $email, $hashedPassword, $phoneNumber, $address, $status);
        return $stmt->execute();
    }

    // Find client by username
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM Client WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Find client by ID
    public function findById($clientId)
    {
        $stmt = $this->db->prepare("SELECT * FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Update client details
    public function update($clientId, $username, $email, $phoneNumber, $address, $status)
    {
        $stmt = $this->db->prepare("UPDATE Client SET username = ?, email = ?, phone_number = ?, address = ?, status = ? WHERE client_id = ?");
        $stmt->bind_param("sssssi", $username, $email, $phoneNumber, $address, $status, $clientId);
        return $stmt->execute();
    }

    // Delete a client
    public function delete($clientId)
    {
        $stmt = $this->db->prepare("DELETE FROM Client WHERE client_id = ?");
        $stmt->bind_param("i", $clientId);
        return $stmt->execute();
    }

    // Get all clients
    public function getAll()
    {
        $result = $this->db->query("SELECT * FROM Client");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
