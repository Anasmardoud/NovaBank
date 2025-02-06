<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include necessary files
require_once __DIR__ . '/../app/models/Database.php';
require_once __DIR__ . '/../app/models/Admin.php';
require_once __DIR__ . '/../app/models/Client.php';
require_once __DIR__ . '/../app/models/Transaction.php';
require_once __DIR__ . '/../app/models/Loan.php';
require_once __DIR__ . '/../app/models/Deposit.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/ClientController.php';

// Instantiate models
$adminModel = new Admin();
$clientModel = new Client();
$transactionModel = new Transaction();
$loanModel = new Loan();

// Instantiate controllers
$authController = new AuthController($adminModel, $clientModel);
$adminController = new AdminController($clientModel, $transactionModel, $loanModel);
$clientController = new ClientController($clientModel, $transactionModel, $loanModel);
$homeController = new HomeController();

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base URL from the URI
$baseUrl = '/PHPLearning/NovaBank/public';
$uri = str_replace($baseUrl, '', $uri);

// Debugging: Log the requested URI
error_log("Requested URI: $uri");

// Route the request
switch ($uri) {
    case '/':
        $homeController->index();
        break;
    case '/login_page':
        $authController->login_page();
        break;
    case '/login_algorithm':
        $authController->login_algorithm();
        break;
    case '/logout':
        $authController->logout();
        break;
    case '/admin/dashboard':
        $adminController->dashboard();
        break;
    case '/client/dashboard':
        $clientController->dashboard();
        break;
    case '/admin/deposit-history':
        $adminController->depositHistory();
        break;
    case '/admin/change-password':
        $adminController->changePassword();
        break;
    case '/admin/client-creation-homepage':
        $adminController->clientCreationHomePage();
        break;
    case '/admin/create-client-algorithm':
        $adminController->createClientAlgorithm();
        break;
    case '/admin/clients':
        $adminController->clients();
        break;
    case '/admin/delete-client':
        $adminController->deleteClient();
        break;
    case '/admin/edit-account':
        $adminController->updateClient();
        break;
    case '/admin/deposit':
        $adminController->deposit();
        break;
    case '/admin/create-deposit':
        $adminController->createDeposit();
        break;
    case '/admin/edit-deposit':
        $adminController->editDeposit();
        break;
    case '/admin/delete-deposit':
        $adminController->deleteDeposit();
        break;
    case '/admin/edit-deposit':
        $adminController->editDeposit();
        break;
    case '/admin/update-deposit':
        $adminController->updateDeposit();
        break;
    case '/admin/loans':
        $adminController->loans();
        break;
    case '/admin/approve-loan':
        $adminController->approveLoan();
        break;
    case '/admin/reject-loan':
        $adminController->rejectLoan();
        break;
    case '/admin/delete-loan':
        $adminController->deleteLoan();
        break;
    case '/admin/calculate-loan-page':
        $adminController->calculateLoanPage();
        break;
    case '/admin/calculate-loan':
        $adminController->calculateLoan();
        break;
    case '/client/dashboard':
        $clientController->dashboard();
        break;
    case '/client/accounts':
        $clientController->accountsAction();
        break;
    case '/client/transferFunds':
        $clientController->transferFunds();
        break;
    case '/client/profile':
        $clientController->profileAction();
        break;
    case '/client/editProfile':
        $clientController->editProfile();
        break;
    case '/client/transaction':
        $clientController->transactionHome();
        break;
    case '/client/transactionAlgorithm':
        $clientController->transactionAlgorithm();
        break;
    case '/client/loans':
        $clientController->loansAction();
        break;
    case '/client/requestLoan':
        $clientController->requestLoan();
        break;
    default:
        header('HTTP/1.1 404 Not Found');
        echo 'Page not found';
        break;
}
