<?php
include 'db_connect.php';

$sql = "
ALTER TABLE deleted_members
ADD COLUMN program VARCHAR(255) NULL,
ADD COLUMN position VARCHAR(255) NULL,
ADD COLUMN birthdate DATE NULL,
ADD COLUMN address VARCHAR(255) NULL,
ADD COLUMN last_name VARCHAR(255) NULL,
ADD COLUMN given_name VARCHAR(255) NULL,
ADD COLUMN middle_initial VARCHAR(10) NULL,
ADD COLUMN extension VARCHAR(10) NULL;
";

if ($conn->query($sql) === TRUE) {
    echo "Columns added successfully!";
} else {
    echo "Error updating table: " . $conn->error;
}
$conn->close();
?> 