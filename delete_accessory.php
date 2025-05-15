<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deco_id'])) {
    $deco_id = $_POST['deco_id'];
    $delete_reason = isset($_POST['delete_reason']) ? trim($_POST['delete_reason']) : 'No reason provided';
    
    // First, get the accessory details
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
        $save_stmt = $conn->prepare("INSERT INTO deleted_items (item_id, item_name, item_type, quantity, condition_status, image_path, deleted_by, reason, details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $item_type = 'accessory';
        $save_stmt->bind_param("isiiissss", $deco_id, $deco_name, $item_type, $quantity, $condition, $image_path, $deleted_by, $delete_reason, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the accessory
    $stmt = $conn->prepare("DELETE FROM accessories WHERE deco_id = ?");
    $stmt->bind_param("i", $deco_id);
    
    if ($stmt->execute()) {
        // If accessory deleted successfully, we keep the image for the deleted items page
        $_SESSION['success_message'] = "Accessory deleted successfully!";
        header("Location: accessory.php");
        exit();
    } else {
        $_SESSION['error_message'] = "Error deleting accessory: " . $conn->error;
        header("Location: accessory.php");
        exit();
    }
    
    $stmt->close();
} else {
    header("Location: accessory.php");
    exit();
}

$conn->close();
?> 