<?php
include 'db_connect.php';

if (isset($_GET['category'])) {
    $category = $_GET['category'];
    $items = [];
    
    if ($category == 'Instruments') {
        $sql = "SELECT instrument_name as name, quantity FROM instruments ORDER BY instrument_name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $items[] = [
                    'name' => $row['name'],
                    'quantity' => $row['quantity']
                ];
            }
        }
    } 
    else if ($category == 'Accessories') {
        $sql = "SELECT deco_name as name, quantity FROM accessories ORDER BY deco_name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $items[] = [
                    'name' => $row['name'],
                    'quantity' => $row['quantity']
                ];
            }
        }
    }
    else if ($category == 'Clothing') {
        $sql = "SELECT clothing_name as name, quantity, clothing_color, clothing_size_id FROM clothing ORDER BY clothing_name";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Format the name to match what's displayed on the card
                $formattedName = $row['name'];
                
                $items[] = [
                    'name' => $formattedName,
                    'full_name' => $formattedName . (!empty($row['clothing_color']) ? ' - ' . $row['clothing_color'] : '') . 
                                (!empty($row['clothing_size_id']) ? ' (' . $row['clothing_size_id'] . ')' : ''),
                    'quantity' => $row['quantity']
                ];
            }
        }
    }
    
    // Return as JSON
    header('Content-Type: application/json');
    echo json_encode($items);
}

$conn->close();
?> 