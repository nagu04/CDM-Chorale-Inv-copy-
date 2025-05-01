<?php
include 'db_connect.php';

// SQL to alter history table
$sql = "ALTER TABLE history 
        ADD COLUMN is_approved boolean DEFAULT FALSE,
        MODIFY COLUMN type enum('BORROW','REPORT','ADD','DELETE') NOT NULL";

if ($conn->query($sql) === TRUE) {
    echo "History table altered successfully";
} else {
    echo "Error altering table: " . $conn->error;
}

$conn->close();
?> 