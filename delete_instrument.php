<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instru_id'])) {
    $instrument_id = $_POST['instru_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM instruments WHERE instru_id = ?");
    $stmt->bind_param("i", $instrument_id);
    
    if ($stmt->execute()) {
        // Success - redirect back to instruments page
        header("Location: instruments.php");
        exit();
    } else {
        // Error
        echo "Error deleting instrument: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 