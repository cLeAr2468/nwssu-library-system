<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if (isset($_SESSION['user_id'])) {
    // Log the activity before destroying the session
    logActivity($_SESSION['user_id'], 'logout', 'User logged out from the system');
}

session_destroy();
header("Location: index.php");
exit();
?> 