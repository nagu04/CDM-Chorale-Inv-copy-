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

        // If this is a borrow operation, update the inventory
        if ($record['type'] === 'BORROW') {
            // Determine which table to update based on category
            $table = '';
            switch(strtolower($record['category'])) {
                case 'instruments':
                    $table = 'instruments';
                    $name_field = 'instrument_name';
                    break;
                case 'clothing':
                    $table = 'clothing';
                    $name_field = 'clothing_name';
                    break;
                case 'accessories':
                    $table = 'accessories';
                    $name_field = 'accessory_name';
                    break;
                default:
                    throw new Exception("Invalid category");
            }

            // Update the quantity in the respective table
            $update_stmt = $conn->prepare("UPDATE $table SET quantity = quantity + ? WHERE $name_field = ?");
            $update_stmt->bind_param("is", $record['quantity'], $record['item_name']);
            $update_stmt->execute();
            $update_stmt->close();
        }

        // Delete the history record
        $delete_stmt = $conn->prepare("DELETE FROM history WHERE history_id = ?");
        $delete_stmt->bind_param("i", $id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Commit transaction
        $conn->commit();

        // Redirect to history page on success
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