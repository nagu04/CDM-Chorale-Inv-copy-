<?php
session_start();
include 'db_connect.php';

// Redirect if already logged in
if (isset($_SESSION['username'])) {
    header("Location: instruments.php");
    exit();
}

$error = '';
$success = '';

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
            // Insert into login table (plain text password for consistency with your current setup)
            $insert_sql = "INSERT INTO login (username, password) VALUES (?, ?)";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("ss", $username, $password);
            if ($insert_stmt->execute()) {
                $success = "Registration successful! You can now log in.";
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
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="cdmlogo.svg" alt="Logo">
            <h2>Colegio de Muntinlupa Chorale</h2>
        </div>
        <img src="cdmlogo.svg" alt="Logo" class="logo">
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
        <div class="register-link" style="text-align:center; margin-top:15px;">
            Already have an account? <a href="index.php">Log in here</a>.
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?> 