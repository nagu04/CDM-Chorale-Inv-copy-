<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    
    // First, get all member details
    $stmt = $conn->prepare("SELECT * FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'] ?? '';
        $member_name = $row['members_name'];
        $program = $row['program'];
        $position = $row['position'];
        $birthdate = $row['birthdate'] ?? NULL;
        $address = $row['address'] ?? '';
        
        // Convert to JSON for additional details
        $details = json_encode([
            'original_table' => 'members',
            'original_id' => $member_id,
            'program' => $program,
            'position' => $position,
            'birthdate' => $birthdate,
            'address' => $address
        ]);
        
        // Store in deleted_items table
        $save_stmt = $conn->prepare("INSERT INTO deleted_items (item_id, item_name, item_type, image_path, deleted_by, details) VALUES (?, ?, ?, ?, ?, ?)");
        $deleted_by = $_SESSION['username'] ?? 'Unknown User';
        $item_type = 'member';
        $save_stmt->bind_param("isssss", $member_id, $member_name, $item_type, $image_path, $deleted_by, $details);
        $save_stmt->execute();
        $save_stmt->close();
    }
    
    $stmt->close();

    // Prepare and execute SQL statement to delete the member
    $stmt = $conn->prepare("DELETE FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    
    if ($stmt->execute()) {
        // Member deleted successfully, but we don't delete the image anymore
        // to keep it for the deleted items page
        
        // Success message
        $_SESSION['success_message'] = "Member deleted successfully!";
        
        // Redirect back to members page
        header("Location: members.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error deleting member: " . $conn->error;
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