<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $sql = "UPDATE login SET username=?, password=?, full_name=?, email=? WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $new_username, $password, $full_name, $email, $username);
    if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $username = $new_username;
        $success = 'Profile updated successfully!';
    } else {
        $error = 'Error updating profile.';
    }
}

// Fetch user info
$sql = "SELECT * FROM login WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Colegio de Muntinlupa Chorale</title>
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
        .main-content {
            margin-left: 80px;
            padding: 20px;
            background-color: rgba(5, 5, 5, 0.637);
            min-height: 100vh;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            background-color: rgba(44, 36, 116, 0.9);
            color: white;
            margin: -20px -20px 20px -20px;
            border-bottom: 4px solid #ffcc00;
        }
        .header-logo {
            width: 50px;
            height: auto;
            margin-right: 20px;
        }
        .section-indicator {
            background-color: #ffcc00;
            color: #000066;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            display: inline-block;
            margin-right: 20px;
        }
        .logout {
            background-color: #000066;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            margin-left: 20px;
        }
        .logout:hover {
            background-color: #000044;
        }
        .profile-container {
            background-color: rgba(44, 36, 116, 0.9);
            padding: 20px;
            border-radius: 8px;
            color: white;
            margin-bottom: 20px;
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-right: 20px;
            border: 4px solid #ffcc00;
        }
        .profile-info h2 {
            margin: 0;
            color: #ffcc00;
        }
        .profile-info p {
            margin: 5px 0;
            color: white;
        }
        .edit-form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #ffcc00;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #444;
            border-radius: 4px;
            background: rgba(5, 5, 5, 0.7);
            color: white;
        }
        .form-group input:focus {
            outline: none;
            border-color: #ffcc00;
        }
        .save-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .save-btn:hover {
            background-color: #218838;
            transform: translateY(-2px);
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
        <?php if (isset($_SESSION['username']) && $_SESSION['username'] === 'admin'): ?>
        <a href="manage_users.php" class="icon-btn">
            <i class="fas fa-users-cog"></i>
            <span>Manage Users</span>
        </a>
        <?php endif; ?>
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
            <div class="section-indicator">My Profile</div>
            <h2>CDM Chorale Inventory System</h2>
            <a href="index.php" class="logout">Log Out</a>
        </div>

        <div class="profile-container">
            <div class="profile-header">
                <img src="default-avatar.png" alt="Profile Picture" class="profile-avatar">
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['full_name']); ?></h2>
                    <p><i class="fas fa-user"></i> <?php echo htmlspecialchars($user['username']); ?></p>
                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>

            <form method="POST" class="edit-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <button type="submit" name="update_profile" class="save-btn">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 