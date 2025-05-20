<?php
session_start();
include 'db_connect.php';

// Handle user management actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_request'])) {
        $request_id = $_POST['request_id'];
        $request_sql = "SELECT * FROM pending_users WHERE id = ?";
        $stmt = $conn->prepare($request_sql);
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
        $request = $stmt->get_result()->fetch_assoc();
        
        if ($request) {
            // Insert into users table
            $insert_sql = "INSERT INTO user_login (username, password, full_name, email) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssss", $request['username'], $request['password'], $request['full_name'], $request['email']);
            
            if ($stmt->execute()) {
                // Update pending_users status
                $update_sql = "UPDATE pending_users SET status = 'approved', approved_by = ?, approved_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $_SESSION['username'], $request_id);
                $stmt->execute();
                
                $success = "User request approved successfully!";
            } else {
                $error = "Error approving user request.";
            }
        }
    } elseif (isset($_POST['reject_request'])) {
        $request_id = $_POST['request_id'];
        // Changed from UPDATE to DELETE
        $delete_sql = "DELETE FROM pending_users WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $request_id);
        
        if ($stmt->execute()) {
            $success = "User request rejected and removed successfully!";
        } else {
            $error = "Error rejecting user request.";
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['id_user_login'];
        $delete_sql = "DELETE FROM user_login WHERE id_user_login = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $success = "User deleted successfully!";
        } else {
            $error = "Error deleting user.";
        }
    } elseif (isset($_POST['edit_user'])) {
        $user_id = $_POST['id_user_login'];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];
        
        $update_sql = "UPDATE user_login SET username = ?, password = ?, full_name = ?, email = ? WHERE id_user_login = ? AND username != 'admin'";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssssi", $username, $password, $full_name, $email, $user_id);
        
        if ($stmt->execute()) {
            $success = "User updated successfully!";
        } else {
            $error = "Error updating user.";
        }
    } elseif (isset($_POST['add_user'])) {
        $new_username = trim($_POST['new_username']);
        $new_password = trim($_POST['new_password']);
        $new_full_name = trim($_POST['new_full_name']);
        $new_email = trim($_POST['new_email']);
        $add_sql = "INSERT INTO user_login (username, password, full_name, email) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($add_sql);
        $stmt->bind_param("ssss", $new_username, $new_password, $new_full_name, $new_email);
        if ($stmt->execute()) {
            $success = "User added successfully!";
        } else {
            $error = "Error adding user: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Colegio de Muntinlupa Chorale</title>
    <link rel="stylesheet" href="instruments_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* Sidebar styles */
        .sidebar {
            width: 80px;
            background-color: rgba(44, 36, 116, 0.9);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 30px;
            position: fixed;
            left: 0;
            transition: width 0.3s ease;
            border-right: 4px solid #ffcc00;
        }
        
        .sidebar:hover {
            width: 200px;
        }
        .icon-btn {
            color: white;
            text-decoration: none;
            padding: 15px;
            width: 100%;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            position: relative;
        }
        .icon-btn i {
            font-size: 24px;
            margin-right: 12px;
            min-width: 24px;
            transition: opacity 0.3s ease;
        }
        .icon-btn span {
            white-space: nowrap;
            opacity: 0;
            transition: opacity 0.3s ease;
            position: absolute;
            left: 60px;
        }
        .sidebar:hover .icon-btn span {
            opacity: 1;
        }
        .sidebar:hover .icon-btn i {
            opacity: 0;
        }
        .icon-btn:hover {
            background-color: rgba(255, 255, 255, 0.16);
            transform: translateX(5px);
            width: 170px;
        }
        .table-container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            color: white;
            margin-bottom: 40px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #444;
        }
        th {
            background-color: rgba(44, 36, 116, 0.9);
            color: white;
            font-weight: bold;
        }
        td {
            background-color: rgba(5, 5, 5, 0.7);
        }
        tr:hover td {
            background-color: rgba(44, 36, 116, 0.7);
        }
        .section-title {
            color: white;
            text-align: center;
            margin: 20px 0;
            text-transform: uppercase;
            font-size: 24px;
        }
        .approve-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .approve-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .edit-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            transition: all 0.3s ease;
            
        }
        .edit-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .delete-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .reject-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 5px;
            transition: all 0.3s ease;
        }
        .reject-btn:hover {
            background-color: #c82333;
            transform: translateY(-2px);
        }
        .success-message {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .error-message {
            background: #dc3545;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .edit-form {
            display: none;
            margin-top: 10px;
            padding: 15px;
            background: rgba(44, 36, 116, 0.9);
            border-radius: 5px;
            color: white;
        }
        .edit-form input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #444;
            border-radius: 3px;
            background: rgba(5, 5, 5, 0.7);
            color: white;
        }
        .edit-form input::placeholder {
            color: #999;
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
            <span>Archives</span>
        </a>
        <a href="manage_users.php" class="icon-btn">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>
        
        <?php if (basename($_SERVER['PHP_SELF']) !== 'manage_users.php'): ?>
        <a href="my_profile.php" class="icon-btn">
            <i class="fas fa-user-circle"></i>
            <span>My Profile</span>
        </a>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php $section_title = 'Manage Users'; include 'header.php'; ?>

        <div class="table-container">
            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Pending Requests Section -->
            <h2 class="section-title">Pending User Requests</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Requested</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pending_sql = "SELECT * FROM pending_users WHERE status = 'pending'";
                    $pending_result = $conn->query($pending_sql);
                    
                    if ($pending_result->num_rows > 0) {
                        while($request = $pending_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($request['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($request['full_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($request['email']) . "</td>";
                            echo "<td>" . date('M d, Y H:i', strtotime($request['requested_at'])) . "</td>";
                            echo "<td>";
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="request_id" value="' . $request['id'] . '">';
                            echo '<button type="submit" name="approve_request" class="approve-btn"><i class="fas fa-check"></i></button>';
                            echo '<button type="submit" name="reject_request" class="reject-btn"><i class="fas fa-times"></i></button>';
                            echo '</form>';
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>No pending requests</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <!-- User List Section -->
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="section-title" style="margin-bottom: 0;">All Users</h2>
                <button id="addUserBtn" style="background: #ffcc00; color: #000066; border: none; border-radius: 5px; padding: 8px 16px; font-weight: bold; cursor: pointer; font-size: 16px; margin-bottom: 10px;"><i class="fas fa-user-plus"></i> Add User</button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users_sql = "SELECT * FROM user_login WHERE username != 'admin'";
                    $users_result = $conn->query($users_sql);
                    
                    if ($users_result->num_rows > 0) {
                        while($user = $users_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                            echo "<td>";
                            echo '<button onclick="showEditForm(' . $user['id_user_login'] . ')" class="edit-btn"><i class="fas fa-edit"></i></button>';
                            echo '<form method="POST" style="display: inline;">';
                            echo '<input type="hidden" name="id_user_login" value="' . $user['id_user_login'] . '">';
                            echo '<button type="submit" name="delete_user" class="delete-btn" onclick="return confirm(\'Are you sure you want to delete this user?\')"><i class="fas fa-trash"></i></button>';
                            echo '</form>';
                            echo "</td>";
                            echo "</tr>";
                            
                            // Edit form (hidden by default)
                            echo '<tr id="edit-form-' . $user['id_user_login'] . '" class="edit-form" style="display: none;">';
                            echo '<td colspan="4">';
                            echo '<form method="POST">';
                            echo '<input type="hidden" name="id_user_login" value="' . $user['id_user_login'] . '">';
                            echo '<input type="text" name="username" placeholder="Username" value="' . htmlspecialchars($user['username']) . '" required><br>';
                            echo '<input type="text" name="password" placeholder="Password" value="' . htmlspecialchars($user['password']) . '" required><br>';
                            echo '<input type="text" name="full_name" placeholder="Full Name" value="' . htmlspecialchars($user['full_name']) . '" required><br>';
                            echo '<input type="email" name="email" placeholder="Email" value="' . htmlspecialchars($user['email']) . '" required><br>';
                            echo '<button type="submit" name="edit_user" class="edit-btn"><i class="fas fa-save"></i> Save</button>';
                            echo '<button type="button" onclick="hideEditForm(' . $user['id_user_login'] . ')" class="delete-btn"><i class="fas fa-times"></i> Cancel</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center;'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add User Modal -->
    <div id="addUserModal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.4); align-items:center; justify-content:center; z-index:1000;">
        <div style="background:#fff; color:#222; padding:32px 24px; border-radius:10px; min-width:320px; max-width:90vw; position:relative;">
            <button id="closeAddUserModal" style="position:absolute; top:10px; right:10px; background:none; border:none; font-size:20px; color:#888; cursor:pointer;"><i class="fas fa-times"></i></button>
            <h3 style="color:#000066; margin-bottom:18px;">Add New User</h3>
            <form method="POST" id="addUserForm">
                <input type="text" name="new_username" placeholder="Username" required style="width:100%; padding:8px; margin-bottom:10px; border-radius:4px; border:1px solid #ccc;">
                <input type="text" name="new_password" placeholder="Password" required style="width:100%; padding:8px; margin-bottom:10px; border-radius:4px; border:1px solid #ccc;">
                <input type="text" name="new_full_name" placeholder="Full Name" required style="width:100%; padding:8px; margin-bottom:10px; border-radius:4px; border:1px solid #ccc;">
                <input type="email" name="new_email" placeholder="Email" required style="width:100%; padding:8px; margin-bottom:18px; border-radius:4px; border:1px solid #ccc;">
                <button type="submit" name="add_user" style="background:#28a745; color:white; border:none; border-radius:5px; padding:10px 20px; font-weight:bold; font-size:16px; cursor:pointer;">Add User</button>
            </form>
        </div>
    </div>

    <script>
    function showEditForm(userId) {
        document.getElementById('edit-form-' + userId).style.display = 'table-row';
    }
    
    function hideEditForm(userId) {
        document.getElementById('edit-form-' + userId).style.display = 'none';
    }
    document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.header');
    
    sidebar.addEventListener('mouseenter', function() {
        sidebar.style.width = '200px';
       
        header.style.marginLeft = '110px'; // Push header to match sidebar expansion
    });
    
    sidebar.addEventListener('mouseleave', function() {
        sidebar.style.width = '80px';
       
        header.style.marginLeft = '-10px';
    });
});
    // Add User Modal logic
    document.getElementById('addUserBtn').onclick = function() {
        document.getElementById('addUserModal').style.display = 'flex';
    };
    document.getElementById('closeAddUserModal').onclick = function() {
        document.getElementById('addUserModal').style.display = 'none';
    };
    window.onclick = function(event) {
        var modal = document.getElementById('addUserModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    };
    </script>
</body>
</html>
<?php
$conn->close();
?> 