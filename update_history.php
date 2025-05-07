<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['history_id']) && isset($_POST['status'])) {
    $history_id = $_POST['history_id'];
    $status = $_POST['status'];
    
    // Update the status
    $stmt = $conn->prepare("UPDATE history SET status = ? WHERE history_id = ?");
    $stmt->bind_param("si", $status, $history_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating status: " . $conn->error;
    }
    
    $stmt->close();
    
    // Redirect back to history page
    header("Location: history.php");
    exit();
} else {
    header("Location: history.php");
    exit();
}

$conn->close();
?>