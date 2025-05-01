<?php
$servername = "localhost";
$username = "root";
$password = "";  // Default XAMPP MySQL root password is empty
$database = "sd_chorale";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>