<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../app/core/Router.php';
require_once '../app/models/Database.php';
require_once '../app/models/Admin.php';
require_once '../app/models/Client.php';
require_once '../app/models/Transaction.php';
require_once '../app/models/Loan.php';
require_once '../app/models/Notification.php';
require_once '../app/controllers/AuthController.php';
require_once '../app/controllers/AdminController.php';
require_once '../app/controllers/ClientController.php';
require_once '../app/controllers/HomeController.php';

$config = require '../config/config.php';
$db = new Database($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['dbname']);

$adminModel = new Admin($db->getConnection());
$clientModel = new Client($db->getConnection());
$transactionModel = new Transaction($db->getConnection());
$loanModel = new Loan($db->getConnection());
$notificationModel = new Notification($db->getConnection());

$authController = new AuthController($adminModel, $clientModel);
$adminController = new AdminController($clientModel, $transactionModel, $loanModel, $notificationModel);
$clientController = new ClientController($clientModel, $transactionModel, $loanModel, $notificationModel);
$homeController = new HomeController();

$router = new Router();
$router->addRoute('/', 'HomeController', 'index');
$router->addRoute('/login', 'AuthController', 'login');
$router->addRoute('/logout', 'AuthController', 'logout');
$router->addRoute('/admin', 'AdminController', 'dashboard');
$router->addRoute('/admin/clients', 'AdminController', 'clients');
$router->addRoute('/admin/create-client', 'AdminController', 'createClient');
$router->addRoute('/admin/edit-client/{id}', 'AdminController', 'editClient');
$router->addRoute('/admin/delete-client/{id}', 'AdminController', 'deleteClient');
$router->addRoute('/admin/deposit-history', 'AdminController', 'depositHistory');
$router->addRoute('/admin/loans', 'AdminController', 'loans');
$router->addRoute('/admin/notifications', 'AdminController', 'notifications');
$router->addRoute('/client/dashboard', 'ClientController', 'dashboard');
$router->addRoute('/client/profile', 'ClientController', 'editProfile');
$router->addRoute('/client/history', 'ClientController', 'history');
$router->addRoute('/client/loan', 'ClientController', 'requestLoan');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->dispatch($uri);
