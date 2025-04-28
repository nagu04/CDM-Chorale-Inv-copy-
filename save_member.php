<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $members_name = $_POST['members_name'];
    $program = $_POST['program'];
    $position = $_POST['position'];
    $birthdate = !empty($_POST['birthdate']) ? $_POST['birthdate'] : null;
    $address = !empty($_POST['address']) ? $_POST['address'] : null;
    
    // Default image path
    $image_path = null;
    
    // Handle image upload if present
    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        // Create the member_profiles directory if it doesn't exist
        if(!file_exists('member_profiles')) {
            mkdir('member_profiles', 0777, true);
        }
        
        // Generate a unique filename
        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = 'member_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
        $target_file = "member_profiles/" . $filename;
        
        // Move the uploaded file to the target location
        if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // File uploaded successfully
            $image_path = $target_file;
        }
    }

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO members (members_name, program, position, birthdate, address, image_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $members_name, $program, $position, $birthdate, $address, $image_path);
    
    if ($stmt->execute()) {
        // Success - redirect back to members page
        $_SESSION['success_message'] = "Member added successfully!";
        header("Location: members.php");
        exit();
    } else {
        // Error
        $_SESSION['error_message'] = "Error adding member: " . $conn->error;
        header("Location: members.php");
        exit();
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 