<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";  // Empty password for XAMPP default MySQL root user
$dbname = "inv"; //palitan mo to depende sa name ng db mo sa phpmyadmin

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // Log the error but don't expose details to the user
    error_log("Database connection failed: " . $conn->connect_error);
    die("Database connection error. Please try again later.");
}
?>