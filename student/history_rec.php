<?php
session_start(); // Start the session
include '../component-library/connect.php';
include '../student/side_navbars.php';
// Assume the logged-in student ID is stored in session
$user_id = $_SESSION['user_id'] ?? null;
if ($user_id) {
    // Fetch student profile data from the database
    $stud = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
    $stud->execute([$user_id]);
    $student = $stud->fetch(PDO::FETCH_ASSOC);
    $profile_image = $student['images'] ?? '../images/prof.jpg'; // Fallback if no image

    // Fetch recently viewed books from activities
    $view_stmt = $conn->prepare("
        SELECT DISTINCT a.activity_details, a.activity_date, b.*
        FROM activities a
        JOIN books b ON a.activity_details = b.title
        WHERE a.user_id = ? AND a.activity_type = 'View Books'
        ORDER BY a.activity_date DESC
        LIMIT 5
    ");
    $view_stmt->execute([$user_id]);
    $recent_views = $view_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all book transactions with proper date sorting
    $transactions_stmt = $conn->prepare("
        SELECT 
            b.id,
            b.title,
            b.books_image,
            b.copyright,
            b.ISBN,
            rb.reserved_date,
            rb.status as reserve_status,
            rb.cancel_date,
            rb.expiration_schedule,
            rb.expired_date,
            bb.borrowed_date,
            bb.status as borrow_status,
            bb.return_sched,
            bb.fine,
            rt.return_date,
            rt.status as return_status,
            GREATEST(
                COALESCE(rb.reserved_date, '1970-01-01'),
                COALESCE(bb.borrowed_date, '1970-01-01'),
                COALESCE(rt.return_date, '1970-01-01')
            ) as latest_date
        FROM books b
        LEFT JOIN reserve_books rb ON b.id = rb.book_id AND rb.user_id = ?
        LEFT JOIN borrowed_books bb ON b.id = bb.book_id AND bb.user_id = ?
        LEFT JOIN return_books rt ON b.id = rt.book_id AND rt.user_id = ?
        WHERE rb.user_id = ? OR bb.user_id = ? OR rt.user_id = ?
        ORDER BY latest_date DESC
    ");
    $transactions_stmt->execute([$user_id, $user_id, $user_id, $user_id, $user_id, $user_id]);
    $transactions = $transactions_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Redirect to login if not logged in
    header('location:../index.php');
    exit();
}

function timeAgo($date, $full = false) {
    if (!$date) return 'Date not available';
    
    $now = new DateTime();
        $ago = new DateTime($date);
        $diff = $now->diff($ago);
    
        $d = [];
        $units = [
            'year' => $diff->y,
            'month' => $diff->m,
            'day' => $diff->d,
            'hour' => $diff->h,
            'minute' => $diff->i,
            'second' => $diff->s,
        ];

        foreach ($units as $key => $value) {
            if ($value) {
                $d[] = $value . ' ' . $key . ($value > 1 ? 's' : '');
            }
        }

        if (!$full) $d = array_slice($d, 0, 1);
        return $d ? implode(', ', $d) . ' ago' : 'just now';
    }

function getStatusColor($status) {
    switch ($status) {
        case 'reserved':
            return 'bg-blue-100 text-blue-800';
        case 'borrowed':
            return 'bg-yellow-100 text-yellow-800';
        case 'returned':
            return 'bg-green-100 text-green-800';
        case 'overdue':
            return 'bg-red-100 text-red-800';
        case 'expired':
            return 'bg-orange-100 text-orange-800';
        case 'canceled':
            return 'bg-gray-100 text-gray-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
}

function getTransactionStatus($transaction) {
    if ($transaction['return_status'] === 'returned') {
        return ['status' => 'returned', 'date' => $transaction['return_date']];
    }
    if ($transaction['borrow_status'] === 'overdue') {
        return ['status' => 'overdue', 'date' => $transaction['return_sched']];
    }
    if ($transaction['borrow_status'] === 'borrowed') {
        return ['status' => 'borrowed', 'date' => $transaction['borrowed_date']];
    }
    if ($transaction['reserve_status'] === 'expired') {
        return ['status' => 'expired', 'date' => $transaction['expired_date']];
    }
    if ($transaction['reserve_status'] === 'canceled') {
        return ['status' => 'canceled', 'date' => $transaction['cancel_date']];
    }
    if ($transaction['reserve_status'] === 'reserved') {
        return ['status' => 'reserved', 'date' => $transaction['reserved_date']];
    }
    return ['status' => 'unknown', 'date' => null];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction History</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Recently Viewed Books Section -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Recently Viewed Books</h2>
                </div>

                <!-- Desktop Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-primary">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider"></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ISBN</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Copyright</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Last Viewed</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($recent_views)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No recently viewed books</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_views as $view): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex justify-center items-center h-28">
                                                <?php if (!empty($view['books_image'])): ?>
                                                    <img src="./uploaded_file/<?php echo htmlspecialchars($view['books_image']); ?>" 
                                                         alt="Book Cover" 
                                                         class="h-28 w-20 object-cover rounded shadow-sm">
                                                <?php else: ?>
                                                    <div class="h-28 w-20 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                                        <span class="text-xs font-bold">Book Cover</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($view['title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($view['ISBN']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($view['copyright']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo timeAgo($view['activity_date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        <?php if (empty($recent_views)): ?>
                            <div class="text-center py-4 text-gray-500">No recently viewed books</div>
        <?php else: ?>
                            <?php foreach ($recent_views as $view): ?>
                                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                    <div class="flex p-4">
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($view['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($view['books_image']); ?>" 
                                                     alt="Book Cover" 
                                                     class="w-20 h-28 object-cover rounded-lg shadow-sm">
                    <?php else: ?>
                                                <div class="w-20 h-28 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                                    <span class="text-xs font-bold">Book Cover</span>
                        </div>
                    <?php endif; ?>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-base font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($view['title']); ?>
                                            </h4>
                                            <div class="mt-2 text-sm text-gray-500">
                                                <p>ISBN: <?php echo htmlspecialchars($view['ISBN']); ?></p>
                                                <p>Copyright: <?php echo htmlspecialchars($view['copyright']); ?></p>
                                                <p class="mt-1">Viewed: <?php echo timeAgo($view['activity_date']); ?></p>
                                            </div>
                                            <div class="mt-3">
                                                <a href="./books?id=<?php echo urlencode($view['id']); ?>" 
                                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    <i class="bi bi-eye-fill mr-1"></i>
                                                    View Details
                                                </a>
                                            </div>
                        </div>
                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Book Transactions Section -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Book Transactions</h2>
                </div>

                <!-- Desktop Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-primary">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider"></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">ISBN</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Fine</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if (empty($transactions)): ?>
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No transactions found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($transactions as $transaction): 
                                    $status = getTransactionStatus($transaction);
                                ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex justify-center items-center h-28">
                                                <?php if (!empty($transaction['books_image'])): ?>
                                                    <img src="./uploaded_file/<?php echo htmlspecialchars($transaction['books_image']); ?>" 
                                                         alt="Book Cover" 
                                                         class="h-28 w-20 object-cover rounded shadow-sm">
                                                <?php else: ?>
                                                    <div class="h-28 w-20 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                                        <span class="text-xs font-bold">Book Cover</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($transaction['title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($transaction['ISBN']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColor($status['status']); ?>">
                                                <?php echo ucfirst($status['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo timeAgo($status['date']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <?php if ($status['status'] === 'overdue' && $transaction['fine']): ?>
                                                <span class="text-red-600">₱<?php echo $transaction['fine']; ?></span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mobile View -->
                    <div class="md:hidden space-y-4">
                        <?php if (empty($transactions)): ?>
                            <div class="text-center py-4 text-gray-500">No transactions found</div>
                        <?php else: ?>
                            <?php foreach ($transactions as $transaction): 
                                $status = getTransactionStatus($transaction);
                            ?>
                                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                    <div class="flex p-4">
                                        <div class="flex-shrink-0">
                                            <?php if (!empty($transaction['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($transaction['books_image']); ?>" 
                                                     alt="Book Cover" 
                                                     class="w-20 h-28 object-cover rounded-lg shadow-sm">
                                            <?php else: ?>
                                                <div class="w-20 h-28 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">
                                                    <span class="text-xs font-bold">Book Cover</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <h4 class="text-base font-semibold text-gray-900">
                                                <?php echo htmlspecialchars($transaction['title']); ?>
                                            </h4>
                                            <div class="mt-2 text-sm text-gray-500">
                                                <p>ISBN: <?php echo htmlspecialchars($transaction['ISBN']); ?></p>
                                                <div class="mt-2 flex items-center">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo getStatusColor($status['status']); ?>">
                                                        <?php echo ucfirst($status['status']); ?>
                                                    </span>
                                                    <span class="ml-2 text-gray-500">
                                                        <?php echo timeAgo($status['date']); ?>
                                                    </span>
                                                </div>
                                                <?php if ($status['status'] === 'overdue' && $transaction['fine']): ?>
                                                    <p class="mt-1 text-red-600">Fine: ₱<?php echo $transaction['fine']; ?></p>
                                                <?php endif; ?>
                                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include '../student/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>