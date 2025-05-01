<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'approved' && $user['account_status'] === 'active') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['patron_type'] = $user['patron_type'];
                
                // Log the activity
                logActivity($user_id, 'login', 'User logged in to the system');
                
                header("Location: dashboard.php");
                exit();
            } else {
                throw new Exception("Account not approved or inactive");
            }
        } else {
            throw new Exception("Invalid credentials");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: index.php");
        exit();
    }
}
?> 