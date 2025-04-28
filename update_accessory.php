<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deco_id'])) {
    $deco_id = $_POST['deco_id'];
    $deco_name = $_POST['deco_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    $current_image_path = $_POST['current_image_path'];
    
    // Default to current image path
    $image_path = $current_image_path;
    
    // Handle image upload if a new image is provided
    if(isset($_FILES['accessory_image']) && $_FILES['accessory_image']['error'] == 0) {
        // Create the accessory_images directory if it doesn't exist
        if(!file_exists('accessory_images')) {
            mkdir('accessory_images', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['accessory_image']['name'], PATHINFO_EXTENSION);
        $filename = 'accessory_' . $deco_id . '_' . time() . '.' . $file_ext;
        $target_file = "accessory_images/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['accessory_image']['tmp_name'], $target_file)) {
            // If upload successful, delete the old image if it exists and is not the default
            if (!empty($current_image_path) && file_exists($current_image_path) && $current_image_path != 'keyboard.jpg') {
                unlink($current_image_path); // Delete the old image file
            }
            
            // Update image path to the new image
            $image_path = $target_file;
        }
    }
    
    // Update the accessory in the database with image path
    $stmt = $conn->prepare("UPDATE accessories SET deco_name = ?, `condition` = ?, quantity = ?, image_path = ? WHERE deco_id = ?");
    $stmt->bind_param("ssisi", $deco_name, $condition, $quantity, $image_path, $deco_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Accessory updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating accessory: " . $conn->error;
    }
    
    $stmt->close();
    
    // Redirect back to accessories page
    header("Location: accessory.php");
    exit();
} else {
    // Not a valid POST request
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 