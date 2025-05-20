<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];
    $last_name = trim($_POST['last_name']);
    $given_name = trim($_POST['given_name']);
    $middle_initial = trim($_POST['middle_initial']);
    $extension = trim($_POST['extension']);
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
    
    // Default to current image path
    $image_path = $current_image_path;
    
    // Check if remove_image checkbox is checked
    if(isset($_POST['remove_image']) && $_POST['remove_image'] == '5') {
        // Set image path to default image
        $image_path = 'default image.jpg';
        
        // Delete the old image if it exists and is not already the default
        if (!empty($current_image_path) && file_exists($current_image_path) && 
            $current_image_path != 'default image.jpg' && $current_image_path != 'picture-1.png') {
            unlink($current_image_path);
        }
    }
    // Check if a new image was uploaded and remove_image is not checked
    elseif(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
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
            // If upload successful, delete the old image if it exists and is not the default
            if (!empty($current_image_path) && file_exists($current_image_path) && 
                $current_image_path != 'default image.jpg' && $current_image_path != 'picture-1.png') {
                unlink($current_image_path); // Delete the old image file
            }
            
            // Update image path to the new image
            $image_path = $target_file;
        }
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
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        if (!file_exists('member_profiles')) {
            mkdir('member_profiles', 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = 'member_' . $member_id . '_' . time() . '.' . $file_ext;
        $target_file = "member_profiles/" . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            // Delete old image if exists
            $old_image_query = "SELECT image_path FROM members WHERE member_id = ?";
            $stmt = $conn->prepare($old_image_query);
            $stmt->bind_param("i", $member_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if (!empty($row['image_path']) && file_exists($row['image_path']) && 
                    $row['image_path'] != 'default image.jpg' && $row['image_path'] != 'picture-1.png') {
                    unlink($row['image_path']);
                }
            }
            $image_path = $target_file;
        }
    }
    
    // Update member information
    if (!empty($image_path)) {
        $stmt = $conn->prepare("UPDATE members SET last_name = ?, given_name = ?, middle_initial = ?, extension = ?, program = ?, position = ?, birthdate = ?, address = ?, image_path = ? WHERE member_id = ?");
        $stmt->bind_param("sssssssssi", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $image_path, $member_id);
    } else {
        $stmt = $conn->prepare("UPDATE members SET last_name = ?, given_name = ?, middle_initial = ?, extension = ?, program = ?, position = ?, birthdate = ?, address = ? WHERE member_id = ?");
        $stmt->bind_param("ssssssssi", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $member_id);
    }
    
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