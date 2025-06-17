<?php
// index.php - Redirects to the login page

// Start the session
session_start();

// If the user is already logged in, redirect to welcome page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: welcome.php");
    exit;
}

// Redirect to login page if not logged in
header("location: login.php");
exit;
?>
