<?php
session_start();
include '../component-library/connect.php';

header('Content-Type: application/json');

// Validate request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get and validate input
$userId = $_POST['user_id'] ?? null;
$bookId = $_POST['book_id'] ?? null;
$paymentAmount = floatval($_POST['payment_amount'] ?? 0);

if (!$userId || !$bookId || $paymentAmount <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
    exit;
}

try {
    // Start transaction
    $conn->beginTransaction();

    // Get current fine amount
    $getFineQuery = $conn->prepare("
        SELECT fine 
        FROM borrowed_books 
        WHERE user_id = ? AND book_id = ? AND fine > 0
    ");
    $getFineQuery->execute([$userId, $bookId]);
    $currentFine = $getFineQuery->fetch(PDO::FETCH_ASSOC);

    if (!$currentFine) {
        throw new Exception('No active fine found for this record');
    }

    if ($paymentAmount > $currentFine['fine']) {
        throw new Exception('Payment amount exceeds current fine amount');
    }

    // Update fine amount and set fine_updated column
    $updateFineQuery = $conn->prepare("
        UPDATE borrowed_books 
        SET fine = fine - ?, 
            fine_updated = NOW()
        WHERE user_id = ? AND book_id = ? AND fine > 0
    ");
    $updateFineQuery->execute([$paymentAmount, $userId, $bookId]);

    // Record payment in payment history
    $recordPaymentQuery = $conn->prepare("
        INSERT INTO pay (user_id, book_id, total_pay, payment_date) 
        VALUES (?, ?, ?, CURRENT_DATE)
    ");
    $recordPaymentQuery->execute([$userId, $bookId, $paymentAmount]);

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Fine payment processed successfully'
    ]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>