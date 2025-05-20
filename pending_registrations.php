<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['username']) || !$_SESSION['is_admin']) {
    header("Location: index.php");
    exit();
}

// Handle registration actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['approve'])) {
        $pending_id = $_POST['pending_id'];
        
        // Get pending user data
        $get_pending_sql = "SELECT * FROM pending_users WHERE id = ?";
        $get_pending_stmt = $conn->prepare($get_pending_sql);
        $get_pending_stmt->bind_param("i", $pending_id);
        $get_pending_stmt->execute();
        $pending_user = $get_pending_stmt->get_result()->fetch_assoc();
        
        if ($pending_user) {
            // Check if username already exists
            $check_username_sql = "SELECT id FROM users WHERE username = ?";
            $check_username_stmt = $conn->prepare($check_username_sql);
            $check_username_stmt->bind_param("s", $pending_user['username']);
            $check_username_stmt->execute();
            
            if ($check_username_stmt->get_result()->num_rows > 0) {
                $_SESSION['error_message'] = "Username already exists";
            } else {
                // Insert into users table
                $insert_sql = "INSERT INTO users (username, password, full_name, email, is_admin, created_at) VALUES (?, ?, ?, ?, 0, NOW())";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("ssss", 
                    $pending_user['username'],
                    $pending_user['password'],
                    $pending_user['full_name'],
                    $pending_user['email']
                );
                
                if ($insert_stmt->execute()) {
                    // Delete from pending_users
                    $delete_sql = "DELETE FROM pending_users WHERE id = ?";
                    $delete_stmt = $conn->prepare($delete_sql);
                    $delete_stmt->bind_param("i", $pending_id);
                    $delete_stmt->execute();
                    
                    $_SESSION['success_message'] = "User registration approved";
                } else {
                    $_SESSION['error_message'] = "Error approving registration";
                }
            }
        }
    } elseif (isset($_POST['reject'])) {
        $pending_id = $_POST['pending_id'];
        
        $delete_sql = "DELETE FROM pending_users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $pending_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success_message'] = "User registration rejected";
        } else {
            $_SESSION['error_message'] = "Error rejecting registration";
        }
    }
}

// Get all pending registrations
$sql = "SELECT * FROM pending_users ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Registrations</title>
    <link rel="stylesheet" href="instruments_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .pending-container {
            padding: 20px;
        }
        .pending-card {
            background-color: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .pending-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .pending-name {
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .pending-date {
            color: #aaa;
            font-size: 14px;
        }
        .pending-details {
            color: white;
            margin-bottom: 15px;
        }
        .pending-actions {
            display: flex;
            gap: 10px;
        }
        .approve-btn, .reject-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .approve-btn {
            background-color: #4CAF50;
            color: white;
        }
        .reject-btn {
            background-color: #f44336;
            color: white;
        }
        .approve-btn:hover {
            background-color: #45a049;
        }
        .reject-btn:hover {
            background-color: #d32f2f;
        }
        
        .profile-link {
            background-color: #ffcc00 !important;
            color: #000066 !important;
        }
        
        .profile-link:hover {
            background-color: #e6b800 !important;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
            <span>Instruments</span>
        </a>
        <a href="accessory.php" class="icon-btn">
            <i class="fas fa-gem"></i>
            <span>Accessories</span>
        </a>
        <a href="clothing.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
            <span>Clothing</span>
        </a>
        <a href="members.php" class="icon-btn">
            <i class="fas fa-user"></i>
            <span>Members</span>
        </a>
        <a href="report.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
            <span>Report</span>
        </a>
        <a href="history.php" class="icon-btn">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a href="deleted_items.php" class="icon-btn">
            <i class="fas fa-trash-alt"></i>
            <span>Deleted</span>
        </a>
        <a href="pending_registrations.php" class="icon-btn">
            <i class="fas fa-user-clock"></i>
            <span>Pending Users</span>
        </a>
        <a href="manage_users.php" class="icon-btn">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>
        <a href="my_profile.php" class="icon-btn">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <img src="picture-1.png" alt="Logo" class="header-logo">
            <div class="section-indicator">Pending Registrations</div>
            <h2>CDM Chorale Inventory System</h2>
            <div style="display: flex; align-items: center; gap: 10px;">
                <a href="my_profile.php" class="logout profile-link">
                    <i class="fas fa-user-circle"></i> My Profile
                </a>
                <a href="index.php" class="logout">Log Out</a>
            </div>
        </div>

        <!-- Pending Registrations List -->
        <div class="pending-container">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert success-alert" style="background-color: rgba(76, 175, 80, 0.8); color: white; padding: 12px; margin-bottom: 15px; border-radius: 5px; text-align: center;">';
                echo '<i class="fas fa-check-circle"></i> ' . $_SESSION['success_message'];
                echo '</div>';
                unset($_SESSION['success_message']);
            }
            
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert error-alert" style="background-color: rgba(244, 67, 54, 0.8); color: white; padding: 12px; margin-bottom: 15px; border-radius: 5px; text-align: center;">';
                echo '<i class="fas fa-exclamation-triangle"></i> ' . $_SESSION['error_message'];
                echo '</div>';
                unset($_SESSION['error_message']);
            }
            
            if ($result->num_rows > 0) {
                while($pending = $result->fetch_assoc()) {
                    echo '<div class="pending-card">';
                    echo '<div class="pending-header">';
                    echo '<div class="pending-name">' . htmlspecialchars($pending['full_name']) . '</div>';
                    echo '<div class="pending-date">' . date('F j, Y', strtotime($pending['created_at'])) . '</div>';
                    echo '</div>';
                    
                    echo '<div class="pending-details">';
                    echo '<div>Username: ' . htmlspecialchars($pending['username']) . '</div>';
                    echo '<div>Email: ' . htmlspecialchars($pending['email']) . '</div>';
                    echo '</div>';
                    
                    echo '<div class="pending-actions">';
                    echo '<form method="POST" style="display: inline;">';
                    echo '<input type="hidden" name="pending_id" value="' . $pending['id'] . '">';
                    echo '<button type="submit" name="approve" class="approve-btn" onclick="return confirm(\'Are you sure you want to approve this registration?\');">';
                    echo '<i class="fas fa-check"></i> Approve';
                    echo '</button>';
                    echo '</form>';
                    
                    echo '<form method="POST" style="display: inline;">';
                    echo '<input type="hidden" name="pending_id" value="' . $pending['id'] . '">';
                    echo '<button type="submit" name="reject" class="reject-btn" onclick="return confirm(\'Are you sure you want to reject this registration?\');">';
                    echo '<i class="fas fa-times"></i> Reject';
                    echo '</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<div class="alert" style="background-color: rgba(255, 255, 255, 0.1); color: white; padding: 20px; border-radius: 5px; text-align: center;">';
                echo 'No pending registrations';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 