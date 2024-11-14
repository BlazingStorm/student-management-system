<?php
session_start();

// Clear session variables
session_unset(); 

// Destroy the session
session_destroy();

// Clear cookies if they exist (for "Remember me" functionality)
if (isset($_COOKIE["student_username"])) {
    setcookie("student_username", "", time() - 3600); // Expire cookie immediately
}
if (isset($_COOKIE["student_password"])) {
    setcookie("student_password", "", time() - 3600); // Expire cookie immediately
}

// Redirect to login page
header('location:login.php');
exit();
?>
