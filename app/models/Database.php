<?php

/**
 * Database configuration and connection setup.
 * This file initializes the database connection using mysqli.
 */

// Database configuration
$host = 'localhost';       // Database host
$dbname = 'nova_bank';     // Database name
$username = 'root';        // Database username
$password = '';            // Database password

// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check the connection
if (!$conn) {
    // Log the error and display a generic message
    error_log("Database connection failed: " . mysqli_connect_error(), 0);
    die("We are currently experiencing technical difficulties. Please try again later.");
}
