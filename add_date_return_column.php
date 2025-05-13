<?php
include 'db_connect.php';

// Check if date_return column exists
$check_column = "SHOW COLUMNS FROM history LIKE 'date_return'";
$result = $conn->query($check_column);

if ($result->num_rows == 0) {
    // Add date_return column if it doesn't exist
    $sql = "ALTER TABLE history ADD COLUMN date_return date DEFAULT NULL";
    
    if ($conn->query($sql) === TRUE) {
        echo "Column date_return added successfully";
    } else {
        echo "Error adding column: " . $conn->error;
    }
} else {
    echo "Column date_return already exists";
}

$conn->close();
?> 