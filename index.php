<?php
session_start();
include 'db_connect.php';

$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // First check admin login table
    $stmt = $conn->prepare("SELECT * FROM login WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if admin credentials match
    if ($result->num_rows > 0) {
        // Valid admin login
        $_SESSION['username'] = $username;
        $_SESSION['user_table'] = 'login';
       
        // Redirect to admin page
        header("Location: instruments.php");
        exit();
    } else {
        // Check user_login table if admin login failed
        $stmt = $conn->prepare("SELECT * FROM user_login WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Check if user credentials match
        if ($result->num_rows > 0) {
            // Valid user login
            $_SESSION['username'] = $username;
            $_SESSION['user_table'] = 'user_login';
          
            // Redirect to member page
            header("Location: instruments_nonadmin.php");
            exit();
        } else {
            // Invalid login for both tables
            $error = "Invalid username or password";
        }
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="index_style.css">
  
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="cdmlogo.svg" alt="Logo">
            <h2>Colegio de Muntinlupa Chorale</h2>
        </div>

        <img src="picture-1.png" alt="Seal" class="logo">

        <div class="login-title">LOGIN</div>

        <?php if (!empty($error)): ?>
        <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>

        <form class="login-form" action="" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" class="input-field" placeholder="Enter your username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" class="input-field" placeholder="Enter your password" required>

            <button type="submit" class="login-button">Log In</button>
        </form>
        <div class="register-link" style="text-align: center; padding-bottom: 20px; margin-top: 15px;"
            Don't have an account? <a href="register.php">Sign up here</a>.
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>

