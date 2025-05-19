<?php
// permanent_delete_all.php - Permanently deletes all items from the deleted_* tables
session_start();
include 'db_connect.php';

// Delete all records from each deleted table
$success = true;
$tables = ['deleted_instruments', 'deleted_accessories', 'deleted_clothing', 'deleted_members'];
foreach ($tables as $table) {
    if (!$conn->query("DELETE FROM $table")) {
        $success = false;
        $_SESSION['error_message'] = "Error emptying $table: " . $conn->error;
        break;
    }
}

if ($success) {
    $_SESSION['success_message'] = "Trash emptied successfully! All deleted items permanently removed.";
}

// Redirect back to deleted items page
header("Location: deleted_items.php");
exit();
?> 