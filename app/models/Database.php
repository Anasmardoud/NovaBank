<?php
// Database configuration
$host = 'localhost';
$dbname = 'nova_bank';
$username = 'root';
$password = '';

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    // Log the error and display a generic message
    error_log("Database connection failed: " . mysqli_connect_error(), 0);
    die("We are currently experiencing technical difficulties. Please try again later.");
}
