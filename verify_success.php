<?php
session_start();

// Check if temp_uid exists
if (!isset($_SESSION['temp_uid'])) {
    header("Location: user_login.php");
    exit;
}

// Move temp_uid to permanent uid and set login time
$_SESSION['uid'] = $_SESSION['temp_uid'];
$_SESSION['login'] = time();
unset($_SESSION['temp_uid']); // Remove temporary ID

// Redirect to dashboard
header("Location: user_dashboard.php");
exit; 