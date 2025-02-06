<?php
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/Client.php';

class AuthController
{
    private $adminModel;
    private $clientModel;

    /**
     * Constructor to initialize models.
     */
    public function __construct()
    {
        global $conn;
        $this->adminModel = new Admin();
        $this->clientModel = new Client();
    }

    /**
     * Render the login page.
     */
    public function login_page()
    {
        error_log("Login page accessed");
        include __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Handle the login process.
     */
    public function login_algorithm()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->sanitizeInput($_POST['username']);
            $password = $this->sanitizeInput($_POST['client-password']);
            $accountType = $this->sanitizeInput($_POST['account-type']);

            // Fetch user based on account type
            $user = $this->getUserByAccountType($username, $accountType);

            if ($user && password_verify($password, $user['password'])) {
                $this->setSessionVariables($user, $accountType);
                $this->redirectBasedOnRole($accountType);
            } else {
                $this->setSessionError('Invalid username or password');
                $this->login_page();
            }
        } else {
            $this->login_page();
        }
    }

    /**
     * Handle the logout process.
     */
    public function logout()
    {
        error_log("User logged out: User ID = {$_SESSION['user_id']}, Role = {$_SESSION['role']}", 0);
        session_destroy();
        $this->redirect('/PHPLearning/NovaBank/public/login_page');
    }

    /**
     * Fetch user based on account type.
     *
     * @param string $username The username.
     * @param string $accountType The account type (admin or client).
     * @return array|null The user data if found, null otherwise.
     */
    private function getUserByAccountType($username, $accountType)
    {
        if ($accountType === 'admin') {
            return $this->adminModel->findByUsername($username);
        } elseif ($accountType === 'client') {
            return $this->clientModel->findByUsername($username);
        }
        return null;
    }

    /**
     * Set session variables after successful login.
     *
     * @param array $user The user data.
     * @param string $accountType The account type (admin or client).
     */
    private function setSessionVariables($user, $accountType)
    {
        $_SESSION['user_id'] = $user[$accountType . '_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['created_at'] = $user['created_at'];
        $_SESSION['role'] = $accountType;
    }

    /**
     * Redirect based on the user's role.
     *
     * @param string $accountType The account type (admin or client).
     */
    private function redirectBasedOnRole($accountType)
    {
        if ($accountType === 'admin') {
            $this->redirect('/PHPLearning/NovaBank/public/admin/dashboard');
        } else {
            $this->redirect('/PHPLearning/NovaBank/public/client/dashboard');
        }
    }

    /**
     * Set a session error message.
     *
     * @param string $message The error message.
     */
    private function setSessionError($message)
    {
        $_SESSION['error'] = $message;
    }

    /**
     * Redirect to a specific URL.
     *
     * @param string $url The URL to redirect to.
     */
    private function redirect($url)
    {
        header("Location: $url");
        exit();
    }

    /**
     * Sanitize input data.
     *
     * @param string $input The input to sanitize.
     * @return string The sanitized input.
     */
    private function sanitizeInput($input)
    {
        return htmlspecialchars($input);
    }
}
