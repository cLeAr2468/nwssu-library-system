<?php
session_start();
include '../component-library/connect.php';
include './activity_logger.php';
try {
    // Fetch student information from the session
    $user_id = $_SESSION['user_id'] ?? null;
    // Fetch account status
    $accountStatusQuery = $conn->prepare("SELECT account_status FROM user_info WHERE user_id = ?");
    $accountStatusQuery->execute([$user_id]);
    $accountStatus = $accountStatusQuery->fetchColumn();
    
    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['action']) && $_POST['action'] === 'reserve_book') {
            try {
                if ($accountStatus === 'inactive') {
                    echo json_encode(['success' => false, 'message' => 'Your account is inactive! Book reservation unavailable.']);
                    exit();
                }
                $book_id = $_POST['id'] ?? '';
                
                // Get book details for logging
                $bookQuery = $conn->prepare("SELECT title, material_type FROM books WHERE id = ?");
                $bookQuery->execute([$book_id]);
                $book = $bookQuery->fetch(PDO::FETCH_ASSOC);

                // Check if the material type is "book"
                if ($book['material_type'] !== 'Book') {
                    echo json_encode(['success' => false, 'message' => 'Only books can be reserved. This material cannot be reserved.']);
                    exit();
                }
                
                // Log the reservation activity
                logActivity($user_id, 'reservation', 'Reserved book: ' . $book['title']);
                
                if ($user_id) {
                    // Check total number of active reservations and borrowed books
                    $checkTotalBooksQuery = "SELECT 
                        (SELECT COUNT(*) FROM reserve_books 
                         WHERE user_id = :user_id AND status = 'reserved') +
                        (SELECT COUNT(*) FROM borrowed_books 
                         WHERE user_id = :user_id AND status = 'borrowed') as total_books";
                    
                    $checkTotalStmt = $conn->prepare($checkTotalBooksQuery);
                    $checkTotalStmt->execute([':user_id' => $user_id]);
                    $totalBooks = $checkTotalStmt->fetchColumn();

                    if ($totalBooks >= 3) {
                        echo json_encode([
                            'success' => false, 
                            'message' => 'You have reached the maximum limit of 3 books (reserved + borrowed).'
                        ]);
                        exit();
                    }

                    // Check if the book is already reserved by the user and not returned
                    $checkReservationSql = "SELECT * FROM reserve_books 
                                           WHERE user_id = :user_id 
                                           AND book_id = :book_id 
                                           AND status = 'reserved'";
                    $checkReservationStmt = $conn->prepare($checkReservationSql);
                    $checkReservationStmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
                    $existingReservation = $checkReservationStmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($existingReservation) {
                        echo json_encode(['success' => false, 'message' => 'This book is already reserved in your account.']);
                        exit();
                    }

                    // Check if the book was previously borrowed and returned
                    $checkPreviousReservationSql = "SELECT * FROM reserve_books 
                                                   WHERE user_id = :user_id 
                                                   AND book_id = :book_id 
                                                   ORDER BY reserved_date DESC 
                                                   LIMIT 1";
                    $checkPreviousReservationStmt = $conn->prepare($checkPreviousReservationSql);
                    $checkPreviousReservationStmt->execute([':user_id' => $user_id, ':book_id' => $book_id]);
                    $previousReservation = $checkPreviousReservationStmt->fetch(PDO::FETCH_ASSOC);

                    // If there's a previous reservation and it's not returned, don't allow new reservation
                    if ($previousReservation && $previousReservation['status'] !== 'returned') {
                        echo json_encode(['success' => false, 'message' => 'Please return your previous reservation first.']);
                        exit();
                    }

                    // Check if the book is available
                    $checkSql = "SELECT copies, status FROM books WHERE id = :id";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->execute([':id' => $book_id]);
                    $book = $checkStmt->fetch(PDO::FETCH_ASSOC);
                    if (!$book) {
                        echo json_encode(['success' => false, 'message' => 'Book not found.']);
                        exit();
                    }
                    if ($book['copies'] <= 0 || $book['status'] !== 'available') {
                        echo json_encode(['success' => false, 'message' => 'This book is not available.']);
                        exit();
                    }
                    // Proceed with reservation ONLY if copies are available
                    $updateSql = "UPDATE books SET copies = copies - 1 WHERE id = :id";
                    $updateStmt = $conn->prepare($updateSql);
                    $updateStmt->execute([':id' => $book_id]);
                    // If only 1 copy left, mark book as unavailable
                    if ($book['copies'] - 1 <= 0) {
                        $conn->prepare("UPDATE books SET status = 'not available' WHERE id = :id")->execute([':id' => $book_id]);
                    }
                    // Insert reservation record
                    $sql = "INSERT INTO reserve_books (user_id, book_id, reserved_date, status, copies)
                            VALUES (:user_id, :book_id, NOW(), 'reserved', 1)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':user_id' => $user_id,
                        ':book_id' => $book_id,
                    ]);
                    echo json_encode(['success' => true, 'message' => 'Reservation Successful!']);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'message' => 'User information not available.']);
                    exit();
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
                exit();
            }
        }
    }
    
    // Regular page load - fetch book details
    $book_id = $_GET['id'] ?? null;
    $book = null;
    $relatedBooks = [];
    $isReserved = false;
    $isBorrowed = false;
    if ($book_id) {
        try {
            // Fetch the main book details
            $details = $conn->prepare("SELECT * FROM books WHERE id = :book_id");
            $details->execute([':book_id' => $book_id]);
            $book = $details->fetch(PDO::FETCH_ASSOC);
            // Check if user has an active (non-returned) reservation for this book
            if ($book && isset($user_id)) {
                $checkReservation = $conn->prepare("SELECT * FROM reserve_books 
                                                  WHERE user_id = :user_id 
                                                  AND book_id = :book_id 
                                                  AND (status = 'reserved' OR status = 'borrowed')");
                $checkReservation->execute([':user_id' => $user_id, ':book_id' => $book_id]);
                $reservation = $checkReservation->fetch(PDO::FETCH_ASSOC);
                $isReserved = !empty($reservation);
                $isBorrowed = !empty($reservation) && $reservation['status'] === 'borrowed';
            }
            // Fetch related books
            if ($book) {
                $booksRelated = $conn->prepare("SELECT * FROM books WHERE category = :category AND id != :book_id LIMIT 4");
                $booksRelated->execute([':category' => $book['category'], ':book_id' => $book_id]);
                $relatedBooks = $booksRelated->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Failed to fetch book details: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    }
    // Check if a related book is clicked
    $relatedId = $_GET['related_id'] ?? null;
    if ($relatedId) {
        try {
            $callno = $conn->prepare("SELECT * FROM books WHERE id = :related_id");
            $callno->execute([':related_id' => $relatedId]);
            $book = $callno->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $_SESSION['message'] = 'Failed to fetch related book details: ' . $e->getMessage();
            $_SESSION['message_type'] = 'error';
        }
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details: <?php echo htmlspecialchars($book['title'] ?? 'Not Found'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    keyframes: {
                        marquee: {
                            '0%': {
                                transform: 'translateX(0)'
                            },
                            '100%': {
                                transform: 'translateX(-50%)'
                            }
                        }
                    },
                    animation: {
                        marquee: 'marquee 2s linear infinite',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes marquee {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }

        .animate-marquee-slow {
            animation: marquee 15s linear infinite;
        }
        
        .hover-pause:hover {
            animation-play-state: paused;
        }
        
        /* Responsive styles for Related Books */
        @media (min-width: 1280px) {
            .animate-marquee-slow {
                animation: marquee 15s linear infinite;
            }
        }
        
        @media (min-width: 768px) and (max-width: 1279px) {
            .animate-marquee-slow {
                animation: marquee 10s linear infinite;
            }
        }
        
        @media (max-width: 767px) {
            .animate-marquee-slow {
                animation: marquee 7s linear infinite;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include '../student/side_navbars.php'; ?>
    <div class="container hidden md:block mx-auto px-[10%] md:px-[5%] py-8">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                <?php echo htmlspecialchars($_SESSION['message']); ?>
                <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($book): ?>
            <div class="bg-white rounded-lg shadow-lg p-6 md:p-10 mb-8 md:mt-0">
                <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-1/3 flex flex-col items-center mb-6 md:mb-0 md:order-last">
                        <?php if (!empty($book['books_image']) && file_exists("../uploaded_file/" . $book['books_image'])): ?>
                            <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" alt="Book Cover"
                                class="w-[150px] h-[200px] object-cover border border-gray-300 rounded-md shadow-custom mb-4">
                        <?php else: ?>
                            <div class="w-[150px] h-[200px] bg-gray-200 flex items-center justify-center text-gray-500 font-bold rounded-md shadow-custom mb-4">
                                Book Cover
                            </div>
                        <?php endif; ?>
                        <?php if ($book['status'] === 'available' && strtolower($book['material_type']) === 'book'): ?>
                            <button class="reserve-btn bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-full transition duration-300 mt-2 <?php echo ($isReserved || $isBorrowed) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                data-book-id="<?php echo htmlspecialchars($book['id']); ?>"
                                <?php echo ($isReserved || $isBorrowed) ? 'disabled' : ''; ?>>
                                <?php 
                                    if ($isBorrowed) {
                                        echo 'Borrowed';
                                    } elseif ($isReserved) {
                                        echo 'Reserved';
                                    } else {
                                        echo 'Reserve';
                                    }
                                ?>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="w-full md:w-2/3 pr-0 md:pr-8 md:order-first">
                        <h2 class="text-2xl text-[#156295] font-bold mb-4"><?php echo htmlspecialchars($book['title']); ?></h2>
                        <table class="w-full border-collapse">
                            <tbody>
                                <tr class="border-b">
                                    <th class="py-2 text-left w-1/3">Material Type</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['material_type']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Sub Type</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['sub_type']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Author</th>
                                    <td class="py-2">
                                        <?php echo htmlspecialchars($book['author']); ?>
                                        [ <a href="./author?author=<?php echo urlencode($book['author']); ?>" class="text-blue-600 hover:underline">Browse</a> ]
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Publisher</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['publisher']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Copy Right</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['copyright']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">ISBN</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['ISBN']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Call Number</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['call_no']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Status</th>
                                    <td class="py-2">
                                        <span class="<?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> px-2 py-1 rounded-full text-sm">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Category</th>
                                    <td class="py-2">
                                        <?php echo htmlspecialchars($book['category']); ?>
                                        [ <a href="./searchcategory?category=<?php echo urlencode($book['category']); ?>" class="text-blue-600 hover:underline">Browse</a> ]
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left"><?php echo $book['copies'] == 1 ? 'Copy' : 'Copies'; ?></th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['copies']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Edition</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['edition']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Page Range</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['page_number']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Subject</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['subject']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Summary</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['summary']); ?></td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Content</th>
                                    <td class="py-2"><?php echo htmlspecialchars($book['content']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-8" role="alert">
                <p>Book not found.</p>
            </div>
        <?php endif; ?>
    </div>
    <div class="max-w-sm mx-auto bg-white rounded-lg shadow-md overflow-hidden md:hidden mt-10">
        <div class="p-4">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="mb-4 p-4 rounded-lg <?php echo $_SESSION['message_type'] === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
                    <?php echo htmlspecialchars($_SESSION['message']); ?>
                    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <?php if (!empty($book['books_image']) && file_exists("./uploaded_file/" . $book['books_image'])): ?>
                        <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>"
                            alt="Book Cover"
                            class="w-24 h-32 object-cover rounded-md shadow-lg border border-gray-200">
                    <?php else: ?>
                        <div class="w-24 h-32 rounded-md shadow-lg bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-500 text-sm text-center">Book Cover</span>
                        </div>
                    <?php endif; ?>
                    <span class="<?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?> px-3 py-1 rounded-full text-sm font-medium inline-flex items-center mt-2">
                        <span class="<?php echo $book['status'] === 'available' ? 'bg-green-400' : 'bg-red-400'; ?> w-2 h-2 rounded-full mr-2"></span>
                        <?php echo htmlspecialchars($book['status']); ?>
                    </span>
                </div>
                <div class="flex-1 ">
                    <h2 class="text-lg font-bold text-[#156295]"><?php echo htmlspecialchars($book['title']); ?></h2>
                    <p class="text-sm text-gray-600">By: <a href="./author?author=<?php echo urlencode($book['author']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($book['author']); ?></a></p>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium"><?php echo $book['copies'] == 1 ? 'Copy' : 'Copies'; ?>:</span> <?php echo htmlspecialchars($book['copies']); ?>
                    </p>
                    <p class="text-sm text-gray-600 mt-2">
                        <span class="font-medium italic text-gray-400"><?php echo htmlspecialchars($book['material_type']); ?>
                    </p>
                </div>
            </div>
            <div class="mt-4 space-y-2 border-t pt-4">
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Sub Type :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['sub_type']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Category :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['category']); ?>
                        [ <a href="./searchcategory?category=<?php echo urlencode($book['category']); ?>" class="text-blue-600 hover:underline">Browse</a> ]
                    </span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Copy Right :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['copyright']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Publisher :</span>
                    <span class="text-gray-600"><a href="./publisher?publisher=<?php echo urlencode($book['publisher']); ?>" class="text-blue-600 hover:underline"><?php echo htmlspecialchars($book['publisher']); ?></a></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Call Number :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['call_no']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">ISBN :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['ISBN']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">ISSN :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['issn']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Edition :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['edition']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Subject :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['subject']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Page Range :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['page_number']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Summary :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['summary']); ?></span>
                </p>
                <p class="text-sm">
                    <span class="font-medium text-gray-700">Content :</span>
                    <span class="text-gray-600"><?php echo htmlspecialchars($book['content']); ?></span>
                </p>
            </div>
            <div class="mt-4 flex flex-col space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex space-x-2">
                        <?php if ($book['status'] === 'available' && strtolower($book['material_type']) === 'book'): ?>
                            <button class="reserve-btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-1.5 rounded-full text-sm font-medium transition duration-150 ease-in-out <?php echo ($isReserved || $isBorrowed) ? 'opacity-50 cursor-not-allowed' : ''; ?>"
                                data-book-id="<?php echo htmlspecialchars($book['id']); ?>"
                                <?php echo ($isReserved || $isBorrowed) ? 'disabled' : ''; ?>>
                                <?php 
                                    if ($isBorrowed) {
                                        echo 'Borrowed';
                                    } elseif ($isReserved) {
                                        echo 'Reserved';
                                    } else {
                                        echo 'Reserve Book';
                                    }
                                ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container mx-auto px-auto py-8 max-w-7xl">
        <div class="bg-white rounded-lg shadow-lg p-6 md:p-10 mb-8">
            <h4 class="text-xl font-semibold mb-6 text-center">Related Items</h4>
            <div class="overflow-hidden relative">
                <div class="flex whitespace-nowrap animate-marquee-slow hover-pause">
                    <?php
                    // Display all related books twice for continuous scrolling effect
                    for ($i = 0; $i < 2; $i++):
                        foreach ($relatedBooks as $relatedBook):
                    ?>
                            <div class="inline-block flex-shrink-0 p-3 mx-2">
                                <a href="?id=<?php echo urlencode($relatedBook['id']); ?>" title="Click to view details">
                                    <div class="w-[100px] h-[150px] rounded-md shadow-lg overflow-hidden">
                                        <?php if (!empty($relatedBook['books_image']) && file_exists("../uploaded_file/" . $relatedBook['books_image'])): ?>
                                            <img src="./uploaded_file/<?php echo htmlspecialchars($relatedBook['books_image']); ?>"
                                                alt="Related Book Cover"
                                                class="w-full h-full object-cover hover:opacity-90 transition duration-300">
                                        <?php else: ?>
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold hover:opacity-90 transition duration-300">
                                                Book Cover
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                    <?php
                        endforeach;
                    endfor;
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php include '../student/footer.php'; ?>
    <script>
        document.querySelectorAll('.reserve-btn').forEach(button => {
            button.addEventListener('click', function() {
                const bookId = this.dataset.bookId;
                if (this.disabled) return;

                Swal.fire({
                    title: 'Confirm Reservation',
                    text: 'Do you want to reserve this book?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, reserve it!',
                    cancelButtonText: 'No, cancel',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(window.location.href, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `action=reserve_book&id=${bookId}`
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        title: 'Reservation Successful!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#3085d6'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Reservation Failed',
                                        text: data.message,
                                        icon: 'error',
                                        confirmButtonColor: '#3085d6'
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire({
                                    title: 'Reservation Failed',
                                    text: 'An error occurred while processing your request.',
                                    icon: 'error',
                                    confirmButtonColor: '#3085d6'
                                });
                            });
                    }
                });
            });
        });
    </script>
</body>

</html>