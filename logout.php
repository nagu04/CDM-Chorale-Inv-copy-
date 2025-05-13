<?php
session_start();
$_SESSION['logout_message'] = 'You have been logged out. Please log in again.';
session_unset();
session_destroy();
header("Location: index.php");
exit();
?> 