<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deco_id'])) {
    $deco_id = $_POST['deco_id'];
    
    // First, get the image path
    $stmt = $conn->prepare("SELECT image_path FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the accessory
    $stmt = $conn->prepare("DELETE FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    
    if ($stmt->execute()) {
        // If accessory deleted successfully, delete the image if it exists and is not the default
        if (!empty($image_path) && file_exists($image_path) && $image_path != 'keyboard.jpg') {
            unlink($image_path); // Delete the image file
        }
        
        // Success message
        $_SESSION['success_message'] = "Accessory deleted successfully!";
        
        // Redirect back to accessories page
        header("Location: accessory.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting accessory: " . $conn->error;
        header("Location: accessory.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 