<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";  // Empty password for XAMPP default MySQL root user
$dbname = "sd_chorale";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>