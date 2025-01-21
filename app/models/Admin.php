<?php
class Admin
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function create($username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO Admin (username, password, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $username, $hashedPassword);
        return $stmt->execute();
    }

    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM Admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
