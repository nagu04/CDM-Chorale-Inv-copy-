<?php
$servername = "localhost"; // Change if your database is on a remote server
$username = "root";
$password = "password";
$database = "sd_chorale";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>