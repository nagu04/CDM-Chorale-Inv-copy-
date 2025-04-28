<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $deco_name = $_POST['deco_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO accessories (deco_name, `condition`, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $deco_name, $condition, $quantity);
    
    if ($stmt->execute()) {
        // Success - redirect back to accessories page
        header("Location: accessory.php");
        exit();
    } else {
        // Error
        echo "Error adding accessory: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 