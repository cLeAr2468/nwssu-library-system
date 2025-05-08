<?php
include '../component-library/connect.php';

// Get parameters
$dateRange = $_GET['date_range'] ?? 'last_30_days';
$reportType = $_GET['report_type'] ?? 'borrowing';

// Calculate date range
$endDate = date('Y-m-d');
switch ($dateRange) {
    case 'last_7_days':
        $startDate = date('Y-m-d', strtotime('-7 days'));
        break;
    case 'last_30_days':
        $startDate = date('Y-m-d', strtotime('-30 days'));
        break;
    case 'last_3_months':
        $startDate = date('Y-m-d', strtotime('-3 months'));
        break;
    case 'last_year':
        $startDate = date('Y-m-d', strtotime('-1 year'));
        break;
    default:
        $startDate = date('Y-m-d', strtotime('-30 days'));
}

try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="library_report_' . date('Y-m-d') . '.csv"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Write UTF-8 BOM for proper Excel encoding
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    switch ($reportType) {
        case 'borrowing':
            // Borrowing Statistics Report
            fputcsv($output, ['Borrowing Statistics Report', '']);
            fputcsv($output, ['Period', $startDate . ' to ' . $endDate]);
            fputcsv($output, ['']);

            // Get borrowing data
            $query = $conn->prepare("
                SELECT 
                    DATE(borrowed_date) as date,
                    COUNT(*) as total_borrowed,
                    COUNT(DISTINCT user_id) as unique_borrowers
                FROM borrowed_books 
                WHERE borrowed_date BETWEEN ? AND ?
                GROUP BY DATE(borrowed_date)
                ORDER BY date
            ");
            $query->execute([$startDate, $endDate]);
            
            fputcsv($output, ['Date', 'Total Books Borrowed', 'Unique Borrowers']);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['date'],
                    $row['total_borrowed'],
                    $row['unique_borrowers']
                ]);
            }
            break;

        case 'user_activity':
            // User Activity Report
            fputcsv($output, ['User Activity Report', '']);
            fputcsv($output, ['Period', $startDate . ' to ' . $endDate]);
            fputcsv($output, ['']);

            // Get user activity data
            $query = $conn->prepare("
                SELECT 
                    u.first_name,
                    u.last_name,
                    u.user_id,
                    COUNT(DISTINCT bb.book_id) as total_borrowed,
                    COUNT(DISTINCT rb.book_id) as total_returned,
                    COALESCE(SUM(bb.fine), 0) as total_fines
                FROM user_info u
                LEFT JOIN borrowed_books bb ON u.user_id = bb.user_id AND bb.borrowed_date BETWEEN ? AND ?
                LEFT JOIN return_books rb ON u.user_id = rb.user_id AND rb.return_date BETWEEN ? AND ?
                GROUP BY u.user_id, u.first_name, u.last_name
                ORDER BY total_borrowed DESC
            ");
            $query->execute([$startDate, $endDate, $startDate, $endDate]);
            
            fputcsv($output, ['User ID', 'Name', 'Total Books Borrowed', 'Total Books Returned', 'Total Fines']);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['user_id'],
                    $row['first_name'] . ' ' . $row['last_name'],
                    $row['total_borrowed'],
                    $row['total_returned'],
                    '₱' . number_format($row['total_fines'], 2)
                ]);
            }
            break;

        case 'fine_collection':
            // Fine Collection Report
            fputcsv($output, ['Fine Collection Report', '']);
            fputcsv($output, ['Period', $startDate . ' to ' . $endDate]);
            fputcsv($output, ['']);

            // Get fine collection data
            $query = $conn->prepare("
                SELECT 
                    DATE(rb.return_date) as date,
                    COUNT(DISTINCT bb.user_id) as users_with_fines,
                    COALESCE(SUM(bb.fine), 0) as total_fines
                FROM return_books rb
                JOIN borrowed_books bb ON rb.user_id = bb.user_id AND rb.book_id = bb.book_id
                WHERE rb.return_date BETWEEN ? AND ?
                GROUP BY DATE(rb.return_date)
                ORDER BY date
            ");
            $query->execute([$startDate, $endDate]);
            
            fputcsv($output, ['Date', 'Users with Fines', 'Total Fines Collected']);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['date'],
                    $row['users_with_fines'],
                    '₱' . number_format($row['total_fines'], 2)
                ]);
            }
            break;

        case 'book_inventory':
            // Book Inventory Report
            fputcsv($output, ['Book Inventory Report', '']);
            fputcsv($output, ['Generated on', date('Y-m-d H:i:s')]);
            fputcsv($output, ['']);

            // Get book inventory data
            $query = $conn->prepare("
                SELECT 
                    b.title,
                    b.author,
                    b.copies as total_copies,
                    COUNT(DISTINCT bb.id) as currently_borrowed,
                    COUNT(DISTINCT rb.id) as total_returns,
                    b.copies - COUNT(DISTINCT bb.id) as available_copies
                FROM books b
                LEFT JOIN borrowed_books bb ON b.id = bb.book_id AND bb.status IN ('borrowed', 'overdue')
                LEFT JOIN return_books rb ON b.id = rb.book_id
                GROUP BY b.id, b.title, b.author, b.copies
                ORDER BY b.title
            ");
            $query->execute();
            
            fputcsv($output, ['Title', 'Author', 'Total Copies', 'Currently Borrowed', 'Total Returns', 'Available Copies']);
            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                fputcsv($output, [
                    $row['title'],
                    $row['author'],
                    $row['total_copies'],
                    $row['currently_borrowed'],
                    $row['total_returns'],
                    $row['available_copies']
                ]);
            }
            break;
    }

    fclose($output);
    exit();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
} 