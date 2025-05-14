<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['instru_id'])) {
    $instrument_id = $_POST['instru_id'];
    $delete_reason = isset($_POST['delete_reason']) ? trim($_POST['delete_reason']) : 'No reason provided';
    
    // First, get the instrument details
    $stmt = $conn->prepare("SELECT * FROM instruments WHERE instru_id = ?");
    $stmt->bind_param("i", $instrument_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        $instrument_name = $row['instrument_name'];
        $condition = $row['condition'];
        $quantity = $row['quantity'];
        
        // Convert to JSON for additional details
        $details = json_encode([
            'original_table' => 'instruments',
            'original_id' => $instrument_id,
            'instrument_name' => $instrument_name,
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
        $item_type = 'instrument';
        $save_stmt->bind_param("isiiissss", $instrument_id, $instrument_name, $item_type, $quantity, $condition, $image_path, $deleted_by, $delete_reason, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the instrument
    $stmt = $conn->prepare("DELETE FROM instruments WHERE instru_id = ?");
    $stmt->bind_param("i", $instrument_id);
    
    if ($stmt->execute()) {
        // If instrument deleted successfully, delete the image if it exists and is not the default
        if (!empty($image_path) && file_exists($image_path) && $image_path != 'keyboard.jpg' && $image_path != 'picture-1.png') {
            // We don't delete the file anymore since we want to keep it for the deleted items page
            // unlink($image_path); 
        }
        
        // Success message
        $_SESSION['success_message'] = "Instrument deleted successfully!";
        
        // Redirect back to instruments page
        header("Location: instruments.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting instrument: " . $conn->error;
        header("Location: instruments.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: instruments.php");
    exit();
}

$conn->close();
?> 