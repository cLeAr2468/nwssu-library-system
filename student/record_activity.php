<?php
session_start();
include '../component-library/connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'error' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['activity_detail'])) {
    try {
        // Get current date and time
        $current_date = date('Y-m-d H:i:s');
        
        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO activities (user_id, activity_type, activity_details, activity_date, status) 
                               VALUES (?, 'View Books', ?, ?, 'active')");
        
        // Execute the statement with the parameters
        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['activity_detail'],
            $current_date
        ]);
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
