<?php
session_start();
include 'db_connect.php';

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

if (!$type || !$id) {
    $_SESSION['error_message'] = "Invalid restore request.";
    header("Location: deleted_items.php");
    exit;
}

$type = strtolower($type);
$success = false;

switch ($type) {
    case 'instrument':
        $stmt = $conn->prepare("SELECT * FROM deleted_instruments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->get_result()->fetch_assoc();
        if ($deleted) {
            $insert = $conn->prepare("INSERT INTO instruments (instrument_name, quantity, `condition`, image_path) VALUES (?, ?, ?, ?)");
            $insert->bind_param(
                "siss",
                $deleted['item_name'],
                $deleted['quantity'],
                $deleted['condition_status'],
                $deleted['image_path']
            );
            if ($insert->execute()) {
                $del = $conn->prepare("DELETE FROM deleted_instruments WHERE id = ?");
                $del->bind_param("i", $id);
                $del->execute();
                $success = true;
            }
        }
        break;
    case 'accessory':
        $stmt = $conn->prepare("SELECT * FROM deleted_accessories WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->get_result()->fetch_assoc();
        if ($deleted) {
            $insert = $conn->prepare("INSERT INTO accessories (deco_name, quantity, `condition`, image_path) VALUES (?, ?, ?, ?)");
            $insert->bind_param(
                "siss",
                $deleted['item_name'],
                $deleted['quantity'],
                $deleted['condition_status'],
                $deleted['image_path']
            );
            if ($insert->execute()) {
                $del = $conn->prepare("DELETE FROM deleted_accessories WHERE id = ?");
                $del->bind_param("i", $id);
                $del->execute();
                $success = true;
            }
        }
        break;
    case 'clothing':
        $stmt = $conn->prepare("SELECT * FROM deleted_clothing WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->get_result()->fetch_assoc();
        if ($deleted) {
            $insert = $conn->prepare("INSERT INTO clothing (clothing_name, quantity, `condition`, image_path) VALUES (?, ?, ?, ?)");
            $insert->bind_param(
                "siss",
                $deleted['item_name'],
                $deleted['quantity'],
                $deleted['condition_status'],
                $deleted['image_path']
            );
            if ($insert->execute()) {
                $del = $conn->prepare("DELETE FROM deleted_clothing WHERE id = ?");
                $del->bind_param("i", $id);
                $del->execute();
                $success = true;
            }
        }
        break;
    case 'member':
        $stmt = $conn->prepare("SELECT * FROM deleted_members WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $deleted = $stmt->get_result()->fetch_assoc();
        if ($deleted) {
            $insert = $conn->prepare("INSERT INTO members (members_name, image_path) VALUES (?, ?)");
            $insert->bind_param(
                "ss",
                $deleted['full_name'],
                $deleted['image_path']
            );
            if ($insert->execute()) {
                $del = $conn->prepare("DELETE FROM deleted_members WHERE id = ?");
                $del->bind_param("i", $id);
                $del->execute();
                $success = true;
            }
        }
        break;
    default:
        $_SESSION['error_message'] = "Invalid item type for restore.";
        header("Location: deleted_items.php");
        exit;
}

if ($success) {
    $_SESSION['success_message'] = ucfirst($type) . " restored successfully!";
} else {
    $_SESSION['error_message'] = "Failed to restore " . $type . ".";
}
header("Location: deleted_items.php");
exit; 