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

    public function dashboard()
    {
        $clientId = $_SESSION['user_id'];
        $client = $this->clientModel->findById($clientId);
        $transactions = $this->transactionModel->getByClientId($clientId);
        include 'app/views/client/dashboard.php';
    }

    public function editProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_SESSION['user_id'];
            $username = htmlspecialchars($_POST['username']);
            $email = htmlspecialchars($_POST['email']);
            $phoneNumber = htmlspecialchars($_POST['phone_number']);
            $address = htmlspecialchars($_POST['address']);

            if ($this->clientModel->update($clientId, $username, $email, $phoneNumber, $address, 'Active')) {
                error_log("Client profile updated: $username", 0);
                header('Location: /client/dashboard');
            } else {
                die("Failed to update profile.");
            }
        } else {
            $client = $this->clientModel->findById($_SESSION['user_id']);
            include 'app/views/client/profile.php';
        }
    }

    public function history()
    {
        $clientId = $_SESSION['user_id'];
        $transactions = $this->transactionModel->getByClientId($clientId);
        include 'app/views/client/history.php';
    }

    public function requestLoan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientId = $_SESSION['user_id'];
            $amount = htmlspecialchars($_POST['amount']);
            $message = htmlspecialchars($_POST['message']);

            if ($this->loanModel->create($clientId, $amount, $message)) {
                error_log("Loan requested by client: $clientId", 0);
                header('Location: /client/loan');
            } else {
                die("Failed to request loan.");
            }
        } else {
            include 'app/views/client/loan.php';
        }
    }
}
