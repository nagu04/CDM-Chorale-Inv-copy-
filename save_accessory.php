<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $deco_name = $_POST['deco_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    
    // Default image path
    $image_path = null;
    
    // Handle image upload if present
    if(isset($_FILES['accessory_image']) && $_FILES['accessory_image']['error'] == 0) {
        // Create the accessory_images directory if it doesn't exist
        if(!file_exists('accessory_images')) {
            mkdir('accessory_images', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['accessory_image']['name'], PATHINFO_EXTENSION);
        $filename = 'accessory_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = "accessory_images/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['accessory_image']['tmp_name'], $target_file)) {
            // File uploaded successfully
            $image_path = $target_file;
        }
    }

    // Prepare and execute SQL statement with image path
    $stmt = $conn->prepare("INSERT INTO accessories (deco_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $deco_name, $condition, $quantity, $image_path);
    
    if ($stmt->execute()) {
        // Success - redirect back to accessories page
        $_SESSION['success_message'] = "Accessory added successfully!";
        header("Location: accessory.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error adding accessory: " . $conn->error;
        header("Location: accessory.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 