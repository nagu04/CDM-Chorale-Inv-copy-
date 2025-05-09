<?php
// permanent_delete.php - Permanently deletes an item from the deleted_items table
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // First, get the item info to potentially delete associated image file
    $stmt = $conn->prepare("SELECT image_path FROM deleted_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        
        // Delete from database
        $delete_stmt = $conn->prepare("DELETE FROM deleted_items WHERE id = ?");
        $delete_stmt->bind_param("i", $id);
        
        if ($delete_stmt->execute()) {
            // Optional: Delete the image file if it's not a default image and exists
            // Uncomment this section if you want to delete the actual image files
            /*
            if (!empty($image_path) && file_exists($image_path) && 
                $image_path != 'picture-1.png' && 
                $image_path != 'keyboard.jpg' && 
                !strpos($image_path, 'default')) {
                
                unlink($image_path);
            }
            */
            
            $_SESSION['success_message'] = "Item permanently deleted!";
        } else {
            $_SESSION['error_message'] = "Error deleting item: " . $conn->error;
        }
        
        $delete_stmt->close();
    } else {
        $_SESSION['error_message'] = "Item not found.";
    }
    
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid request. No item ID specified.";
}

// Redirect back to deleted items page
header("Location: deleted_items.php");
exit();
?> 