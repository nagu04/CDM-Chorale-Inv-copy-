<?php
session_start();
include 'db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['username'])) {
    // Check which type of user is logged in
    if (isset($_SESSION['user_table']) && $_SESSION['user_table'] === 'login') {
        header("Location: instruments.php"); // Admin
    } else {
        header("Location: instruments_nonadmin.php"); // Regular user
    }
    exit();
}

$error = '';
$success = '';

// Handle logout if requested
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: register.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    
    // Validate input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name) || empty($email)) {
        $error = "All fields are required";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long";
    } else {
        // Check if username already exists in login table
        $check_sql = "SELECT login_id FROM login WHERE username = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $username);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = "Username already exists";
        } else {
            // Insert into pending_users table
            $insert_sql = "INSERT INTO pending_users (username, password, full_name, email, status, requested_at) VALUES (?, ?, ?, ?, 'pending', NOW())";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ssss", $username, $password, $full_name, $email);
            if ($insert_stmt->execute()) {
                $success = "Registration submitted! Your account is pending approval by an admin.";
            } else {
                $error = "Error submitting registration";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Colegio de Muntinlupa Chorale</title>
    <link rel="stylesheet" href="index_style.css">
    <script>
    window.addEventListener('beforeunload', function() {
        // Make an AJAX call to a logout script
        navigator.sendBeacon('logout.php');
    });
    </script>
    <style>
        .login-container {
  background: rgba(255, 255, 255, 0.85);
  border-radius: 20px;
  padding: 0;
  width: 400px;
  text-align: center;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
  position: relative;
  overflow: hidden;
  
}

    </style>
</head>
<body>
    <div class="login-container">
        <?php if (isset($_SESSION['username'])): ?>
        <div style="text-align: right; margin-bottom: 10px;">
            <a href="register.php?logout=1" style="color: #ffcc00; text-decoration: none;">Logout first to register</a>
        </div>
        <?php endif; ?>
        <div class="login-header">
            <img src="cdmlogo.svg" alt="Logo">
            <h2>Colegio de Muntinlupa Chorale</h2>
        </div>
        <div class="login-title">SIGN UP</div>
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input-field" placeholder="Enter your username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="input-field" placeholder="Enter your password" required>
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Confirm your password" required>
            <label for="full_name">Full Name:</label>
            <input type="text" id="full_name" name="full_name" class="input-field" placeholder="Enter your full name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" class="input-field" placeholder="Enter your email" required>
            <button type="submit" class="login-button">Sign Up</button>
        </form>
        <div class="register-link" style="text-align: center; padding-bottom: 20px; margin-top: 15px;">
            Already have an account? <a href="index.php">Log in here</a>.
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 