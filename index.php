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

        <form class="login-form" action="admin_page.php" method="POST">
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

