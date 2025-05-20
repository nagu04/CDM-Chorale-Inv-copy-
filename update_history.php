<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $history_id = $_POST['id'];
    $borrowed_by = $_POST['borrowedBy'];
    $date = $_POST['date'];
    $date_return = $_POST['date_return'];
    $category = $_POST['category'];
    $item_name = $_POST['itemName'];
    $quantity = $_POST['quantity'];
    $sn = $_POST['sn'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];
    
    // Update all fields
    $stmt = $conn->prepare("UPDATE history SET borrowed_by = ?, date = ?, date_return = ?, category = ?, item_name = ?, quantity = ?, sn = ?, status = ?, remarks = ? WHERE history_id = ?");
    $stmt->bind_param("sssssisssi", $borrowed_by, $date, $date_return, $category, $item_name, $quantity, $sn, $status, $remarks, $history_id);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "History record updated successfully!";
    } else {
        $_SESSION['error_message'] = "Error updating record: " . $conn->error;
    }
    
    $stmt->close();
    
    // Determine which page to redirect to based on the HTTP referer
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    if (strpos($referer, '_nonadmin') !== false) {
        // Redirect to non-admin history page
        header("Location: history_nonadmin.php");
    } else {
        // Redirect to admin history page
        header("Location: history.php");
    }
    exit();
} else {
    // Determine which page to redirect to based on the HTTP referer for error case
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    
    if (strpos($referer, '_nonadmin') !== false) {
        // Redirect to non-admin history page
        header("Location: history_nonadmin.php");
    } else {
        // Redirect to admin history page
        header("Location: history.php");
    }
    exit();
}

$conn->close();
?>