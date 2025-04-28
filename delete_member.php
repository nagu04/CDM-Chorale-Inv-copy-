<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    
    // First, get the image path
    $stmt = $conn->prepare("SELECT image_path FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the member
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    
    if ($stmt->execute()) {
        // If member deleted successfully, delete their image if it exists
        if (!empty($image_path) && file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }
        
        // Success message
        $_SESSION['success_message'] = "Member deleted successfully!";
        
        // Redirect back to members page
        header("Location: members.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting member: " . $conn->error;
        header("Location: members.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 