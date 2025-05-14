<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];

// Ensure required columns exist in tables
function ensure_column_exists($conn, $table, $column) {
    $check = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    if ($check->num_rows == 0) {
        return $conn->query("ALTER TABLE $table ADD $column VARCHAR(100) DEFAULT ''");
    }
    return true;
}

// Make sure full_name and email columns exist in all user tables
ensure_column_exists($conn, 'login', 'full_name');
ensure_column_exists($conn, 'login', 'email');
ensure_column_exists($conn, 'user_login', 'full_name');
ensure_column_exists($conn, 'user_login', 'email');

// Check which table the user exists in - could be login, user_login, or users
$user_table = "";
$user = null;

// First, check if we already know which table from the session
if (isset($_SESSION['user_table'])) {
    $user_table = $_SESSION['user_table'];
    
    $sql = "SELECT * FROM $user_table WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    }
} else {
    // If user_table is not set in session, check each table in order
    // First check login table
    $sql = "SELECT * FROM login WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_table = "login";
        $_SESSION['user_table'] = $user_table;
    } else {
        // Check user_login table
        $sql = "SELECT * FROM user_login WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_table = "user_login";
            $_SESSION['user_table'] = $user_table;
        } else {
            // Check users table
            $sql = "SELECT * FROM users WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $user_table = "users";
                $_SESSION['user_table'] = $user_table;
            }
        }
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    
    // Check which table to update
    if ($user_table == "login") {
        // Make sure we have the right columns
        $check = $conn->query("SHOW COLUMNS FROM login LIKE 'full_name'");
        if ($check->num_rows > 0 && $conn->query("SHOW COLUMNS FROM login LIKE 'email'")->num_rows > 0) {
            $sql = "UPDATE login SET username=?, password=?, full_name=?, email=? WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $new_username, $password, $full_name, $email, $username);
        } else {
            // Fall back to just username/password if columns don't exist
            $sql = "UPDATE login SET username=?, password=? WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $new_username, $password, $username);
        }
    } else if ($user_table == "user_login") {
        // Make sure we have the right columns
        $check = $conn->query("SHOW COLUMNS FROM user_login LIKE 'full_name'");
        if ($check->num_rows > 0 && $conn->query("SHOW COLUMNS FROM user_login LIKE 'email'")->num_rows > 0) {
            $sql = "UPDATE user_login SET username=?, password=?, full_name=?, email=? WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sssss', $new_username, $password, $full_name, $email, $username);
        } else {
            // Fall back to just username/password if columns don't exist
            $sql = "UPDATE user_login SET username=?, password=? WHERE username=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $new_username, $password, $username);
        }
    } else if ($user_table == "users") {
        $sql = "UPDATE users SET username=?, password=?, full_name=?, email=? WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $new_username, $password, $full_name, $email, $username);
    } else {
        $error = 'User not found in any table.';
    }
    
    if (isset($stmt) && $stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $username = $new_username;
        $success = 'Profile updated successfully!';
        
        // Refresh user info after update
        $sql = "SELECT * FROM $user_table WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $new_username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error = 'Error updating profile. ' . (isset($stmt) ? $stmt->error : '');
    }
}

// Provide fallback values if keys are missing or $user is false
$user_full_name = isset($user['full_name']) ? $user['full_name'] : '';
$user_email = isset($user['email']) ? $user['email'] : '';
$user_username = isset($user['username']) ? $user['username'] : '';
$user_password = isset($user['password']) ? $user['password'] : '';
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
            width: 600px;
            margin: 40px auto 20px auto;
            padding: 16px 24px 24px 24px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }
        .profile-header i {
            font-size: 32px;
            margin-right: 15px;
            color: #ffcc00;
        }
        .profile-form .form-group {
            margin-bottom: 20px;
        }
        .profile-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #ffcc00;
        }
        .profile-form input {
            width: 100%;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        .password-input-container {
            position: relative;
            width: 100%;
        }
        .password-input-container input {
            width: calc(100% - 40px);
            padding-right: 30px;
        }
        .password-toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #ffcc00;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .alert-success {
            background-color: rgba(40, 167, 69, 0.7);
            color: white;
        }
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.7);
            color: white;
        }
        .btn-update {
            background-color: #ffcc00;
            color: #000066;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn-update:hover {
            background-color: #e6b800;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <a href="instruments_nonadmin.php" class="icon-btn">
            <i class="fas fa-guitar"></i>
            <span>Instruments</span>
        </a>
        <a href="accessory_nonadmin.php" class="icon-btn">
            <i class="fas fa-gem"></i>
            <span>Accessories</span>
        </a>
        <a href="clothing_nonadmin.php" class="icon-btn">
            <i class="fas fa-tshirt"></i>
            <span>Clothing</span>
        </a>
        <a href="members_nonadmin.php" class="icon-btn">
            <i class="fas fa-user"></i>
            <span>Members</span>
        </a>
        <a href="report_nonadmin.php" class="icon-btn">
            <i class="fas fa-file-alt"></i>
            <span>Report</span>
        </a>
        <a href="history_nonadmin.php" class="icon-btn">
            <i class="fas fa-clock"></i>
            <span>History</span>
        </a>
        <a href="deleted_items_nonadmin.php" class="icon-btn">
            <i class="fas fa-trash-alt"></i>
            <span>Deleted</span>
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

        <!-- Profile Container -->
        <div class="profile-container">
            <div class="profile-header">
                <i class="fas fa-user-circle"></i>
                <h2>My Profile</h2>
            </div>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form class="profile-form" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_username); ?>" readonly style="background-color: #222; color: #aaa; cursor: not-allowed;">
                </div>
                
                <div class="form-group password-group">
                    <label for="password">Password</label>
                    <div class="password-input-container">
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($user_password); ?>" required>
                        <button type="button" id="togglePassword" class="password-toggle-btn">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user_full_name); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>">
                </div>
                
                <button type="submit" class="btn-update">Save Changes</button>
            </form>
        </div>
    </div>
    <script>
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });
</script>
</body>
</html> 