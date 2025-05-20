<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $clothing_name = $_POST['clothing_name'];
    $clothing_color = $_POST['clothing_color'];
    $clothing_size_id = $_POST['clothing_size_id'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    
    // Default image path
    $image_path = null;
    
    // Handle image upload if present
    if(isset($_FILES['clothing_image']) && $_FILES['clothing_image']['error'] == 0) {
        // Create the clothing_images directory if it doesn't exist
        if(!file_exists('clothing_images')) {
            mkdir('clothing_images', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['clothing_image']['name'], PATHINFO_EXTENSION);
        $filename = 'clothing_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = "clothing_images/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['clothing_image']['tmp_name'], $target_file)) {
            // File uploaded successfully
            $image_path = $target_file;
        }
    }

    // Prepare and execute SQL statement with image path
    $stmt = $conn->prepare("INSERT INTO clothing (clothing_name, clothing_color, clothing_size_id, `condition`, quantity, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $clothing_name, $clothing_color, $clothing_size_id, $condition, $quantity, $image_path);
    
    if ($stmt->execute()) {
        // Success - redirect back to clothing page
        $_SESSION['success_message'] = "Clothing added successfully!";
        header("Location: clothing.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error adding clothing: " . $conn->error;
        header("Location: clothing.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: clothing.php");
    exit();
}

$conn->close();
?> 