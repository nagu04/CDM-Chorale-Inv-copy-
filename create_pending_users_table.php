<?php
include 'db_connect.php';

// Create pending_users table
$sql = "CREATE TABLE IF NOT EXISTS `pending_users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `password` varchar(255) NOT NULL,
    `email` varchar(100) NOT NULL,
    `full_name` varchar(100) NOT NULL,
    `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `approved_by` varchar(50) DEFAULT NULL,
    `approved_at` timestamp NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `username` (`username`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

if ($conn->query($sql) === TRUE) {
    echo "Table pending_users created successfully";
} else {
    echo "Error creating table: " . $conn->error;
}

$conn->close();
?> 