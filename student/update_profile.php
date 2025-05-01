<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    try {
        $stmt = $conn->prepare("UPDATE user_info SET first_name = ?, middle_name = ?, last_name = ?, email = ?, address = ? WHERE user_id = ?");
        $stmt->execute([$first_name, $middle_name, $last_name, $email, $address, $user_id]);
        
        // Log the activity
        logActivity($user_id, 'profile_update', 'Updated personal information');
        
        $_SESSION['success'] = "Profile updated successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating profile: " . $e->getMessage();
    }
    
    header("Location: profile.php");
    exit();
}
?> 