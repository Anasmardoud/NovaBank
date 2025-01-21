<?php
class ClientController
{
    private $clientModel;
    private $transactionModel;
    private $loanModel;
    private $notificationModel;

    public function __construct($clientModel, $transactionModel, $loanModel, $notificationModel)
    {
        $this->clientModel = $clientModel;
        $this->transactionModel = $transactionModel;
        $this->loanModel = $loanModel;
        $this->notificationModel = $notificationModel;
    }

    // Client dashboard
    public function dashboard()
    {
        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $transactions = $this->transactionModel->getByClientId($clientId);
        include 'app/views/client/dashboard.php';
    }

    // Edit client profile
    public function editProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_SESSION['user_id'];
            $username = Helper::sanitizeInput($_POST['username']);
            $email = Helper::sanitizeInput($_POST['email']);
            $phoneNumber = Helper::sanitizeInput($_POST['phone_number']);
            $address = Helper::sanitizeInput($_POST['address']);

            if ($this->clientModel->update($clientId, $username, $email, $phoneNumber, $address, 'Active')) {
                Helper::log("Client profile updated: $username", 'INFO');
                header('Location: /client/dashboard');
            } else {
                die("Failed to update profile.");
            }
        } else {
            $client = $this->clientModel->findById($_SESSION['user_id']);
            include 'app/views/client/profile.php';
        }
    }

    // View transaction history
    public function history()
    {
        $clientId = $_SESSION['user_id'];
        $transactions = $this->transactionModel->getByClientId($clientId);
        include 'app/views/client/history.php';
    }

    // Request a loan
    public function requestLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_SESSION['user_id'];
            $amount = Helper::sanitizeInput($_POST['amount']);
            $message = Helper::sanitizeInput($_POST['message']);

            if ($this->loanModel->create($clientId, $amount, $message)) {
                Helper::log("Loan requested by client: $clientId", 'INFO');
                header('Location: /client/loan');
            } else {
                die("Failed to request loan.");
            }
        } else {
            include 'app/views/client/loan.php';
        }
    }
}
