<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];
    
    // First, get all clothing details
    $stmt = $conn->prepare("SELECT * FROM clothing WHERE clothing_id = ?");
    $stmt->bind_param("i", $clothing_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        $clothing_name = $row['clothing_name'];
        $condition = $row['condition'];
        $quantity = $row['quantity'];
        
        // Convert to JSON for additional details
        $details = json_encode([
            'original_table' => 'clothing',
            'original_id' => $clothing_id,
            'clothing_name' => $clothing_name,
            'condition' => $condition,
            'quantity' => $quantity
        ]);
        
        // Store in deleted_items table
        $save_stmt = $conn->prepare("INSERT INTO deleted_items (item_id, item_name, item_type, quantity, condition_status, image_path, deleted_by, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $deleted_by = $_SESSION['username'] ?? 'Unknown User';
        $item_type = 'clothing';
        $save_stmt->bind_param("isiissss", $clothing_id, $clothing_name, $item_type, $quantity, $condition, $image_path, $deleted_by, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the clothing item
    $stmt = $conn->prepare("DELETE FROM clothing WHERE clothing_id = ?");
    $stmt->bind_param("i", $clothing_id);
    
    if ($stmt->execute()) {
        // We don't delete the image anymore so we can use it in the deleted items page
        
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