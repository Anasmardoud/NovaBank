<?php
class AuthController
{
    private $adminModel;
    private $clientModel;

    public function __construct($adminModel, $clientModel)
    {
        $this->adminModel = $adminModel;
        $this->clientModel = $clientModel;
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF token
            if (!Helper::validateCsrfToken($_POST['csrf_token'])) {
                Helper::log("CSRF token validation failed.", 'ERROR');
                die("CSRF token validation failed.");
            }

            // Sanitize and validate inputs
            $username = Helper::sanitizeInput($_POST['username']);
            $password = Helper::sanitizeInput($_POST['client-password']);

            if (!Helper::validateEmail($username)) {
                Helper::log("Invalid email format: $username", 'ERROR');
                die("Invalid email format.");
            }

            if (!Helper::validatePassword($password)) {
                Helper::log("Weak password attempt for user: $username", 'WARNING');
                die("Password must be at least 8 characters long.");
            }

            // Proceed with login logic
            if ($_POST['account-type'] === 'admin') {
                $user = $this->adminModel->findByUsername($username);
            } elseif ($_POST['account-type'] === 'client') {
                $user = $this->clientModel->findByUsername($username);
            } else {
                die("Invalid account type.");
            }

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user[$_POST['account-type'] . '_id'];
                $_SESSION['role'] = $_POST['account-type'];
                Helper::log("User logged in: $username", 'INFO');

                // Redirect based on user type
                if ($_POST['account-type'] === 'admin') {
                    header('Location: /admin/dashboard');
                } else {
                    header('Location: /client/dashboard');
                }
                exit();
            } else {
                Helper::log("Failed login attempt for user: $username", 'WARNING');
                die("Invalid credentials.");
            }
        } else {
            include __DIR__ . '/../views/auth/login.php';
        }
    }

    public function logout()
    {
        // Destroy the session and redirect to the login page
        session_destroy();
        header('Location: /login');
        exit();
    }
}
