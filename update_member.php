<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    $members_name = $_POST['members_name'];
    $program = $_POST['program'];
    $position = $_POST['position'];
    $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $address = !empty($_POST['address']) ? $_POST['address'] : null;
    
    // First, get the current image path
    $stmt = $conn->prepare("SELECT image_path FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $current_image_path = $row['image_path'];
    } else {
        $current_image_path = null;
    }
    
    $stmt->close();
    
    // Check if a new image was uploaded
    $image_path = $current_image_path; // Default to current image path
    
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        // Create the member_profiles directory if it doesn't exist
        if(!file_exists('member_profiles')) {
            mkdir('member_profiles', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = 'member_' . $member_id . '_' . time() . '.' . $file_ext;
        $target_file = "member_profiles/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // If upload successful, delete the old image if it exists
            if (!empty($current_image_path) && file_exists($current_image_path) && $current_image_path != $target_file) {
                unlink($current_image_path); // Delete the old image file
            }
            
            // Update image path to the new image
            $image_path = $target_file;
        }
    }
    
    // Update the member in the database
    $stmt = $conn->prepare("UPDATE members SET members_name = ?, program = ?, position = ?, birthdate = ?, address = ?, image_path = ? WHERE member_id = ?");
    $stmt->bind_param("ssssssi", $members_name, $program, $position, $birthdate, $address, $image_path, $member_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Member updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating member: " . $conn->error;
    }
    
    $stmt->close();
    
    // Redirect back to members page
    header("Location: members.php");
    exit();
} else {
    // Not a valid POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 