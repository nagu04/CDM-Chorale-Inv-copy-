<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Get the record to check if we need to update inventory
        $stmt = $conn->prepare("SELECT * FROM history WHERE history_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $record = $result->fetch_assoc();
        $stmt->close();

        // Update the approval status
        $update_stmt = $conn->prepare("UPDATE history SET is_approved = TRUE WHERE history_id = ?");
        $update_stmt->bind_param("i", $id);
        $update_stmt->execute();
        $update_stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect back to history page
        header("Location: history.php");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
} else {
    die("No ID provided");
}

$conn->close();
?> 