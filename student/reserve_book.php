<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $copies = $_POST['copies'];
    $reserved_date = date('Y-m-d H:i:s');

    try {
        // Check if book is available
        $check_stmt = $conn->prepare("SELECT copies FROM books WHERE id = ? AND status = 'available'");
        $check_stmt->execute([$book_id]);
        $book = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($book && $book['copies'] >= $copies) {
            // Insert into reserve_books
            $stmt = $conn->prepare("INSERT INTO reserve_books (user_id, book_id, reserved_date, status, copies) VALUES (?, ?, ?, 'reserved', ?)");
            $stmt->execute([$user_id, $book_id, $reserved_date, $copies]);

            // Log the activity
            logActivity($user_id, 'book_reserve', "Reserved book ID: $book_id, Copies: $copies");
            
            $_SESSION['success'] = "Book reserved successfully!";
        } else {
            throw new Exception("Book not available or insufficient copies");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Error reserving book: " . $e->getMessage();
    }
    
    header("Location: books.php");
    exit();
}
?> 