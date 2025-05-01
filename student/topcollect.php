<?php

session_start();
include '../component-library/connect.php';
include '../student/side_navbars.php';
try {
    $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $user_name, $user_password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Assume the logged-in student ID is stored in session
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('location:student_login.php');
    exit();
}

// Logout logic
if (isset($_POST['logout'])) {
    unset($_SESSION['user_id']);
    header('location:../index.html');
    exit();
}

// Pagination variables
$limit = 10; // Number of books to display per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page
$offset = ($page - 1) * $limit; // Calculate offset
$totalPages = 0; // Initialize totalPages

try {
    // Prepare SQL to get the most reserved books
    $stmt = $conn->prepare("
        SELECT b.id, b.title, b.books_image, b.status, b.copies, b.copyright, b.material_type, b.category, b.publisher, b.author, b.ISBN, 
               COUNT(rb.book_id) AS reservation_count
        FROM books b
        LEFT JOIN reserve_books rb ON b.id = rb.book_id
        GROUP BY b.id, b.title, b.books_image, b.status, b.copies, b.copyright, b.publisher, b.author, b.ISBN
        ORDER BY reservation_count DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total count for pagination
    $countStmt = $conn->prepare("
        SELECT COUNT(DISTINCT b.id) AS total_count
        FROM books b
        LEFT JOIN reserve_books rb ON b.id = rb.book_id
    ");
    $countStmt->execute();
    $totalBooks = $countStmt->fetchColumn();

    // Calculate total pages only if we have books
    if ($totalBooks > 0) {
        $totalPages = ceil($totalBooks / $limit); // Calculate total pages
    }
} catch (PDOException $e) {
    // Handle error
    echo "Error: " . $e->getMessage();
    exit(); // Exit on error
}

// Fetch student profile data
$stud = $conn->prepare("SELECT * FROM user_info WHERE user_id = ?");
$stud->execute([$user_id]);
$student = $stud->fetch(PDO::FETCH_ASSOC);
$profile_image = $student['images'] ?? '../images/prof.jpg'; // Fallback if no image

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

</head>

<body class="bg-gray-50">



    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-4 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800">Top Collection</h2>
                </div>

                <!-- Responsive Table -->
                <div class="overflow-x-auto">
                    <!-- Desktop Table (hidden on mobile) -->
                    <table class="min-w-full divide-y divide-gray-200 hidden md:table">
                        <thead class="bg-primary">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider"></th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Title</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Authors/Editors</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Publisher</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-white uppercase tracking-wider">Copies</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="bookTable">
                            <?php if (empty($books)): ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">No books found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($books as $book): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex justify-center items-center h-28">
                                                <?php if (!empty($book['books_image'])): ?>
                                                    <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>" alt="Book Cover" class="h-28 w-20 object-cover rounded shadow-sm">
                                                <?php else: ?>
                                                    <div class="h-28 w-20 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                                                        <span class="text-xs font-bold">Book Cover</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <a href="studbook_detail.php?id=<?php echo urlencode($book['id']); ?>" class="text-[#156295] hover:text-blue-800 hover:underline font-medium">
                                                    <?php echo htmlspecialchars($book['title']); ?>
                                                </a>
                                                <div class="text-gray-500 text-xs mt-1">
                                                    <p>Copy Right: <?php echo htmlspecialchars($book['copyright']); ?></p>
                                                    <p>ISBN: <?php echo htmlspecialchars($book['ISBN']); ?></p>
                                                    <p>Call No: <?php echo htmlspecialchars($book['id']); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="selected_author.php?author=<?php echo urlencode($book['author']); ?>" class="text-blue-800 hover:text-blue-800 text-sm">
                                                <?php echo htmlspecialchars($book['author']); ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="publisher_browse.php?publisher=<?php echo urlencode($book['publisher']); ?>" class="text-blue-800 hover:text-blue-800 text-sm">
                                                <?php echo htmlspecialchars($book['publisher']); ?>
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <?php echo htmlspecialchars($book['status']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            <?php echo htmlspecialchars($book['copies']); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Mobile Content -->
                    <div class="flex-1 px-4 py-4 md:hidden">
                        <div class="space-y-4">
                            <?php if (empty($books)): ?>
                                <!-- Empty State -->
                                <div class="flex flex-col items-center justify-center py-12">
                                    <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                    <p class="mt-4 text-gray-500 text-center">No books found</p>
                                    <a href="?clear=1" class="mt-2 text-primary hover:text-primary-hover">Clear filters</a>
                                </div>
                            <?php else: ?>
                                <?php foreach ($books as $book): ?>
                                    <!-- Book Card -->
                                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                        <div class="flex p-4">
                                            <!-- Book Cover -->
                                            <div class="flex-shrink-0">
                                                <!-- Book Cover -->
                                                <div class="relative">
                                                    <?php if (!empty($book['books_image'])): ?>
                                                        <img src="./uploaded_file/<?php echo htmlspecialchars($book['books_image']); ?>"
                                                            alt="Book Cover"
                                                            class="w-20 h-28 object-cover border border-gray-200 rounded-lg shadow-sm">
                                                    <?php else: ?>
                                                        <div class="w-20 h-28 bg-gray-200 flex items-center justify-center text-gray-600 text-xs font-medium border border-gray-300 rounded-lg">
                                                            Book Cover
                                                        </div>
                                                    <?php endif; ?>


                                                </div>

                                                <!-- Material Type (below book cover) -->
                                                <div class="mt-2 text-center">
                                                    <span class="text-xs text-gray-500  px-2 py-1 italic">
                                                        <?php echo htmlspecialchars($book['material_type']); ?>
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Book Info -->
                                            <div class="ml-4 flex-1">
                                                <div class="flex flex-col h-full">
                                                    <div>
                                                        <a href="./books ?id=<?php echo urlencode($book['id']); ?>"
                                                            class="text-base font-semibold text-[#156295] hover:text-primary line-clamp-2">
                                                            <?php echo htmlspecialchars($book['title']); ?>
                                                        </a>
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            Author : <a href="./author?author=<?php echo urlencode($book['author']); ?>"
                                                                class="text-blue-600 hover:text-primary-hover">
                                                                <?php echo htmlspecialchars($book['author']); ?>
                                                            </a>
                                                        </p>
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            Publisher : <a href="./publisher?publisher=<?php echo urlencode($book['publisher']); ?>"
                                                                class="text-blue-600 hover:text-primary-hover">
                                                                <?php echo htmlspecialchars($book['publisher']); ?>
                                                            </a>
                                                        </p>
                                                        <div class="mt-auto">
                                                            <div class="flex flex-wrap gap-y-1 gap-x-4 text-xs text-gray-500 mt-2">
                                                                <div class="flex items-center">
                                                                    <?php echo $book['copies'] == 1 ? 'copy' : 'copies'; ?> : <?php echo htmlspecialchars($book['copies']); ?>
                                                                </div>
                                                                <div class="flex items-center">
                                                                    Category : <a href="./searchcategory?category=<?php echo urlencode($book['category']); ?>"
                                                                        class="text-blue-600 font-medium hover:text-primary-hover mx-auto ml-2">
                                                                        <?php echo htmlspecialchars($book['category']); ?>
                                                                    </a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="border-t border-gray-100 bg-gray-50/50 px-4 py-3 flex justify-between items-center">
                                            <div class="text-xs text-gray-500">
                                                <span class="<?php echo $book['status'] === 'available' ? 'bg-green-100 text-green-800 ring-green-200' : 'bg-red-100 text-red-800 ring-red-200'; ?> text-xs font-medium px-2 py-1 rounded-full ring-1">
                                                    <?php echo htmlspecialchars($book['status']); ?>
                                                </span>
                                            </div>
                                            <div class="flex gap-2">
                                                <a href="./books?id=<?php echo urlencode($book['id']); ?>"
                                                    class="text-xs bg-primary text-white px-3 py-1.5 rounded-full hover:bg-primary-hover transition-colors">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 0): ?>
                <div class="mt-6 py-6">
                    <nav class="flex justify-center" aria-label="Page navigation">
                        <ul class="flex flex-wrap justify-center gap-1">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li>
                                    <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50'; ?> px-3 py-1 sm:px-4 sm:py-2 border border-gray-300 rounded-md text-sm font-medium">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>

    <?php include '../student/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    </script>
</body>

</html>