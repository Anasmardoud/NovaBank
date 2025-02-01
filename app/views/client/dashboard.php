<?php
session_start();

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header('Location: /PHPLearning/NovaBank/public/login');
    exit();
}

include __DIR__ . '/../layouts/header.php';
?>

<div class="container">
    <h1>Client Dashboard</h1>
    <p>Welcome, Client!</p>
    <a href="/PHPLearning/NovaBank/public/logout">Logout</a>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>