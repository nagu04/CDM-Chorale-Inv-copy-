<?php
// restore_item.php
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Get the deleted item info
    $stmt = $conn->prepare("SELECT * FROM deleted_items WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $item_id = $row['item_id'];
        $item_name = $row['item_name'];
        $item_type = $row['item_type'];
        $quantity = $row['quantity'];
        $condition_status = $row['condition_status'];
        $image_path = $row['image_path'];
        $details = json_decode($row['details'], true);
        
        // Check if item_type is empty and try to determine from details
        if (empty($item_type)) {
            $detected_type = '';
            
            // First check details for original_table
            if (!empty($details['original_table'])) {
                $original_table = strtolower(trim($details['original_table']));
                
                if ($original_table == 'instruments') {
                    $detected_type = 'instrument';
                } elseif ($original_table == 'accessories') {
                    $detected_type = 'accessory';
                } elseif ($original_table == 'clothing') {
                    $detected_type = 'clothing';
                } elseif ($original_table == 'members') {
                    $detected_type = 'member';
                }
            }
            
            // If still not detected, try to guess from the field names in details
            if (empty($detected_type)) {
                if (isset($details['instrument_name'])) {
                    $detected_type = 'instrument';
                } elseif (isset($details['deco_name'])) {
                    $detected_type = 'accessory';
                } elseif (isset($details['clothing_name'])) {
                    $detected_type = 'clothing';
                } elseif (isset($details['program']) || isset($details['position'])) {
                    $detected_type = 'member';
                }
            }
            
            // Update the item_type if we detected one
            if (!empty($detected_type)) {
                $item_type = $detected_type;
                
                // Update the database record
                $update_stmt = $conn->prepare("UPDATE deleted_items SET item_type = ? WHERE id = ?");
                $update_stmt->bind_param("si", $item_type, $id);
                $update_stmt->execute();
                $update_stmt->close();
                error_log("Updated empty item_type to '$item_type' for item ID $id based on details");
            }
        }
        
        // Based on item type, restore to appropriate table
        $success = false;
        
        // Normalize item type to handle case sensitivity and whitespace
        $normalized_type = strtolower(trim($item_type));
        
        // Debug info in case of issues
        error_log("Restoring item type: '$item_type', normalized to: '$normalized_type'");
        
        if (empty($normalized_type)) {
            // Still empty after attempting to fix - show detailed error
            $_SESSION['error_message'] = "Item type is empty. Cannot determine type for restoration. Please check database records for ID $id.";
            header("Location: deleted_items.php");
            exit();
        }
        elseif ($normalized_type == 'instrument') {
            // Check if item with same ID or name already exists
            $check_stmt = $conn->prepare("SELECT instru_id FROM instruments WHERE instru_id = ? OR instrument_name = ?");
            $check_stmt->bind_param("is", $item_id, $item_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Item doesn't exist, safe to restore
                $restore_stmt = $conn->prepare("INSERT INTO instruments (instru_id, instrument_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?, ?)");
                $restore_stmt->bind_param("issis", $item_id, $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            } else {
                // Item exists, create new entry
                $restore_stmt = $conn->prepare("INSERT INTO instruments (instrument_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?)");
                $restore_stmt->bind_param("ssis", $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            }
            $check_stmt->close();
        } 
        elseif ($normalized_type == 'accessory') {
            // Check if item already exists
            $check_stmt = $conn->prepare("SELECT deco_id FROM accessories WHERE deco_id = ? OR deco_name = ?");
            $check_stmt->bind_param("is", $item_id, $item_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Item doesn't exist, safe to restore
                $restore_stmt = $conn->prepare("INSERT INTO accessories (deco_id, deco_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?, ?)");
                $restore_stmt->bind_param("issis", $item_id, $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            } else {
                // Item exists, create new entry
                $restore_stmt = $conn->prepare("INSERT INTO accessories (deco_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?)");
                $restore_stmt->bind_param("ssis", $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            }
            $check_stmt->close();
        } 
        elseif ($normalized_type == 'clothing') {
            // Check if item already exists
            $check_stmt = $conn->prepare("SELECT clothing_id FROM clothing WHERE clothing_id = ? OR clothing_name = ?");
            $check_stmt->bind_param("is", $item_id, $item_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Item doesn't exist, safe to restore
                $restore_stmt = $conn->prepare("INSERT INTO clothing (clothing_id, clothing_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?, ?)");
                $restore_stmt->bind_param("issis", $item_id, $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            } else {
                // Item exists, create new entry
                $restore_stmt = $conn->prepare("INSERT INTO clothing (clothing_name, `condition`, quantity, image_path) VALUES (?, ?, ?, ?)");
                $restore_stmt->bind_param("ssis", $item_name, $condition_status, $quantity, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            }
            $check_stmt->close();
        } 
        elseif ($normalized_type == 'member') {
            // Check if member already exists
            $check_stmt = $conn->prepare("SELECT member_id FROM members WHERE member_id = ? OR members_name = ?");
            $check_stmt->bind_param("is", $item_id, $item_name);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            
            if ($check_result->num_rows == 0) {
                // Member doesn't exist, safe to restore
                // Extract additional member details from JSON
                $program = $details['program'] ?? '';
                $position = $details['position'] ?? '';
                $birthdate = $details['birthdate'] ?? NULL;
                $address = $details['address'] ?? '';
                
                $restore_stmt = $conn->prepare("INSERT INTO members (member_id, members_name, program, position, birthdate, address, image_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $restore_stmt->bind_param("issssss", $item_id, $item_name, $program, $position, $birthdate, $address, $image_path);
                $success = $restore_stmt->execute();
                $restore_stmt->close();
            } else {
                // Member exists, cannot restore with same details
                $_SESSION['error_message'] = "Cannot restore member. A member with the same name already exists.";
                header("Location: deleted_items.php");
                exit();
            }
            $check_stmt->close();
        }
        else {
            // Unknown item type - show detailed error
            $_SESSION['error_message'] = "Unknown item type: '$item_type' (normalized: '$normalized_type'). Cannot restore.";
            header("Location: deleted_items.php");
            exit();
        }
        
        if ($success) {
            // Remove from deleted_items table
            $delete_stmt = $conn->prepare("DELETE FROM deleted_items WHERE id = ?");
            $delete_stmt->bind_param("i", $id);
            $delete_stmt->execute();
            $delete_stmt->close();
            
            $_SESSION['success_message'] = "Item restored successfully!";
        } else {
            $_SESSION['error_message'] = "Error restoring item: " . $conn->error;
        }
    } else {
        $_SESSION['error_message'] = "Deleted item not found.";
    }
    
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Invalid request. No item ID specified.";
}

header("Location: deleted_items.php");
exit();
?> 