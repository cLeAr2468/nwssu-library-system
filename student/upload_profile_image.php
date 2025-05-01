<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "../uploaded_file/";
    $file_extension = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    try {
        if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE user_info SET images = ? WHERE user_id = ?");
            $stmt->execute([$new_filename, $user_id]);
            
            // Log the activity
            logActivity($user_id, 'profile_image_update', 'Updated profile picture');
            
            $_SESSION['success'] = "Profile image updated successfully!";
        } else {
            throw new Exception("Error uploading file");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error updating profile image: " . $e->getMessage();
    }
    
    header("Location: profile.php");
    exit();
}
?> 