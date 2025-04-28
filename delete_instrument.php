<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instru_id'])) {
    $instrument_id = $_POST['instru_id'];
    
    // First, get the image path
    $stmt = $conn->prepare("SELECT image_path FROM instruments WHERE instru_id = ?");
    $stmt->bind_param("i", $instrument_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the instrument
    $stmt = $conn->prepare("DELETE FROM instruments WHERE instru_id = ?");
    $stmt->bind_param("i", $instrument_id);
    
    if ($stmt->execute()) {
        // If instrument deleted successfully, delete the image if it exists and is not the default
        if (!empty($image_path) && file_exists($image_path) && $image_path != 'keyboard.jpg') {
            unlink($image_path); // Delete the image file
        }
        
        // Success message
        $_SESSION['success_message'] = "Instrument deleted successfully!";
        
        // Redirect back to instruments page
        header("Location: instruments.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting instrument: " . $conn->error;
        header("Location: instruments.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 