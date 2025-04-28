<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];
    
    // First, get the image path
    $stmt = $conn->prepare("SELECT image_path FROM clothing WHERE clothing_id = ?");
    $stmt->bind_param("i", $clothing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the clothing item
    $stmt = $conn->prepare("DELETE FROM clothing WHERE clothing_id = ?");
    $stmt->bind_param("i", $clothing_id);
    
    if ($stmt->execute()) {
        // If clothing deleted successfully, delete the image if it exists and is not the default
        if (!empty($image_path) && file_exists($image_path) && $image_path != 'barong.png') {
            unlink($image_path); // Delete the image file
        }
        
        // Success message
        $_SESSION['success_message'] = "Clothing deleted successfully!";
        
        // Redirect back to clothing page
        header("Location: clothing.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting clothing: " . $conn->error;
        header("Location: clothing.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: clothing.php");
    exit();
}

$conn->close();
?> 