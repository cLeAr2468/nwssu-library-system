<?php
session_start();
include '../component-library/connect.php';
// Fetch user reservations for display


if (isset($_GET['type']) && isset($_GET['query'])) {
    header('Content-Type: application/json');
    try {
        $type = $_GET['type'];
        $query = $_GET['query'];
        if ($type === 'user') {
            // Search for users by ID or name
            $stmt = $conn->prepare("SELECT user_id, CONCAT(last_name, ', ', first_name, ' ', middle_name) as full_name, patron_type 
                                  FROM user_info 
                                  WHERE user_id LIKE :query 
                                  OR last_name LIKE :query 
                                  OR first_name LIKE :query 
                                  OR middle_name LIKE :query 
                                  LIMIT 10");
            $stmt->execute([':query' => "%$query%"]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'status' => 'success',
                'suggestions' => array_map(function($row) {
                    return [
                        'id' => $row['user_id'],
                        'display_text' => $row['user_id'] . ' - ' . $row['full_name'],
                        'sub_text' => 'Patron Type: ' . $row['patron_type']
                    ];
                }, $results)
            ]);
            exit;
        } elseif ($type === 'book') {
            // Search for books by title, ISBN, or call number
            $stmt = $conn->prepare("SELECT b.id, b.title, b.ISBN, b.call_no, b.copies, b.status, 
                                  (SELECT COUNT(*) FROM reserve_books rb WHERE rb.book_id = b.id AND rb.user_id = :user_id AND rb.status = 'reserved') as is_reserved
                                  FROM books b
                                  WHERE b.title LIKE :query 
                                  OR b.ISBN LIKE :query 
                                  OR b.call_no LIKE :query 
                                  LIMIT 10");
            $stmt->execute([
                ':query' => "%$query%",
                ':user_id' => isset($_GET['user_id']) ? $_GET['user_id'] : ''
            ]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode([
                'status' => 'success',
                'suggestions' => array_map(function($row) {
                    $availability = ($row['status'] === 'available' && $row['copies'] > 0) ? 'Available' : 'Not Available';
                    $availabilityClass = ($row['status'] === 'available' && $row['copies'] > 0) ? 'text-green-600' : 'text-red-600';
                    $isReserved = ($row['is_reserved'] > 0);
                    
                    if ($isReserved) {
                        $availability = 'Reserved by You';
                        $availabilityClass = 'text-blue-600';
                    }
                    
                    return [
                        'id' => $row['id'],
                        'display_text' => $row['title'],
                        'sub_text' => 'ISBN: ' . $row['ISBN'] . ' | Call No: ' . $row['call_no'] . ' | <span class="' . $availabilityClass . '">' . $availability . '</span>',
                        'full_value' => $row['title'], // Store the title as the value to display in input
                        'is_available' => ($row['status'] === 'available' && $row['copies'] > 0) || $isReserved,
                        'is_reserved' => $isReserved
                    ];
                }, $results)
            ]);
            exit;
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Database error: ' . $e->getMessage()
        ]);
        exit;
    }
}


$queryReserved = "
    SELECT rb.user_id, ui.last_name, ui.first_name, ui.middle_name, ui.patron_type, rb.book_id, b.title AS book_title, rb.copies, rb.status 
    FROM reserve_books rb 
    JOIN user_info ui ON rb.user_id = ui.user_id 
    JOIN books b ON rb.book_id = b.id 
    WHERE rb.status = 'reserved'";
$stmtReserved = $conn->prepare($queryReserved);
$stmtReserved->execute();
$reservedBooks = $stmtReserved->fetchAll(PDO::FETCH_ASSOC);
// Fetch borrowed books for display
$queryBorrowed = "
    SELECT rb.user_id, ui.last_name, ui.first_name, ui.middle_name, ui.patron_type, rb.book_id, b.title AS book_title, rb.copies, rb.status 
    FROM borrowed_books rb 
    JOIN user_info ui ON rb.user_id = ui.user_id 
    JOIN books b ON rb.book_id = b.id 
    WHERE rb.status = 'borrowed'";
$stmtBorrowed = $conn->prepare($queryBorrowed);
$stmtBorrowed->execute();
$borrowedBooks = $stmtBorrowed->fetchAll(PDO::FETCH_ASSOC);
// Check if the request is a POST request
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['action'])) {
    if ($data['action'] === 'checkout') {
        $userId = $data['user_id'];
        $bookId = $data['book_id'];
        $copies = $data['copies'];
        $returnSched = date('Y-m-d', strtotime('+14 days'));
        $borrowedDate = date('Y-m-d');
        
        try {
            // Check if the user has already borrowed this book
            $checkBorrowedQuery = "SELECT COUNT(*) as borrowed_count FROM borrowed_books 
                                  WHERE user_id = ? AND book_id = ? AND status = 'borrowed'";
            $stmtCheckBorrowed = $conn->prepare($checkBorrowedQuery);
            $stmtCheckBorrowed->execute([$userId, $bookId]);
            $borrowedCount = $stmtCheckBorrowed->fetchColumn();
            
            if ($borrowedCount > 0) {
                echo json_encode(['success' => false, 'message' => 'This user has already borrowed this book.']);
                exit;
            }
            
            // Check if the user has reserved this specific book
            $checkReservedQuery = "SELECT COUNT(*) as reserved_count FROM reserve_books 
                                  WHERE user_id = ? AND book_id = ? AND status = 'reserved'";
            $stmtCheckReserved = $conn->prepare($checkReservedQuery);
            $stmtCheckReserved->execute([$userId, $bookId]);
            $reservedCount = $stmtCheckReserved->fetchColumn();
            
            // If user has not reserved this book, check if the book is available
            if ($reservedCount === 0) {
                // Check if the book is available
                $checkBookQuery = "SELECT copies, status FROM books WHERE id = ?";
                $stmtCheckBook = $conn->prepare($checkBookQuery);
                $stmtCheckBook->execute([$bookId]);
                $bookInfo = $stmtCheckBook->fetch(PDO::FETCH_ASSOC);
                
                if (!$bookInfo) {
                    echo json_encode(['success' => false, 'message' => 'Book not found.']);
                    exit;
                }
                
                if ($bookInfo['status'] === 'not available' || $bookInfo['copies'] <= 0) {
                    echo json_encode(['success' => false, 'message' => 'This book is not available for borrowing.']);
                    exit;
                }
            }
            
            // Insert into borrowed_books table
            $insertQuery = "INSERT INTO borrowed_books (user_id, book_id, copies, return_sched, status, borrowed_date) VALUES (?, ?, ?, ?, 'borrowed', ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->execute([$userId, $bookId, $copies, $returnSched, $borrowedDate]);
            
            if ($reservedCount === 0) {
                // User has no reserved books, decrement copies
                $updateBooksQuery = "UPDATE books SET copies = copies - 1 WHERE id = ?";
                $stmtUpdateBooks = $conn->prepare($updateBooksQuery);
                $stmtUpdateBooks->execute([$bookId]);
                // Check if copies are now zero
                $checkCopiesQuery = "SELECT copies FROM books WHERE id = ?";
                $stmtCheckCopies = $conn->prepare($checkCopiesQuery);
                $stmtCheckCopies->execute([$bookId]);
                $remainingCopies = $stmtCheckCopies->fetchColumn();
                if ($remainingCopies <= 0) {
                    // Update book status to 'not available'
                    $updateBookStatusQuery = "UPDATE books SET status = 'not available' WHERE id = ?";
                    $stmtUpdateBookStatus = $conn->prepare($updateBookStatusQuery);
                    $stmtUpdateBookStatus->execute([$bookId]);
                }
            }
            
            // Update the status in reserve_books table
            $updateQuery = "UPDATE reserve_books SET status = 'borrowed' WHERE user_id = ? AND book_id = ?";
            $stmtUpdate = $conn->prepare($updateQuery);
            $stmtUpdate->execute([$userId, $bookId]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit; // Stop further execution for AJAX requests
    } elseif ($data['action'] === 'checkin') {
        $userId = $data['user_id'];
        $bookId = $data['book_id'];
        try {
            // Fetch the borrowed book details
            $fetchBorrowedQuery = "SELECT copies FROM borrowed_books WHERE user_id = ? AND book_id = ?";
            $stmtFetch = $conn->prepare($fetchBorrowedQuery);
            $stmtFetch->execute([$userId, $bookId]);
            $borrowedBook = $stmtFetch->fetch(PDO::FETCH_ASSOC);
            if ($borrowedBook) {
                // Insert into return_books table
                $returnDate = date('Y-m-d'); // Get the current date
                $insertReturnQuery = "INSERT INTO return_books (user_id, book_id, copies, status, return_date) VALUES (?, ?, ?, 'returned', ?)";
                $stmtInsertReturn = $conn->prepare($insertReturnQuery);
                $stmtInsertReturn->execute([$userId, $bookId, $borrowedBook['copies'], $returnDate]);
                // Delete the record from borrowed_books table
                $updateBorrowedQuery = "UPDATE borrowed_books SET status = 'returned' WHERE user_id = ? AND book_id = ?";
                $stmtupdateBorrowed = $conn->prepare($updateBorrowedQuery);
                $stmtupdateBorrowed->execute([$userId, $bookId]);
                // Update the status in books table to 'available' and increment copies
                $updateBooksQuery = "UPDATE books SET status = 'available', copies = copies + ? WHERE id = ?";
                $stmtUpdateBooks = $conn->prepare($updateBooksQuery);
                $stmtUpdateBooks->execute([$borrowedBook['copies'], $bookId]);
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No borrowed record found.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit; // Stop further execution for AJAX requests
        
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NwSSU : Circulation</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.lineicons.com/4.0/lineicons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Custom styles for the table */
        .table-fixed {
            table-layout: fixed;
        }
        
        /* Tooltip styles */
        .truncate {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        /* Responsive table styles */
        @media (max-width: 1024px) {
            .table-fixed th:nth-child(2),
            .table-fixed td:nth-child(2) {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .table-fixed th:nth-child(4),
            .table-fixed td:nth-child(4) {
                display: none;
            }
        }
        
        /* Custom scrollbar for better user experience */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        
        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <?php include '../admin_panel/side_nav.php'?>
    <div class="p-4 lg:ml-64 mt-14">
        <div class="p-4 rounded-lg">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Circulation</h1>
                    <p class="text-gray-600">Manage book checkouts and returns</p>
                </div>
            </div>
            <!-- Main Content -->
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 md:mb-0">Users</h2>
                    <div class="flex flex-col md:flex-row items-start md:items-center space-y-4 md:space-y-0 md:space-x-4 w-full md:w-auto">
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center" onclick="openCirculateModal()">
                            <i class="lni lni-cart mr-2"></i> Quick Check Out
                        </button>
                        <div class="relative w-full md:w-64">
                            <i class="lni lni-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="searchInput" placeholder="Search ID or Name" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" onkeyup="searchUsers()">
                        </div>
                    </div>
                </div>
                <!-- Users Table -->
                <div class="overflow-hidden">
                    <div class="overflow-x-auto max-w-full">
                        <table class="min-w-full divide-gray-200 table-fixed">
                            <thead class="bg-primary-600 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/12">User ID</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-2/12">User Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-3/12">Books Title</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/12">Patron Type</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/12">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/12">Copy</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider w-1/12">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="userTableBody">
                                <!-- Display Reserved Books -->
                                <?php foreach ($reservedBooks as $book): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php echo htmlspecialchars($book['user_id']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php 
                                            // Display last name, first name, and middle name
                                            echo htmlspecialchars($book['last_name'] . ', ' . $book['first_name'] . ' ' . $book['middle_name']); 
                                            ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="truncate" title="<?php echo htmlspecialchars($book['book_title']); ?>">
                                                <?php echo htmlspecialchars($book['book_title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php echo htmlspecialchars($book['patron_type']); ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <?php echo htmlspecialchars($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                                            <?php echo htmlspecialchars($book['copies']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium whitespace-nowrap">
                                            <button class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-md transition duration-200 w-full" onclick="quickCheckout('<?php echo htmlspecialchars($book['user_id']); ?>', '<?php echo htmlspecialchars($book['book_id']); ?>', '<?php echo htmlspecialchars($book['copies']); ?>')">
                                                Check Out
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <!-- Display Borrowed Books -->
                                <?php foreach ($borrowedBooks as $book): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php echo htmlspecialchars($book['user_id']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php 
                                            // Display last name, first name, and middle name
                                            echo htmlspecialchars($book['last_name'] . ', ' . $book['first_name'] . ' ' . $book['middle_name']); 
                                            ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="truncate" title="<?php echo htmlspecialchars($book['book_title']); ?>">
                                                <?php echo htmlspecialchars($book['book_title']); ?>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 truncate">
                                            <?php echo htmlspecialchars($book['patron_type']); ?>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                <?php echo htmlspecialchars($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900 text-center">
                                            <?php echo htmlspecialchars($book['copies']); ?>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium whitespace-nowrap">
                                            <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md transition duration-200 w-full" onclick="checkIn('<?php echo htmlspecialchars($book['user_id']); ?>', '<?php echo htmlspecialchars($book['book_id']); ?>')">
                                                Check In
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <!-- Circulate Item Modal -->
     <div id="circulateModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center pb-3">
                <h3 class="text-xl font-semibold text-gray-900" id="circulateModalLabel">Circulate Item</h3>
                <button onclick="closeCirculateModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="lni lni-close"></i>
                </button>
            </div>
            <div class="mt-2">
                <form id="circulateForm" class="space-y-4" onsubmit="submitQuickCheckout(event)">
                    <div class="relative">
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User ID/Name</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                               id="user_id" name="user_id" required autocomplete="off">
                        <div id="userSuggestions" class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg border border-gray-200 hidden"></div>
                    </div>
                    <div class="relative">
                        <label for="book_input" class="block text-sm font-medium text-gray-700">Book Title/ISBN</label>
                        <input type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" 
                               id="book_input" name="book_input" required autocomplete="off">
                        <div id="bookAvailability" class="mt-1 text-sm hidden"></div>
                        <div id="bookSuggestions" class="absolute z-10 w-full bg-white mt-1 rounded-md shadow-lg border border-gray-200 hidden"></div>
                    </div>
                    <input type="hidden" name="action" value="quick_checkout">
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeCirculateModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">Cancel</button>
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let userDebounceTimer;
        let bookDebounceTimer;
        // Function to handle user input suggestions
        document.getElementById('user_id').addEventListener('input', function(e) {
            clearTimeout(userDebounceTimer);
            const query = e.target.value.trim();
            const suggestionsDiv = document.getElementById('userSuggestions');
            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                return;
            }
            userDebounceTimer = setTimeout(() => {
                fetch(`circulation.php?type=user&query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.suggestions.length > 0) {
                            suggestionsDiv.innerHTML = data.suggestions.map(suggestion => 
                                `<div class="suggestion-item p-2 hover:bg-blue-600 hover:text-white cursor-pointer" data-id="${suggestion.id}">
                                    <div class="font-medium">${suggestion.display_text}</div>
                                    <div class="text-sm text-gray-500 hover:text-white">${suggestion.sub_text}</div>
                                </div>`
                            ).join('');
                            suggestionsDiv.classList.remove('hidden');
                            // Add click handlers to suggestions
                            suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    document.getElementById('user_id').value = this.dataset.id;
                                    suggestionsDiv.classList.add('hidden');
                                    
                                    // Clear book input when user changes
                                    document.getElementById('book_input').value = '';
                                    document.getElementById('bookAvailability').classList.add('hidden');
                                });
                            });
                        } else {
                            suggestionsDiv.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching suggestions:', error);
                        suggestionsDiv.classList.add('hidden');
                    });
            }, 300);
        });
        // Function to handle book input suggestions
        document.getElementById('book_input').addEventListener('input', function(e) {
            clearTimeout(bookDebounceTimer);
            const query = e.target.value.trim();
            const suggestionsDiv = document.getElementById('bookSuggestions');
            const userId = document.getElementById('user_id').value;
            
            if (query.length < 2) {
                suggestionsDiv.classList.add('hidden');
                return;
            }
            
            // Don't search for books if no user is selected
            if (!userId) {
                Swal.fire({
                    title: 'Select User First',
                    text: 'Please select a user before searching for books.',
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
                return;
            }
            
            bookDebounceTimer = setTimeout(() => {
                fetch(`circulation.php?type=book&query=${encodeURIComponent(query)}&user_id=${encodeURIComponent(userId)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success' && data.suggestions.length > 0) {
                            suggestionsDiv.innerHTML = data.suggestions.map(suggestion => 
                                `<div class="suggestion-item p-2 hover:bg-blue-600 hover:text-white cursor-pointer" 
                                      data-id="${suggestion.id}"
                                      data-value="${suggestion.full_value}"
                                      data-available="${suggestion.is_available}"
                                      data-reserved="${suggestion.is_reserved}">
                                    <div class="font-medium">${suggestion.display_text}</div>
                                    <div class="text-sm text-gray-500 hover:text-white">${suggestion.sub_text}</div>
                                </div>`
                            ).join('');
                            suggestionsDiv.classList.remove('hidden');
                            // Add click handlers to suggestions
                            suggestionsDiv.querySelectorAll('.suggestion-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    const isAvailable = this.dataset.available === 'true';
                                    const isReserved = this.dataset.reserved === 'true';
                                    const bookInput = document.getElementById('book_input');
                                    
                                    if (!isAvailable && !isReserved) {
                                        Swal.fire({
                                            title: 'Book Not Available',
                                            text: 'This book is not available for borrowing.',
                                            icon: 'warning',
                                            confirmButtonText: 'OK'
                                        });
                                        return;
                                    }
                                    
                                    bookInput.value = this.dataset.value; // Display the book title
                                    bookInput.dataset.selectedId = this.dataset.id; // Store the ID separately
                                    
                                    // Show availability indicator
                                    const availabilityDiv = document.getElementById('bookAvailability');
                                    if (isReserved) {
                                        availabilityDiv.innerHTML = '<span class="text-blue-600">Reserved by You</span>';
                                    } else {
                                        availabilityDiv.innerHTML = '<span class="text-green-600">Available</span>';
                                    }
                                    availabilityDiv.classList.remove('hidden');
                                    
                                    suggestionsDiv.classList.add('hidden');
                                });
                            });
                        } else {
                            suggestionsDiv.classList.add('hidden');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching suggestions:', error);
                        suggestionsDiv.classList.add('hidden');
                    });
            }, 300);
        });
        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#user_id') && !e.target.closest('#userSuggestions')) {
                document.getElementById('userSuggestions').classList.add('hidden');
            }
            if (!e.target.closest('#book_input') && !e.target.closest('#bookSuggestions')) {
                document.getElementById('bookSuggestions').classList.add('hidden');
            }
        });
        // Function to open the Circulate Item modal
        function openCirculateModal() {
            document.getElementById('circulateModal').classList.remove('hidden');
        }
        // Function to close the Circulate Item modal
        function closeCirculateModal() {
            document.getElementById('circulateModal').classList.add('hidden');
            resetCirculateModal();
        }
        // Function to reset the Circulate Item modal fields
        function resetCirculateModal() {
            document.getElementById('user_id').value = '';
            document.getElementById('book_input').value = '';
            document.getElementById('bookAvailability').classList.add('hidden');
        }
        // Function to search users
        function searchUsers() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const tbody = document.getElementById('userTableBody');
            const rows = tbody.getElementsByTagName('tr');
            for (let i = 0; i < rows.length; i++) {
                const userIdCell = rows[i].getElementsByTagName('td')[0];
                const userNameCell = rows[i].getElementsByTagName('td')[1];
                if (userIdCell && userNameCell) {
                    const userId = userIdCell.textContent || userIdCell.innerText;
                    const userName = userNameCell.textContent || userNameCell.innerText;
                    if (userId.toLowerCase().indexOf(filter) > -1 || userName.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        }
        
        // Function to handle long book titles
        function setupBookTitleTooltips() {
            const bookTitleCells = document.querySelectorAll('td:nth-child(3) .truncate');
            bookTitleCells.forEach(cell => {
                // Check if the content is actually truncated
                if (cell.scrollWidth > cell.clientWidth) {
                    cell.classList.add('cursor-help');
                }
            });
        }
        
        // Initialize tooltips when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            setupBookTitleTooltips();
        });

        // Function to handle quick checkout
        function quickCheckout(userId, bookId, copies) {
            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to check out this book.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, check out!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        action: 'checkout',
                        user_id: userId,
                        book_id: bookId,
                        copies: copies
                    };
                    fetch('circulation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Checked Out!',
                                'The book has been checked out successfully.',
                                'success'
                            );
                            location.reload(); // Reload the page to see updated data
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message,
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
                }
            });
        }
        // Function to handle check in
        function checkIn(userId, bookId) {
            // Show SweetAlert confirmation dialog
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to check in this book.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, check in!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const data = {
                        action: 'checkin', // Indicate check-in action
                        user_id: userId,
                        book_id: bookId
                    };
                    fetch('circulation.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(data)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire(
                                'Checked In!',
                                'The book has been checked in successfully.',
                                'success'
                            );
                            location.reload(); // Reload the page to see updated data
                        } else {
                            Swal.fire(
                                'Error!',
                                data.message,
                                'error'
                            );
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    });
                }
            });
        }
        // Function to submit quick checkout from the modal
        function submitQuickCheckout(event) {
            event.preventDefault(); // Prevent default form submission
            const userId = document.getElementById('user_id').value;
            const bookInput = document.getElementById('book_input');
            const bookId = bookInput.dataset.selectedId; // Get selected book ID
            
            // Validate inputs
            if (!userId || !bookId) {
                Swal.fire(
                    'Error!',
                    'Please select both a user and a book.',
                    'error'
                );
                return;
            }
            
            const copies = 1; // Assuming 1 copy is checked out
            const data = {
                action: 'checkout',
                user_id: userId,
                book_id: bookId,
                copies: copies
            };
            
            // Show loading state
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your request.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('circulation.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        'Checked Out!',
                        'The book has been checked out successfully.',
                        'success'
                    );
                    closeCirculateModal(); // Close the modal after success
                    location.reload(); // Reload the page to see updated data
                } else {
                    // Check for specific error messages
                    let errorTitle = 'Book Not Available!';
                    let errorIcon = 'warning';
                    
                    if (data.message.includes('already borrowed')) {
                        errorTitle = 'Already Borrowed!';
                        errorIcon = 'info';
                    }
                    
                    Swal.fire(
                        errorTitle,
                        data.message || 'This book is not available for borrowing.',
                        errorIcon
                    );
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                Swal.fire(
                    'Error!',
                    'An error occurred while processing your request.',
                    'error'
                );
            });
        }
    </script>
</body>
</html>