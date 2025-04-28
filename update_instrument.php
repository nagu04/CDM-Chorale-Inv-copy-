<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instru_id'])) {
    $instru_id = $_POST['instru_id'];
    $instrument_name = $_POST['instrument_name'];
    $condition = $_POST['condition'];
    $quantity = $_POST['quantity'];
    $current_image_path = $_POST['current_image_path'];
    
    // Default to current image path
    $image_path = $current_image_path;
    
    // Handle image upload if a new image is provided
    if(isset($_FILES['instrument_image']) && $_FILES['instrument_image']['error'] == 0) {
        // Create the instrument_images directory if it doesn't exist
        if(!file_exists('instrument_images')) {
            mkdir('instrument_images', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['instrument_image']['name'], PATHINFO_EXTENSION);
        $filename = 'instrument_' . $instru_id . '_' . time() . '.' . $file_ext;
        $target_file = "instrument_images/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['instrument_image']['tmp_name'], $target_file)) {
            // If upload successful, delete the old image if it exists and is not the default
            if (!empty($current_image_path) && file_exists($current_image_path) && $current_image_path != 'keyboard.jpg') {
                unlink($current_image_path); // Delete the old image file
            }
            
            // Update image path to the new image
            $image_path = $target_file;
        }
    }
    
    // Update the instrument in the database with image path
    $stmt = $conn->prepare("UPDATE instruments SET instrument_name = ?, `condition` = ?, quantity = ?, image_path = ? WHERE instru_id = ?");
    $stmt->bind_param("ssisi", $instrument_name, $condition, $quantity, $image_path, $instru_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Instrument updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating instrument: " . $conn->error;
    }
    
    $stmt->close();
    
    // Redirect back to instruments page
    header("Location: instruments.php");
    exit();
} else {
    // Not a valid POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 