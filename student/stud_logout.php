<?php
session_start();
include '../component-library/connect.php';
include './activity_logger.php';

// Check if the student is logged in
if (isset($_SESSION['student_logged_in']) && $_SESSION['student_logged_in'] === true) {
    // Log the logout activity
    logActivity($conn, $_SESSION['user_id'], 'logout', 'User logged out successfully');
    
    // Unset and destroy the student session
    session_unset();
    session_destroy();
    
    // Redirect to the student login page
    header('Location: ./login');
    exit();
} else {
    // If not logged in, redirect to the login page
    header('Location: ./login');
    exit();
}
?>
