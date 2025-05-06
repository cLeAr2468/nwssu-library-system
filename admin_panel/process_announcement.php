<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

include '../component-library/connect.php';

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $response = ['success' => false, 'message' => 'Invalid action'];
    
    // Handle different actions
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';
        
        switch ($action) {
            case 'add':
            case 'update':
                $title = trim($_POST['title'] ?? '');
                $content = trim($_POST['content'] ?? '');
                $id = $_POST['id'] ?? null;
                
                if (empty($title) || empty($content)) {
                    throw new Exception('Title and content are required');
                }
                
                // Handle image upload
                $image_path = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                    $max_size = 5 * 1024 * 1024; // 5MB
                    
                    if (!in_array($_FILES['image']['type'], $allowed_types)) {
                        throw new Exception('Invalid image type. Only JPG, PNG and GIF are allowed.');
                    }
                    
                    if ($_FILES['image']['size'] > $max_size) {
                        throw new Exception('Image size should not exceed 5MB.');
                    }
                    
                    $upload_dir = '../uploads/announcements/';
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid() . '.' . $file_extension;
                    $target_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                        $image_path = 'uploads/announcements/' . $file_name;
                    }
                }
                
                if ($action === 'add') {
                    $stmt = $conn->prepare("INSERT INTO announcement (title, message, image, date) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$title, $content, $image_path]);
                    $response = ['success' => true, 'message' => 'Announcement added successfully'];
                } else {
                    // For update, first get the old image path
                    $stmt = $conn->prepare("SELECT image FROM announcement WHERE id = ?");
                    $stmt->execute([$id]);
                    $old_image = $stmt->fetchColumn();
                    
                    // If new image is uploaded, delete the old one
                    if ($image_path && $old_image) {
                        $old_image_path = '../' . $old_image;
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                    
                    $stmt = $conn->prepare("UPDATE announcement SET title = ?, message = ?, image = COALESCE(?, image), date = NOW() WHERE id = ?");
                    $stmt->execute([$title, $content, $image_path, $id]);
                    $response = ['success' => true, 'message' => 'Announcement updated successfully'];
                }
                break;
                
            case 'delete':
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    throw new Exception('Invalid announcement ID');
                }
                
                // Get the image path before deleting
                $stmt = $conn->prepare("SELECT image FROM announcement WHERE id = ?");
                $stmt->execute([$id]);
                $image_path = $stmt->fetchColumn();
                
                // Delete the announcement
                $stmt = $conn->prepare("DELETE FROM announcement WHERE id = ?");
                $stmt->execute([$id]);
                
                // Delete the associated image if it exists
                if ($image_path) {
                    $image_path = '../' . $image_path;
                    if (file_exists($image_path)) {
                        unlink($image_path);
                    }
                }
                
                $response = ['success' => true, 'message' => 'Announcement deleted successfully'];
                break;
        }
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

header('Content-Type: application/json');
echo json_encode($response); 