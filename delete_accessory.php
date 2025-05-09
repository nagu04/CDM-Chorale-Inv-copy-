<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deco_id'])) {
    $deco_id = $_POST['deco_id'];
    
    // First, get all accessory details
    $stmt = $conn->prepare("SELECT * FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        $deco_name = $row['deco_name'];
        $condition = $row['condition'];
        $quantity = $row['quantity'];
        
        // Convert to JSON for additional details
        $details = json_encode([
            'original_table' => 'accessories',
            'original_id' => $deco_id,
            'deco_name' => $deco_name,
            'condition' => $condition,
            'quantity' => $quantity
        ]);
        
        // Store in deleted_items table
        $save_stmt = $conn->prepare("INSERT INTO deleted_items (item_id, item_name, item_type, quantity, condition_status, image_path, deleted_by, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $deleted_by = $_SESSION['username'] ?? 'Unknown User';
        $item_type = 'accessory';
        $save_stmt->bind_param("isiissss", $deco_id, $deco_name, $item_type, $quantity, $condition, $image_path, $deleted_by, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the accessory
    $stmt = $conn->prepare("DELETE FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    
    if ($stmt->execute()) {
        // We don't delete the image anymore so we can use it in the deleted items page
        
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