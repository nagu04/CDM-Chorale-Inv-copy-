<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $last_name = trim($_POST['last_name']);
    $given_name = trim($_POST['given_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $extension = trim($_POST['extension']);
    $program = trim($_POST['program']);
    $position = trim($_POST['position']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if (!file_exists('member_profiles')) {
            mkdir('member_profiles', 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'member_' . time() . '.' . $file_ext;
        $target_file = "member_profiles/" . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }
    
    // Insert new member
    $stmt = $conn->prepare("INSERT INTO members (last_name, given_name, middle_initial, extension, program, position, birthdate, address, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $image_path);
    
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