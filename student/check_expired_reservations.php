<?php
include 'component-library/connect.php';

try {
    // Start transaction
    $conn->beginTransaction();

    // Get current date and time
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    // Update expired reservations
    // Only update if current time is after 5 PM or if it's a new day
    if ($currentTime >= '17:00:00') {
        $updateQuery = $conn->prepare("
            UPDATE reserve_books 
            SET status = 'expired'
            WHERE status = 'reserved' 
            AND (
                (DATE(expiration_date) < :current_date) 
                OR 
                (DATE(expiration_date) = :current_date AND TIME(expiration_date) <= :current_time)
            )
        ");
        $updateQuery->execute([
            ':current_date' => $currentDate,
            ':current_time' => $currentTime
        ]);

        // For each expired reservation, increment the book copies back
        $expiredQuery = $conn->prepare("
            SELECT book_id 
            FROM reserve_books 
            WHERE status = 'expired' 
            AND (
                (DATE(expiration_date) < :current_date) 
                OR 
                (DATE(expiration_date) = :current_date AND TIME(expiration_date) <= :current_time)
            )
        ");
        $expiredQuery->execute([
            ':current_date' => $currentDate,
            ':current_time' => $currentTime
        ]);
        $expiredBooks = $expiredQuery->fetchAll(PDO::FETCH_ASSOC);

        foreach ($expiredBooks as $book) {
            // Increment the copies in the books table
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
    }

    // Commit transaction
    $conn->commit();
    
    echo "Successfully processed expired reservations";
} catch (PDOException $e) {
    // Rollback transaction on error
    $conn->rollBack();
    echo "Error processing expired reservations: " . $e->getMessage();
}
?> 