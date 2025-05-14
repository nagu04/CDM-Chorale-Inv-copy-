<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    $delete_reason = isset($_POST['delete_reason']) ? trim($_POST['delete_reason']) : 'No reason provided';
    
    // First, get the member details
    $stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        $member_name = $row['members_name'];
        
        // Convert to JSON for additional details
        $details = json_encode([
            'original_table' => 'members',
            'original_id' => $member_id,
            'member_name' => $member_name,
            'program' => $row['program'] ?? '',
            'position' => $row['position'] ?? '',
            'birthdate' => $row['birthdate'] ?? '',
            'address' => $row['address'] ?? ''
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
        $save_stmt = $conn->prepare("INSERT INTO deleted_items (item_id, item_name, item_type, image_path, deleted_by, reason, details) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $item_type = 'member';
        $save_stmt->bind_param("issssss", $member_id, $member_name, $item_type, $image_path, $deleted_by, $delete_reason, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the member
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    
    if ($stmt->execute()) {
        // Success message
        $_SESSION['success_message'] = "Member removed successfully!";
        header("Location: members.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error removing member: " . $conn->error;
        header("Location: members.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a valid POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 