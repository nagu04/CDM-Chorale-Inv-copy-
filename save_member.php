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

    // Prepare and execute SQL statement
    $stmt = $conn->prepare("INSERT INTO members (members_name, program, position, birthdate, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $members_name, $program, $position, $birthdate, $address);
    
    if ($stmt->execute()) {
        // Success - redirect back to members page
        header("Location: members.php");
        exit();
    } else {
        // Error
        echo "Error adding member: " . $conn->error;
    }
    
    $stmt->close();
} else {
    // Not a POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 