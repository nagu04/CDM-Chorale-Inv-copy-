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

// Get member information if user exists in members table
$member_info = null;
if (isset($user['full_name'])) {
    $sql = "SELECT * FROM members WHERE CONCAT(last_name, ', ', given_name, 
        CASE WHEN middle_initial != '' THEN CONCAT(' ', middle_initial, '.') ELSE '' END,
        CASE WHEN extension != '' THEN CONCAT(' ', extension) ELSE '' END) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $user['full_name']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $member_info = $result->fetch_assoc();
    }
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['form_type'])) {
        if ($_POST['form_type'] === 'account') {
            // Handle account information update
    $new_username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);
    
            if ($user_table == "login" || $user_table == "user_login") {
                $sql = "UPDATE $user_table SET username=?, password=?, email=? WHERE username=?";
            $stmt = $conn->prepare($sql);
                $stmt->bind_param('ssss', $new_username, $password, $email, $username);
                
                if ($stmt->execute()) {
        $_SESSION['username'] = $new_username;
        $username = $new_username;
                    $success = 'Account information updated successfully!';
        
        // Refresh user info after update
        $sql = "SELECT * FROM $user_table WHERE username=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $new_username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
                    $error = 'Error updating account information. ' . $stmt->error;
                }
            }
        } else if ($_POST['form_type'] === 'member') {
            // Handle member information update
            $last_name = trim($_POST['last_name']);
            $given_name = trim($_POST['given_name']);
            $middle_initial = trim($_POST['middle_initial']);
            $extension = trim($_POST['extension']);
            $program = trim($_POST['program']);
            $position = trim($_POST['position']);
            $birthdate = trim($_POST['birthdate']);
            $address = trim($_POST['address']);
            
            // Combine names for display
            $full_name = $last_name . ', ' . $given_name;
            if (!empty($middle_initial)) {
                $full_name .= ' ' . $middle_initial . '.';
            }
            if (!empty($extension)) {
                $full_name .= ' ' . $extension;
            }
            
            // Update login/user_login table with new full name
            if ($user_table == "login" || $user_table == "user_login") {
                $sql = "UPDATE $user_table SET full_name=? WHERE username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $full_name, $username);
                $stmt->execute();
            }
            
            // Handle profile image upload
            $image_path = isset($member_info['image_path']) ? $member_info['image_path'] : '';
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                if (!file_exists('member_profiles')) {
                    mkdir('member_profiles', 0777, true);
                }
                $file_ext = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
                $filename = 'member_' . (isset($member_info['member_id']) ? $member_info['member_id'] : time()) . '_' . time() . '.' . $file_ext;
                $target_file = "member_profiles/" . $filename;
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                    if (!empty($image_path) && file_exists($image_path) && 
                        $image_path != 'default image.jpg' && $image_path != 'picture-1.png') {
                        unlink($image_path);
                    }
                    $image_path = $target_file;
                }
            }
            // Check for existing member by name
            $check_stmt = $conn->prepare("SELECT member_id, image_path FROM members WHERE last_name = ? AND given_name = ? AND middle_initial = ? AND extension = ?");
            $check_stmt->bind_param("ssss", $last_name, $given_name, $middle_initial, $extension);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            if ($check_result && $check_result->num_rows > 0) {
                // Member exists, update instead of insert
                $row = $check_result->fetch_assoc();
                $member_id = $row['member_id'];
                $current_image_path = $row['image_path'];
                // If no new image uploaded, keep current image
                if (empty($image_path)) {
                    $image_path = $current_image_path;
                } else {
                    // If new image uploaded, delete old image if not default
                    if (!empty($current_image_path) && file_exists($current_image_path) && $current_image_path != 'default image.jpg' && $current_image_path != 'picture-1.png') {
                        unlink($current_image_path);
                    }
                }
                $update_stmt = $conn->prepare("UPDATE members SET last_name=?, given_name=?, middle_initial=?, extension=?, program=?, position=?, birthdate=?, address=?, image_path=? WHERE member_id=?");
                $update_stmt->bind_param("sssssssssi", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $image_path, $member_id);
                if ($update_stmt->execute()) {
                    $success = 'Member already exists. Information updated!';
                    // Refresh member info
                    $sql = "SELECT * FROM members WHERE member_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $member_id);
                    $stmt->execute();
                    $member_info = $stmt->get_result()->fetch_assoc();
                } else {
                    $error = 'Error updating member profile. ' . $conn->error;
                }
                $update_stmt->close();
            } else {
                // Insert new member
                $sql = "INSERT INTO members (last_name, given_name, middle_initial, extension, program, position, birthdate, address, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssss", $last_name, $given_name, $middle_initial, $extension, $program, $position, $birthdate, $address, $image_path);
                if ($stmt->execute()) {
                    $success = 'Member profile updated successfully!';
                    // Refresh member info
                    $member_info = array('member_id' => $stmt->insert_id);
                    $sql = "SELECT * FROM members WHERE member_id=?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param('i', $member_info['member_id']);
                    $stmt->execute();
                    $member_info = $stmt->get_result()->fetch_assoc();
                } else {
                    $error = 'Error updating member profile. ' . $stmt->error;
                }
            }
            $check_stmt->close();
        }
    }
}

