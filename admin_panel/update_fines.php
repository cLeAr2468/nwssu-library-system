<?php
include '../component-library/connect.php';

function updateOverdueFines($conn) {
    try {
        // Get all returned books where return_date is later than return_sched
        $query = $conn->prepare("
            SELECT bb.user_id, bb.book_id, bb.return_sched, rb.return_date, bb.fine
            FROM borrowed_books bb
            JOIN return_books rb ON bb.user_id = rb.user_id AND bb.book_id = rb.book_id
            WHERE bb.status = 'returned'
            AND rb.return_date > bb.return_sched
        ");
        $query->execute();
        $overdueBooks = $query->fetchAll(PDO::FETCH_ASSOC);

        foreach ($overdueBooks as $book) {
            // Calculate days overdue using TIMESTAMPDIFF
            $daysOverdue = $conn->prepare("
                SELECT TIMESTAMPDIFF(DAY, return_sched, :return_date) AS days_overdue
            ");
            $daysOverdue->execute([':return_date' => $book['return_date']]);
            $days = $daysOverdue->fetchColumn();

            // Calculate the new fine
            $newFine = $days * 3;

            // Update the fine in borrowed_books
            $updateQuery = $conn->prepare("
                UPDATE borrowed_books 
                SET fine = GREATEST(fine, :new_fine)
                WHERE user_id = :user_id 
                AND book_id = :book_id 
                AND status = 'returned'
            ");
            $updateQuery->execute([
                ':new_fine' => $newFine,
                ':user_id' => $book['user_id'],
                ':book_id' => $book['book_id']
            ]);
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error updating fines: " . $e->getMessage());
        return false;
    }
}

// Function to check and update fines for currently borrowed books
function updateCurrentOverdueFines($conn) {
    try {
        // Update fines for currently borrowed books that are overdue
        $updateQuery = $conn->prepare("
            UPDATE borrowed_books 
            SET fine = fine + TIMESTAMPDIFF(DAY, GREATEST(return_sched, fine_updated), CURRENT_TIMESTAMP) * 3,
                fine_updated = CURRENT_TIMESTAMP,
                status = CASE 
                    WHEN return_sched < CURRENT_TIMESTAMP THEN 'overdue' 
                    ELSE status 
                END
            WHERE return_sched < CURRENT_TIMESTAMP 
            AND status IN ('borrowed', 'overdue')
        ");
        $updateQuery->execute();
        
        return true;
    } catch (PDOException $e) {
        error_log("Error updating current fines: " . $e->getMessage());
        return false;
    }
}

// Call the functions when this file is included
updateOverdueFines($conn);
updateCurrentOverdueFines($conn);
?>