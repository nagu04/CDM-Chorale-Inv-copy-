<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deco_id'])) {
    $deco_id = $_POST['deco_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    
    if ($stmt->execute()) {
        // Success - redirect back to accessories page
        header("Location: accessory.php");
        exit();
    } else {
        // Error
        echo "Error deleting accessory: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 