<?php
session_start();
include '../component-library/connect.php';
include 'activity_logger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $book_id = $_POST['book_id'];
    $copies = $_POST['copies'];
    $return_date = date('Y-m-d');

    try {
        // Insert into return_books
        $stmt = $conn->prepare("INSERT INTO return_books (user_id, book_id, copies, status, return_date) VALUES (?, ?, ?, 'returned', ?)");
        $stmt->execute([$user_id, $book_id, $copies, $return_date]);

        // Update book copies
        $update_stmt = $conn->prepare("UPDATE books SET copies = copies + ? WHERE id = ?");
        $update_stmt->execute([$copies, $book_id]);

        // Update borrowed_books status
        $update_borrowed = $conn->prepare("UPDATE borrowed_books SET status = 'returned' WHERE user_id = ? AND book_id = ? AND status = 'borrowed'");
        $update_borrowed->execute([$user_id, $book_id]);

        // Log the activity
        logActivity($user_id, 'book_return', "Returned book ID: $book_id, Copies: $copies");
        
        $_SESSION['success'] = "Book returned successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error returning book: " . $e->getMessage();
    }
    
    header("Location: borrowed_books.php");
    exit();
}
?> 