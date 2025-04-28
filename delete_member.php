<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    
    if ($stmt->execute()) {
        // Success - redirect back to members page
        header("Location: members.php");
        exit();
    } else {
        // Error
        echo "Error deleting member: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 