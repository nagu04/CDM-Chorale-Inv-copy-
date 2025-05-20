<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clothing_id'])) {
    $clothing_id = $_POST['clothing_id'];
    $delete_reason = isset($_POST['delete_reason']) ? trim($_POST['delete_reason']) : 'No reason provided';
    
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
        
        // Get user's full name for deleted_by field
        $deleted_by = $_SESSION['username'] ?? 'Unknown User';
        
        // If we know which table the user belongs to
        if (isset($_SESSION['user_table'])) {
            $user_table = $_SESSION['user_table'];
            $user_query = $conn->prepare("SELECT full_name FROM $user_table WHERE username = ?");
            $user_query->bind_param("s", $_SESSION['username']);
            $user_query->execute();
            $user_result = $user_query->get_result();
            
            if ($user_result && $user_result->num_rows > 0) {
                $user_data = $user_result->fetch_assoc();
                if (!empty($user_data['full_name'])) {
                    $deleted_by = $user_data['full_name'];
                }
            }
            $user_query->close();
        }
        
        // Store in deleted_items table
        $save_stmt = $conn->prepare("INSERT INTO deleted_clothing (item_id, item_name, quantity, condition_status, image_path, deleted_by, reason, deleted_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        
        $item_type = 'clothing';
        $save_stmt->bind_param("isissss", $clothing_id, $clothing_name, $quantity, $condition, $image_path, $deleted_by, $delete_reason);
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