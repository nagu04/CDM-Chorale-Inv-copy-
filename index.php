<?php
session_start();
include 'db_connect.php';

// Show logout message if set
$logout_message = '';
if (isset($_SESSION['logout_message'])) {
    $logout_message = $_SESSION['logout_message'];
    unset($_SESSION['logout_message']);
}

// Redirect if already logged in
if (isset($_SESSION['username'])) {
    header("Location: instruments.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        // Check if user exists in login table
        $sql = "SELECT * FROM login WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if ($password === $user['password']) {
                // Set session variables
                $_SESSION['username'] = $user['username'];
                // Redirect to main page
                header("Location: instruments.php");
                exit();
            } else {
                $error = "Invalid password";
            }
        } else {
            $error = "Invalid username";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Colegio de Muntinlupa Chorale</title>
    <link rel="stylesheet" href="index_style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="cdmlogo.svg" alt="Logo">
            <h2>Colegio de Muntinlupa Chorale</h2>
        </div>
        <img src="cdmlogo.svg" alt="Logo" class="logo">
        <div class="login-title">LOGIN</div>
        <?php if ($logout_message): ?>
            <div class="success-message">
                <?php echo $logout_message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        <form class="login-form" method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input-field" placeholder="Enter your username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="input-field" placeholder="Enter your password" required>
            <button type="submit" class="login-button">Log In</button>
        </form>
        <div class="register-link" style="text-align:center; margin-top:15px;">
            Don't have an account? <a href="register.php">Sign up here</a>.
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>

