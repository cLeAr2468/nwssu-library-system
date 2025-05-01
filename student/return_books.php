<?php
session_start();
include '../component-library/connect.php';
include './activity_logger.php';

if (isset($_POST['return_book'])) {
    $book_id = $_POST['book_id'];
    $user_id = $_SESSION['user_id'];
    
    try {
        // Get book details for logging
        $bookQuery = $conn->prepare("SELECT title FROM books WHERE id = ?");
        $bookQuery->execute([$book_id]);
        $book = $bookQuery->fetch(PDO::FETCH_ASSOC);
        
        // Log the return activity
        logActivity($conn, $user_id, 'return', 'Returned book: ' . $book['title']);
        
        // Update the book status
        $updateBook = $conn->prepare("UPDATE books SET status = 'available' WHERE id = ?");
        $updateBook->execute([$book_id]);
        
        // Update borrowed_books table
        $updateBorrowed = $conn->prepare("UPDATE borrowed_books SET status = 'returned' WHERE book_id = ? AND user_id = ?");
        $updateBorrowed->execute([$book_id, $user_id]);
        
        // Insert into return_books table
        $insertReturn = $conn->prepare("INSERT INTO return_books (user_id, book_id, copies, status, return_date) VALUES (?, ?, 1, 'returned', NOW())");
        $insertReturn->execute([$user_id, $book_id]);
        
        header('Location: ./borrowed?message=Book returned successfully&message_type=success');
        exit();
    } catch (PDOException $e) {
        header('Location: ./borrowed?message=Error returning book&message_type=error');
        exit();
    }
}
?> 