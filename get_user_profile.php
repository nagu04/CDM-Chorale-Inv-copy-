<?php
session_start();
include 'db_connect.php';

// Default response
$response = [
    'success' => false,
    'full_name' => '',
    'message' => 'User not found'
];

// Check if user is logged in
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    
    // Check if we know which table the user belongs to from the session
    if (isset($_SESSION['user_table'])) {
        $table = $_SESSION['user_table'];
        
        $sql = "SELECT * FROM $table WHERE username=?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                $user = $result->fetch_assoc();
                
                // Check if full_name column exists and has data
                $full_name = isset($user['full_name']) && !empty($user['full_name']) 
                    ? $user['full_name'] 
                    : $username; // Default to username if no full name
                
                $response = [
                    'success' => true,
                    'full_name' => $full_name,
                    'message' => 'User found'
                ];
            }
        }
    } else {
        // If user_table is not in session, try each possible user table
        $tables = ['login', 'user_login', 'users'];
        
        foreach ($tables as $table) {
            $sql = "SELECT * FROM $table WHERE username=?";
            $stmt = $conn->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param('s', $username);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result && $result->num_rows > 0) {
                    $user = $result->fetch_assoc();
                    
                    // Store the table in session for future use
                    $_SESSION['user_table'] = $table;
                    
                    // Check if full_name column exists and has data
                    $full_name = isset($user['full_name']) && !empty($user['full_name']) 
                        ? $user['full_name'] 
                        : $username; // Default to username if no full name
                    
                    $response = [
                        'success' => true,
                        'full_name' => $full_name,
                        'message' => 'User found'
                    ];
                    
                    break; // Exit loop once user is found
                }
            }
        }
    }
}

// Return as JSON
header('Content-Type: application/json');
echo json_encode($response);
?> 