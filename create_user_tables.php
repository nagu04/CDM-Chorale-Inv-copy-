<?php
include 'db_connect.php';

// Create users table
$create_users_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    is_admin TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_users_table) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . $conn->error . "<br>";
}

// Create pending_users table
$create_pending_users_table = "CREATE TABLE IF NOT EXISTS pending_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($create_pending_users_table) === TRUE) {
    echo "Pending users table created successfully<br>";
} else {
    echo "Error creating pending users table: " . $conn->error . "<br>";
}

// Create default admin user if not exists
$check_admin_sql = "SELECT id FROM users WHERE username = 'admin'";
$result = $conn->query($check_admin_sql);

if ($result->num_rows == 0) {
    $admin_password = password_hash('admin', PASSWORD_DEFAULT);
    $insert_admin_sql = "INSERT INTO users (username, password, full_name, email, is_admin) VALUES ('admin', ?, 'Administrator', 'admin@example.com', 1)";
    $stmt = $conn->prepare($insert_admin_sql);
    $stmt->bind_param("s", $admin_password);
    
    if ($stmt->execute()) {
        echo "Default admin user created successfully<br>";
    } else {
        echo "Error creating default admin user: " . $stmt->error . "<br>";
    }
}

$conn->close();
?> 