<?php
// permanent_delete_all.php - Permanently deletes all items from the deleted_items table
session_start();
include 'db_connect.php';

// First, get all image paths if you want to delete associated files
$get_images = $conn->query("SELECT image_path FROM deleted_items");
$image_paths = [];

if ($get_images) {
    while ($row = $get_images->fetch_assoc()) {
        if (!empty($row['image_path'])) {
            $image_paths[] = $row['image_path'];
        }
    }
}

// Now delete all records from the table
$delete_all = $conn->query("DELETE FROM deleted_items");

if ($delete_all) {
    // Get the count of affected rows
    $deleted_count = $conn->affected_rows;
    
    // Optional: Delete the image files if they're not default images
    // Uncomment this section if you want to delete actual image files
    /*
    foreach ($image_paths as $path) {
        if (file_exists($path) && 
            $path != 'picture-1.png' && 
            $path != 'keyboard.jpg' && 
            !strpos($path, 'default')) {
            
            unlink($path);
        }
    }
    */
    
    $_SESSION['success_message'] = "Trash emptied successfully! $deleted_count item(s) permanently deleted.";
} else {
    $_SESSION['error_message'] = "Error emptying trash: " . $conn->error;
}

// Redirect back to deleted items page
header("Location: deleted_items.php");
exit();
?> 