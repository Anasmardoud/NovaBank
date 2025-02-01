<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Client.php';

class AuthController
{
    private $adminModel;
    private $clientModel;

    public function __construct()
    {
        global $conn;
        $this->adminModel = new Admin();
        $this->clientModel = new Client();
    }
    public function login_page()
    {
        error_log("Login page accessed");
        include __DIR__ . '/../views/auth/login.php';
    }
    public function login_algorithm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = htmlspecialchars($_POST['username']);
            $password = htmlspecialchars($_POST['client-password']);
            $accountType = htmlspecialchars($_POST['account-type']);

            if ($accountType === 'admin') {
                $user = $this->adminModel->findByUsername($username);
            } elseif ($accountType === 'client') {
                $user = $this->clientModel->findByUsername($username);
            } else {
                die("Invalid account type.");
            }

            if ($user && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user[$accountType . '_id'];
                $_SESSION['username'] = $user['username']; // Set username
                $_SESSION['created_at'] = $user['created_at']; // Set creation date
                $_SESSION['role'] = $accountType;

                // Redirect based on user type
                if ($accountType === 'admin') {
                    header('Location: /PHPLearning/NovaBank/public/admin/dashboard');
                } else {
                    header('Location: /PHPLearning/NovaBank/public/client/dashboard');
                }
                exit();
            } else {
                die("Invalid credentials.");
            }
        } else {
            include __DIR__ . '/../views/auth/login.php';
        }
    }
    public function logout()
    {
        // Log logout action
        error_log("User logged out: User ID = {$_SESSION['user_id']}, Role = {$_SESSION['role']}", 0);

        // Destroy the session and redirect to the login page
        session_destroy();
        header('Location: /PHPLearning/NovaBank/public/login_page');
        exit();
    }
}
