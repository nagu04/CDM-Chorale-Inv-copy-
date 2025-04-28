<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $instrument_name = $_POST['instrument_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO instruments (instrument_name, `condition`, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $instrument_name, $condition, $quantity);
    
    if ($stmt->execute()) {
        // Success - redirect back to instruments page
        header("Location: instruments.php");
        exit();
    } else {
        // Error
        echo "Error adding instrument: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 