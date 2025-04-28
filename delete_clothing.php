<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM clothing WHERE clothing_id = ?");
    $stmt->bind_param("i", $clothing_id);
    
    if ($stmt->execute()) {
        // Success - redirect back to clothing page
        header("Location: clothing.php");
        exit();
    } else {
        // Error
        echo "Error deleting clothing: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: clothing.php");
    exit();
}

$conn->close();
?> 