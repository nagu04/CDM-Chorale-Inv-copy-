<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "password"; // Ensure this is the correct password for the 'root' user
$dbname = "sd_chorale";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>