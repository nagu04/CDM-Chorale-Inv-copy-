<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $clothing_name = $_POST['clothing_name'];
    $clothing_color = $_POST['clothing_color'];
    $clothing_size_id = $_POST['clothing_size_id'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO clothing (clothing_name, clothing_color, clothing_size_id, `condition`, quantity) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $clothing_name, $clothing_color, $clothing_size_id, $condition, $quantity);
    
    if ($stmt->execute()) {
        // Success - redirect back to clothing page
        header("Location: clothing.php");
        exit();
    } else {
        // Error
        echo "Error adding clothing: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: clothing.php");
    exit();
}

$conn->close();
?> 