<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $instrument_name = $_POST['instrument_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    
    // Default image path
    $image_path = null;
    
    // Handle image upload if present
    if(isset($_FILES['instrument_image']) && $_FILES['instrument_image']['error'] == 0) {
        // Create the instrument_images directory if it doesn't exist
        if(!file_exists('instrument_images')) {
            mkdir('instrument_images', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['instrument_image']['name'], PATHINFO_EXTENSION);
        $filename = 'instrument_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = "instrument_images/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['instrument_image']['tmp_name'], $target_file)) {
            // File uploaded successfully
            $image_path = $target_file;
        }
    }

    // Prepare and execute SQL statement with image path
    $stmt = $conn->prepare("INSERT INTO instruments (instrument_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $instrument_name, $condition, $quantity, $image_path);
    
    if ($stmt->execute()) {
        // Success - redirect back to instruments page
        $_SESSION['success_message'] = "Instrument added successfully!";
        header("Location: instruments.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error adding instrument: " . $conn->error;
        header("Location: instruments.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 