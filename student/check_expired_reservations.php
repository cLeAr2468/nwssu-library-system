<?php
include "../component-library/connect.php"; // Include database connection

try {
    // Start transaction
    $conn->beginTransaction();

    // Get current date and time
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');
    $currentDateTime = date('Y-m-d H:i:s');

    // Update expired reservations (those that are reserved and have expired)
    $updateQuery = $conn->prepare("
        UPDATE reserve_books 
        SET status = 'expired', 
            expired_date = :current_datetime
        WHERE status = 'reserved' 
        AND (
            DATE(expiration_schedule) < :current_date 
            OR 
            (DATE(expiration_schedule) = :current_date AND TIME(expiration_schedule) <= :current_time)
        )
    ");
    $updateQuery->execute([
        ':current_date' => $currentDate,
        ':current_time' => $currentTime,
        ':current_datetime' => $currentDateTime
    ]);

    // Fetch all reservations that were just marked as "expired"
    $expiredQuery = $conn->prepare("
        SELECT book_id 
        FROM reserve_books 
        WHERE status = 'expired' 
        AND expired_date = :current_datetime
    ");
    $expiredQuery->execute([':current_datetime' => $currentDateTime]);
    $expiredBooks = $expiredQuery->fetchAll(PDO::FETCH_ASSOC);

    // Increment book copies for each expired reservation
    foreach ($expiredBooks as $book) {
        $updateCopiesQuery = $conn->prepare("
            UPDATE books 
            SET copies = copies + 1,
                status = CASE 
                    WHEN copies + 1 > 0 THEN 'available'
                    ELSE status 
                END
            WHERE id = :book_id
        ");
        $updateCopiesQuery->execute([':book_id' => $book['book_id']]);
    }

    // Commit transaction
    $conn->commit();

} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    echo "Error processing expired reservations: " . $e->getMessage();
}
?>