<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

include '../component-library/connect.php';

// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit();
}

// Get the action type from the request
$actionType = isset($_POST['action_type']) ? $_POST['action_type'] : '';

// Handle different actions
switch ($actionType) {
    case 'borrow':
        handleBorrow($conn);
        break;
    case 'check_in':
        handleCheckIn($conn);
        break;
    case 'payment':
        handlePayment($conn);
        break;
    case 'quick_checkout':
        quickCheckout($conn);
        break;
    default:
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Invalid action type']);
        exit();
}

// Function to handle borrowing
function handleBorrow($conn) {
    // Get the user ID, book title, and ISBN from the request
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $bookTitle = isset($_POST['book_title']) ? $_POST['book_title'] : '';
    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
    
    // Validate input
    if (empty($userId) || empty($bookTitle) || empty($isbn)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Check if the book is available
        $checkBookQuery = $conn->prepare("SELECT copies FROM books WHERE ISBN = :isbn");
        $checkBookQuery->execute([':isbn' => $isbn]);
        $bookResult = $checkBookQuery->fetch(PDO::FETCH_ASSOC);
        
        if (!$bookResult || $bookResult['copies'] <= 0) {
            $conn->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Book is not available for checkout']);
            exit();
        }
        
        // Update the reserve_books table - only update status to 'borrowed'
        $updateReserveQuery = $conn->prepare("
            UPDATE reserve_books 
            SET status = 'borrowed'
            WHERE user_id = :user_id 
            AND ISBN = :isbn 
            AND status = 'reserved'
        ");
        $updateReserveQuery->execute([
            ':user_id' => $userId,
            ':isbn' => $isbn
        ]);
        
        // Update the books table to decrease the number of copies
        $updateBookQuery = $conn->prepare("
            UPDATE books 
            SET copies = copies - 1 
            WHERE ISBN = :isbn
        ");
        $updateBookQuery->execute([':isbn' => $isbn]);
        
        // Commit transaction
        $conn->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Book checked out successfully']);
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Function to handle check-in
function handleCheckIn($conn) {
    // Get the user ID and ISBN from the request
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
    
    // Validate input
    if (empty($userId) || empty($isbn)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Update the reserve_books table
        $updateReserveQuery = $conn->prepare("
            UPDATE reserve_books 
            SET status = 'returned'
            WHERE user_id = :user_id 
            AND ISBN = :isbn 
            AND (status = 'borrowed' OR status = 'overdue')
        ");
        $updateReserveQuery->execute([
            ':user_id' => $userId,
            ':isbn' => $isbn
        ]);
        
        // Update the books table to increase the number of copies
        $updateBookQuery = $conn->prepare("
            UPDATE books 
            SET copies = copies + 1 
            WHERE ISBN = :isbn
        ");
        $updateBookQuery->execute([':isbn' => $isbn]);
        
        // Commit transaction
        $conn->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Book checked in successfully']);
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

// Function to handle payment for overdue books
function handlePayment($conn) {
    // Get the user ID, ISBN, and payment amount from the request
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    $isbn = isset($_POST['isbn']) ? $_POST['isbn'] : '';
    $paymentAmount = isset($_POST['payment_amount']) ? floatval($_POST['payment_amount']) : 0;
    
    // Validate input
    if (empty($userId) || empty($isbn) || $paymentAmount <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing or invalid payment information']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Get the current fine amount
        $getFineQuery = $conn->prepare("
            SELECT fine 
            FROM reserve_books 
            WHERE user_id = :user_id 
            AND ISBN = :isbn 
            AND status = 'overdue'
        ");
        $getFineQuery->execute([
            ':user_id' => $userId,
            ':isbn' => $isbn
        ]);
        $fineResult = $getFineQuery->fetch(PDO::FETCH_ASSOC);
        
        if (!$fineResult) {
            $conn->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No overdue book found']);
            exit();
        }
        
        $currentFine = $fineResult['fine'];
        
        // Check if payment amount is valid
        if ($paymentAmount > $currentFine) {
            $conn->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Payment amount exceeds current fine']);
            exit();
        }
        
        // Record the payment
        $recordPaymentQuery = $conn->prepare("
            INSERT INTO payments (user_id, isbn, amount, payment_date) 
            VALUES (:user_id, :isbn, :amount, CURRENT_DATE)
        ");
        $recordPaymentQuery->execute([
            ':user_id' => $userId,
            ':isbn' => $isbn,
            ':amount' => $paymentAmount
        ]);
        
        // Update the fine amount
        $updateFineQuery = $conn->prepare("
            UPDATE reserve_books 
            SET fine = fine - :payment_amount 
            WHERE user_id = :user_id 
            AND ISBN = :isbn 
            AND status = 'overdue'
        ");
        $updateFineQuery->execute([
            ':user_id' => $userId,
            ':isbn' => $isbn,
            ':payment_amount' => $paymentAmount
        ]);
        
        // Commit transaction
        $conn->commit();
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'message' => 'Payment processed successfully']);
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}

function quickCheckout($conn) {
    // Get the book ID and user ID from the request
    $bookId = isset($_POST['book_id']) ? $_POST['book_id'] : '';
    $userId = isset($_POST['user_id']) ? $_POST['user_id'] : '';
    
    // Validate input
    if (empty($bookId) || empty($userId)) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit();
    }
    
    try {
        // Start transaction
        $conn->beginTransaction();
        
        // Check if the book is already reserved by this user
        $checkReservationQuery = $conn->prepare("
            SELECT * FROM reserve_books 
            WHERE book_id = :book_id 
            AND user_id = :user_id 
            AND status = 'reserved'
        ");
        $checkReservationQuery->execute([
            ':book_id' => $bookId,
            ':user_id' => $userId
        ]);
        $reservation = $checkReservationQuery->fetch(PDO::FETCH_ASSOC);
        
        if (!$reservation) {
            $conn->rollBack();
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'No active reservation found for this book']);
            exit();
        }
        
        // Get current date and calculate return date (e.g., 14 days from now)
        $currentDate = date('Y-m-d H:i:s');
        $returnDate = date('Y-m-d H:i:s', strtotime('+14 days'));
        
        // Update the reserve_books table status to 'borrowed'
        $updateReserveQuery = $conn->prepare("
            UPDATE reserve_books 
            SET status = 'borrowed',
                borrowed_date = :borrowed_date,
                return_sched = :return_sched,
                fine = 0
            WHERE book_id = :book_id 
            AND user_id = :user_id 
            AND status = 'reserved'
        ");
        $updateReserveQuery->execute([
            ':book_id' => $bookId,
            ':user_id' => $userId,
            ':borrowed_date' => $currentDate,
            ':return_sched' => $returnDate
        ]);
        
        // Update the books table to decrease the number of copies
        $updateBookQuery = $conn->prepare("
            UPDATE books 
            SET copies = copies - 1 
            WHERE id = :book_id
        ");
        $updateBookQuery->execute([':book_id' => $bookId]);
        
        // Commit transaction
        $conn->commit();
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Book checked out successfully',
            'return_date' => $returnDate
        ]);
        
    } catch (PDOException $e) {
        // Rollback transaction on error
        $conn->rollBack();
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
}
?> 