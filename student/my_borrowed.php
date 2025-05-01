<?php
session_start(); // Start the session
include '../component-library/connect.php';
include '../student/side_navbars.php';
// Database connection
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    // Set PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
// Fetch student profile data
$stud = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
$stud->execute([$user_id]);
$student = $stud->fetch(PDO::FETCH_ASSOC);
$profile_image = $student['images'] ?? '../images/prof.jpg'; // Fallback if no image
// Handle profile update via AJAX

$reservedBooksQuery = $conn->prepare("
    SELECT rb.*, b.title, b.books_image, b.author, b.publisher, b.copyright, b.ISBN, rb.status 
    FROM reserve_books rb 
    JOIN books b ON rb.book_id = b.id
    WHERE rb.user_id = ? AND rb.status = 'cancel' 
");
$reservedBooksQuery->execute([$user_id]);
$reservedBooks = $reservedBooksQuery->fetchAll(PDO::FETCH_ASSOC);

// Get total reserved books count
$totalReservedBooks = count($reservedBooks);
// Check if the logout button is clicked

// Determine the current date for overdue check
$currentDate = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Borrowed Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2 md:mb-0">My Borrowed Books</h2>
                    <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg">
                        <span class="font-medium">Student ID:</span> 
                        <span class="font-bold"><?php echo htmlspecialchars($user_id); ?></span>
                    </div>
                </div>
                
                <?php if (empty($reservedBooks)): ?>
                    <div class="text-center py-10">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-book-open text-6xl"></i>
                        </div>
                        <p class="text-gray-500 text-lg">No books found</p>
                    </div>
                <?php else: ?>
                    <!-- Mobile view (card layout) -->
                    <div class="md:hidden space-y-4">
                        <?php foreach ($reservedBooks as $book): ?>
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="flex p-4">
                                    <div class="flex-shrink-0 mr-4">
                                        <?php if (!empty($book['books_image'])): ?>
                                            <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" 
                                                 alt="Book Cover" 
                                                 class="w-20 h-28 object-cover rounded shadow-sm">
                                        <?php else: ?>
                                            <div class="w-20 h-28 bg-gray-200 rounded flex items-center justify-center text-gray-500 text-xs text-center p-2">
                                                Missing Cover Photo
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 mb-1">
                                            <a href="./books?call_no=<?php echo urlencode($book['id']); ?>" class="hover:text-blue-600">
                                                <?php echo htmlspecialchars($book['title']); ?>
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-1"><?php echo htmlspecialchars($book['author']); ?></p>
                                        <p class="text-xs text-gray-500 mb-1"><?php echo htmlspecialchars($book['publisher']); ?></p>
                                        <div class="flex items-center mt-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php echo $book['status'] === 'cancel' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo htmlspecialchars($book['status']); ?>
                                            </span>
                                            <span class="ml-2 text-xs text-gray-500">
                                                Borrowed: <?php echo htmlspecialchars($book['borrowed_date']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Desktop view (table layout) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Cover
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Authors/Editors
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Publisher
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Copies
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Borrowed Date
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($reservedBooks as $book): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if (!empty($book['books_image'])): ?>
                                                <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" 
                                                     alt="Book Cover" 
                                                     class="w-16 h-22 object-cover rounded shadow-sm">
                                            <?php else: ?>
                                                <div class="w-16 h-22 bg-gray-200 rounded flex items-center justify-center text-gray-500 text-xs text-center p-2">
                                                    Missing Cover Photo
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="./books?call_no=<?php echo urlencode($book['id']); ?>" class="hover:text-blue-600">
                                                    <?php echo htmlspecialchars($book['title']); ?>
                                                </a>
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                <div>Publish Date: <?php echo htmlspecialchars($book['copyright']); ?></div>
                                                <div>ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></div>
                                                <div>Call No: <?php echo htmlspecialchars($book['call_no']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['author']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['publisher']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php echo $book['status'] === 'cancel' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'; ?>">
                                                <?php echo htmlspecialchars($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['copies']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo htmlspecialchars($book['borrowed_date']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../student/footer.php'; ?>
</body>
</html>