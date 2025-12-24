<?php 
session_start();

// Redirect if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit;
}
?>