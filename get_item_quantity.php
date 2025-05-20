<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if category and item name were provided
if (isset($_GET['category']) && isset($_GET['item_name'])) {
    $category = $_GET['category'];
    $item_name = $_GET['item_name'];
    
    // Determine which table and field to use based on category
    $table = '';
    $name_field = '';
    
    switch(strtolower($category)) {
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
            $name_field = 'deco_name';
            break;
        default:
            echo json_encode(['error' => 'Invalid category']);
            exit();
    }
    
    // Query the appropriate table for the quantity of the specified item
    $stmt = $conn->prepare("SELECT quantity FROM $table WHERE $name_field = ?");
    $stmt->bind_param("s", $item_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $max_quantity = $row['quantity'];
        
        // Generate an array of quantities from 1 to max_quantity
        $quantities = range(1, $max_quantity);
        
        echo json_encode(['quantities' => $quantities, 'max_quantity' => $max_quantity]);
    } else {
        echo json_encode(['error' => 'Item not found']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Missing parameters']);
}

$conn->close();
?> 