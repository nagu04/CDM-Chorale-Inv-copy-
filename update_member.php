<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $last_name = trim($_POST['last_name']);
    $given_name = trim($_POST['given_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $extension = trim($_POST['extension']);
    $program = trim($_POST['program']);
    $position = trim($_POST['position']);
    $birthdate = trim($_POST['birthdate']);
    $address = trim($_POST['address']);
    
    // Fetch current image path
    $current_image_path = '';
    $stmt = $conn->prepare("SELECT image_path FROM members WHERE member_id = ?");
    $stmt->bind_param("i", $member_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $current_image_path = $row['image_path'];
    }
    $stmt->close();

    // Handle remove image checkbox
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == '5') {
        // Delete the old image if it exists and is not already the default
        if (!empty($current_image_path) && file_exists($current_image_path) &&
            $current_image_path != 'default image.jpg' && $current_image_path != 'picture-1.png') {
            unlink($current_image_path);
        }
        $image_path = 'default image.jpg';
    }
    // Handle new image upload (only if remove_image is not checked)
    elseif (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        if (!file_exists('member_profiles')) {
            mkdir('member_profiles', 0777, true);
        }
        $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $filename = 'member_' . $member_id . '_' . time() . '.' . $file_ext;
        $target_file = "member_profiles/" . $filename;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            // Delete old image if exists and is not default
            if (!empty($current_image_path) && file_exists($current_image_path) &&
                $current_image_path != 'default image.jpg' && $current_image_path != 'picture-1.png') {
                unlink($current_image_path);
            }
            $image_path = $target_file;
        } else {
            $image_path = $current_image_path; // fallback
        }
    } else {
        $image_path = $current_image_path; // keep current if nothing changes
    }

    // Update member information
    $stmt = $conn->prepare("UPDATE members SET last_name = ?, given_name = ?, middle_initial = ?, extension = ?, program = ?, position = ?, birthdate = ?, address = ?, image_path = ? WHERE member_id = ?");
    $stmt->bind_param("sssssssssi", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $image_path, $member_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Member updated successfully!";
        header("Location: members.php");
    } else {
        $_SESSION['error_message'] = "Error updating member: " . $conn->error;
        header("Location: members.php");
    }
    
    $stmt->close();

} else {
    // Not a valid POST request
    header("Location: members.php");
    exit();
}

$conn->close();
?> 