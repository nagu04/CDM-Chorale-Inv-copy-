<?php
// permanent_delete_all.php - Permanently deletes all items from the selected deleted_* table
session_start();
include 'db_connect.php';

$type = $_GET['type'] ?? '';

$table = '';
switch ($type) {
    case 'instruments':
        $table = 'deleted_instruments';
        break;
    case 'accessories':
        $table = 'deleted_accessories';
        break;
    case 'clothing':
        $table = 'deleted_clothing';
        break;
    case 'members':
        $table = 'deleted_members';
        break;
    default:
        $_SESSION['error_message'] = "Invalid trash type specified.";
        header("Location: deleted_items.php");
        exit();
}

if ($conn->query("DELETE FROM $table")) {
    $_SESSION['success_message'] = ucfirst($type) . " trash emptied successfully!";
} else {
    $_SESSION['error_message'] = "Error emptying trash: " . $conn->error;
}

header("Location: deleted_items.php");
exit();
?> 