// Provide fallback values if keys are missing or $user is false
$user_full_name = isset($user['full_name']) ? $user['full_name'] : '';
$user_email = isset($user['email']) ? $user['email'] : '';
$user_username = isset($user['username']) ? $user['username'] : '';
$user_password = isset($user['password']) ? $user['password'] : '';

// Member profile values
$member_program = isset($member_info['program']) ? $member_info['program'] : '';
$member_position = isset($member_info['position']) ? $member_info['position'] : '';
$member_birthdate = isset($member_info['birthdate']) ? $member_info['birthdate'] : '';
$member_address = isset($member_info['address']) ? $member_info['address'] : '';
$member_image = isset($member_info['image_path']) && !empty($member_info['image_path']) ? $member_info['image_path'] : 'picture-1.png';
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

        /* end of sidebar */
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
        .profile-link {
            background-color: #ffcc00 !important;
            color: #000066 !important;
        }
        .profile-link:hover {
            background-color: #e6b800 !important;
        }
        .profile-container {
            background-color: rgba(44, 36, 116, 0.9);
            width: 800px;
            margin: 40px auto 20px auto;
            padding: 16px 24px 24px 24px;
            border-radius: 10px;
            color: white;
            box-shadow: 0 4px 24px rgba(0,0,0,0.12);
        }
        .profile-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .profile-tab {
            flex: 1;
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .profile-tab.active {
            background-color: #ffcc00;
            color: #000066;
        }
        .profile-tab:hover {
            background-color: rgba(255, 204, 0, 0.3);
        }
        .profile-form {
            display: none;
        }
        .profile-form.active {
            display: block;
        }
        .profile-image-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .profile-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #ffcc00;
        }
        .profile-section {
            background-color: rgba(44, 36, 116, 0.9);
            padding: 20px;
            border-radius: 10px;
        }
        .section-title {
            color: #ffcc00;
            margin-bottom: 20px;
            font-size: 1.2em;
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
        .profile-form select {
            width: 100%;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23000066' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 16px;
        }
        .profile-form select:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(255, 204, 0, 0.3);
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
        .save-btn {
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
        .save-btn:hover {
            background-color: #e6b800;
            transform: translateY(-2px);
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
            <span>Archives</span>
        </a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <?php $section_title = 'My Profile'; include 'header_nonadmin.php'; ?>

        <div class="profile-container">
            <?php if (isset($success)): ?>
            <div class="alert alert-success" style="background-color: rgba(40, 167, 69, 0.2); color: #28a745; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <p><i class="fas fa-check-circle"></i> <?php echo $success; ?></p>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger" style="background-color: rgba(220, 53, 69, 0.2); color: #dc3545; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                <p><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></p>
            </div>
            <?php endif; ?>
            
            <div class="profile-header">
                <i class="fas fa-user-circle"></i>
                <h2>My Profile</h2>
            </div>

            <div class="profile-tabs">
                <button type="button" class="profile-tab active" data-tab="account">Account Information</button>
                <button type="button" class="profile-tab" data-tab="member">Profile Information</button>
            </div>

            <!-- Account Information Form -->
            <form method="POST" class="profile-form active" id="accountForm">
                <input type="hidden" name="form_type" value="account">
                <div class="profile-section">
                    <h3 class="section-title">Account Information</h3>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_username); ?>" required>
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
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
                    </div>
                    <button type="submit" name="update_account" class="save-btn">Update Account</button>
                </div>
            </form>

            <!-- Member Information Form -->
            <form method="POST" class="profile-form" id="memberForm" enctype="multipart/form-data">
                <input type="hidden" name="form_type" value="member">
                <div class="profile-section">
                    <div class="profile-image-container">
                        <img src="<?php echo htmlspecialchars($member_image); ?>" alt="Profile Image" class="profile-image">
                        <div class="form-group" style="margin-top: 10px;">
                            <label for="profile_image">Change Profile Image:</label>
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                        </div>
                    </div>

                    <h3 class="section-title">Profile Information</h3>
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars(isset($member_info['last_name']) ? $member_info['last_name'] : ''); ?>" required oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label for="given_name">Given Name</label>
                        <input type="text" id="given_name" name="given_name" value="<?php echo htmlspecialchars(isset($member_info['given_name']) ? $member_info['given_name'] : ''); ?>" required oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label for="middle_initial">Middle Initial</label>
                        <input type="text" id="middle_initial" name="middle_initial" maxlength="1" value="<?php echo htmlspecialchars(isset($member_info['middle_initial']) ? $member_info['middle_initial'] : ''); ?>" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label for="extension">Extension (Jr., Sr., III, etc.)</label>
                        <input type="text" id="extension" name="extension" value="<?php echo htmlspecialchars(isset($member_info['extension']) ? $member_info['extension'] : ''); ?>" oninput="this.value = this.value.toUpperCase()">
                    </div>
                    <div class="form-group">
                        <label for="program">Program</label>
                        <select id="program" name="program" required>
                            <option value="">Select Program</option>
                            <option value="Architecture" <?php echo ($member_program === 'Architecture') ? 'selected' : ''; ?>>Architecture</option>
                            <option value="Civil Engineering" <?php echo ($member_program === 'Civil Engineering') ? 'selected' : ''; ?>>Civil Engineering</option>
                            <option value="Computer Engineering" <?php echo ($member_program === 'Computer Engineering') ? 'selected' : ''; ?>>Computer Engineering</option>
                            <option value="Electrical Engineering" <?php echo ($member_program === 'Electrical Engineering') ? 'selected' : ''; ?>>Electrical Engineering</option>
                            <option value="Electronics Engineering" <?php echo ($member_program === 'Electronics Engineering') ? 'selected' : ''; ?>>Electronics Engineering</option>
                            <option value="Environmental and Sanitary Engineering" <?php echo ($member_program === 'Environmental and Sanitary Engineering') ? 'selected' : ''; ?>>Environmental and Sanitary Engineering</option>
                            <option value="Industrial Engineering" <?php echo ($member_program === 'Industrial Engineering') ? 'selected' : ''; ?>>Industrial Engineering</option>
                            <option value="Mechanical Engineering" <?php echo ($member_program === 'Mechanical Engineering') ? 'selected' : ''; ?>>Mechanical Engineering</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="position">Position</label>
                        <input type="text" id="position" name="position" value="<?php echo htmlspecialchars($member_position); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="birthdate">Birthdate</label>
                        <input type="date" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($member_birthdate); ?>" required>
                </div>
                <div class="form-group">
                        <label for="address">Address</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($member_address); ?>" required>
                    </div>
                    <button type="submit" name="update_member" class="save-btn">Update Member Profile</button>
                </div>
            </form>
        </div>
    </div>

<script>
        // Password toggle functionality
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = document.getElementById('eyeIcon');
        
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        eyeIcon.classList.toggle('fa-eye');
        eyeIcon.classList.toggle('fa-eye-slash');
    });

        // Tab switching functionality
        const tabs = document.querySelectorAll('.profile-tab');
        const forms = document.querySelectorAll('.profile-form');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Remove active class from all tabs and forms
                tabs.forEach(t => t.classList.remove('active'));
                forms.forEach(f => f.classList.remove('active'));

                // Add active class to clicked tab and corresponding form
                tab.classList.add('active');
                const formId = tab.getAttribute('data-tab') + 'Form';
                document.getElementById(formId).classList.add('active');
            });
        });

        // Preview profile image before upload
        document.getElementById('profile_image').addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-image').src = e.target.result;
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });

        // Sidebar functionality
    document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    const header = document.querySelector('.header');
    
    sidebar.addEventListener('mouseenter', function() {
        sidebar.style.width = '200px';
                header.style.marginLeft = '110px';
    });
    
    sidebar.addEventListener('mouseleave', function() {
        sidebar.style.width = '80px';
        header.style.marginLeft = '-10px';
    });
});
</script>
</body>
</html>
<?php
$conn->close();
?> 