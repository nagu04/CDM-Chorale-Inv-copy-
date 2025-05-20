<?php
include 'db_connect.php';

// Function to check if a column exists
function columnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// SQL statements to update the members table
$sql_statements = [];

// Only add new columns if they don't exist
if (!columnExists($conn, 'members', 'last_name')) {
    $sql_statements[] = "ALTER TABLE members ADD COLUMN last_name VARCHAR(50) AFTER member_id";
}
if (!columnExists($conn, 'members', 'given_name')) {
    $sql_statements[] = "ALTER TABLE members ADD COLUMN given_name VARCHAR(50) AFTER last_name";
}
if (!columnExists($conn, 'members', 'middle_initial')) {
    $sql_statements[] = "ALTER TABLE members ADD COLUMN middle_initial CHAR(1) AFTER given_name";
}
if (!columnExists($conn, 'members', 'extension')) {
    $sql_statements[] = "ALTER TABLE members ADD COLUMN extension VARCHAR(10) AFTER middle_initial";
}

// Only migrate data if members_name column exists
if (columnExists($conn, 'members', 'members_name')) {
    $sql_statements[] = "UPDATE members SET 
        last_name = SUBSTRING_INDEX(members_name, ',', 1),
        given_name = TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 1)),
        middle_initial = CASE 
            WHEN LENGTH(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 2)) > 0 
            THEN SUBSTRING(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(members_name, ',', -1), ' ', 2)), 1, 1)
            ELSE ''
        END,
        extension = CASE 
            WHEN LENGTH(SUBSTRING_INDEX(members_name, ' ', -1)) > 0 
            AND SUBSTRING_INDEX(members_name, ' ', -1) NOT IN (given_name, middle_initial)
            THEN SUBSTRING_INDEX(members_name, ' ', -1)
            ELSE ''
        END";
    
    // Drop the old members_name column
    $sql_statements[] = "ALTER TABLE members DROP COLUMN members_name";
}

// Execute each SQL statement
foreach ($sql_statements as $sql) {
    if (!$conn->query($sql)) {
        echo "Error executing SQL: " . $sql . "<br>";
        echo "Error message: " . $conn->error . "<br>";
    } else {
        echo "Successfully executed: " . $sql . "<br>";
    }
}

echo "Database update completed!";
$conn->close();
?> 