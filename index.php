<?php
session_start();
include 'db_connect.php';

$error = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM login WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if credentials match
    if ($result->num_rows > 0) {
        // Valid login
        $_SESSION['username'] = $username;
        // Redirect to admin page
        header("Location: admin_page.php");
        exit();
    } else {
        // Invalid login
        $error = "Invalid username or password";
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
    <link rel="stylesheet" href="style.css">
    
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
    </div>
</body>
</html>


<?php

?>

