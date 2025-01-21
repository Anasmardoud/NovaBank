<?php
class AdminController
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

    // Admin dashboard
    public function dashboard()
    {
        $clients = $this->clientModel->getAll();
        include 'app/views/admin/dashboard.php';
    }

    // Create a new client
    public function createClient()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['user_id'];
            $username = Helper::sanitizeInput($_POST['username']);
            $email = Helper::sanitizeInput($_POST['email']);
            $password = Helper::sanitizeInput($_POST['password']);
            $phoneNumber = Helper::sanitizeInput($_POST['phone_number']);
            $address = Helper::sanitizeInput($_POST['address']);
            $status = Helper::sanitizeInput($_POST['status']);

            if ($this->clientModel->create($adminId, $username, $email, $password, $phoneNumber, $address, $status)) {
                Helper::log("Client created: $username", 'INFO');
                header('Location: /admin/clients');
            } else {
                die("Failed to create client.");
            }
        } else {
            include 'app/views/admin/create_client.php';
        }
    }

    // Edit client details
    public function editClient($clientId)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = Helper::sanitizeInput($_POST['username']);
            $email = Helper::sanitizeInput($_POST['email']);
            $phoneNumber = Helper::sanitizeInput($_POST['phone_number']);
            $address = Helper::sanitizeInput($_POST['address']);
            $status = Helper::sanitizeInput($_POST['status']);

            if ($this->clientModel->update($clientId, $username, $email, $phoneNumber, $address, $status)) {
                Helper::log("Client updated: $username", 'INFO');
                header('Location: /admin/clients');
            } else {
                die("Failed to update client.");
            }
        } else {
            $client = $this->clientModel->findById($clientId);
            include 'app/views/admin/edit_client.php';
        }
    }

    // Delete a client
    public function deleteClient($clientId)
    {
        if ($this->clientModel->delete($clientId)) {
            Helper::log("Client deleted: $clientId", 'INFO');
            header('Location: /admin/clients');
        } else {
            die("Failed to delete client.");
        }
    }

    // View deposit history
    public function depositHistory()
    {
        $deposits = $this->transactionModel->getDeposits();
        include 'app/views/admin/deposit_history.php';
    }

    // View loans
    public function loans()
    {
        $loans = $this->loanModel->getAll();
        include 'app/views/admin/loans.php';
    }

    // View notifications
    public function notifications()
    {
        $notifications = $this->notificationModel->getAll();
        include 'app/views/admin/notifications.php';
    }
}
